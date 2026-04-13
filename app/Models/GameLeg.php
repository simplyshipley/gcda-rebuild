<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One row per game leg in a match.
 *
 * Replaces the 9 separate legacy game tables:
 * 501_games, doubles_501, doubles_cricket, singles_cricket,
 * doubles_301, trip_601, quad_801, etc.
 *
 * PPD formula: ((501 × finished_legs) - score_remaining) / total_darts
 */
class GameLeg extends Model
{
    protected $table = 'game_legs';

    protected $fillable = [
        'match_id',
        'game_number',
        'game_type',
        'home_player1_id',
        'home_player2_id',
        'away_player1_id',
        'away_player2_id',
        'home_score_remaining',
        'away_score_remaining',
        'home_total_darts',
        'away_total_darts',
        'home_mvp_count',
        'away_mvp_count',
        'home_won',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'game_number'          => 'integer',
            'home_score_remaining' => 'integer',
            'away_score_remaining' => 'integer',
            'home_total_darts'     => 'integer',
            'away_total_darts'     => 'integer',
            // G-044/G-045: unified tinyint for MVP counts
            'home_mvp_count'       => 'integer',
            'away_mvp_count'       => 'integer',
            'home_won'             => 'boolean',
        ];
    }

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    /** @return BelongsTo<LeagueMatch, $this> */
    public function match(): BelongsTo
    {
        return $this->belongsTo(LeagueMatch::class, 'match_id');
    }

    /** @return BelongsTo<Member, $this> */
    public function homePlayer1(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'home_player1_id');
    }

    /** @return BelongsTo<Member, $this> */
    public function homePlayer2(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'home_player2_id');
    }

    /** @return BelongsTo<Member, $this> */
    public function awayPlayer1(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'away_player1_id');
    }

    /** @return BelongsTo<Member, $this> */
    public function awayPlayer2(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'away_player2_id');
    }

    // ──────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────

    public function isDoubles(): bool
    {
        return str_contains($this->game_type, 'doubles')
            || in_array($this->game_type, ['trip_601', 'quad_801'], true);
    }

    public function homeFinished(): bool
    {
        return $this->home_score_remaining === 0;
    }

    public function awayFinished(): bool
    {
        return $this->away_score_remaining === 0;
    }
}
