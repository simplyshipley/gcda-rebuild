<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * High In/Out records per division per season.
 * One row per division per season (unique constraint on season_id + division_id).
 * Max possible score: 170 (T20-T20-Bull).
 */
class HighScore extends Model
{
    protected $table = 'high_scores';

    // No created_at per spec — only updated_at
    const CREATED_AT = null;

    protected $fillable = [
        'season_id',
        'division_id',
        'high_in_score',
        'high_in_member_id',
        'high_out_score',
        'high_out_member_id',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'high_in_score'  => 'integer',
            'high_out_score' => 'integer',
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

    /** @return BelongsTo<Member, $this> */
    public function highInMember(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'high_in_member_id');
    }

    /** @return BelongsTo<Member, $this> */
    public function highOutMember(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'high_out_member_id');
    }
}
