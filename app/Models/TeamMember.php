<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Pivot model for team_members table.
 *
 * Use this model when you need direct access to the pivot row with all its
 * extra columns (role, joined_at). For collection traversal, use the
 * Team::members() or Member::teams() BelongsToMany relationships.
 */
class TeamMember extends Pivot
{
    protected $table = 'team_members';

    public $incrementing = true;

    protected $fillable = [
        'team_id',
        'member_id',
        'role',
        'joined_at',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'joined_at' => 'date',
        ];
    }

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    /** @return BelongsTo<Team, $this> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /** @return BelongsTo<Member, $this> */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    // ──────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────

    public function isSubstitute(): bool
    {
        return $this->role === 'substitute';
    }
}
