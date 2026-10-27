<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectRepository extends Model
{
    protected $fillable = [
        'project_id',
        'git_connection_id',
        'connected_by',
        'provider',
        'external_id',
        'full_name',
        'name',
        'description',
        'repository_url',
        'default_branch',
        'is_private',
        'is_active',
        'last_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'is_private' => 'boolean',
            'is_active' => 'boolean',
            'last_synced_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function connectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'connected_by');
    }

    public function gitConnection(): BelongsTo
    {
        return $this->belongsTo(GitConnection::class);
    }
}
