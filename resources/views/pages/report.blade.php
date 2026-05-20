<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report — {{ $report->project_name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@300;400;500&family=DM+Mono&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --ink:    #0f0f0e;
            --ink2:   #5a5a56;
            --ink3:   #9a9a94;
            --paper:  #faf9f6;
            --paper2: #f0efe9;
            --paper3: #e8e7df;
            --accent: #1a472a;
            --accent2:#2d7a49;
            --rule:   rgba(15,15,14,.1);
            --red:    #c0392b;
            --red-bg: #fef2f2;
            --amber:  #b7791f;
            --amber-bg: #fffbeb;
            --green:  #166534;
            --green-bg: #f0fdf4;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--paper);
            color: var(--ink);
            min-height: 100vh;
        }

        /* ── HEADER ── */
        header {
            border-bottom: 1px solid var(--rule);
            padding: .9rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .wordmark {
            font-family: 'DM Serif Display', serif;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: var(--ink);
        }
        .wordmark span { color: var(--accent2); }
        .header-url {
            font-family: 'DM Mono', monospace;
            font-size: .75rem;
            color: var(--ink3);
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* ── LAYOUT ── */
        .page {
            max-width: 860px;
            margin: 0 auto;
            padding: 2rem 1.5rem 4rem;
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 1.5rem;
        }
        .main-col { min-width: 0; }
        .side-col { min-width: 0; }

        /* ── SECTION ── */
        section { margin-bottom: 1.5rem; }
        .section-label {
            font-size: .7rem;
            font-weight: 500;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--ink3);
            margin-bottom: .75rem;
        }

        /* ── SCORE OVERVIEW ── */
        .overview-card {
            background: var(--ink);
            color: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .overview-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 1.25rem;
        }
        .project-name {
            font-family: 'DM Serif Display', serif;
            font-size: 1.4rem;
            line-height: 1.2;
        }
        .big-score {
            text-align: right;
        }
        .big-score .num {
            font-family: 'DM Serif Display', serif;
            font-size: 3rem;
            line-height: 1;
        }
        .big-score .label {
            font-size: .72rem;
            color: rgba(255,255,255,.5);
            letter-spacing: .06em;
            text-transform: uppercase;
        }
        .pillar-scores {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        .pillar-score-item {
            background: rgba(255,255,255,.08);
            border-radius: 8px;
            padding: .75rem;
            text-align: center;
        }
        .pillar-score-item .ps-num {
            font-family: 'DM Serif Display', serif;
            font-size: 1.6rem;
            line-height: 1.1;
        }
        .pillar-score-item .ps-label {
            font-size: .72rem;
            color: rgba(255,255,255,.55);
            margin-top: 2px;
        }
        .ps-num.score-green { color: #6ee7b7; }
        .ps-num.score-amber { color: #fcd34d; }
        .ps-num.score-red   { color: #fca5a5; }

        /* ── CARD ── */
        .card {
            background: var(--paper);
            border: 1px solid var(--paper3);
            border-radius: 10px;
            padding: 1.25rem;
            margin-bottom: 1rem;
        }
        .card h3 {
            font-size: .9rem;
            font-weight: 500;
            margin-bottom: .75rem;
            display: flex;
            align-items: center;
            gap: 7px;
        }
        .card h3 .icon { font-size: 1rem; }

        /* ── ISSUES LIST ── */
        .issues-list { list-style: none; }
        .issue-item {
            display: flex;
            gap: 10px;
            padding: .6rem 0;
            border-bottom: 1px solid var(--rule);
            font-size: .85rem;
            line-height: 1.5;
        }
        .issue-item:last-child { border-bottom: none; }
        .severity-badge {
            flex-shrink: 0;
            font-size: .65rem;
            font-weight: 500;
            letter-spacing: .05em;
            text-transform: uppercase;
            padding: 2px 7px;
            border-radius: 4px;
            align-self: flex-start;
            margin-top: 1px;
        }
        .sev-high   { background: var(--red-bg);   color: var(--red); }
        .sev-medium { background: var(--amber-bg);  color: var(--amber); }
        .sev-low    { background: var(--green-bg);  color: var(--green); }

        /* ── SCORE METERS ── */
        .score-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: .6rem;
            font-size: .83rem;
        }
        .score-row-label { width: 120px; flex-shrink: 0; color: var(--ink2); }
        .score-bar-wrap { flex: 1; background: var(--paper3); border-radius: 4px; height: 6px; overflow: hidden; }
        .score-bar { height: 100%; border-radius: 4px; transition: width .8s cubic-bezier(.4,0,.2,1); }
        .bar-green { background: #22c55e; }
        .bar-amber { background: #f59e0b; }
        .bar-red   { background: #ef4444; }
        .score-val { width: 32px; text-align: right; font-weight: 500; font-size: .82rem; }

        /* ── AI FIXES ── */
        .ai-summary {
            background: var(--paper2);
            border-left: 3px solid var(--accent2);
            border-radius: 0 6px 6px 0;
            padding: .75rem 1rem;
            font-size: .87rem;
            color: var(--ink2);
            line-height: 1.6;
            margin-bottom: 1rem;
            font-style: italic;
        }
        .fix-item {
            border: 1px solid var(--paper3);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: .75rem;
            background: var(--paper);
        }
        .fix-item:last-child { margin-bottom: 0; }
        .fix-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: .5rem;
        }
        .fix-issue {
            font-size: .83rem;
            font-weight: 500;
            flex: 1;
        }
        .fix-body {
            font-size: .85rem;
            color: var(--ink2);
            line-height: 1.6;
            margin-bottom: .5rem;
        }
        .fix-example {
            font-family: 'DM Mono', monospace;
            font-size: .78rem;
            background: var(--paper2);
            border-radius: 5px;
            padding: .5rem .75rem;
            color: var(--accent);
            white-space: pre-wrap;
            word-break: break-all;
        }

        /* ── OPTIMIZED META ── */
        .meta-box {
            background: var(--paper2);
            border-radius: 8px;
            padding: .9rem 1rem;
            margin-bottom: .75rem;
            font-size: .85rem;
        }
        .meta-box .meta-label {
            font-size: .7rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: var(--ink3);
            margin-bottom: .25rem;
        }
        .meta-box .meta-val {
            color: var(--ink);
            line-height: 1.5;
        }
        .copy-btn {
            font-size: .72rem;
            padding: 2px 8px;
            border: 1px solid var(--paper3);
            border-radius: 4px;
            background: var(--paper);
            cursor: pointer;
            color: var(--ink2);
            font-family: 'DM Sans', sans-serif;
            margin-top: .4rem;
            display: inline-block;
            transition: border-color .15s;
        }
        .copy-btn:hover { border-color: var(--accent2); color: var(--accent2); }

        /* ── CHAT SIDEBAR ── */
        .chat-card {
            background: var(--paper);
            border: 1px solid var(--paper3);
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            height: 560px;
            position: sticky;
            top: 1.5rem;
        }
        .chat-header {
            padding: 1rem 1.1rem .75rem;
            border-bottom: 1px solid var(--rule);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .chat-header h3 { font-size: .9rem; font-weight: 500; }
        .chat-badge {
            font-size: .65rem;
            background: var(--accent2);
            color: #fff;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: 500;
        }
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: .9rem 1rem;
            display: flex;
            flex-direction: column;
            gap: .75rem;
            scroll-behavior: smooth;
        }
        .chat-bubble {
            max-width: 90%;
            font-size: .83rem;
            line-height: 1.6;
            border-radius: 10px;
            padding: .6rem .9rem;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .bubble-user {
            background: var(--ink);
            color: #fff;
            align-self: flex-end;
            border-bottom-right-radius: 3px;
        }
        .bubble-ai {
            background: var(--paper2);
            color: var(--ink);
            align-self: flex-start;
            border-bottom-left-radius: 3px;
        }
        .bubble-ai code {
            font-family: 'DM Mono', monospace;
            font-size: .8em;
            background: var(--paper3);
            padding: 1px 4px;
            border-radius: 3px;
        }
        .chat-welcome {
            font-size: .8rem;
            color: var(--ink3);
            text-align: center;
            padding: 1rem;
            line-height: 1.5;
        }
        .chat-form {
            padding: .75rem 1rem;
            border-top: 1px solid var(--rule);
            display: flex;
            gap: 7px;
        }
        .chat-input {
            flex: 1;
            padding: 7px 11px;
            border: 1px solid var(--paper3);
            border-radius: 7px;
            font-family: 'DM Sans', sans-serif;
            font-size: .82rem;
            background: var(--paper2);
            color: var(--ink);
            outline: none;
            resize: none;
            height: 36px;
            max-height: 90px;
            transition: border-color .15s;
        }
        .chat-input:focus { border-color: var(--accent2); background: #fff; }
        .chat-send {
            width: 36px;
            height: 36px;
            background: var(--accent);
            border: none;
            border-radius: 7px;
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: background .15s;
        }
        .chat-send:hover { background: var(--accent2); }
        .chat-send:disabled { opacity: .5; cursor: not-allowed; }
        .chat-send svg { width: 14px; height: 14px; }

        .thinking {
            display: flex;
            gap: 4px;
            align-items: center;
            padding: .5rem .75rem;
        }
        .thinking span {
            width: 6px; height: 6px;
            background: var(--ink3);
            border-radius: 50%;
            animation: bounce .9s infinite;
        }
        .thinking span:nth-child(2) { animation-delay: .18s; }
        .thinking span:nth-child(3) { animation-delay: .36s; }
        @keyframes bounce {
            0%,60%,100% { transform: translateY(0); }
            30%          { transform: translateY(-5px); }
        }

        /* ── QUICK PROMPTS ── */
        .quick-prompts {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            padding: 0 1rem .6rem;
        }
        .qp-btn {
            font-size: .73rem;
            padding: 3px 9px;
            border: 1px solid var(--paper3);
            border-radius: 12px;
            background: var(--paper);
            cursor: pointer;
            color: var(--ink2);
            font-family: 'DM Sans', sans-serif;
            transition: border-color .12s, color .12s;
        }
        .qp-btn:hover { border-color: var(--accent2); color: var(--accent2); }

        /* ── EMPTY STATE ── */
        .empty-issues {
            text-align: center;
            padding: 1.5rem;
            color: var(--ink3);
            font-size: .85rem;
        }
        .empty-issues .checkmark { font-size: 1.5rem; display: block; margin-bottom: .5rem; }

        /* ── RESPONSIVE ── */
        @media (max-width: 700px) {
            .page { grid-template-columns: 1fr; }
            .side-col { order: -1; }
            .chat-card { height: 420px; position: static; }
            .pillar-scores { grid-template-columns: repeat(3,1fr); }
        }

        /* ── MISC ── */
        .new-analysis {
            font-size: .82rem;
            color: var(--ink3);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: color .12s;
        }
        .new-analysis:hover { color: var(--accent2); }
    </style>
</head>
<body>

<header>
    <a href="{{ route('home') }}" class="wordmark">
        <svg width="20" height="20" viewBox="0 0 22 22" fill="none">
            <rect width="22" height="22" rx="5" fill="#1a472a"/>
            <path d="M6 16L11 6L16 16" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M8 13h6" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        SEO<span>Lens</span>
    </a>
    <span class="header-url">{{ $report->website_url }}</span>
    <a href="{{ route('home') }}" class="new-analysis">
        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
            <path d="M8 3H3v10h10V8"/><path d="M11 2h3v3"/><path d="M14 2L8 8"/>
        </svg>
        New analysis
    </a>
</header>

<div class="page">

    {{-- ── MAIN COLUMN ── --}}
    <div class="main-col">

        {{-- OVERVIEW CARD --}}
        <div class="overview-card">
            <div class="overview-top">
                <div>
                    <div class="project-name">{{ $report->project_name }}</div>
                    <div style="font-size:.78rem;color:rgba(255,255,255,.45);margin-top:3px">
                        Analyzed {{ $report->created_at->diffForHumans() }}
                    </div>
                </div>
                <div class="big-score">
                    @php
                        $overall = $report->overall_score;
                        $oc = $overall >= 80 ? 'score-green' : ($overall >= 50 ? 'score-amber' : 'score-red');
                    @endphp
                    <div class="num {{ $oc }}">{{ $overall }}</div>
                    <div class="label">Overall Score</div>
                </div>
            </div>
            <div class="pillar-scores">
                @foreach ([
                    ['On-Page',   $report->on_page_score],
                    ['Technical', $report->technical_score],
                    ['Off-Page',  $report->off_page_score],
                ] as [$name, $score])
                    @php $c = $score >= 80 ? 'score-green' : ($score >= 50 ? 'score-amber' : 'score-red'); @endphp
                    <div class="pillar-score-item">
                        <div class="ps-num {{ $c }}">{{ $score }}</div>
                        <div class="ps-label">{{ $name }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── PILLAR 1: ON-PAGE ── --}}
        <section>
            <div class="section-label">📄 Pillar 1 — On-Page SEO</div>

            {{-- Score meters --}}
            <div class="card">
                <h3><span class="icon">📊</span> Score Breakdown</h3>
                @php $raw = $report->raw_seo_data ?? []; @endphp
                @foreach ([
                    ['Title',       $raw['title_score']     ?? 0],
                    ['Meta Desc',   $raw['meta_score']      ?? 0],
                    ['H1 Heading',  $raw['h1_score']        ?? 0],
                    ['Images Alt',  $raw['image_score']     ?? 0],
                    ['Canonical',   $raw['canonical_score'] ?? 0],
                ] as [$label, $val])
                    @php $bc = $val >= 80 ? 'bar-green' : ($val >= 50 ? 'bar-amber' : 'bar-red'); @endphp
                    <div class="score-row">
                        <div class="score-row-label">{{ $label }}</div>
                        <div class="score-bar-wrap">
                            <div class="score-bar {{ $bc }}" style="width:{{ $val }}%"></div>
                        </div>
                        <div class="score-val">{{ $val }}</div>
                    </div>
                @endforeach
            </div>

            {{-- Issues --}}
            <div class="card">
                <h3><span class="icon">⚠️</span> Issues Found</h3>
                @php $issues = $raw['issues'] ?? []; @endphp
                @if (count($issues) === 0)
                    <div class="empty-issues">
                        <span class="checkmark">✅</span>
                        No on-page issues detected — great work!
                    </div>
                @else
                    <ul class="issues-list">
                        @foreach ($issues as $issue)
                            @if (!in_array($issue['type'], ['fetch_error']))
                                <li class="issue-item">
                                    <span class="severity-badge sev-{{ $issue['severity'] }}">
                                        {{ $issue['severity'] }}
                                    </span>
                                    <span>{{ $issue['description'] }}</span>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @endif
            </div>
        </section>

        {{-- ── PILLAR 2: TECHNICAL ── --}}
        <section>
            <div class="section-label">⚡ Pillar 2 — Technical SEO</div>
            @php $ps = $report->page_speed_data ?? []; @endphp

            <div class="card">
                <h3><span class="icon">🚀</span> PageSpeed Insights</h3>
                @foreach ([
                    ['Performance',   $ps['performance']   ?? 0],
                    ['SEO',           $ps['seo']           ?? 0],
                    ['Accessibility', $ps['accessibility'] ?? 0],
                ] as [$label, $val])
                    @php $bc = $val >= 80 ? 'bar-green' : ($val >= 50 ? 'bar-amber' : 'bar-red'); @endphp
                    <div class="score-row">
                        <div class="score-row-label">{{ $label }}</div>
                        <div class="score-bar-wrap">
                            <div class="score-bar {{ $bc }}" style="width:{{ $val }}%"></div>
                        </div>
                        <div class="score-val">{{ $val }}</div>
                    </div>
                @endforeach
            </div>

            <div class="card">
                <h3><span class="icon">⏱</span> Core Web Vitals</h3>
                @foreach ([
                    ['First Contentful Paint',    $ps['first_contentful_paint']    ?? 'N/A'],
                    ['Largest Contentful Paint',  $ps['largest_contentful_paint']  ?? 'N/A'],
                    ['Total Blocking Time',       $ps['total_blocking_time']       ?? 'N/A'],
                    ['Cumulative Layout Shift',   $ps['cumulative_layout_shift']   ?? 'N/A'],
                    ['Speed Index',               $ps['speed_index']               ?? 'N/A'],
                ] as [$label, $val])
                    <div class="score-row">
                        <div class="score-row-label">{{ $label }}</div>
                        <div style="flex:1; font-size:.85rem; font-weight:500;">{{ $val }}</div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ── PILLAR 3: OFF-PAGE ── --}}
        <section>
            <div class="section-label">🔗 Pillar 3 — Off-Page Signals</div>
            @php $offPage = $raw['off_page'] ?? []; $checks = $offPage['checks'] ?? []; @endphp

            <div class="card">
                <h3><span class="icon">🔒</span> Trust & Security Checks</h3>
                @foreach ($checks as $check => $result)
                    <div class="score-row">
                        <div class="score-row-label" style="text-transform:capitalize">{{ str_replace('_', ' ', $check) }}</div>
                        <div style="flex:1">
                            @if ($result === 'pass')
                                <span style="color:var(--green);font-size:.83rem;font-weight:500">✓ Pass</span>
                            @elseif ($result === 'fail')
                                <span style="color:var(--red);font-size:.83rem;font-weight:500">✗ Fail</span>
                            @else
                                <span style="color:var(--ink3);font-size:.83rem">{{ ucfirst($result) }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
                @if (!empty($offPage['note']))
                    <p style="font-size:.78rem;color:var(--ink3);margin-top:.75rem;line-height:1.5">
                        💡 {{ $offPage['note'] }}
                    </p>
                @endif
            </div>
        </section>

        {{-- ── AI SUGGESTIONS ── --}}
        <section>
            <div class="section-label">🤖 AI-Generated Fixes</div>
            @php $aiFixes = $report->ai_fixes ?? []; @endphp

            @if (!empty($aiFixes['summary']))
                <div class="ai-summary">{{ $aiFixes['summary'] }}</div>
            @endif

            {{-- Optimized title & meta suggestions --}}
            @if (!empty($aiFixes['optimized_title']) || !empty($aiFixes['optimized_meta']))
                <div class="card">
                    <h3><span class="icon">✏️</span> AI-Optimized Tags</h3>
                    @if (!empty($aiFixes['optimized_title']))
                        <div class="meta-box">
                            <div class="meta-label">Suggested Title</div>
                            <div class="meta-val" id="opt-title">{{ $aiFixes['optimized_title'] }}</div>
                            <button class="copy-btn" onclick="copyText('opt-title', this)">Copy</button>
                        </div>
                    @endif
                    @if (!empty($aiFixes['optimized_meta']))
                        <div class="meta-box">
                            <div class="meta-label">Suggested Meta Description</div>
                            <div class="meta-val" id="opt-meta">{{ $aiFixes['optimized_meta'] }}</div>
                            <button class="copy-btn" onclick="copyText('opt-meta', this)">Copy</button>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Fix items --}}
            @if (!empty($aiFixes['fixes']))
                @foreach ($aiFixes['fixes'] as $fix)
                    @php
                        $prio = $fix['priority'] ?? 'medium';
                        $pClass = $prio === 'high' ? 'sev-high' : ($prio === 'low' ? 'sev-low' : 'sev-medium');
                    @endphp
                    <div class="fix-item">
                        <div class="fix-header">
                            <span class="fix-issue">{{ $fix['issue'] ?? 'SEO Issue' }}</span>
                            <span class="severity-badge {{ $pClass }}">{{ $prio }}</span>
                        </div>
                        <div class="fix-body">{{ $fix['fix'] ?? '' }}</div>
                        @if (!empty($fix['example']))
                            <div class="fix-example">{{ $fix['example'] }}</div>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="empty-issues">
                    <span class="checkmark">🎉</span>
                    No critical fixes needed — your SEO looks solid!
                </div>
            @endif
        </section>

    </div>{{-- /main-col --}}

    {{-- ── SIDEBAR: AI CHAT ── --}}
    <div class="side-col">
        <div class="chat-card">
            <div class="chat-header">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <circle cx="8" cy="8" r="7" stroke="var(--accent2)" stroke-width="1.5"/>
                    <path d="M5 8.5c.5 1 2.5 1.5 3.5.5" stroke="var(--accent2)" stroke-width="1.2" stroke-linecap="round"/>
                    <circle cx="6" cy="6.5" r=".8" fill="var(--accent2)"/>
                    <circle cx="10" cy="6.5" r=".8" fill="var(--accent2)"/>
                </svg>
                <h3>SEO Assistant</h3>
                <span class="chat-badge">AI</span>
            </div>

            <div class="chat-messages" id="chatMessages">
                <div class="chat-welcome">
                    Ask me anything about your SEO report — I know the full context of your site's issues.
                </div>

                {{-- Render saved history --}}
                @foreach ($chatHistory as $msg)
                    <div class="chat-bubble {{ $msg->role === 'user' ? 'bubble-user' : 'bubble-ai' }}">
                        {!! nl2br(e($msg->message)) !!}
                    </div>
                @endforeach
            </div>

            {{-- Quick prompt pills --}}
            <div class="quick-prompts">
                <button class="qp-btn" onclick="sendQuick('How do I fix my missing meta description?')">Fix meta desc</button>
                <button class="qp-btn" onclick="sendQuick('What are the top 3 things I should fix first?')">Top 3 priorities</button>
                <button class="qp-btn" onclick="sendQuick('How can I improve my page speed score?')">Boost speed</button>
                <button class="qp-btn" onclick="sendQuick('Give me the HTML code to fix my title tag.')">Title tag code</button>
            </div>

            <div class="chat-form">
                <textarea
                    class="chat-input"
                    id="chatInput"
                    placeholder="Ask about your SEO…"
                    rows="1"
                ></textarea>
                <button class="chat-send" id="chatSend" title="Send">
                    <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 7h12M7 1l6 6-6 6"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>{{-- /side-col --}}

</div>{{-- /page --}}

<script>
const CHAT_URL   = "{{ route('report.chat', $report) }}";
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
const messages   = document.getElementById('chatMessages');
const input      = document.getElementById('chatInput');
const sendBtn    = document.getElementById('chatSend');

// Auto-scroll to bottom on load
messages.scrollTop = messages.scrollHeight;

// Auto-resize textarea
input.addEventListener('input', function () {
    this.style.height = '36px';
    this.style.height = Math.min(this.scrollHeight, 90) + 'px';
});

// Send on Enter (Shift+Enter for newline)
input.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});

sendBtn.addEventListener('click', sendMessage);

function sendQuick(text) {
    input.value = text;
    sendMessage();
}

function addBubble(text, role) {
    const div = document.createElement('div');
    div.className = 'chat-bubble ' + (role === 'user' ? 'bubble-user' : 'bubble-ai');
    div.textContent = text;
    messages.appendChild(div);
    messages.scrollTop = messages.scrollHeight;
    return div;
}

function addThinking() {
    const div = document.createElement('div');
    div.className = 'chat-bubble bubble-ai thinking';
    div.innerHTML = '<span></span><span></span><span></span>';
    messages.appendChild(div);
    messages.scrollTop = messages.scrollHeight;
    return div;
}

async function sendMessage() {
    const text = input.value.trim();
    if (!text) return;

    input.value = '';
    input.style.height = '36px';
    sendBtn.disabled = true;

    addBubble(text, 'user');
    const thinking = addThinking();

    try {
        const res = await fetch(CHAT_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':        'application/json',
                'X-CSRF-TOKEN':  CSRF_TOKEN,
            },
            body: JSON.stringify({ message: text }),
        });

        const data = await res.json();
        thinking.remove();
        addBubble(data.assistant, 'assistant');
    } catch (err) {
        thinking.remove();
        addBubble('Sorry, something went wrong. Please try again.', 'assistant');
    } finally {
        sendBtn.disabled = false;
        input.focus();
    }
}

// Copy helper
function copyText(id, btn) {
    const text = document.getElementById(id).textContent;
    navigator.clipboard.writeText(text).then(() => {
        btn.textContent = 'Copied!';
        setTimeout(() => btn.textContent = 'Copy', 1800);
    });
}
</script>
</body>
</html>