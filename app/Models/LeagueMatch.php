<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Named LeagueMatch to avoid collision with PHP/MySQL reserved word "matches".
 * Maps to the "league_matches" table.
 */
class LeagueMatch extends Model
{
    protected $table = 'league_matches';

    protected $fillable = [
        'season_id',
        'division_id',
        'week_number',
        'is_playoff',
        'playoff_round',
        'match_date',
        'home_team_id',
        'away_team_id',
        'home_score',
        'away_score',
        'received_status',
        'received_at',
        'scoresheet_submitted_by',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'week_number'  => 'integer',
            'is_playoff'   => 'boolean',
            'match_date'   => 'date',
            'home_score'   => 'integer',
            'away_score'   => 'integer',
            'received_at'  => 'datetime',
        ];
    }

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    /** @return BelongsTo<Season, $this> */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    /** @return BelongsTo<Division, $this> */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    /** @return BelongsTo<Team, $this> */
    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    /** @return BelongsTo<Team, $this> */
    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    /** @return BelongsTo<User, $this> */
    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scoresheet_submitted_by');
    }

    /** @return HasMany<GameLeg, $this> */
    public function gameLegs(): HasMany
    {
        return $this->hasMany(GameLeg::class, 'match_id');
    }

    /** @return HasMany<Patch, $this> */
    public function patches(): HasMany
    {
        return $this->hasMany(Patch::class, 'match_id');
    }

    // ──────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────

    public function weekLabel(): string
    {
        if ($this->is_playoff && $this->playoff_round) {
            return match ($this->playoff_round) {
                'Q' => 'Quarterfinals',
                'S' => 'Semifinals',
                'F' => 'Finals',
                default => 'Playoff - ' . $this->playoff_round,
            };
        }

        return 'Week ' . $this->week_number;
    }

    public function homeWon(): bool
    {
        return $this->home_score > $this->away_score;
    }
}
