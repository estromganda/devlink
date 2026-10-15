<?php

namespace App\Models;

// use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail, PasskeyUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function userTechnologies(): HasMany
    {
        return $this->hasMany(UserTechnology::class);
    }

    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(
            Technology::class,
            'user_technologies'
        )->withPivot([
            'skill_level',
            'years_experience',
            'is_primary',
        ])->withTimestamps();
    }

    public function createdCommunities(): HasMany
    {
        return $this->hasMany(Community::class, 'creator_id');
    }

    public function communityMemberships(): HasMany
    {
        return $this->hasMany(CommunityMember::class);
    }

    public function communities(): BelongsToMany
    {
        return $this->belongsToMany(
            Community::class,
            'community_members'
        )->withPivot([
            'role',
            'joined_at',
        ])->withTimestamps();
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function postLikes(): HasMany
    {
        return $this->hasMany(PostLike::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function ownedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'owner_id');
    }

    public function projectMemberships(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function collaboratedProjects(): BelongsToMany
    {
        return $this->belongsToMany(
            Project::class,
            'project_members'
        )->withPivot([
            'role',
            'joined_at',
        ])->withTimestamps();
    }

    public function collaborationOffers(): HasMany
    {
        return $this->hasMany(CollaborationOffer::class);
    }

    public function reviewedCollaborationOffers(): HasMany
    {
        return $this->hasMany(
            CollaborationOffer::class,
            'reviewed_by'
        );
    }

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(
            Conversation::class,
            'conversation_members'
        )->withPivot([
            'role',
            'joined_at',
            'last_read_at',
            'left_at',
        ])->withTimestamps();
    }

    public function conversationMemberships(): HasMany
    {
        return $this->hasMany(ConversationMember::class);
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(
            Message::class,
            'sender_id'
        );
    }

    public function submittedReports(): HasMany
    {
        return $this->hasMany(
            Report::class,
            'reporter_id'
        );
    }

    public function reviewedReports(): HasMany
    {
        return $this->hasMany(
            Report::class,
            'reviewed_by'
        );
    }
}
