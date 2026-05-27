<style>
    :root {
        --bg:         #0d0f14;
        --surface:    #161921;
        --border:     #252830;
        --accent:     #e8ff47;
        --accent-dim: rgba(232,255,71,0.12);
        --text:       #e8eaf0;
        --muted:      #6b7280;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        background: var(--bg);
        color: var(--text);
        font-family: 'DM Sans', sans-serif;
        font-size: 15px;
        min-height: 100vh;
    }

    /* PAGE WRAP */
    .page-wrap {
        max-width: 1100px;
        margin: 0 auto;
        padding: 2.5rem 1.5rem 4rem;
    }

    /* PAGE HEADER */
    .page-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 2rem;
        gap: 1rem;
        flex-wrap: wrap;
        opacity: 0;
        animation: fadeUp .5s ease forwards;
    }
    .page-title {
        font-family: 'Syne', sans-serif;
        font-weight: 800;
        font-size: clamp(1.6rem, 4vw, 2.4rem);
        line-height: 1.1;
        letter-spacing: -.03em;
    }
    .page-title span {
        display: block;
        color: var(--muted);
        font-size: .75em;
        font-weight: 600;
        letter-spacing: .05em;
        text-transform: uppercase;
        margin-bottom: .2rem;
    }
    .badge-count {
        background: var(--accent-dim);
        color: var(--accent);
        border: 1px solid rgba(232,255,71,0.3);
        border-radius: 100px;
        padding: .25rem .85rem;
        font-size: .78rem;
        font-weight: 600;
        font-family: 'Syne', sans-serif;
        white-space: nowrap;
        align-self: center;
    }

    /* FILTER BAR */
    .filter-bar {
        display: flex;
        align-items: center;
        gap: .75rem;
        margin-bottom: 1.75rem;
        flex-wrap: wrap;
        opacity: 0;
        animation: fadeUp .5s ease .1s forwards;
    }
    .filter-label {
        font-size: .8rem;
        font-weight: 500;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: .06em;
        white-space: nowrap;
    }
    .filter-select {
        background: var(--surface);
        color: var(--text);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: .45rem 2rem .45rem 1rem;
        font-family: 'DM Sans', sans-serif;
        font-size: .875rem;
        cursor: pointer;
        min-width: 220px;
        transition: border-color .2s;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right .75rem center;
    }
    .filter-select:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px var(--accent-dim);
    }
    .btn-filter {
        background: var(--accent);
        color: #0d0f14;
        border: none;
        border-radius: 8px;
        padding: .45rem 1.2rem;
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        font-size: .82rem;
        cursor: pointer;
        transition: opacity .2s, transform .15s;
    }
    .btn-filter:hover { opacity: .88; transform: translateY(-1px); }
    .btn-reset {
        background: transparent;
        color: var(--muted);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: .45rem 1rem;
        font-family: 'DM Sans', sans-serif;
        font-size: .82rem;
        cursor: pointer;
        text-decoration: none;
        transition: color .2s, border-color .2s;
    }
    .btn-reset:hover { color: var(--text); border-color: #444; }
    .search-input {
        background: var(--surface);
        color: var(--text);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: .45rem 1rem;
        font-family: 'DM Sans', sans-serif;
        font-size: .875rem;
        min-width: 200px;
        margin-left: auto;
        transition: border-color .2s;
    }
    .search-input::placeholder { color: var(--muted); }
    .search-input:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px var(--accent-dim);
    }

    /* TABLE */
    .table-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
        opacity: 0;
        animation: fadeUp .5s ease .2s forwards;
    }
    .table-responsive { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    thead tr { border-bottom: 1px solid var(--border); }
    thead th {
        padding: .85rem 1.25rem;
        font-family: 'Syne', sans-serif;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--muted);
        white-space: nowrap;
    }
    tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background .15s;
    }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: rgba(255,255,255,.025); }
    tbody td { padding: .95rem 1.25rem; vertical-align: middle; font-size: .875rem; }

    .cell-name  { font-weight: 500; }
    .cell-email { color: var(--muted); font-size: .82rem; }
    .cell-date  { color: var(--muted); font-size: .82rem; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .event-badge {
        display: inline-block;
        background: var(--accent-dim);
        color: var(--accent);
        border: 1px solid rgba(232,255,71,0.2);
        border-radius: 6px;
        padding: .2rem .65rem;
        font-size: .78rem;
        font-weight: 600;
        font-family: 'Syne', sans-serif;
        white-space: nowrap;
    }

    /* EMPTY STATE */
    .empty-state { padding: 4rem 2rem; text-align: center; }
    .empty-icon  { font-size: 2.5rem; margin-bottom: 1rem; opacity: .4; }
    .empty-state h3 {
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: .4rem;
    }
    .empty-state p { color: var(--muted); font-size: .875rem; }

    /* ANIMATION */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* SCROLLBAR */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: var(--bg); }
    ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }
</style>
