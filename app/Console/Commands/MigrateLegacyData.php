<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Division;
use App\Models\HighScore;
use App\Models\League;
use App\Models\LeagueMatch;
use App\Models\Member;
use App\Models\MemberStats;
use App\Models\Patch;
use App\Models\Season;
use App\Models\Team;
use App\Models\Venue;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateLegacyData extends Command
{
    protected $signature   = 'gcda:migrate-legacy {--fresh : Truncate all new tables before migrating}';
    protected $description = 'Migrate legacy GCDA database into the new schema';

    /** @var array<int, int> Legacy member_id → new member id */
    private array $memberMap = [];

    /** @var array<int, int> Legacy season_id → new season id */
    private array $seasonMap = [];

    /** @var array<int, int> Legacy team_id → new team id */
    private array $teamMap = [];

    /** @var array<int, int> Legacy pubs_id → new venue id */
    private array $venueMap = [];

    /** @var array<int, int> Legacy match_id → new match id */
    private array $matchMap = [];

    /**
     * Division key "season_id-division_code" → new division id
     *
     * @var array<string, int>
     */
    private array $divisionMap = [];

    /** @var array<string, int> League slug → league id */
    private array $leagueSlugs = [];

    /** @var array<string, string> Legacy league slug → human name */
    private const LEAGUE_NAMES = [
        'tuesday'  => 'Tuesday League',
        'tues6'    => 'Tuesday League (Div 6)',
        'tuesAA'   => 'Tuesday League (AA)',
        'weds'     => 'Wednesday League',
        'thursday' => 'Thursday League',
    ];

    /** @var array<string, string> League slug → day of week */
    private const LEAGUE_DAYS = [
        'tuesday'  => 'tuesday',
        'tues6'    => 'tuesday',
        'tuesAA'   => 'tuesday',
        'weds'     => 'wednesday',
        'thursday' => 'thursday',
    ];

    /** @var array<string, string> Legacy patch_type → new enum */
    private const PATCH_TYPE_MAP = [
        ' 6 Bulls' => 'six_bulls',
        '6 Bulls'  => 'six_bulls',
        '5 Bulls'  => 'five_bulls',
        'Cricket'  => 'cricket',
        'Ton70'    => 'ton70',
        'Ton80'    => 'ton80',
    ];

    /** @var array<string, string> Legacy received_status → new enum */
    private const STATUS_MAP = [
        '0'       => 'pending',
        'Late'    => 'late',
        'On Time' => 'on_time',
    ];

    public function handle(): int
    {
        if ($this->option('fresh')) {
            $this->freshTruncate();
        }

        $this->info('Starting GCDA legacy migration…');

        $this->migrateLeagues();
        $this->migrateSeasons();
        $this->migrateDivisions();
        $this->migrateVenues();
        $this->migrateMembers();
        $this->migrateTeams();
        $this->migrateMatches();
        $this->migrateMemberStats();
        $this->migratePatches();
        $this->migrateHighScores();

        $this->info('Migration complete.');

        return Command::SUCCESS;
    }

    // ──────────────────────────────────────────────
    // Fresh truncate
    // ──────────────────────────────────────────────

    private function freshTruncate(): void
    {
        $this->warn('Truncating all new tables…');
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach ([
            'high_scores', 'patches', 'member_stats', 'game_legs',
            'league_matches', 'team_members', 'teams',
            'members', 'venues', 'divisions', 'seasons', 'leagues',
        ] as $table) {
            DB::table($table)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    // ──────────────────────────────────────────────
    // Leagues
    // ──────────────────────────────────────────────

    private function migrateLeagues(): void
    {
        $this->info('Migrating leagues…');

        $slugs = DB::connection('legacy')
            ->table('seasons')
            ->select('league')
            ->distinct()
            ->pluck('league')
            ->filter()
            ->unique();

        foreach ($slugs as $slug) {
            $name = self::LEAGUE_NAMES[$slug] ?? ucfirst($slug).' League';
            $day  = self::LEAGUE_DAYS[$slug] ?? 'tuesday';

            $league = League::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'day_of_week' => $day, 'is_active' => true]
            );

            $this->leagueSlugs[$slug] = $league->id;
        }

        $this->line('  '.count($this->leagueSlugs).' leagues created/found.');
    }

    // ──────────────────────────────────────────────
    // Seasons
    // ──────────────────────────────────────────────

    private function migrateSeasons(): void
    {
        $this->info('Migrating seasons…');

        $legacySeasons = DB::connection('legacy')
            ->table('seasons')
            ->where('year', '>', 0)
            ->whereNotNull('league')
            ->where('league', '!=', '')
            ->get();

        foreach ($legacySeasons as $ls) {
            $leagueId = $this->leagueSlugs[$ls->league] ?? null;
            if (! $leagueId) {
                continue;
            }

            $status = match ($ls->status) {
                'current' => 'current',
                'future'  => 'future',
                default   => 'completed',
            };

            $season = Season::firstOrCreate(
                [
                    'league_id'   => $leagueId,
                    'season_code' => $ls->season,
                    'year'        => (int) $ls->year,
                ],
                [
                    'status'       => $status,
                    'week_count'   => $ls->num_weeks > 0 ? (int) $ls->num_weeks : null,
                    'current_week' => (int) $ls->current_week,
                ]
            );

            $this->seasonMap[(int) $ls->season_id] = $season->id;
        }

        $this->line('  '.count($this->seasonMap).' seasons mapped.');
    }

    // ──────────────────────────────────────────────
    // Divisions (derived from teams.division per season)
    // ──────────────────────────────────────────────

    private function migrateDivisions(): void
    {
        $this->info('Migrating divisions…');

        $rows = DB::connection('legacy')
            ->table('teams')
            ->select('season_id', 'division')
            ->distinct()
            ->orderBy('season_id')
            ->orderBy('division')
            ->get();

        foreach ($rows as $row) {
            $newSeasonId = $this->seasonMap[(int) $row->season_id] ?? null;
            if (! $newSeasonId) {
                continue;
            }

            $code = strtoupper(trim((string) $row->division));
            if ($code === '') {
                continue;
            }

            $div = Division::firstOrCreate(
                ['season_id' => $newSeasonId, 'code' => $code],
                [
                    'name'          => 'Division '.$code,
                    'display_order' => $this->divisionOrder($code),
                ]
            );

            $key = $row->season_id.'-'.$row->division;
            $this->divisionMap[$key] = $div->id;
        }

        $this->line('  '.count($this->divisionMap).' divisions derived.');
    }

    /** Return null for any bad legacy date (year 0, year <2000, blank). */
    private function safeDate(string $date): ?string
    {
        if (empty($date) || str_starts_with($date, '0000')) {
            return null;
        }

        $year = (int) substr($date, 0, 4);
        if ($year < 2000 || $year > 2030) {
            return null;
        }

        return $date;
    }

    private function divisionOrder(string $code): int
    {
        $order = ['AA' => 0, 'A' => 1, 'A1' => 2, 'AB' => 3, 'AT' => 4,
                  'B' => 5, 'C' => 6, 'D' => 7, 'E' => 8, 'F' => 9, 'G' => 10];

        return $order[$code] ?? 99;
    }

    // ──────────────────────────────────────────────
    // Venues (from pubs)
    // ──────────────────────────────────────────────

    private function migrateVenues(): void
    {
        $this->info('Migrating venues (pubs)…');

        $pubs = DB::connection('legacy')->table('pubs')->get();

        foreach ($pubs as $pub) {
            $venue = Venue::firstOrCreate(
                ['name' => trim((string) $pub->pub_name)],
                [
                    'website'   => $pub->pub_website ?: null,
                    'notes'     => $pub->pub_comments ?: null,
                    'is_active' => ($pub->pub_status === 'ac'),
                ]
            );

            $this->venueMap[(int) $pub->pubs_id] = $venue->id;
        }

        $this->line('  '.count($this->venueMap).' venues migrated.');
    }

    // ──────────────────────────────────────────────
    // Members
    // ──────────────────────────────────────────────

    private function migrateMembers(): void
    {
        $this->info('Migrating members…');

        $legacyMembers = DB::connection('legacy')->table('members')->get();

        foreach ($legacyMembers as $lm) {
            $statusRaw  = trim((string) $lm->status);
            $isSub      = $statusRaw === '2';
            $isInactive = $statusRaw === '0';

            $dartCardNum = (int) $lm->dart_id;

            $member = Member::firstOrCreate(
                ['dart_card_number' => $dartCardNum > 0 ? $dartCardNum : null],
                [
                    'first_name'     => trim((string) $lm->first_name) ?: '(Unknown)',
                    'last_name'      => trim((string) $lm->last_name),
                    'nickname'       => trim((string) $lm->nick_name) ?: null,
                    'email'          => filter_var(trim((string) $lm->email), FILTER_VALIDATE_EMAIL) ?: null,
                    'is_substitute'  => $isSub,
                    'is_active'      => ! $isInactive,
                    'is_placeholder' => false,
                ]
            );

            $this->memberMap[(int) $lm->member_id] = $member->id;
        }

        $this->line('  '.count($this->memberMap).' members migrated.');
    }

    // ──────────────────────────────────────────────
    // Teams
    // ──────────────────────────────────────────────

    private function migrateTeams(): void
    {
        $this->info('Migrating teams…');

        $legacyTeams = DB::connection('legacy')->table('teams')->get();

        foreach ($legacyTeams as $lt) {
            $newSeasonId = $this->seasonMap[(int) $lt->season_id] ?? null;
            if (! $newSeasonId) {
                continue;
            }

            $divKey        = $lt->season_id.'-'.$lt->division;
            $newDivisionId = $this->divisionMap[$divKey] ?? null;
            if (! $newDivisionId) {
                continue;
            }

            $newVenueId = $this->venueMap[(int) $lt->home_bar_id] ?? null;

            $team = Team::firstOrCreate(
                [
                    'season_id' => $newSeasonId,
                    'name'      => trim((string) $lt->team_name),
                ],
                [
                    'division_id'     => $newDivisionId,
                    'venue_id'        => $newVenueId,
                    'starting_points' => (float) $lt->points,
                    'penalties'       => (int) $lt->penalties,
                ]
            );

            $this->teamMap[(int) $lt->team_id] = $team->id;
        }

        $this->line('  '.count($this->teamMap).' teams migrated.');
    }

    // ──────────────────────────────────────────────
    // Matches
    // ──────────────────────────────────────────────

    private function migrateMatches(): void
    {
        $this->info('Migrating matches…');

        $legacyMatches = DB::connection('legacy')->table('match_info')->get();
        $count = 0;

        foreach ($legacyMatches as $lm) {
            $newSeasonId = $this->seasonMap[(int) $lm->season_id] ?? null;
            if (! $newSeasonId) {
                continue;
            }

            $divKey        = $lm->season_id.'-'.$lm->division;
            $newDivisionId = $this->divisionMap[$divKey] ?? null;
            if (! $newDivisionId) {
                continue;
            }

            $homeId = $this->teamMap[(int) $lm->hometeam_id] ?? null;
            $awayId = $this->teamMap[(int) $lm->awayteam_id] ?? null;
            if (! $homeId || ! $awayId) {
                continue;
            }

            $status = self::STATUS_MAP[trim((string) $lm->received_status)] ?? 'pending';

            $matchDate    = $this->safeDate((string) $lm->date);
            $receivedDate = $this->safeDate((string) $lm->date_received);

            $match = LeagueMatch::firstOrCreate(
                [
                    'season_id'    => $newSeasonId,
                    'week_number'  => (int) $lm->week_number,
                    'home_team_id' => $homeId,
                    'away_team_id' => $awayId,
                ],
                [
                    'division_id'     => $newDivisionId,
                    'match_date'      => $matchDate,
                    'home_score'      => (int) $lm->hometeam_score,
                    'away_score'      => (int) $lm->awayteam_score,
                    'received_status' => $status,
                    'received_at'     => $receivedDate,
                ]
            );

            $this->matchMap[(int) $lm->match_id] = $match->id;
            $count++;
        }

        $this->line("  {$count} matches migrated.");
    }

    // ──────────────────────────────────────────────
    // Member Stats
    // ──────────────────────────────────────────────

    private function migrateMemberStats(): void
    {
        $this->info('Migrating member stats…');

        $rows  = DB::connection('legacy')->table('member_stats')->get();
        $count = 0;

        foreach ($rows as $row) {
            $newMemberId = $this->memberMap[(int) $row->member_id] ?? null;
            $newSeasonId = $this->seasonMap[(int) $row->season_id] ?? null;
            $newTeamId   = $this->teamMap[(int) $row->team_id] ?? null;

            if (! $newMemberId || ! $newSeasonId) {
                continue;
            }

            $divKey        = $row->season_id.'-'.$row->division;
            $newDivisionId = $this->divisionMap[$divKey] ?? null;

            // fastest_501: 0 or 255 = sentinel "not set" → NULL
            $fastest501 = (int) $row->fastest_501;
            $fastest501 = ($fastest501 === 0 || $fastest501 === 255) ? null : $fastest501;

            MemberStats::firstOrCreate(
                [
                    'member_id' => $newMemberId,
                    'season_id' => $newSeasonId,
                ],
                [
                    'team_id'     => $newTeamId,
                    'division_id' => $newDivisionId,
                    'mvp_count'   => (int) $row->MVPs,
                    'fastest_501' => $fastest501,
                ]
            );

            $count++;
        }

        $this->line("  {$count} member stat records migrated.");
    }

    // ──────────────────────────────────────────────
    // Patches (Big Shots)
    // ──────────────────────────────────────────────

    private function migratePatches(): void
    {
        $this->info('Migrating patches (Big Shots)…');

        $rows  = DB::connection('legacy')->table('patches')->get();
        $count = 0;

        foreach ($rows as $row) {
            $newMemberId = $this->memberMap[(int) $row->member_id] ?? null;
            $newSeasonId = $this->seasonMap[(int) $row->season_id] ?? null;

            if (! $newMemberId || ! $newSeasonId) {
                continue;
            }

            $patchType = self::PATCH_TYPE_MAP[trim((string) $row->patch_type)] ?? null;
            if (! $patchType) {
                $this->warn("  Unknown patch_type: '{$row->patch_type}'");
                continue;
            }

            $weekRaw   = trim((string) $row->week);
            $weekNum   = is_numeric($weekRaw) ? (int) $weekRaw : null;
            $weekLabel = ! is_numeric($weekRaw) ? $weekRaw : null;

            Patch::firstOrCreate(
                [
                    'member_id'   => $newMemberId,
                    'season_id'   => $newSeasonId,
                    'patch_type'  => $patchType,
                    'week_number' => $weekNum,
                ],
                [
                    'week_label' => $weekLabel,
                ]
            );

            $count++;
        }

        $this->line("  {$count} patches migrated.");
    }

    // ──────────────────────────────────────────────
    // High Scores (aggregate max per season+division)
    // ──────────────────────────────────────────────

    private function migrateHighScores(): void
    {
        $this->info('Migrating high scores (season+division aggregates)…');

        // Collect all unique season+division combinations from legacy teams
        $combos = DB::connection('legacy')
            ->table('teams')
            ->select('season_id', 'division')
            ->distinct()
            ->get();

        $count = 0;

        foreach ($combos as $combo) {
            $newSeasonId = $this->seasonMap[(int) $combo->season_id] ?? null;
            $divKey      = $combo->season_id.'-'.$combo->division;
            $newDivId    = $this->divisionMap[$divKey] ?? null;

            if (! $newSeasonId || ! $newDivId) {
                continue;
            }

            // Best high_in for this season+division
            $bestIn = DB::connection('legacy')
                ->table('high_in')
                ->where('season_id', $combo->season_id)
                ->where('division', $combo->division)
                ->orderByDesc('high_in')
                ->first();

            // Best high_out for this season+division
            $bestOut = DB::connection('legacy')
                ->table('high_out')
                ->where('season_id', $combo->season_id)
                ->where('division', $combo->division)
                ->orderByDesc('high_out')
                ->first();

            if (! $bestIn && ! $bestOut) {
                continue;
            }

            $highInMemberId  = $bestIn  ? ($this->memberMap[(int) $bestIn->member_id]  ?? null) : null;
            $highOutMemberId = $bestOut ? ($this->memberMap[(int) $bestOut->member_id] ?? null) : null;

            HighScore::firstOrCreate(
                ['season_id' => $newSeasonId, 'division_id' => $newDivId],
                [
                    'high_in_score'      => $bestIn  ? (int) $bestIn->high_in   : null,
                    'high_in_member_id'  => $highInMemberId,
                    'high_out_score'     => $bestOut ? (int) $bestOut->high_out  : null,
                    'high_out_member_id' => $highOutMemberId,
                ]
            );

            $count++;
        }

        $this->line("  {$count} high score aggregate records migrated.");
    }
}
