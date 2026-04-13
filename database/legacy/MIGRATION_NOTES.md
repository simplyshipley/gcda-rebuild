# Legacy Data Migration Notes

Source: `/Users/bo/Sites/ddev/gcda` (MySQL 5.7, DDEV)
Target: `/Users/bo/Sites/ddev/gcda-rebuild` (MySQL 8.0, DDEV)
Command: `php artisan gcda:migrate-legacy --fresh`
Completed: 2026-04-13

---

## Record Counts

| Table | Migrated | Legacy Source |
|---|---|---|
| leagues | 5 | `seasons.league` (distinct) |
| seasons | 183 | `seasons` |
| divisions | 632 | derived from `teams.division` (distinct per season) |
| venues | 37 | `pubs` |
| members | 8,761 | `members` |
| teams | 3,353 | `teams` |
| league_matches | 19,146 | `match_info` |
| member_stats | 24,625 | `member_stats` |
| patches | 8,023 | `patches` |
| high_scores | 621 | `high_in` + `high_out` (aggregated per season+division) |

---

## Known NULL Date Issues

The legacy database stored minimal date information. Most tables tracked
week numbers and season IDs but not actual calendar dates. Several date
columns in the new schema are NULL for all or most migrated records.

**Rule that applies everywhere:** Never sort migrated data by a nullable date
column alone. Always use `season_id DESC` as the primary sort key. Nullable
date columns are for display only until new data is entered after go-live.

### patches.earned_at — 100% NULL (8,023 rows)

Legacy `patches` had no date column — only `season_id` and `week`.

**Impact:** `ORDER BY earned_at DESC` returns arbitrary order, surfacing
players from 2006 instead of current season.

**Fix applied 2026-04-13:** `RecentPatches` component now orders by
`season_id DESC, id DESC`. Current Winter 2026 patches show first.

### member_stats.stats_calculated_at — 100% NULL (24,625 rows)

Stats were recalculated via an admin button in the legacy site; no timestamp
was stored.

**Impact:** Any query ordering by `stats_calculated_at` returns rows in
arbitrary order.

**Fix needed if:** A future "last updated" display or sort is added. Use
`season_id DESC` or `updated_at DESC` instead. `stats_calculated_at` will
only be meaningful for stats recalculated after go-live.

### league_matches.match_date — 41 NULL (0.2% of 19,146)

Some `match_info.date` rows had invalid dates (year 0, year < 2000) rejected
by `safeDate()`.

**Impact:** 41 matches have no calendar date. Schedule views must sort by
`week_number ASC` within a season, not by `match_date`. Show "—" when NULL.

### league_matches.received_at — 879 NULL (4.6% of 19,146)

`match_info.date_received` had invalid dates (year 0, year 201 typo) rejected
by `safeDate()`. Display-only field — does not affect standings or logic.

No fix needed.

---

## Data Transformations Applied

### Member status mapping

Legacy `members.status` varchar → new boolean flags:

| Legacy value | `is_substitute` | `is_active` |
|---|---|---|
| `''` (empty — 8,731 rows) | false | true |
| `'1'` (23 rows) | false | true |
| `'0'` (9 rows) | false | false |
| `'2'` (1 row) | true | true |

### Patch type normalization

| Legacy `patch_type` | New enum |
|---|---|
| `' 6 Bulls'` (leading space) | `six_bulls` |
| `'5 Bulls'` | `five_bulls` |
| `'Cricket'` | `cricket` |
| `'Ton70'` | `ton70` |
| `'Ton80'` | `ton80` |

### Match received_status mapping

| Legacy value | New enum |
|---|---|
| `'0'` | `pending` |
| `'Late'` | `late` |
| `'On Time'` | `on_time` |

### fastest_501 sentinel removal

Legacy used `0` and `255` as "never finished a 501" sentinels (tinyint
unsigned). Both converted to `NULL` in the new schema per gap G-001/G-031.

### High scores aggregated

Legacy `high_in` and `high_out` stored per-member-per-week records. New
`high_scores` stores one aggregate row per season+division (best score +
holder). Migration picks MAX score per season+division. Per-week detail
is not preserved in the new schema.

### Divisions derived from teams

Legacy schema had no divisions table — division was a `char(2)` on each
`teams` row. The new `divisions` table was built from distinct
`(season_id, division)` combinations in `teams`. Display names default to
`"Division {CODE}"` and can be renamed in Filament admin.

### Date sanitization (`safeDate()`)

Rejects any date where the string is empty, starts with `0000`, or the year
is outside 2000–2030. Catches zero-date MySQL sentinels and typos like
`0201-11-02` (year 201).

---

## Duplicate Legacy Seasons

One duplicate: `tuesday / 2006 / sum / completed` appears as both
`season_id=1` and `season_id=5`. Both map to the same new season record via
`firstOrCreate`. All teams and stats from both IDs attach to the single
new record.

---

## Schema Fixes Required During Migration

Three ALTER TABLE migrations were added after initial schema creation when
legacy data exposed constraints that were too strict:

| Migration | Fix |
|---|---|
| `fix_seasons_nullable_columns` | `week_count` → nullable; `scoresheet_type` → nullable |
| `fix_member_stats_nullable_fks` | `team_id`, `division_id` → nullable (subs with no team) |
| `fix_patches_nullable_team_id` | `team_id` → nullable (legacy patches had no team FK) |
