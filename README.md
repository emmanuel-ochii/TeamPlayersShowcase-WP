# Team Players Showcase (Phase 1)

This plugin is now intentionally scoped to one job only: render a single screenshot-style player card.

## Phase 1 Scope

- Custom post type: `Players`
- Secure player detail fields:
  - Jersey number
  - Position
  - Age
  - Experience (years)
  - Height (imperial)
  - Height (metric cm)
- One dedicated frontend component (single player card layout)
- Single player fallback template (`/players/{player-slug}/`)

## Installation

1. Copy `team-players-showcase` into `wp-content/plugins/` or upload the zip.
2. Activate **Team Players Showcase**.
3. Go to **Players** and add player entries (with featured image).

## Shortcode

Use:

```text
[stp_player_card]
```

Options:

- `id` specific player post ID (optional)
- `class` extra CSS classes (optional)

Examples:

```text
[stp_player_card id="123"]
```

```text
[stp_player_card class="my-custom-wrapper"]
```

Notes:

- If `id` is omitted and you are on a single player page, current player is used.
- If `id` is omitted outside a single player page, latest published player is used.
- Backward aliases still work: `[stp_players]` and `[team_players]`.
