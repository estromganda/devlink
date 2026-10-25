<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\ConversationMember;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ConversationController extends Controller
{
    /**
     * Liste des conversations de l'utilisateur connecté.
     */
    public function index(Request $request): Response
    {
        $userId = $request->user()->id;

        $conversations = Conversation::query()
            ->whereHas('members', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->whereNull('left_at');
            })
            ->with([
                'members' => function ($query) {
                    $query->whereNull('left_at')
                        ->with('user:id,name');
                },
                'messages' => function ($query) {
                    $query->whereNull('deleted_at')
                        ->latest()
                        ->limit(1);
                },
            ])
            ->withCount([
                'messages as unread_count' => function ($query) use ($userId) {
                    $query->whereNull('deleted_at')
                        ->where('sender_id', '!=', $userId)
                        ->whereRaw(
                            'messages.created_at > COALESCE(
                                (
                                    SELECT last_read_at
                                    FROM conversation_members
                                    WHERE conversation_members.conversation_id = messages.conversation_id
                                    AND conversation_members.user_id = ?
                                    LIMIT 1
                                ),
                                TIMESTAMP \'1970-01-01 00:00:00\'
                            )',
                            [$userId]
                        );
                },
            ])
            ->orderByDesc(
                Message::query()
                    ->select('created_at')
                    ->whereColumn(
                        'messages.conversation_id',
                        'conversations.id'
                    )
                    ->whereNull('messages.deleted_at')
                    ->latest()
                    ->limit(1)
            )
            ->orderByDesc('conversations.updated_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('messages/Index', [
            'conversations' => $conversations,
        ]);
    }

    /**
     * Affiche les développeurs disponibles pour démarrer une conversation.
     */
    public function create(Request $request): Response
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $search = trim($validated['search'] ?? '');

        $developers = User::query()
            ->where('id', '!=', $request->user()->id)
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'ilike', '%' . $search . '%');
            })
            ->select(['id', 'name'])
            ->with('profile')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('messages/New', [
            'developers' => $developers,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Crée ou retrouve une conversation privée avec un développeur.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'recipient_id' => [
                'required',
                'integer',
                'exists:users,id',
                'different:' . $request->user()->id,
            ],
        ]);

        $currentUserId = $request->user()->id;
        $recipientId = (int) $validated['recipient_id'];

        $conversation = DB::transaction(function () use ($currentUserId, $recipientId) {
            /*
             * Recherche d'une conversation individuelle existante
             * à laquelle les deux utilisateurs participent encore.
             */
            $existingConversation = Conversation::query()
                ->where('type', 'direct')
                ->whereHas('members', function ($query) use ($currentUserId) {
                    $query->where('user_id', $currentUserId)
                        ->whereNull('left_at');
                })
                ->whereHas('members', function ($query) use ($recipientId) {
                    $query->where('user_id', $recipientId)
                        ->whereNull('left_at');
                })
                ->whereDoesntHave('members', function ($query) use (
                    $currentUserId,
                    $recipientId
                ) {
                    $query->whereNull('left_at')
                        ->whereNotIn('user_id', [
                            $currentUserId,
                            $recipientId,
                        ]);
                })
                ->first();

            if ($existingConversation) {
                return $existingConversation;
            }

            $conversation = Conversation::create([
                'type' => 'direct',
                'title' => null,
                'created_by' => $currentUserId,
            ]);

            ConversationMember::create([
                'conversation_id' => $conversation->id,
                'user_id' => $currentUserId,
                'role' => 'member',
                'joined_at' => now(),
            ]);

            ConversationMember::create([
                'conversation_id' => $conversation->id,
                'user_id' => $recipientId,
                'role' => 'member',
                'joined_at' => now(),
            ]);

            return $conversation;
        });

        return redirect()
            ->route('messages.show', $conversation->id)
            ->with('success', 'Conversation ouverte.');
    }

    /**
     * Affiche l'historique d'une conversation.
     */
    public function show(
        Request $request,
        Conversation $conversation
    ): Response {
        $userId = $request->user()->id;

        $membership = ConversationMember::query()
            ->where('conversation_id', $conversation->id)
            ->where('user_id', $userId)
            ->whereNull('left_at')
            ->first();

        abort_unless($membership, 403, 'Accès à cette conversation refusé.');

        $conversation->load([
            'members' => function ($query) {
                $query->whereNull('left_at')
                    ->with('user:id,name');
            },
        ]);

        $messages = Message::query()
            ->where('conversation_id', $conversation->id)
            ->whereNull('deleted_at')
            ->with([
                'sender:id,name',
                'replyTo.sender:id,name',
            ])
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        /*
         * Met à jour la dernière date de lecture.
         */
        $membership->update([
            'last_read_at' => now(),
        ]);

        return Inertia::render('messages/Show', [
            'conversation' => $conversation,
            'messages' => $messages,
            'currentUserId' => $userId,
        ]);
    }

    /**
     * Envoie un message dans une conversation.
     */
    public function send(Request $request, Conversation $conversation): RedirectResponse
    {
        $userId = $request->user()->id;

        $membership = ConversationMember::query()
            ->where('conversation_id', $conversation->id)
            ->where('user_id', $userId)
            ->whereNull('left_at')
            ->exists();

        abort_unless($membership, 403, 'Accès à cette conversation refusé.');

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:10000'],
            'reply_to_id' => [
                'nullable',
                'integer',
                'exists:messages,id',
            ],
        ]);

        if (! empty($validated['reply_to_id'])) {
            $replyExists = Message::query()
                ->where('id', $validated['reply_to_id'])
                ->where('conversation_id', $conversation->id)
                ->whereNull('deleted_at')
                ->exists();

            if (! $replyExists) {
                return back()->withErrors([
                    'reply_to_id' => 'Le message auquel vous répondez est introuvable dans cette conversation.',
                ]);
            }
        }

        $message = DB::transaction(function () use ($conversation, $validated, $userId, $request) {
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $userId,
                'body' => $validated['body'],
                'type' => 'text',
                'reply_to_id' => $validated['reply_to_id'] ?? null,
            ]);

            $conversation->touch();

            ConversationMember::query()
                ->where('conversation_id', $conversation->id)
                ->where('user_id', $userId)
                ->update([
                    'last_read_at' => now(),
                ]);
            return $message;
        });
        // Notification
        $conversation->loadMissing('members');
        foreach ($conversation->users as $member) {
            if ($member->id === $request->user()->id) {
                continue;
            }
            $member->notify(new NewMessageNotification($message));
        }
        // Broadcast
        broadcast(new MessageSent($message));

        return redirect()
            ->route('messages.show', $conversation->id)
            ->with('success', 'Message envoyé.');
    }
}
