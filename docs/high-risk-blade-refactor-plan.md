# High-Risk Blade Refactor Plan

## Goal
Refactor the remaining high-risk Blade forms only as coordinated blocks, not file-by-file.

## Block 1: Offline Visits
Files:
- resources/views/agente/visitas/create.blade.php
- resources/views/agente/visitas/edit.blade.php
- resources/views/saude/visitas/create.blade.php

Dependencies:
- localStorage draft handling
- custom window helpers
- offline restore events
- role-specific visit payloads

Notes:
- Keep the offline draft contract stable.
- Refactor shared field wrappers only after the shared draft state is isolated.

## Block 2: Local Map and CEP
Files:
- resources/views/agente/locais/create.blade.php
- resources/views/agente/locais/edit.blade.php

Dependencies:
- Leaflet map initialization
- CEP lookup flow
- geolocation button behavior
- hardcoded field ids used by the script

Notes:
- Split the map script only after extracting the field id contract.
- Validate edit vs create parity before changing markup.

## Block 3: Socioeconomic Household Form
Files:
- resources/views/municipio/locais/_form_ocupantes.blade.php
- resources/views/municipio/locais/_form_socioeconomico_head.blade.php
- resources/views/municipio/locais/_form_socioeconomico_tail.blade.php

Dependencies:
- Alpine x-for dynamic rows
- titular enforcement
- hidden field sync across partials
- computed socioeconomic totals

Notes:
- Refactor as a single subsystem.
- Do not wrap individual rows without updating the sync logic.

## Suggested Sequence
1. Extract shared state contracts.
2. Add regression tests for each subsystem.
3. Replace legacy labels and inputs in one coordinated pass.
4. Re-run feature tests covering offline, map, and socioeconomic flows.

## Deferred Items
- Auth templates using Fortify can be revisited later for consistency, but they are no longer blocking.
- Report filters can continue to be standardized separately because they do not share state with the high-risk blocks.
