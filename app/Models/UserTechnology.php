<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTechnology extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'technology_id',
        'skill_level',
        'years_experience',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'years_experience' => 'integer',
            'is_primary' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function technology(): BelongsTo
    {
        return $this->belongsTo(Technology::class);
    }
}
