<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Big Shots patches earned by a member in a match.
 * Patch types: Ton80 (180), Ton70, Cricket, 5 Bulls, 6 Bulls.
 */
class Patch extends Model
{
    protected $fillable = [
        'member_id',
        'team_id',
        'season_id',
        'match_id',
        'week_number',
        'week_label',
        'patch_type',
        'earned_at',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'week_number' => 'integer',
            'earned_at'   => 'date',
        ];
    }

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    /** @return BelongsTo<Member, $this> */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /** @return BelongsTo<Team, $this> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /** @return BelongsTo<Season, $this> */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    /** @return BelongsTo<LeagueMatch, $this> */
    public function match(): BelongsTo
    {
        return $this->belongsTo(LeagueMatch::class, 'match_id');
    }

    // ──────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────

    public function displayLabel(): string
    {
        return match ($this->patch_type) {
            'ton80'      => 'Ton 80 (180)',
            'ton70'      => 'Ton 70',
            'cricket'    => 'Cricket Patch',
            'five_bulls' => '5 Bulls',
            'six_bulls'  => '6 Bulls',
            default      => ucfirst($this->patch_type),
        };
    }
}
