<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'owner_id',
        'name',
        'slug',
        'description',
        'short_description',
        'status',
        'visibility',
        'repository_url',
        'demo_url',
        'cover_path',
        'looking_for_collaborators',
    ];

    protected function casts(): array
    {
        return [
            'looking_for_collaborators' => 'boolean',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function collaborators(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
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

    public function tasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class);
    }

    public function repositories(): HasMany
    {
        return $this->hasMany(ProjectRepository::class);
    }
}
