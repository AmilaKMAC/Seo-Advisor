<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO Analyzer — Audit Your Website</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
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
            --amber:  #d68910;
            --green:  #1a6b3a;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--paper);
            color: var(--ink);
            min-height: 100vh;
        }

        /* ── HEADER ────────────────────────────────── */
        header {
            border-bottom: 1px solid var(--rule);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .wordmark {
            font-family: 'DM Serif Display', serif;
            font-size: 1.2rem;
            letter-spacing: -.01em;
        }
        .wordmark span { color: var(--accent2); }

        /* ── HERO ──────────────────────────────────── */
        .hero {
            max-width: 680px;
            margin: 0 auto;
            padding: 5rem 2rem 3rem;
        }
        .hero-label {
            font-size: .7rem;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--ink3);
            margin-bottom: 1rem;
        }
        .hero h1 {
            font-family: 'DM Serif Display', serif;
            font-size: clamp(2rem, 6vw, 3.2rem);
            line-height: 1.1;
            margin-bottom: 1rem;
            letter-spacing: -.02em;
        }
        .hero h1 em {
            font-style: italic;
            color: var(--accent2);
        }
        .hero p {
            font-size: .95rem;
            color: var(--ink2);
            line-height: 1.7;
            max-width: 500px;
            margin-bottom: 2.5rem;
        }

        /* ── FORM CARD ─────────────────────────────── */
        .form-card {
            background: var(--paper);
            border: 1px solid var(--paper3);
            border-radius: 12px;
            padding: 1.75rem;
            box-shadow: 0 2px 16px rgba(0,0,0,.05), 0 1px 4px rgba(0,0,0,.04);
        }
        .form-card fieldset { border: none; }
        .field { margin-bottom: 1rem; }
        .field label {
            display: block;
            font-size: .78rem;
            font-weight: 500;
            color: var(--ink2);
            margin-bottom: 5px;
            letter-spacing: .02em;
        }
        .field input[type="url"],
        .field input[type="text"] {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--paper3);
            border-radius: 8px;
            background: var(--paper2);
            color: var(--ink);
            font-family: 'DM Sans', sans-serif;
            font-size: .9rem;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        .field input:focus {
            border-color: var(--accent2);
            box-shadow: 0 0 0 3px rgba(45,122,73,.12);
            background: #fff;
        }
        .field input::placeholder { color: var(--ink3); }

        .btn-analyze {
            width: 100%;
            padding: 12px;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: .95rem;
            font-weight: 500;
            cursor: pointer;
            transition: background .15s, transform .1s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: .25rem;
        }
        .btn-analyze:hover { background: var(--accent2); }
        .btn-analyze:active { transform: scale(.99); }
        .btn-analyze svg { width: 16px; height: 16px; }

        /* ── ERROR ─────────────────────────────────── */
        .errors {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            border-radius: 8px;
            padding: .75rem 1rem;
            margin-bottom: 1rem;
            font-size: .85rem;
            color: var(--red);
        }
        .errors ul { padding-left: 1rem; }

        /* ── PILLARS ───────────────────────────────── */
        .pillars {
            max-width: 680px;
            margin: 0 auto;
            padding: 0 2rem 4rem;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }
        .pillar {
            background: var(--paper2);
            border: 1px solid var(--paper3);
            border-radius: 10px;
            padding: 1.1rem 1rem;
        }
        .pillar-icon {
            font-size: 1.4rem;
            margin-bottom: .5rem;
        }
        .pillar-name {
            font-size: .78rem;
            font-weight: 500;
            color: var(--ink2);
            letter-spacing: .04em;
            text-transform: uppercase;
            margin-bottom: .25rem;
        }
        .pillar-desc {
            font-size: .8rem;
            color: var(--ink3);
            line-height: 1.5;
        }

        /* ── FOOTER ────────────────────────────────── */
        footer {
            border-top: 1px solid var(--rule);
            text-align: center;
            padding: 1.25rem;
            font-size: .78rem;
            color: var(--ink3);
        }

        /* Loading state */
        .btn-analyze.loading { opacity: .7; pointer-events: none; }
        .btn-analyze .spinner {
            width: 16px; height: 16px;
            border: 2px solid rgba(255,255,255,.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .7s linear infinite;
            display: none;
        }
        .btn-analyze.loading .spinner { display: block; }
        .btn-analyze.loading .btn-text { display: none; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>

<header>
    <svg width="22" height="22" viewBox="0 0 22 22" fill="none">
        <rect width="22" height="22" rx="5" fill="#1a472a"/>
        <path d="M6 16L11 6L16 16" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M8 13h6" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/>
    </svg>
    <span class="wordmark">SEO<span>Lens</span></span>
</header>

<main>
    <section class="hero">
        <p class="hero-label">Three-pillar analysis</p>
        <h1>Audit any website.<br><em>Fix what matters.</em></h1>
        <p>Analyze on-page SEO, technical performance, and off-page signals in one click — then get AI-generated fixes you can implement today.</p>

        @if ($errors->any())
            <div class="errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-card">
            <form action="{{ route('analyze') }}" method="POST" id="analyzeForm">
                @csrf
                <fieldset>
                    <div class="field">
                        <label for="website_url">Website URL</label>
                        <input
                            type="url"
                            id="website_url"
                            name="website_url"
                            placeholder="https://yourwebsite.com"
                            value="{{ old('website_url') }}"
                            required
                            autocomplete="off"
                        >
                    </div>
                    <div class="field">
                        <label for="project_name">Project name <span style="font-weight:300;color:var(--ink3)">(optional)</span></label>
                        <input
                            type="text"
                            id="project_name"
                            name="project_name"
                            placeholder="My Website"
                            value="{{ old('project_name') }}"
                        >
                    </div>
                    <button type="submit" class="btn-analyze" id="analyzeBtn">
                        <span class="spinner"></span>
                        <span class="btn-text">
                            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                                <circle cx="7" cy="7" r="5"/><path d="M11 11l3 3"/>
                            </svg>
                            Run Full Analysis
                        </span>
                    </button>
                </fieldset>
            </form>
        </div>
    </section>

    <div class="pillars">
        <div class="pillar">
            <div class="pillar-icon">📄</div>
            <div class="pillar-name">On-Page</div>
            <div class="pillar-desc">Title, meta, headings, alt tags, canonical — every HTML signal</div>
        </div>
        <div class="pillar">
            <div class="pillar-icon">⚡</div>
            <div class="pillar-name">Technical</div>
            <div class="pillar-desc">PageSpeed, Core Web Vitals, performance and accessibility scores</div>
        </div>
        <div class="pillar">
            <div class="pillar-icon">🔗</div>
            <div class="pillar-name">Off-Page</div>
            <div class="pillar-desc">HTTPS, domain signals, backlink readiness, trust indicators</div>
        </div>
    </div>
</main>

<footer>SEOLens — powered by Google PageSpeed &amp; Gemini AI</footer>

<script>
document.getElementById('analyzeForm').addEventListener('submit', function () {
    const btn = document.getElementById('analyzeBtn');
    btn.classList.add('loading');
});
</script>
</body>
</html>