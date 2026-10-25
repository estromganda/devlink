<?php

namespace Tests\Feature;

use App\Models\User;
// use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_notifications(): void
    {
        $this->getJson('/notifications')
            ->assertUnauthorized();
    }

    public function test_user_can_list_only_their_own_notifications(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $user->notify(new TestDatabaseNotification([
            'type' => 'new_answer',
            'message' => 'Someone answered your question.',
        ]));

        $otherUser->notify(new TestDatabaseNotification([
            'type' => 'new_message',
            'message' => 'You received a message.',
        ]));

        $response = $this->actingAs($user)
            ->getJson('/notifications')
            ->assertOk()
            ->assertJsonPath('unread_count', 1);

        $response->assertJsonCount(1, 'notifications.data');
        $response->assertJsonFragment([
            'type' => 'new_answer',
        ]);
        $response->assertJsonMissing([
            'type' => 'new_message',
        ]);
    }

    public function test_user_can_mark_their_notification_as_read(): void
    {
        $user = User::factory()->create();

        $user->notify(new TestDatabaseNotification([
            'type' => 'new_answer',
        ]));

        $notification = $user->notifications()->firstOrFail();

        $this->actingAs($user)
            ->patchJson("/notifications/{$notification->id}/read")
            ->assertOk()
            ->assertJsonPath('unread_count', 0);

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_cannot_mark_another_users_notification_as_read(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $otherUser->notify(new TestDatabaseNotification([
            'type' => 'new_answer',
        ]));

        $notification = $otherUser->notifications()->firstOrFail();

        $this->actingAs($user)
            ->patchJson("/notifications/{$notification->id}/read")
            ->assertNotFound();

        $this->assertNull($notification->fresh()->read_at);
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $user = User::factory()->create();

        $user->notify(new TestDatabaseNotification(['type' => 'new_answer']));
        $user->notify(new TestDatabaseNotification(['type' => 'new_message']));

        $this->actingAs($user)
            ->patchJson('/notifications/read-all')
            ->assertOk()
            ->assertJsonPath('unread_count', 0);

        $this->assertSame(
            0,
            $user->unreadNotifications()->count()
        );
    }
}


class TestDatabaseNotification extends \Illuminate\Notifications\Notification
{
    public function __construct(private array $data = []) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return $this->data;
    }
}
