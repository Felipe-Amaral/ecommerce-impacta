<!DOCTYPE html>
<html lang="pt-BR">
<head>
    @php
        $currentRouteName = request()->route()?->getName() ?? '';
        $defaultNoindex = request()->routeIs(
            'admin.*',
            'account.*',
            'cart.*',
            'checkout.*',
            'login',
            'register'
        );
        $defaultRobots = $defaultNoindex
            ? 'noindex, nofollow, noarchive'
            : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';

        $seoTitle = html_entity_decode(trim($__env->yieldContent('title', 'Gráfica Uriah Criativa')), ENT_QUOTES, 'UTF-8');
        $seoDescription = html_entity_decode(trim($__env->yieldContent('meta_description', 'E-commerce para gráfica com catálogo, carrinho e checkout.')), ENT_QUOTES, 'UTF-8');
        $seoCanonical = trim($__env->yieldContent('canonical_url', url()->current()));
        $seoRobots = trim($__env->yieldContent('meta_robots', $defaultRobots));
        $seoOgType = trim($__env->yieldContent('og_type', 'website'));
        $seoOgImage = trim($__env->yieldContent('og_image', asset('favicon.svg?v=uriah2')));
        $siteName = 'Uriah Criativa';
        $siteUrl = url('/');
        $seoTitle = str_replace(['Gráfica Impacta', 'Grafica Impacta'], 'Uriah Criativa', $seoTitle);
        $seoDescription = str_replace(['Gráfica Impacta', 'Grafica Impacta'], 'Uriah Criativa', $seoDescription);
        $seoJsonLdWebSite = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteName,
            'url' => $siteUrl,
            'inLanguage' => 'pt-BR',
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => url('/catalogo').'?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
        $seoJsonLdOrganization = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $siteName,
            'url' => $siteUrl,
            'logo' => asset('favicon.svg?v=uriah2'),
            'description' => 'Gráfica online com catálogo de impressos, pedido digital, cobrança e acompanhamento de produção.',
        ];
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}" />
    <meta name="robots" content="{{ $seoRobots }}">
    <meta name="googlebot" content="{{ $seoRobots }}">
    <meta name="author" content="Uriah Criativa">
    <meta name="application-name" content="Uriah Criativa">
    <meta name="apple-mobile-web-app-title" content="Uriah Criativa">
    <meta name="format-detection" content="telephone=no">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <link rel="canonical" href="{{ $seoCanonical }}">
    <link rel="alternate" hreflang="pt-BR" href="{{ $seoCanonical }}">
    <link rel="alternate" hreflang="x-default" href="{{ $seoCanonical }}">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:type" content="{{ $seoOgType }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $seoCanonical }}">
    <meta property="og:image" content="{{ $seoOgImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $seoOgImage }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg?v=uriah2') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.svg?v=uriah2') }}">
    <meta name="theme-color" content="#c6a14a">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <script type="application/ld+json">{!! json_encode($seoJsonLdOrganization, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
    <script type="application/ld+json">{!! json_encode($seoJsonLdWebSite, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite('resources/js/app.js')
    @endif
    @yield('seo_json_ld')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Fraunces:opsz,wght@9..144,600;700&family=Manrope:wght@400;500;600;700;800&display=swap');

        :root {
            --bg: #f5f1e8;
            --surface: #fffdfa;
            --surface-2: #efe8da;
            --ink: #1f1a16;
            --muted: #6a5f55;
            --line: #d8cebe;
            --brand: #b3261e;
            --brand-2: #1f5eff;
            --success: #0f8a5f;
            --warning: #b46a00;
            --radius: 16px;
            --shadow: 0 12px 32px rgba(31, 26, 22, .08);
        }

        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            font-family: "Segoe UI", "Helvetica Neue", Helvetica, Arial, sans-serif;
            background:
                radial-gradient(circle at 10% 0%, rgba(179, 38, 30, .08), transparent 50%),
                radial-gradient(circle at 90% 10%, rgba(31, 94, 255, .08), transparent 55%),
                var(--bg);
            color: var(--ink);
            line-height: 1.45;
        }

        a { color: inherit; text-decoration: none; }
        img { max-width: 100%; display: block; }

        .container {
            width: 100%;
            margin-inline: auto;
        }

        .site-header {
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(8px);
            background: color-mix(in srgb, var(--bg) 80%, white 20%);
            border-bottom: 1px solid rgba(216, 206, 190, .7);
        }

        .header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            min-height: 72px;
        }

        .brand {
            display: grid;
            gap: 2px;
        }

        .brand strong {
            font-size: 1.05rem;
            letter-spacing: .02em;
        }

        .brand small {
            color: var(--muted);
            font-size: .78rem;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .nav {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .nav-link {
            padding: 10px 12px;
            border-radius: 999px;
            color: var(--muted);
            transition: .18s ease;
        }

        .nav-link:hover { background: rgba(31, 26, 22, .05); color: var(--ink); }

        .btn {
            border: 0;
            border-radius: 12px;
            padding: 11px 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: .18s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--brand), #d1411a);
            color: white;
            box-shadow: 0 8px 16px rgba(179, 38, 30, .22);
        }

        .btn-primary:hover { transform: translateY(-1px); }

        .btn-secondary {
            background: var(--surface);
            border: 1px solid var(--line);
            color: var(--ink);
        }

        .btn-secondary:hover { background: #fff; }

        .btn-link {
            background: transparent;
            color: var(--brand-2);
            padding-inline: 0;
        }

        .hero {
            margin: 24px 0 28px;
            background:
                radial-gradient(circle at 80% 0%, rgba(31, 94, 255, .16), transparent 48%),
                radial-gradient(circle at 0% 100%, rgba(179, 38, 30, .14), transparent 52%),
                linear-gradient(180deg, rgba(255, 255, 255, .75), rgba(255, 255, 255, .92));
            border: 1px solid rgba(216, 206, 190, .8);
            border-radius: 24px;
            padding: 28px;
            box-shadow: var(--shadow);
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 18px;
            align-items: start;
        }

        .eyebrow {
            color: var(--brand);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            font-size: .78rem;
            margin: 0 0 8px;
        }

        h1, h2, h3 { line-height: 1.05; margin: 0; }
        h1 { font-size: clamp(2rem, 4vw, 3.4rem); margin-bottom: 12px; }
        h2 { font-size: clamp(1.25rem, 2vw, 1.8rem); margin-bottom: 12px; }
        h3 { font-size: 1.08rem; margin-bottom: 6px; }

        p { margin: 0; }
        .lead { color: var(--muted); font-size: 1.02rem; max-width: 56ch; }

        .grid {
            display: grid;
            gap: 16px;
        }

        .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }

        .card {
            background: var(--surface);
            border: 1px solid rgba(216, 206, 190, .8);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .card-pad { padding: 16px; }

        .muted { color: var(--muted); }
        .small { font-size: .88rem; }
        .tiny { font-size: .78rem; }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: .75rem;
            font-weight: 700;
            background: rgba(31, 94, 255, .08);
            color: var(--brand-2);
        }

        .badge-brand {
            background: rgba(179, 38, 30, .08);
            color: var(--brand);
        }

        .product-card {
            display: grid;
            gap: 12px;
            padding: 12px;
        }

        .product-thumb {
            min-height: 130px;
            border-radius: 14px;
            border: 1px solid rgba(216, 206, 190, .8);
            background:
                linear-gradient(135deg, rgba(31, 94, 255, .12), rgba(31, 94, 255, .02)),
                linear-gradient(315deg, rgba(179, 38, 30, .12), rgba(179, 38, 30, .02)),
                #faf7f0;
            display: grid;
            place-items: center;
            text-align: center;
            padding: 12px;
            color: var(--ink);
            font-weight: 700;
        }

        .product-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .price {
            font-weight: 800;
            font-size: 1.05rem;
        }

        .price small {
            font-weight: 500;
            color: var(--muted);
            font-size: .8rem;
        }

        .stack { display: grid; gap: 10px; }
        .stack-lg { display: grid; gap: 16px; }
        .stack-xl { display: grid; gap: 24px; }

        .split {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 360px;
            gap: 18px;
            align-items: start;
        }

        .details-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: minmax(0, 1.05fr) minmax(0, .95fr);
        }

        .spec-list, .clean-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: 8px;
        }

        .spec-list li {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            border-bottom: 1px dashed rgba(216, 206, 190, .9);
            padding-bottom: 8px;
        }

        .spec-list li span:first-child { color: var(--muted); }

        .input, .select, .textarea {
            width: 100%;
            border: 1px solid var(--line);
            background: #fff;
            border-radius: 12px;
            padding: 11px 12px;
            color: var(--ink);
            font: inherit;
        }

        .textarea { min-height: 92px; resize: vertical; }
        .input:focus, .select:focus, .textarea:focus {
            outline: 2px solid rgba(31, 94, 255, .2);
            border-color: rgba(31, 94, 255, .35);
        }

        .field { display: grid; gap: 6px; }
        .field label { font-size: .86rem; font-weight: 600; color: var(--muted); }

        .form-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .form-grid-3 {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .full { grid-column: 1 / -1; }

        .alert {
            margin-top: 16px;
            border-radius: 12px;
            border: 1px solid;
            padding: 12px 14px;
            font-size: .92rem;
        }

        .alert-success {
            background: rgba(15, 138, 95, .08);
            border-color: rgba(15, 138, 95, .2);
            color: #0b6e4b;
        }

        .alert-error {
            background: rgba(179, 38, 30, .08);
            border-color: rgba(179, 38, 30, .18);
            color: #8f221c;
        }

        .table-wrap {
            overflow: auto;
            border: 1px solid rgba(216, 206, 190, .8);
            border-radius: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            text-align: left;
            padding: 12px;
            vertical-align: top;
            border-bottom: 1px solid rgba(216, 206, 190, .65);
        }

        th { font-size: .8rem; color: var(--muted); text-transform: uppercase; letter-spacing: .06em; }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
        }

        .summary-row.total {
            font-weight: 800;
            font-size: 1.05rem;
            padding-top: 8px;
            border-top: 1px solid var(--line);
        }

        .radio-list {
            display: grid;
            gap: 8px;
        }

        .radio-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #fff;
        }

        .pill-list {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .pill {
            padding: 8px 10px;
            border-radius: 999px;
            background: var(--surface-2);
            color: var(--ink);
            font-size: .78rem;
            border: 1px solid rgba(216, 206, 190, .7);
        }

        .site-footer {
            margin: 36px 0 28px;
            color: var(--muted);
            font-size: .88rem;
        }

        .mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: .86rem;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        /* Refresh visual */
        :root {
            --bg: #f6f1e7;
            --surface: rgba(255, 255, 255, .78);
            --surface-2: rgba(241, 233, 220, .92);
            --ink: #221b16;
            --muted: #6c6258;
            --line: rgba(124, 106, 85, .16);
            --brand: #c33a1d;
            --brand-2: #0f5df5;
            --shadow: 0 20px 50px rgba(27, 20, 13, .08);
            --shadow-soft: 0 10px 24px rgba(27, 20, 13, .05);
            --radius: 18px;
        }

        body {
            font-family: "Manrope", system-ui, sans-serif;
            background:
                radial-gradient(circle at 12% 2%, rgba(195, 58, 29, .12), transparent 40%),
                radial-gradient(circle at 82% 12%, rgba(15, 93, 245, .10), transparent 48%),
                radial-gradient(circle at 70% 90%, rgba(195, 58, 29, .08), transparent 40%),
                linear-gradient(180deg, #fbf8f3, #f4ede2 60%, #efe6d9);
            min-height: 100vh;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            opacity: .16;
            background-image:
                linear-gradient(rgba(34, 27, 22, .05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(34, 27, 22, .05) 1px, transparent 1px);
            background-size: 26px 26px;
            mask-image: radial-gradient(circle at center, black 35%, transparent 95%);
        }

        .container {
            width: 100%;
        }

        .site-header {
            top: 8px;
            width: 100%;
            margin-inline: auto;
            border: 1px solid rgba(255, 255, 255, .7);
            border-radius: 16px;
            box-shadow: var(--shadow-soft);
            background: linear-gradient(180deg, rgba(255, 255, 255, .8), rgba(255, 255, 255, .62));
        }

        .header-inner {
            min-height: 62px;
            padding-inline: 12px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            color: white;
            font-weight: 800;
            letter-spacing: .04em;
            background:
                linear-gradient(135deg, rgba(255,255,255,.18), rgba(255,255,255,0)),
                linear-gradient(140deg, var(--brand) 12%, #f26629 55%, #0f5df5 140%);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.35),
                0 10px 20px rgba(195, 58, 29, .22);
        }

        .brand-text {
            display: grid;
            gap: 2px;
        }

        .brand strong {
            font-family: "Fraunces", Georgia, serif;
            font-size: 1.18rem;
            letter-spacing: .01em;
            margin: 0;
        }

        .brand small {
            letter-spacing: .1em;
            font-size: .68rem;
        }

        .nav {
            gap: 8px;
        }

        .nav-link {
            font-weight: 600;
            border: 1px solid transparent;
            padding: 9px 12px;
            font-size: .9rem;
        }

        .nav-link:hover {
            background: rgba(255,255,255,.66);
            border-color: rgba(255,255,255,.7);
            box-shadow: 0 8px 16px rgba(27,20,13,.05);
        }

        .nav-link.active {
            color: var(--ink);
            background: rgba(255,255,255,.75);
            border-color: rgba(255,255,255,.8);
            box-shadow: 0 8px 16px rgba(27,20,13,.04);
        }

        .user-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 11px;
            border-radius: 999px;
            background: rgba(255,255,255,.72);
            border: 1px solid rgba(255,255,255,.8);
            color: var(--muted);
            font-size: .78rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .user-chip::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--brand), #f26629);
            box-shadow: 0 0 0 4px rgba(195, 58, 29, .12);
        }

        .inline-form {
            display: inline-flex;
        }

        .btn {
            border-radius: 14px;
            padding: 10px 13px;
            font-weight: 700;
            box-shadow: 0 6px 14px rgba(27,20,13,.04);
        }

        .btn-primary {
            background:
                linear-gradient(180deg, rgba(255,255,255,.22), rgba(255,255,255,0)),
                linear-gradient(135deg, #b52b17, #db5c23 62%, #ff8b3d);
            box-shadow: 0 10px 22px rgba(181, 43, 23, .24);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 26px rgba(181, 43, 23, .28);
        }

        .btn-secondary {
            background: rgba(255,255,255,.72);
            border: 1px solid rgba(255,255,255,.95);
            backdrop-filter: blur(4px);
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,.88);
            border-color: rgba(255,255,255,1);
        }

        .btn-link {
            box-shadow: none;
            font-weight: 700;
        }

        .btn-sm {
            padding: 7px 10px;
            border-radius: 12px;
            font-size: .82rem;
        }

        h1, h2 {
            font-family: "Fraunces", Georgia, serif;
            line-height: 1.02;
            letter-spacing: -.01em;
        }

        h3 {
            font-family: "Manrope", system-ui, sans-serif;
            line-height: 1.1;
            letter-spacing: -.01em;
        }

        .hero {
            position: relative;
            overflow: hidden;
            margin-top: 26px;
            border: 1px solid rgba(255,255,255,.72);
            background:
                radial-gradient(circle at 85% 6%, rgba(15, 93, 245, .15), transparent 42%),
                radial-gradient(circle at 4% 96%, rgba(195, 58, 29, .14), transparent 46%),
                linear-gradient(180deg, rgba(255,255,255,.84), rgba(255,255,255,.66));
            box-shadow: var(--shadow);
        }

        .hero::after {
            content: "";
            position: absolute;
            inset: auto -80px -80px auto;
            width: 220px;
            height: 220px;
            border-radius: 40px;
            background:
                linear-gradient(135deg, rgba(15,93,245,.06), rgba(15,93,245,0)),
                linear-gradient(315deg, rgba(195,58,29,.08), rgba(195,58,29,0));
            transform: rotate(18deg);
            pointer-events: none;
        }

        .card {
            background: linear-gradient(180deg, rgba(255,255,255,.86), rgba(255,255,255,.72));
            border-color: rgba(255,255,255,.72);
            box-shadow: var(--shadow-soft);
            backdrop-filter: blur(3px);
        }

        .card-pad {
            padding: 18px;
        }

        .badge {
            background: rgba(15, 93, 245, .09);
            border: 1px solid rgba(15, 93, 245, .10);
            color: #0f5df5;
            font-weight: 800;
        }

        .badge-brand {
            background: rgba(195, 58, 29, .10);
            border: 1px solid rgba(195, 58, 29, .10);
            color: var(--brand);
        }

        .pill {
            background: rgba(244, 237, 227, .96);
            border-color: rgba(111, 94, 74, .10);
        }

        .product-card {
            border-radius: 18px;
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }

        .product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 34px rgba(27,20,13,.08);
            border-color: rgba(255,255,255,.95);
        }

        .product-thumb {
            min-height: 150px;
            border: 1px solid rgba(255,255,255,.78);
            background:
                radial-gradient(circle at 15% 20%, rgba(195, 58, 29, .12), transparent 42%),
                radial-gradient(circle at 85% 15%, rgba(15, 93, 245, .12), transparent 45%),
                linear-gradient(145deg, rgba(255,255,255,.82), rgba(247,240,231,.92));
            box-shadow: inset 0 1px 0 rgba(255,255,255,.9);
        }

        .lead {
            font-size: 1.03rem;
            line-height: 1.55;
            color: color-mix(in srgb, var(--muted) 88%, black 12%);
        }

        .input, .select, .textarea {
            background: rgba(255,255,255,.92);
            border: 1px solid rgba(113, 95, 76, .16);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.95);
        }

        .input:hover, .select:hover, .textarea:hover {
            border-color: rgba(113, 95, 76, .22);
        }

        .field label {
            text-transform: uppercase;
            letter-spacing: .06em;
            font-size: .73rem;
            font-weight: 800;
        }

        .alert {
            backdrop-filter: blur(6px);
            box-shadow: var(--shadow-soft);
            border-width: 1px;
        }

        .table-wrap {
            background: rgba(255,255,255,.6);
            border-color: rgba(255,255,255,.7);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.7);
        }

        table {
            background: transparent;
        }

        th, td {
            border-bottom-color: rgba(120, 102, 83, .12);
        }

        th {
            color: color-mix(in srgb, var(--muted) 80%, black 20%);
            font-weight: 800;
        }

        .site-footer {
            margin-top: 36px;
        }

        .container-narrow {
            width: min(760px, calc(100% - 28px));
            margin-inline: auto;
        }

        .auth-shell {
            display: grid;
            gap: 18px;
            grid-template-columns: 1.05fr .95fr;
            align-items: start;
            margin: 30px 0;
        }

        .auth-card {
            padding: 22px;
            border-radius: 22px;
        }

        .auth-side {
            display: grid;
            gap: 14px;
        }

        .auth-hero {
            position: relative;
            overflow: hidden;
        }

        .auth-hero::after {
            content: "";
            position: absolute;
            width: 180px;
            height: 180px;
            right: -40px;
            bottom: -40px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(15,93,245,.16), rgba(15,93,245,0));
            pointer-events: none;
        }

        .check-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            gap: 9px;
        }

        .check-list li {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            color: var(--muted);
            font-size: .9rem;
        }

        .check-list li::before {
            content: "✓";
            color: var(--success);
            font-weight: 800;
            margin-top: -1px;
        }

        .metric-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .metric-card {
            padding: 14px;
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,.72);
            background: rgba(255,255,255,.65);
            box-shadow: var(--shadow-soft);
            display: grid;
            gap: 3px;
        }

        .metric-card strong {
            font-family: "Fraunces", Georgia, serif;
            font-size: 1.35rem;
        }

        .metric-card span {
            color: var(--muted);
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            font-weight: 700;
        }

        .table-compact td, .table-compact th {
            padding: 10px 12px;
        }

        .status-dot {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: .82rem;
            font-weight: 700;
        }

        .status-dot::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #9ca3af;
            box-shadow: 0 0 0 4px rgba(156,163,175,.12);
        }

        .status-dot.pending::before { background: #f59e0b; box-shadow: 0 0 0 4px rgba(245,158,11,.14); }
        .status-dot.paid::before { background: #10b981; box-shadow: 0 0 0 4px rgba(16,185,129,.14); }
        .status-dot.production::before { background: #3b82f6; box-shadow: 0 0 0 4px rgba(59,130,246,.14); }

        .link-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        /* Stage 2 visual polish (print-studio aesthetic) */
        main.container {
            padding-top: 18px;
            position: relative;
            z-index: 1;
        }

        .text-gradient {
            background: linear-gradient(135deg, #1f1a16 0%, #7a2718 40%, #0f5df5 120%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .section-head {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 14px;
            align-items: end;
        }

        .section-head .copy {
            display: grid;
            gap: 6px;
        }

        .section-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            color: var(--muted);
            font-size: .76rem;
            font-weight: 800;
            letter-spacing: .11em;
            text-transform: uppercase;
        }

        .section-kicker::before {
            content: "";
            width: 18px;
            height: 2px;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--brand), var(--brand-2));
        }

        .hero-studio {
            padding: 0;
            overflow: hidden;
        }

        .hero-studio-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.12fr) minmax(0, .88fr);
            gap: 0;
        }

        .hero-pane {
            padding: 26px;
            position: relative;
        }

        .hero-pane + .hero-pane {
            border-left: 1px solid rgba(255,255,255,.62);
            background:
                radial-gradient(circle at 92% 10%, rgba(15, 93, 245, .16), transparent 38%),
                linear-gradient(180deg, rgba(255,255,255,.58), rgba(255,255,255,.45));
        }

        .hero-pane::before {
            content: "";
            position: absolute;
            inset: 16px;
            border-radius: 16px;
            pointer-events: none;
            border: 1px dashed rgba(122, 97, 72, .12);
        }

        .hero-tagline {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            border: 1px solid rgba(255,255,255,.85);
            background: rgba(255,255,255,.72);
            border-radius: 999px;
            padding: 7px 11px;
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .07em;
            text-transform: uppercase;
            color: var(--muted);
            width: fit-content;
        }

        .hero-tagline .dot {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--brand), #ff8b3d);
            box-shadow: 0 0 0 4px rgba(195, 58, 29, .11);
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .hero-proof {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .hero-proof .pill {
            background: rgba(255,255,255,.68);
            border-color: rgba(255,255,255,.92);
            font-weight: 700;
        }

        .hero-metrics {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .hero-metric {
            padding: 14px;
            border-radius: 14px;
            background: rgba(255,255,255,.7);
            border: 1px solid rgba(255,255,255,.85);
            box-shadow: var(--shadow-soft);
            display: grid;
            gap: 4px;
        }

        .hero-metric strong {
            font-family: "Fraunces", Georgia, serif;
            font-size: 1.28rem;
            line-height: 1;
        }

        .hero-metric span {
            color: var(--muted);
            font-size: .76rem;
            text-transform: uppercase;
            letter-spacing: .09em;
            font-weight: 800;
        }

        .print-board {
            display: grid;
            gap: 12px;
        }

        .board-card {
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,.82);
            background: rgba(255,255,255,.72);
            box-shadow: var(--shadow-soft);
            padding: 15px;
        }

        .board-card::after {
            content: "";
            position: absolute;
            inset: auto -24px -24px auto;
            width: 120px;
            height: 120px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(15,93,245,.08), rgba(15,93,245,0));
            pointer-events: none;
        }

        .board-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 10px;
        }

        .board-title strong {
            font-size: .92rem;
        }

        .board-title .tiny {
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .swatch-row {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
        }

        .swatch {
            height: 38px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,.85);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.35);
            position: relative;
            overflow: hidden;
        }

        .swatch::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(255,255,255,.2), rgba(255,255,255,0));
        }

        .swatch.c { background: linear-gradient(135deg, #14b8ff, #0f5df5); }
        .swatch.m { background: linear-gradient(135deg, #ef476f, #b91c1c); }
        .swatch.y { background: linear-gradient(135deg, #facc15, #f59e0b); }
        .swatch.k { background: linear-gradient(135deg, #111827, #334155); }

        .process-rail {
            display: grid;
            gap: 8px;
        }

        .process-step {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 10px;
            align-items: center;
            padding: 9px 10px;
            border-radius: 12px;
            background: rgba(255,255,255,.6);
            border: 1px solid rgba(255,255,255,.8);
        }

        .process-step .num {
            width: 22px;
            height: 22px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            font-size: .72rem;
            font-weight: 800;
            background: linear-gradient(135deg, rgba(195,58,29,.15), rgba(15,93,245,.13));
            color: var(--ink);
            border: 1px solid rgba(255,255,255,.7);
        }

        .process-step .label {
            font-size: .86rem;
            font-weight: 700;
        }

        .process-step .eta {
            color: var(--muted);
            font-size: .75rem;
            font-weight: 700;
        }

        .surface-dark {
            color: #f8f5ef;
            background:
                radial-gradient(circle at 85% 10%, rgba(15,93,245,.20), transparent 42%),
                radial-gradient(circle at 5% 100%, rgba(195,58,29,.22), transparent 45%),
                linear-gradient(160deg, #1c1713, #221a16 48%, #191411);
            border: 1px solid rgba(255,255,255,.08);
            box-shadow: 0 18px 42px rgba(17, 13, 10, .26);
        }

        .surface-dark .muted {
            color: rgba(248,245,239,.72);
        }

        .surface-dark .pill {
            background: rgba(255,255,255,.06);
            color: rgba(248,245,239,.9);
            border-color: rgba(255,255,255,.08);
        }

        .category-tile {
            position: relative;
            overflow: hidden;
            display: grid;
            gap: 14px;
            min-height: 170px;
        }

        .category-tile::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 90% 8%, rgba(15,93,245,.14), transparent 46%),
                radial-gradient(circle at 8% 92%, rgba(195,58,29,.12), transparent 52%);
            opacity: .9;
            pointer-events: none;
        }

        .category-tile::after {
            content: "";
            position: absolute;
            inset: auto 14px 14px auto;
            width: 42px;
            height: 42px;
            border-radius: 14px;
            border: 1px dashed rgba(122,97,72,.18);
            transform: rotate(8deg);
        }

        .category-tile > * { position: relative; }

        .category-index {
            width: fit-content;
            padding: 6px 10px;
            border-radius: 999px;
            font-weight: 800;
            font-size: .74rem;
            background: rgba(255,255,255,.74);
            border: 1px solid rgba(255,255,255,.9);
            box-shadow: var(--shadow-soft);
        }

        .category-tile h3 {
            font-size: 1.15rem;
            line-height: 1.08;
        }

        .category-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-top: auto;
            color: var(--muted);
            font-size: .8rem;
            font-weight: 700;
        }

        .category-arrow {
            width: 30px;
            height: 30px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(255,255,255,.88);
            background: rgba(255,255,255,.72);
            color: var(--ink);
        }

        .product-card {
            gap: 14px;
            padding: 14px;
        }

        .product-card .product-thumb {
            position: relative;
            isolation: isolate;
            min-height: 230px;
            align-content: start;
            text-align: left;
        }

        .product-card .product-thumb::before {
            content: "";
            position: absolute;
            inset: 12px;
            border-radius: 12px;
            border: 1px dashed rgba(255,255,255,.5);
            z-index: -1;
        }

        .product-card .product-thumb::after {
            content: "";
            position: absolute;
            width: 54px;
            height: 54px;
            top: 10px;
            right: 10px;
            border-radius: 14px;
            background: linear-gradient(135deg, color-mix(in srgb, var(--accent, var(--brand)) 14%, white 86%), rgba(255,255,255,.75));
            border: 1px solid rgba(255,255,255,.8);
            box-shadow: var(--shadow-soft);
        }

        .product-brow {
            display: inline-flex;
            width: fit-content;
            align-items: center;
            gap: 8px;
            font-size: .7rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: color-mix(in srgb, var(--ink) 78%, white 22%);
            background: rgba(255,255,255,.74);
            border: 1px solid rgba(255,255,255,.92);
            border-radius: 999px;
            padding: 6px 9px;
        }

        .product-card h3 {
            font-size: 1.03rem;
            line-height: 1.15;
        }

        .product-card .price {
            display: grid;
            gap: 1px;
            font-size: 1.08rem;
        }

        .product-card .price small {
            display: block;
        }

        .product-card-cta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--ink);
            font-weight: 700;
            font-size: .86rem;
        }

        .product-card-cta .bullet {
            width: 24px;
            height: 24px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(255,255,255,.86);
            background: rgba(255,255,255,.74);
            box-shadow: var(--shadow-soft);
        }

        .catalog-toolbar {
            position: relative;
            overflow: hidden;
        }

        .catalog-toolbar::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 96% 0%, rgba(15,93,245,.12), transparent 35%),
                radial-gradient(circle at 0% 100%, rgba(195,58,29,.1), transparent 45%);
            pointer-events: none;
        }

        .catalog-toolbar > * {
            position: relative;
        }

        .filter-pills {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .filter-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(255,255,255,.76);
            border: 1px solid rgba(255,255,255,.88);
            font-size: .76rem;
            font-weight: 700;
        }

        .filter-pill strong {
            color: var(--ink);
        }

        .floating-sticky {
            position: sticky;
            top: 100px;
        }

        .checkout-progress {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .step-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 10px;
            border-radius: 999px;
            background: rgba(255,255,255,.7);
            border: 1px solid rgba(255,255,255,.9);
            font-size: .78rem;
            font-weight: 700;
        }

        .step-chip .n {
            width: 18px;
            height: 18px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            font-size: .68rem;
            font-weight: 800;
            background: linear-gradient(135deg, rgba(195,58,29,.18), rgba(15,93,245,.16));
            border: 1px solid rgba(255,255,255,.85);
        }

        .variant-grid {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .variant-card {
            display: grid;
            gap: 8px;
            padding: 12px;
            border-radius: 14px;
            background: rgba(255,255,255,.76);
            border: 1px solid rgba(255,255,255,.88);
            box-shadow: var(--shadow-soft);
        }

        .variant-card .row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .variant-card strong {
            line-height: 1.14;
        }

        .variant-card .variant-price {
            font-weight: 800;
            color: var(--ink);
            white-space: nowrap;
        }

        .variant-tags {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .variant-tags .pill {
            padding: 5px 8px;
            font-size: .72rem;
        }

        .form-section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 4px;
        }

        .form-section-head > div {
            display: grid;
            gap: 3px;
        }

        .form-section-head p {
            color: var(--muted);
            font-size: .84rem;
        }

        .summary-sticky-card {
            position: sticky;
            top: 102px;
        }

        .order-line {
            display: grid;
            gap: 4px;
            padding: 10px 0;
            border-bottom: 1px dashed rgba(120, 102, 83, .15);
        }

        .order-line:first-child {
            padding-top: 0;
        }

        .ribbon-note {
            padding: 9px 12px;
            border-radius: 12px;
            font-size: .82rem;
            font-weight: 700;
            background: rgba(200,164,78,.10);
            border: 1px solid rgba(200,164,78,.16);
            color: #7f6120;
        }

        .spot-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .spot-card {
            min-height: 108px;
            display: grid;
            align-content: start;
            gap: 8px;
            padding: 14px;
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,.84);
            background: rgba(255,255,255,.72);
            box-shadow: var(--shadow-soft);
            position: relative;
            overflow: hidden;
        }

        .spot-card::before {
            content: "";
            position: absolute;
            inset: auto -16px -16px auto;
            width: 88px;
            height: 88px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(195,58,29,.09), rgba(195,58,29,0));
            pointer-events: none;
        }

        .spot-card .spot-icon {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-size: .95rem;
            background: rgba(255,255,255,.82);
            border: 1px solid rgba(255,255,255,.92);
            box-shadow: var(--shadow-soft);
        }

        .spot-card h3 {
            font-size: .98rem;
            margin: 0;
        }

        .spot-card p {
            color: var(--muted);
            font-size: .83rem;
        }

        .reveal-up {
            animation: revealUp .55s cubic-bezier(.2,.8,.2,1) both;
        }

        .reveal-up:nth-child(2) { animation-delay: .04s; }
        .reveal-up:nth-child(3) { animation-delay: .08s; }
        .reveal-up:nth-child(4) { animation-delay: .12s; }

        .print-scene {
            position: relative;
            width: 100%;
            max-width: 320px;
            min-height: 120px;
            margin-inline: auto;
            display: grid;
            place-items: center;
            pointer-events: none;
        }

        .print-scene.large {
            max-width: 460px;
            min-height: 210px;
        }

        .print-scene .shadow-ellipse {
            position: absolute;
            inset: auto 12% 10px;
            height: 22px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(34,27,22,.12), rgba(34,27,22,0));
            filter: blur(2px);
        }

        .print-scene .board {
            position: absolute;
            inset: 10px;
            border-radius: 18px;
            border: 1px dashed rgba(122,97,72,.18);
            background:
                linear-gradient(rgba(122,97,72,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(122,97,72,.03) 1px, transparent 1px);
            background-size: 12px 12px;
        }

        .scene-object {
            position: absolute;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,.88);
            box-shadow: 0 14px 26px rgba(27,20,13,.09), inset 0 1px 0 rgba(255,255,255,.8);
            overflow: hidden;
        }

        .scene-object::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(255,255,255,.22), rgba(255,255,255,0));
            pointer-events: none;
        }

        .scene-object::after {
            content: "";
            position: absolute;
            inset: auto 6px 6px auto;
            width: 20px;
            height: 20px;
            border-radius: 7px;
            border: 1px dashed rgba(255,255,255,.45);
            opacity: .8;
        }

        .scene-label {
            position: absolute;
            top: 8px;
            left: 8px;
            padding: 4px 7px;
            border-radius: 999px;
            background: rgba(255,255,255,.78);
            border: 1px solid rgba(255,255,255,.9);
            font-size: .58rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            font-weight: 800;
            color: rgba(31,26,22,.75);
        }

        .scene-text {
            position: absolute;
            left: 10px;
            right: 10px;
            bottom: 10px;
            display: grid;
            gap: 4px;
        }

        .scene-text strong {
            font-size: .84rem;
            line-height: 1.05;
            color: #1f1a16;
            text-align: left;
        }

        .scene-text span {
            height: 6px;
            border-radius: 999px;
            background: rgba(31,26,22,.08);
        }

        .scene-accent {
            position: absolute;
            width: 58px;
            height: 58px;
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,.88);
            box-shadow: var(--shadow-soft);
            background: linear-gradient(145deg, rgba(255,255,255,.55), rgba(255,255,255,.15));
        }

        .scene-accent.ring {
            border-radius: 999px;
            width: 66px;
            height: 66px;
            background: radial-gradient(circle at center, rgba(255,255,255,.32) 30%, rgba(255,255,255,0) 32%),
                linear-gradient(145deg, rgba(255,255,255,.3), rgba(255,255,255,.1));
        }

        .print-scene.card .scene-object.main {
            width: 150px;
            height: 92px;
            transform: rotate(-7deg);
            background: linear-gradient(135deg, #ffffff, #f3f1ed 58%, #e7e0d2);
        }

        .print-scene.card .scene-object.back {
            width: 150px;
            height: 92px;
            transform: rotate(7deg) translate(38px, 10px);
            background: linear-gradient(135deg, #f5f7ff, #dbeafe 55%, #bfdbfe);
            opacity: .95;
        }

        .print-scene.card .scene-accent {
            top: 18px;
            right: 18px;
            background:
                linear-gradient(140deg, rgba(195,58,29,.18), rgba(195,58,29,.02)),
                linear-gradient(310deg, rgba(15,93,245,.16), rgba(15,93,245,.02)),
                rgba(255,255,255,.64);
        }

        .print-scene.flyer .scene-object.main {
            width: 114px;
            height: 154px;
            transform: rotate(-5deg) translate(-20px, 0);
            background: linear-gradient(135deg, #fff7ed, #fed7aa 50%, #fdba74);
        }

        .print-scene.flyer .scene-object.mid {
            width: 114px;
            height: 154px;
            transform: rotate(3deg) translate(18px, 8px);
            background: linear-gradient(135deg, #eff6ff, #bfdbfe 52%, #93c5fd);
        }

        .print-scene.flyer .scene-accent {
            bottom: 16px;
            left: 20px;
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background:
                linear-gradient(140deg, rgba(15,93,245,.2), rgba(15,93,245,.04)),
                rgba(255,255,255,.7);
        }

        .print-scene.banner .scene-object.main {
            width: 118px;
            height: 168px;
            border-radius: 16px;
            background:
                linear-gradient(180deg, rgba(255,255,255,.25), rgba(255,255,255,0)),
                linear-gradient(140deg, #0f172a 0%, #1d4ed8 48%, #38bdf8 100%);
        }

        .print-scene.banner .scene-object.base {
            width: 132px;
            height: 16px;
            border-radius: 999px;
            bottom: 28px;
            background: linear-gradient(180deg, #fff, #d1d5db);
            box-shadow: 0 8px 16px rgba(27,20,13,.08);
        }

        .print-scene.banner .scene-object.pole {
            width: 6px;
            height: 160px;
            border-radius: 999px;
            bottom: 38px;
            left: calc(50% - 3px);
            background: linear-gradient(180deg, #f8fafc, #94a3b8);
            box-shadow: none;
        }

        .print-scene.banner .scene-object.pole::before,
        .print-scene.banner .scene-object.pole::after,
        .print-scene.banner .scene-object.base::before,
        .print-scene.banner .scene-object.base::after {
            display: none;
        }

        .print-scene.banner .scene-accent.ring {
            top: 18px;
            right: 18px;
            background:
                radial-gradient(circle at center, rgba(255,255,255,.2) 30%, transparent 31%),
                linear-gradient(140deg, rgba(15,93,245,.18), rgba(15,93,245,.04));
        }

        .print-scene.labels .scene-object.sheet {
            width: 156px;
            height: 128px;
            background: linear-gradient(135deg, #fff, #f8fafc 60%, #eef2ff);
            transform: rotate(-4deg);
        }

        .print-scene.labels .scene-object.sheet .grid-dots {
            position: absolute;
            inset: 14px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .print-scene.labels .scene-object.sheet .grid-dots span {
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.8);
            background:
                radial-gradient(circle at 40% 35%, rgba(15,93,245,.22), rgba(15,93,245,.04)),
                radial-gradient(circle at 70% 70%, rgba(195,58,29,.18), rgba(195,58,29,.03)),
                rgba(245,247,255,.85);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.75);
        }

        .print-scene.labels .scene-object.roll {
            width: 112px;
            height: 52px;
            border-radius: 999px;
            bottom: 28px;
            right: 28px;
            transform: rotate(8deg);
            background:
                linear-gradient(180deg, rgba(255,255,255,.35), rgba(255,255,255,.05)),
                linear-gradient(145deg, #fef3c7, #fde68a 48%, #f59e0b);
        }

        .print-scene.labels .scene-object.roll::after {
            width: 14px;
            height: 14px;
            border-radius: 999px;
            border-style: solid;
            border-color: rgba(255,255,255,.42);
        }

        .print-scene.large .scene-object.main { transform-origin: center; }
        .print-scene.large.card .scene-object.main,
        .print-scene.large.card .scene-object.back {
            width: 210px;
            height: 124px;
        }
        .print-scene.large.flyer .scene-object.main,
        .print-scene.large.flyer .scene-object.mid {
            width: 146px;
            height: 194px;
        }
        .print-scene.large.banner .scene-object.main {
            width: 152px;
            height: 214px;
        }
        .print-scene.large.banner .scene-object.pole {
            height: 205px;
            bottom: 42px;
        }
        .print-scene.large.banner .scene-object.base {
            width: 164px;
            bottom: 30px;
        }
        .print-scene.large.labels .scene-object.sheet {
            width: 210px;
            height: 164px;
        }
        .print-scene.large.labels .scene-object.roll {
            width: 146px;
            height: 62px;
        }

        .timeline-card {
            display: grid;
            gap: 10px;
        }

        .timeline-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: 10px;
        }

        .timeline-item {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 10px;
            align-items: start;
            padding: 10px 12px;
            border-radius: 14px;
            background: rgba(255,255,255,.7);
            border: 1px solid rgba(255,255,255,.85);
            box-shadow: var(--shadow-soft);
        }

        .timeline-item .marker {
            width: 24px;
            height: 24px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            font-size: .72rem;
            font-weight: 800;
            border: 1px solid rgba(255,255,255,.85);
            background: linear-gradient(135deg, rgba(195,58,29,.16), rgba(15,93,245,.15));
        }

        .timeline-item .title {
            font-weight: 800;
            font-size: .88rem;
            line-height: 1.1;
        }

        .timeline-item .desc {
            color: var(--muted);
            font-size: .8rem;
            margin-top: 2px;
        }

        .glass-panel {
            background: linear-gradient(180deg, rgba(255,255,255,.76), rgba(255,255,255,.62));
            border: 1px solid rgba(255,255,255,.86);
            backdrop-filter: blur(6px);
            box-shadow: var(--shadow-soft);
            border-radius: 16px;
            padding: 14px;
        }

        @keyframes revealUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }

        @media (max-width: 980px) {
            .hero-grid, .split, .details-grid, .hero-studio-grid {
                grid-template-columns: 1fr;
            }

            .grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .grid-3 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .auth-shell { grid-template-columns: 1fr; }
            .metric-grid { grid-template-columns: 1fr; }
            .hero-metrics { grid-template-columns: 1fr; }
            .spot-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .variant-grid { grid-template-columns: 1fr; }
            .section-head { grid-template-columns: 1fr; align-items: start; }
            .hero-pane + .hero-pane { border-left: 0; border-top: 1px solid rgba(255,255,255,.62); }
        }

        @media (max-width: 680px) {
            .site-header {
                top: 8px;
                border-radius: 16px;
            }
            .header-inner { align-items: flex-start; padding-block: 10px; }
            .nav { width: 100%; justify-content: flex-start; }
            .grid-4, .grid-3, .grid-2, .form-grid, .form-grid-3 { grid-template-columns: 1fr; }
            .hero { padding: 18px; border-radius: 18px; }
            th:nth-child(2), td:nth-child(2) { min-width: 160px; }
            .brand small { display: none; }
            .user-chip { order: 99; }
            .hero-pane { padding: 18px; }
            .spot-grid { grid-template-columns: 1fr; }
        }

        /* Stage 3 visual round (premium nav/footer/mobile UX) */
        ::selection {
            background: rgba(15, 93, 245, .16);
            color: var(--ink);
        }

        :focus-visible {
            outline: 2px solid rgba(15, 93, 245, .35);
            outline-offset: 2px;
        }

        .skip-link {
            position: fixed;
            left: 14px;
            top: 12px;
            z-index: 60;
            padding: 8px 10px;
            border-radius: 12px;
            background: #111827;
            color: #f8fafc;
            border: 1px solid rgba(255,255,255,.08);
            box-shadow: 0 12px 24px rgba(17,24,39,.25);
            transform: translateY(-160%);
            transition: transform .18s ease;
            font-size: .82rem;
            font-weight: 700;
        }

        .skip-link:focus-visible {
            transform: translateY(0);
            outline: none;
        }

        .header-shell {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            align-items: center;
            gap: 8px;
        }

        .header-utility {
            display: none;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 8px 14px 0;
            font-size: .74rem;
            color: color-mix(in srgb, var(--muted) 88%, black 12%);
        }

        .utility-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .utility-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 6px 9px;
            border: 1px solid rgba(255,255,255,.85);
            background: rgba(255,255,255,.52);
            font-weight: 700;
            letter-spacing: .02em;
        }

        .utility-pill .dot {
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--brand), #f26629);
            box-shadow: 0 0 0 3px rgba(195,58,29,.10);
        }

        .utility-text {
            color: var(--muted);
            font-weight: 700;
            white-space: nowrap;
        }

        .header-inner {
            border-bottom: 0;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            min-height: 0;
            padding: 8px 0 8px 12px;
        }

        .menu-toggle {
            display: none;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255,255,255,.92);
            background: rgba(255,255,255,.8);
            border-radius: 12px;
            padding: 10px 12px;
            color: var(--ink);
            font-weight: 800;
            font-size: .82rem;
            box-shadow: 0 8px 18px rgba(27,20,13,.06);
            cursor: pointer;
        }

        .menu-toggle .bars {
            width: 16px;
            height: 12px;
            position: relative;
            display: inline-block;
        }

        .menu-toggle .bars::before,
        .menu-toggle .bars::after,
        .menu-toggle .bars span {
            content: "";
            position: absolute;
            left: 0;
            width: 100%;
            height: 2px;
            border-radius: 999px;
            background: currentColor;
            transition: transform .16s ease, opacity .16s ease, top .16s ease;
        }

        .menu-toggle .bars::before { top: 0; }
        .menu-toggle .bars span { top: 5px; }
        .menu-toggle .bars::after { top: 10px; }

        .site-header[data-nav-open="true"] .menu-toggle .bars::before {
            top: 5px;
            transform: rotate(45deg);
        }

        .site-header[data-nav-open="true"] .menu-toggle .bars span {
            opacity: 0;
        }

        .site-header[data-nav-open="true"] .menu-toggle .bars::after {
            top: 5px;
            transform: rotate(-45deg);
        }

        .header-panel {
            display: grid;
            grid-template-columns: minmax(0, 250px) minmax(0, 1fr);
            align-items: center;
            gap: 12px;
            padding: 8px 12px 8px 0;
        }

        .header-search {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,.78);
            background: rgba(255,255,255,.62);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.9);
            backdrop-filter: blur(4px);
        }

        .header-search input {
            width: 100%;
            border: 0;
            background: transparent;
            padding: 7px 8px;
            font: inherit;
            color: var(--ink);
        }

        .header-search input::placeholder {
            color: color-mix(in srgb, var(--muted) 82%, white 18%);
        }

        .header-search input:focus {
            outline: none;
        }

        .header-search .btn {
            padding: 8px 10px;
            border-radius: 10px;
            font-size: .8rem;
            box-shadow: none;
        }

        .header-search-kicker {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding-left: 8px;
            color: var(--muted);
            font-size: .73rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .header-search-kicker::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
        }

        .header-panel .nav {
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: nowrap;
        }

        .header-panel .nav .btn-primary {
            margin-left: 2px;
        }

        .nav-link {
            position: relative;
        }

        .nav-link.active::after {
            content: "";
            position: absolute;
            left: 12px;
            right: 12px;
            bottom: 6px;
            height: 2px;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--brand), var(--brand-2));
            opacity: .8;
        }

        .footer-shell {
            padding: 22px;
            border-radius: 22px;
            display: grid;
            gap: 18px;
            overflow: hidden;
            position: relative;
            background:
                radial-gradient(circle at 10% 0%, rgba(195,58,29,.08), transparent 36%),
                radial-gradient(circle at 92% 8%, rgba(15,93,245,.10), transparent 42%),
                linear-gradient(180deg, rgba(255,255,255,.84), rgba(255,255,255,.68));
        }

        .footer-shell::after {
            content: "";
            position: absolute;
            right: -40px;
            bottom: -46px;
            width: 180px;
            height: 180px;
            border-radius: 28px;
            background:
                linear-gradient(135deg, rgba(15,93,245,.05), rgba(15,93,245,0)),
                linear-gradient(315deg, rgba(195,58,29,.08), rgba(195,58,29,0));
            transform: rotate(20deg);
            pointer-events: none;
        }

        .footer-top {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(0, .9fr);
            gap: 14px;
            align-items: stretch;
        }

        .footer-brand {
            display: grid;
            gap: 10px;
        }

        .footer-brand-head {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .footer-brand-copy strong {
            display: block;
            font-family: "Fraunces", Georgia, serif;
            font-size: 1.15rem;
            letter-spacing: -.01em;
        }

        .footer-brand-copy span {
            color: var(--muted);
            font-size: .85rem;
        }

        .footer-badge-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .footer-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 10px;
            border-radius: 999px;
            font-size: .76rem;
            font-weight: 800;
            border: 1px solid rgba(255,255,255,.82);
            background: rgba(255,255,255,.56);
        }

        .footer-chip .mini-dot {
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background: var(--brand-2);
            box-shadow: 0 0 0 3px rgba(15,93,245,.08);
        }

        .footer-kpis {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .footer-kpi {
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,.75);
            background: rgba(255,255,255,.56);
            padding: 12px;
            display: grid;
            gap: 3px;
        }

        .footer-kpi strong {
            font-family: "Fraunces", Georgia, serif;
            font-size: 1.15rem;
        }

        .footer-kpi span {
            color: var(--muted);
            font-size: .76rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            font-weight: 800;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            align-items: stretch;
        }

        .footer-col {
            border: 1px solid rgba(255,255,255,.74);
            background: rgba(255,255,255,.48);
            border-radius: 16px;
            padding: 14px;
            display: grid;
            gap: 10px;
            grid-template-rows: auto auto 1fr;
            height: 100%;
            align-content: start;
        }

        .footer-col h3 {
            font-size: .95rem;
            margin: 0;
        }

        .footer-col p {
            color: var(--muted);
            font-size: .84rem;
            line-height: 1.45;
        }

        .footer-links {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: 6px;
        }

        .footer-links a {
            color: color-mix(in srgb, var(--ink) 88%, var(--muted) 12%);
            font-weight: 700;
            font-size: .88rem;
        }

        .footer-links a:hover {
            color: var(--brand-2);
        }

        .footer-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding-top: 2px;
            border-top: 1px dashed rgba(120,102,83,.14);
            color: var(--muted);
            font-size: .82rem;
            position: relative;
            z-index: 1;
        }

        .footer-status {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .footer-status .mono {
            border-radius: 999px;
            padding: 6px 10px;
            border: 1px solid rgba(255,255,255,.75);
            background: rgba(255,255,255,.52);
        }

        .mobile-quickbar {
            position: fixed;
            left: 12px;
            right: 12px;
            bottom: 12px;
            z-index: 30;
            display: none;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 8px;
            border-radius: 18px;
            border: 1px solid rgba(255,255,255,.82);
            background: linear-gradient(180deg, rgba(255,255,255,.86), rgba(255,255,255,.74));
            box-shadow: 0 16px 32px rgba(27,20,13,.12);
            backdrop-filter: blur(8px);
        }

        .mobile-quickbar a {
            flex: 1 1 0;
            min-width: 0;
            padding: 10px 8px;
            border-radius: 12px;
            text-align: center;
            font-size: .77rem;
            font-weight: 800;
            color: var(--muted);
            border: 1px solid transparent;
            background: rgba(255,255,255,.52);
        }

        .mobile-quickbar a.active {
            color: var(--ink);
            border-color: rgba(255,255,255,.88);
            background: rgba(255,255,255,.84);
        }

        .mobile-quickbar a.cta {
            color: #fff;
            background: linear-gradient(135deg, #b52b17, #db5c23 62%, #ff8b3d);
            box-shadow: 0 10px 18px rgba(181,43,23,.16);
        }

        .mobile-quickbar .count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 18px;
            height: 18px;
            padding-inline: 5px;
            margin-left: 6px;
            border-radius: 999px;
            background: rgba(15,93,245,.12);
            color: var(--brand-2);
            font-size: .68rem;
            line-height: 1;
        }

        /* Stage 4 brand refresh (Uriah logo + stronger nav/footer) */
        .vertice-logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .vertice-logo-mark {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            background:
                radial-gradient(circle at 25% 20%, rgba(255,255,255,.55), transparent 52%),
                linear-gradient(180deg, rgba(255,255,255,.65), rgba(255,255,255,.28));
            border: 1px solid rgba(191, 151, 46, .18);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.6),
                0 8px 18px rgba(159, 122, 31, .08);
            padding: 2px;
        }

        .vertice-logo-mark svg {
            width: 100%;
            height: 100%;
            display: block;
            filter: drop-shadow(0 2px 6px rgba(159, 122, 31, .12));
        }

        .vertice-logo-text {
            display: grid;
            gap: 1px;
            min-width: 0;
        }

        .vertice-logo-text strong {
            font-family: "Fraunces", Georgia, serif;
            font-size: 1.05rem;
            line-height: 1;
            letter-spacing: .01em;
            font-weight: 700;
            color: #8a6717;
            background: linear-gradient(135deg, #f0e3a5 0%, #cfab4d 38%, #a77a20 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            white-space: nowrap;
            text-transform: none;
        }

        .vertice-logo-text small {
            color: color-mix(in srgb, var(--muted) 92%, black 8%);
            font-size: .58rem;
            letter-spacing: .09em;
            text-transform: uppercase;
            font-weight: 800;
            white-space: nowrap;
        }

        .vertice-logo.footer .vertice-logo-mark {
            width: 48px;
            height: 48px;
            border-radius: 14px;
        }

        .vertice-logo.footer .vertice-logo-text strong {
            font-size: 1.2rem;
            letter-spacing: .01em;
        }

        .vertice-logo.footer .vertice-logo-text small {
            font-size: .66rem;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }

        .brand.brand-vertice {
            padding: 6px 8px 6px 4px;
            border-radius: 14px;
            transition: background .16s ease;
        }

        .brand.brand-vertice:hover {
            background: rgba(255,255,255,.42);
        }

        .brand-mark,
        .brand-text {
            display: none;
        }

        .site-header {
            background:
                linear-gradient(180deg, rgba(255,255,255,.88), rgba(255,255,255,.74)),
                linear-gradient(90deg, rgba(15,93,245,.04), rgba(195,58,29,.04));
            border-color: rgba(255,255,255,.78);
        }

        .site-header::before {
            content: "";
            position: absolute;
            inset: 0 0 auto 0;
            height: 2px;
            border-radius: 16px 16px 0 0;
            background: linear-gradient(90deg, #0f5df5 0%, #0f5df5 28%, #c33a1d 68%, #ff8b3d 100%);
            opacity: .8;
            pointer-events: none;
        }

        .header-shell {
            grid-template-columns: auto minmax(0, 1fr);
            align-items: center;
            gap: 10px;
            padding: 4px 8px;
        }

        .header-inner {
            padding: 6px 4px 6px 8px;
            min-height: 0;
        }

        .header-panel {
            padding: 4px;
            gap: 10px;
            grid-template-columns: minmax(0, 280px) minmax(0, 1fr);
            align-items: center;
            border-radius: 14px;
            background:
                linear-gradient(180deg, rgba(255,255,255,.66), rgba(255,255,255,.52));
            border: 1px solid rgba(255,255,255,.82);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.9);
        }

        .header-search {
            border-radius: 12px;
            background: rgba(255,255,255,.84);
            border-color: rgba(255,255,255,.95);
            gap: 6px;
            padding: 4px 4px 4px 8px;
        }

        .header-search-kicker {
            display: inline-flex;
            min-width: fit-content;
            font-size: .66rem;
            letter-spacing: .14em;
        }

        .header-search input {
            padding: 6px 4px;
            font-size: .9rem;
        }

        .header-search .btn {
            border-radius: 10px;
            padding: 7px 9px;
            font-size: .76rem;
        }

        .header-panel .nav {
            border-radius: 12px;
            padding: 3px;
            background: rgba(255,255,255,.68);
            border: 1px solid rgba(255,255,255,.82);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.9);
            flex-wrap: nowrap;
            gap: 4px;
        }

        .nav-link {
            padding: 8px 11px;
            border-radius: 10px;
            font-weight: 800;
            font-size: .86rem;
            color: color-mix(in srgb, var(--muted) 82%, black 18%);
        }

        .nav-link:hover {
            background: rgba(255,255,255,.92);
            border-color: rgba(255,255,255,.95);
            box-shadow: 0 8px 16px rgba(27,20,13,.05);
            color: var(--ink);
        }

        .nav-link.active {
            background:
                linear-gradient(180deg, rgba(255,255,255,.96), rgba(255,255,255,.84));
            border-color: rgba(255,255,255,.98);
            color: #111827;
        }

        .nav-link.active::after {
            left: 10px;
            right: 10px;
            bottom: 4px;
            height: 2px;
            background: linear-gradient(90deg, #0f5df5, #b52b17);
        }

        .user-chip {
            background: rgba(255,255,255,.9);
            border-color: rgba(255,255,255,.96);
            font-size: .72rem;
            padding: 7px 9px;
        }

        .header-panel .nav .btn-primary {
            margin-left: 0;
            border-radius: 10px;
            padding-inline: 12px;
            white-space: nowrap;
        }

        .header-panel .nav .inline-form .btn {
            border-radius: 10px;
            font-size: .78rem;
            padding-inline: 10px;
        }

        .footer-shell {
            padding: 20px;
            gap: 16px;
        }

        .footer-top {
            grid-template-columns: minmax(0, 1fr) 420px;
            align-items: stretch;
        }

        .footer-brand {
            height: 100%;
            align-content: start;
            gap: 12px;
        }

        .footer-brand-head {
            align-items: flex-start;
        }

        .footer-brand-copy span {
            display: block;
            max-width: 48ch;
            line-height: 1.35;
        }

        .footer-badge-row {
            gap: 7px;
        }

        .footer-chip {
            font-size: .72rem;
            padding: 6px 9px;
            font-weight: 800;
        }

        .footer-kpis {
            align-content: stretch;
            height: 100%;
        }

        .footer-kpi {
            min-height: 92px;
            align-content: start;
        }

        .footer-kpi strong {
            font-size: 1.02rem;
            letter-spacing: -.01em;
        }

        .footer-kpi span {
            font-size: .7rem;
        }

        .footer-grid {
            align-items: stretch;
            gap: 12px;
        }

        .footer-col {
            grid-template-rows: auto auto 1fr;
            align-content: start;
            min-height: 152px;
            gap: 8px;
        }

        .footer-col p {
            min-height: 2.6em;
            margin: 0;
        }

        .footer-links {
            gap: 5px;
            align-content: start;
        }

        .footer-links a {
            display: inline-block;
            line-height: 1.25;
        }

        .footer-bottom {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 12px;
            align-items: start;
            padding-top: 10px;
        }

        .footer-status {
            display: block;
            line-height: 1.45;
        }

        .footer-status .mono {
            display: inline-flex;
            margin-top: 8px;
            margin-right: 6px;
        }

        @media (max-width: 1180px) {
            .header-panel {
                grid-template-columns: minmax(0, 240px) minmax(0, 1fr);
            }

            .header-search-kicker {
                display: none;
            }
        }

        @media (max-width: 980px) {
            .header-shell {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .header-inner {
                justify-content: space-between;
                padding: 8px 12px;
            }

            .header-panel {
                grid-template-columns: 1fr;
                gap: 10px;
                padding: 0 12px 10px;
                background: transparent;
                border: 0;
                box-shadow: none;
            }

            .header-panel .nav {
                justify-content: flex-start;
                flex-wrap: wrap;
            }

            .footer-top {
                grid-template-columns: 1fr;
            }

            .footer-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 680px) {
            body {
                padding-bottom: 84px;
            }

            .header-utility {
                padding: 8px 12px 0;
            }

            .header-utility .utility-text {
                display: none;
            }

            .header-inner {
                min-height: 62px;
                padding: 8px 12px;
            }

            .menu-toggle {
                display: inline-flex;
            }

            .header-panel {
                display: none;
                padding: 8px 12px 12px;
                grid-template-columns: 1fr;
                border-top: 1px solid rgba(255,255,255,.52);
                background: transparent;
            }

            .site-header[data-nav-open="true"] .header-panel {
                display: grid;
            }

            .header-search {
                width: 100%;
            }

            .header-search-kicker {
                display: none;
            }

            .header-panel .nav {
                width: 100%;
                display: grid;
                gap: 8px;
                background: transparent;
                border: 0;
                box-shadow: none;
                padding: 0;
            }

            .header-panel .nav .nav-link,
            .header-panel .nav .user-chip,
            .header-panel .nav .inline-form,
            .header-panel .nav .btn {
                width: 100%;
            }

            .header-panel .nav .inline-form .btn {
                width: 100%;
            }

            .header-panel .nav .btn-primary {
                margin-left: 0;
            }

            .vertice-logo-text strong {
                font-size: .85rem;
                letter-spacing: .06em;
            }

            .vertice-logo-text small {
                font-size: .55rem;
                letter-spacing: .08em;
            }

            .footer-brand-head {
                align-items: center;
            }

            .nav-link.active::after {
                left: 12px;
                right: auto;
                width: 28px;
                bottom: 7px;
            }

            .footer-kpis {
                grid-template-columns: 1fr;
            }

            .footer-grid {
                grid-template-columns: 1fr;
            }

            .footer-bottom {
                flex-direction: column;
                align-items: flex-start;
            }

            .mobile-quickbar {
                display: flex;
            }
        }

        /* Stage 5 refinement (compact premium header + aligned footer) */
        .site-footer {
            margin: 36px auto 28px;
        }

        .chat-widget {
            border-radius: 18px;
            gap: 12px;
        }

        .chat-connection {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: .74rem;
            font-weight: 800;
            background: rgba(17,24,39,.05);
            border: 1px solid rgba(17,24,39,.06);
            color: #4b5563;
            white-space: nowrap;
        }

        .chat-connection::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: #9ca3af;
            box-shadow: 0 0 0 3px rgba(156,163,175,.10);
        }

        .chat-connection[data-tone="success"] {
            color: #0f8a5f;
            background: rgba(15,138,95,.07);
            border-color: rgba(15,138,95,.12);
        }

        .chat-connection[data-tone="success"]::before {
            background: #10b981;
            box-shadow: 0 0 0 3px rgba(16,185,129,.14);
        }

        .chat-connection[data-tone="warning"] {
            color: #b46a00;
            background: rgba(180,106,0,.07);
            border-color: rgba(180,106,0,.12);
        }

        .chat-connection[data-tone="warning"]::before {
            background: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245,158,11,.12);
        }

        .chat-connection[data-tone="error"] {
            color: #b52b17;
            background: rgba(181,43,23,.07);
            border-color: rgba(181,43,23,.12);
        }

        .chat-connection[data-tone="error"]::before {
            background: #ef4444;
            box-shadow: 0 0 0 3px rgba(239,68,68,.12);
        }

        .chat-thread {
            display: grid;
            gap: 10px;
            max-height: 340px;
            overflow: auto;
            padding: 4px;
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,.84);
            background:
                linear-gradient(180deg, rgba(255,255,255,.58), rgba(255,255,255,.36));
        }

        .chat-message {
            max-width: min(88%, 680px);
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,.86);
            background: rgba(255,255,255,.88);
            box-shadow: 0 8px 18px rgba(27,20,13,.04);
            padding: 10px 12px;
            display: grid;
            gap: 6px;
        }

        .chat-message.mine {
            margin-left: auto;
            background:
                linear-gradient(180deg, rgba(15,93,245,.08), rgba(15,93,245,.03)),
                rgba(255,255,255,.92);
            border-color: rgba(15,93,245,.12);
        }

        .chat-message.from-admin:not(.mine) {
            background:
                linear-gradient(180deg, rgba(195,58,29,.07), rgba(195,58,29,.03)),
                rgba(255,255,255,.92);
            border-color: rgba(195,58,29,.12);
        }

        .chat-message.system {
            max-width: 100%;
            background: rgba(17,24,39,.03);
            border-style: dashed;
            border-color: rgba(17,24,39,.08);
        }

        .chat-message-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            font-size: .75rem;
            color: var(--muted);
        }

        .chat-message-meta strong {
            color: var(--ink);
            font-size: .78rem;
        }

        .chat-message-body {
            color: color-mix(in srgb, var(--ink) 92%, white 8%);
            line-height: 1.45;
            font-size: .9rem;
            overflow-wrap: anywhere;
        }

        .chat-form {
            display: grid;
            gap: 10px;
        }

        .chat-input {
            min-height: 92px;
        }

        .chat-empty {
            border: 1px dashed rgba(120,102,83,.16);
            border-radius: 12px;
            padding: 10px 12px;
            background: rgba(255,255,255,.35);
        }

        .social-auth-grid {
            display: grid;
            gap: 8px;
        }

        .social-btn {
            justify-content: flex-start;
            gap: 10px;
            padding: 10px 12px;
            font-weight: 800;
        }

        .social-icon {
            width: 24px;
            height: 24px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            font-size: .78rem;
            font-weight: 900;
            background: rgba(17,24,39,.06);
            border: 1px solid rgba(17,24,39,.06);
        }

        .social-btn.google .social-icon {
            color: #ea4335;
            background: rgba(234,67,53,.07);
            border-color: rgba(234,67,53,.11);
        }

        .social-btn.facebook .social-icon {
            color: #1877f2;
            background: rgba(24,119,242,.07);
            border-color: rgba(24,119,242,.11);
        }

        .social-btn.github .social-icon {
            color: #111827;
            background: rgba(17,24,39,.05);
            border-color: rgba(17,24,39,.08);
        }

        .oauth-divider {
            position: relative;
            text-align: center;
            font-size: .74rem;
            font-weight: 800;
            color: var(--muted);
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .oauth-divider::before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: 50%;
            border-top: 1px solid rgba(120,102,83,.14);
            transform: translateY(-50%);
        }

        .oauth-divider span {
            position: relative;
            background: rgba(255,255,255,.92);
            padding: 0 10px;
            border-radius: 999px;
        }

        .header-meta-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin-left: 10px;
            padding: 7px 10px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.88);
            background: rgba(255,255,255,.72);
            color: color-mix(in srgb, var(--muted) 82%, black 18%);
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .03em;
            white-space: nowrap;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.95);
        }

        .header-meta-chip .dot {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: linear-gradient(135deg, #0f5df5, #c33a1d);
            box-shadow: 0 0 0 3px rgba(15,93,245,.08);
        }

        .floating-sticky,
        .summary-sticky-card {
            top: 126px;
        }

        .header-panel {
            grid-template-columns: minmax(0, 330px) minmax(0, 1fr);
            gap: 10px;
            padding: 6px 10px 8px 0;
        }

        .header-search {
            min-height: 44px;
            border-radius: 14px;
            gap: 4px;
            padding: 4px;
            background:
                linear-gradient(180deg, rgba(255,255,255,.94), rgba(255,255,255,.82));
            border: 1px solid rgba(255,255,255,.98);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.98),
                0 6px 16px rgba(27,20,13,.04);
        }

        .header-search-kicker {
            margin-left: 2px;
            padding-left: 8px;
            padding-right: 2px;
            letter-spacing: .12em;
            font-size: .64rem;
        }

        .header-search input {
            padding: 6px 4px;
            font-size: .92rem;
        }

        .header-search .btn {
            min-width: 42px;
            border-radius: 10px;
            padding: 8px 10px;
            font-size: .76rem;
        }

        .header-panel .nav {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            align-items: center;
            gap: 8px;
            min-width: 0;
            width: 100%;
        }

        .nav-main {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            min-width: 0;
            max-width: 100%;
            flex-wrap: wrap;
            padding: 3px;
            border-radius: 13px;
            border: 1px solid rgba(255,255,255,.9);
            background:
                linear-gradient(180deg, rgba(255,255,255,.82), rgba(255,255,255,.68));
            box-shadow: inset 0 1px 0 rgba(255,255,255,.98);
        }

        .nav-actions {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: 6px;
            row-gap: 6px;
            min-width: 0;
            flex-wrap: wrap;
        }

        .nav-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-height: 38px;
            padding: 8px 11px;
            border-radius: 10px;
            font-size: .84rem;
            line-height: 1;
            white-space: nowrap;
        }

        .nav-main .nav-link {
            color: color-mix(in srgb, var(--muted) 80%, black 20%);
        }

        .nav-main .nav-link.active {
            background: rgba(255,255,255,.95);
            box-shadow: 0 10px 16px rgba(27,20,13,.04);
        }

        .nav-main .nav-link.active::after {
            left: 10px;
            right: 10px;
            bottom: 5px;
        }

        .nav-actions .nav-link {
            background: rgba(255,255,255,.74);
            border: 1px solid rgba(255,255,255,.9);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.95);
        }

        .nav-actions .nav-link.active {
            background: rgba(255,255,255,.92);
        }

        .nav-link .badge {
            margin-left: 2px;
            padding: 3px 7px;
            min-height: 18px;
            font-size: .68rem;
        }

        .user-chip {
            min-height: 38px;
            align-self: center;
        }

        .header-checkout-btn {
            min-height: 38px;
            border-radius: 10px;
            padding-inline: 13px;
            box-shadow: 0 10px 18px rgba(181,43,23,.16);
        }

        .footer-shell {
            padding: 18px;
            gap: 14px;
        }

        .footer-top {
            grid-template-columns: minmax(0, 1.15fr) minmax(300px, .85fr);
            gap: 12px;
        }

        .footer-brand {
            justify-content: space-between;
        }

        .footer-kpis {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
        }

        .footer-kpi {
            min-height: 96px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 10px;
            padding: 12px 11px;
        }

        .footer-kpi strong {
            line-height: 1;
        }

        .footer-grid {
            gap: 10px;
        }

        .footer-col {
            min-height: 146px;
            padding: 13px;
        }

        .footer-bottom {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: start;
            gap: 12px;
            padding-top: 10px;
        }

        .footer-status {
            display: grid;
            gap: 8px;
            align-content: start;
            line-height: 1.35;
        }

        .footer-status-copy {
            min-width: 0;
        }

        .footer-status-tags {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .footer-status .mono {
            margin: 0;
        }

        .footer-signature {
            display: grid;
            justify-items: end;
            gap: 2px;
            text-align: right;
            white-space: nowrap;
            align-self: start;
            padding-top: 2px;
        }

        .footer-signature strong {
            font-family: "Manrope", system-ui, sans-serif;
            font-size: .78rem;
            letter-spacing: .08em;
            font-weight: 900;
            color: #111827;
        }

        .footer-signature .small {
            font-size: .74rem;
            letter-spacing: .05em;
        }

        @media (max-width: 1600px) and (min-width: 1181px) {
            .header-panel {
                grid-template-columns: 1fr;
                gap: 8px;
                padding: 6px 10px 8px;
            }

            .header-search {
                width: 100%;
            }

            .header-panel .nav {
                grid-template-columns: 1fr;
                align-items: start;
                gap: 8px;
            }

            .nav-main,
            .nav-actions {
                width: 100%;
            }

            .nav-actions {
                justify-content: flex-start;
            }

            .header-checkout-btn {
                margin-left: auto;
            }
        }

        @media (max-width: 1180px) {
            .header-meta-chip {
                display: none;
            }

            .header-panel {
                grid-template-columns: minmax(0, 280px) minmax(0, 1fr);
            }

            .header-panel .nav {
                grid-template-columns: 1fr;
                align-items: start;
            }

            .nav-main,
            .nav-actions {
                width: 100%;
            }

            .nav-actions {
                justify-content: flex-start;
            }

            .nav-actions .user-chip {
                display: none;
            }
        }

        @media (max-width: 980px) {
            .floating-sticky,
            .summary-sticky-card {
                top: 98px;
            }

            .header-panel .nav {
                display: grid;
                gap: 8px;
            }

            .nav-main,
            .nav-actions {
                display: grid;
                width: 100%;
                gap: 8px;
                padding: 0;
                border: 0;
                background: transparent;
                box-shadow: none;
            }

            .nav-actions {
                grid-template-columns: 1fr;
            }

            .nav-main .nav-link,
            .nav-actions .nav-link,
            .nav-actions .user-chip,
            .nav-actions .inline-form,
            .nav-actions .btn {
                width: 100%;
            }

            .nav-main .nav-link,
            .nav-actions .nav-link {
                justify-content: space-between;
            }

            .nav-actions .nav-link,
            .nav-main .nav-link {
                background: rgba(255,255,255,.82);
                border: 1px solid rgba(255,255,255,.92);
                box-shadow: inset 0 1px 0 rgba(255,255,255,.95);
            }

            .header-checkout-btn {
                width: 100%;
                justify-content: center;
            }

            .footer-top {
                grid-template-columns: 1fr;
            }

            .footer-kpis {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .footer-bottom {
                grid-template-columns: 1fr;
                align-items: flex-start;
            }

            .footer-signature {
                justify-items: start;
                text-align: left;
                white-space: normal;
            }
        }

        @media (max-width: 680px) {
            .header-meta-chip {
                display: none;
            }

            .footer-kpis {
                grid-template-columns: 1fr;
            }
        }

        /* Header stability overrides (avoid menu overlap in logged/admin states) */
        @media (min-width: 981px) {
            .header-panel {
                display: grid;
                grid-template-columns: minmax(0, 1fr);
                align-items: stretch;
                gap: 8px;
                min-width: 0;
            }

            .header-search {
                width: 100%;
                min-width: 0;
            }

            .header-panel .nav {
                display: grid;
                grid-template-columns: minmax(0, 1fr) auto;
                align-items: center;
                gap: 8px;
                min-width: 0;
                width: 100%;
            }

            .nav-main {
                display: flex;
                width: 100%;
                min-width: 0;
                flex-wrap: wrap;
                gap: 4px;
            }

            .nav-actions {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 6px;
                row-gap: 6px;
                flex-wrap: wrap;
                min-width: 0;
                max-width: 100%;
            }

            .nav-actions > * {
                flex: 0 0 auto;
                min-width: 0;
            }

            .nav-actions .inline-form {
                display: inline-flex;
            }

            .header-checkout-btn {
                margin-left: 0;
            }
        }

        @media (max-width: 1680px) and (min-width: 681px) {
            .header-shell {
                grid-template-columns: 1fr;
                align-items: stretch;
                gap: 6px;
            }

            .header-inner {
                width: 100%;
            }

            .header-meta-chip {
                margin-left: auto;
            }
        }

        @media (max-width: 1480px) and (min-width: 981px) {
            .header-meta-chip {
                display: none;
            }

            .header-panel .nav {
                grid-template-columns: 1fr;
                align-items: start;
            }

            .nav-main,
            .nav-actions {
                width: 100%;
            }

            .nav-actions {
                justify-content: flex-start;
            }

            .nav-actions .user-chip {
                display: none;
            }

            .header-checkout-btn {
                margin-left: auto;
            }
        }

        /* Uriah premium theme: black / white / gold */
        :root {
            --bg: #f4f2ed;
            --surface: #ffffff;
            --surface-2: #f7f4ee;
            --ink: #101011;
            --muted: #666056;
            --line: rgba(16, 16, 17, .10);
            --brand: #c8a44e;
            --brand-2: #a7802d;
            --shadow: 0 24px 64px rgba(9, 9, 11, .10);
            --shadow-soft: 0 12px 28px rgba(9, 9, 11, .06);
        }

        body {
            background:
                radial-gradient(circle at 14% 3%, rgba(200, 164, 78, .10), transparent 42%),
                radial-gradient(circle at 88% 10%, rgba(16, 16, 17, .05), transparent 50%),
                radial-gradient(circle at 70% 92%, rgba(200, 164, 78, .05), transparent 42%),
                linear-gradient(180deg, #faf9f6, #f4f2ed 60%, #efece5);
            color: var(--ink);
        }

        body::before {
            opacity: .06;
            background-image:
                linear-gradient(rgba(16,16,17,.09) 1px, transparent 1px),
                linear-gradient(90deg, rgba(16,16,17,.09) 1px, transparent 1px);
        }

        ::selection {
            background: rgba(200, 164, 78, .24);
            color: #0b0b0c;
        }

        :focus-visible {
            outline: 2px solid rgba(200, 164, 78, .38);
            outline-offset: 2px;
        }

        .text-gradient {
            background: linear-gradient(135deg, #0e0e10 0%, #403315 38%, #c9a54f 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .section-kicker::before,
        .hero-tagline .dot,
        .header-search-kicker::before {
            background: linear-gradient(90deg, #8d6c22, #d7bc76);
            box-shadow: none;
        }

        .site-header {
            background:
                linear-gradient(180deg, rgba(13, 13, 15, .94), rgba(19, 19, 22, .90)),
                linear-gradient(90deg, rgba(200,164,78,.06), rgba(255,255,255,0));
            border-color: rgba(200, 164, 78, .18);
            box-shadow: 0 18px 34px rgba(0,0,0,.18);
        }

        .site-header::before {
            background: linear-gradient(90deg, rgba(200,164,78,.45), #e4cd8f, rgba(200,164,78,.45));
            opacity: .95;
        }

        .header-inner {
            color: #f4efe2;
        }

        .site-header .vertice-logo-text small {
            color: rgba(244, 239, 226, .52);
        }

        .header-meta-chip {
            color: rgba(244, 239, 226, .9);
            background: rgba(255,255,255,.04);
            border-color: rgba(200, 164, 78, .18);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.04);
        }

        .header-panel {
            background: rgba(255,255,255,.03);
            border-color: rgba(200, 164, 78, .14);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.03);
        }

        .header-search {
            background: rgba(255,255,255,.03);
            border-color: rgba(200, 164, 78, .14);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.03);
        }

        .header-search-kicker {
            color: rgba(244, 239, 226, .72);
        }

        .header-search input {
            color: #f4efe2;
        }

        .header-search input::placeholder {
            color: rgba(244, 239, 226, .42);
        }

        .nav-main {
            background: rgba(255,255,255,.03);
            border-color: rgba(200, 164, 78, .12);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.03);
        }

        .nav-main .nav-link,
        .nav-actions .nav-link {
            color: rgba(244, 239, 226, .85);
            background: rgba(255,255,255,.03);
            border-color: rgba(255,255,255,.06);
            box-shadow: none;
        }

        .nav-link:hover {
            background: rgba(255,255,255,.06);
            border-color: rgba(200, 164, 78, .18);
            color: #fff8e6;
        }

        .nav-link.active,
        .nav-actions .nav-link.active,
        .nav-main .nav-link.active {
            background: rgba(255,255,255,.08);
            border-color: rgba(200, 164, 78, .22);
            color: #fff8ea;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.03);
        }

        .nav-link.active::after,
        .nav-main .nav-link.active::after {
            background: linear-gradient(90deg, rgba(200,164,78,.35), #e0c985, rgba(200,164,78,.35));
            opacity: 1;
        }

        .user-chip {
            background: rgba(255,255,255,.04);
            border-color: rgba(200, 164, 78, .16);
            color: rgba(244, 239, 226, .9);
        }

        .user-chip::before {
            background: linear-gradient(135deg, #8d6c22, #d7bc76);
            box-shadow: 0 0 0 4px rgba(200,164,78,.10);
        }

        .menu-toggle {
            color: #f5efdf;
            background: rgba(255,255,255,.04);
            border-color: rgba(200, 164, 78, .18);
            box-shadow: none;
        }

        .btn {
            box-shadow: none;
        }

        .btn-primary {
            color: #f8f5ee;
            border: 1px solid rgba(200, 164, 78, .32);
            background:
                linear-gradient(180deg, rgba(255,255,255,.05), rgba(255,255,255,0)),
                linear-gradient(180deg, #171718, #0f0f10);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.05),
                0 10px 20px rgba(0,0,0,.15);
        }

        .btn-primary:hover {
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.07),
                0 14px 26px rgba(0,0,0,.18);
        }

        .btn-secondary {
            color: #101011;
            background: rgba(255,255,255,.98);
            border: 1px solid rgba(16, 16, 17, .10);
            box-shadow: 0 8px 16px rgba(0,0,0,.04);
        }

        .btn-secondary:hover {
            border-color: rgba(200, 164, 78, .22);
            background: #fff;
        }

        .badge,
        .badge-brand {
            color: #7f6120;
            background: rgba(200, 164, 78, .12);
            border: 1px solid rgba(200, 164, 78, .18);
        }

        .pill,
        .hero-proof .pill {
            background: rgba(255,255,255,.92);
            border-color: rgba(200, 164, 78, .14);
            color: #242017;
        }

        .hero-tagline {
            color: #31280f;
            background: rgba(255,255,255,.92);
            border-color: rgba(200, 164, 78, .16);
            box-shadow: 0 8px 16px rgba(0,0,0,.03);
        }

        .hero {
            border-color: rgba(16,16,17,.05);
            background:
                radial-gradient(circle at 86% 8%, rgba(200,164,78,.10), transparent 40%),
                radial-gradient(circle at 4% 96%, rgba(16,16,17,.03), transparent 48%),
                linear-gradient(180deg, rgba(255,255,255,.93), rgba(255,255,255,.84));
            box-shadow: var(--shadow);
        }

        .hero::after {
            background:
                linear-gradient(135deg, rgba(200,164,78,.08), rgba(200,164,78,0)),
                linear-gradient(315deg, rgba(16,16,17,.04), rgba(16,16,17,0));
        }

        .hero-pane + .hero-pane {
            border-color: rgba(16,16,17,.06);
            background:
                radial-gradient(circle at 92% 10%, rgba(200,164,78,.11), transparent 36%),
                linear-gradient(180deg, rgba(255,255,255,.72), rgba(255,255,255,.64));
        }

        .hero-pane::before {
            border-color: rgba(200,164,78,.13);
        }

        .card,
        .glass-panel,
        .board-card,
        .metric-card,
        .hero-metric,
        .timeline-item,
        .process-step {
            background:
                linear-gradient(180deg, rgba(255,255,255,.98), rgba(255,255,255,.92));
            border-color: rgba(16,16,17,.07);
            box-shadow: var(--shadow-soft);
            backdrop-filter: none;
        }

        .board-card::after {
            background: radial-gradient(circle, rgba(200,164,78,.12), rgba(200,164,78,0));
        }

        .timeline-item .marker,
        .process-step .num {
            background: linear-gradient(135deg, rgba(200,164,78,.22), rgba(200,164,78,.10));
            border-color: rgba(200, 164, 78, .20);
            color: #4a3913;
        }

        .metric-card strong,
        .hero-metric strong,
        .footer-kpi strong {
            color: #161618;
        }

        .surface-dark {
            color: #f6f2e8;
            background:
                radial-gradient(circle at 86% 8%, rgba(200,164,78,.16), transparent 42%),
                radial-gradient(circle at 6% 100%, rgba(255,255,255,.04), transparent 45%),
                linear-gradient(160deg, #0f0f10, #18181b 48%, #101012);
            border: 1px solid rgba(200, 164, 78, .16);
            box-shadow: 0 20px 44px rgba(0,0,0,.26);
        }

        .surface-dark .muted {
            color: rgba(246, 242, 232, .70);
        }

        .surface-dark .pill {
            background: rgba(255,255,255,.05);
            border-color: rgba(200, 164, 78, .14);
            color: rgba(246, 242, 232, .92);
        }

        .surface-dark .process-step,
        .surface-dark .board-card {
            background: rgba(255,255,255,.03);
            border-color: rgba(200, 164, 78, .12);
            box-shadow: none;
        }

        .surface-dark .process-step .label,
        .surface-dark .board-title strong {
            color: #f8f5ec;
        }

        .surface-dark .process-step .eta {
            color: rgba(246, 242, 232, .62);
        }

        .surface-dark .process-step .num {
            color: #f3e6ba;
            background: rgba(200, 164, 78, .14);
            border-color: rgba(200, 164, 78, .18);
        }

        .input,
        .select,
        .textarea {
            background: #fff;
            border-color: rgba(16, 16, 17, .11);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.95);
        }

        .input:hover,
        .select:hover,
        .textarea:hover {
            border-color: rgba(200, 164, 78, .28);
        }

        .input:focus,
        .select:focus,
        .textarea:focus {
            outline: 2px solid rgba(200, 164, 78, .18);
            border-color: rgba(200, 164, 78, .36);
        }

        .field label {
            color: color-mix(in srgb, var(--muted) 92%, black 8%);
        }

        .table-wrap {
            border-color: rgba(16,16,17,.08);
            background: #fff;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.7);
        }

        table {
            background: #fff;
        }

        th,
        td {
            border-bottom-color: rgba(16,16,17,.07);
        }

        th {
            color: rgba(16,16,17,.58);
        }

        .status-dot.production::before {
            background: #c8a44e;
            box-shadow: 0 0 0 4px rgba(200,164,78,.14);
        }

        .chat-connection {
            background: rgba(16,16,17,.03);
            border-color: rgba(16,16,17,.06);
            color: #59524a;
        }

        .chat-thread {
            border-color: rgba(16,16,17,.07);
            background: linear-gradient(180deg, rgba(255,255,255,.94), rgba(255,255,255,.88));
        }

        .chat-message {
            border-color: rgba(16,16,17,.07);
            background: #fff;
        }

        .chat-message.mine {
            background:
                linear-gradient(180deg, rgba(200,164,78,.11), rgba(200,164,78,.04)),
                rgba(255,255,255,.98);
            border-color: rgba(200, 164, 78, .18);
        }

        .chat-message.from-admin:not(.mine) {
            background:
                linear-gradient(180deg, rgba(16,16,17,.04), rgba(16,16,17,.02)),
                rgba(255,255,255,.98);
            border-color: rgba(16, 16, 17, .10);
        }

        .chat-message.system {
            background: rgba(16,16,17,.02);
            border-color: rgba(16,16,17,.08);
        }

        .social-icon {
            background: rgba(16,16,17,.04);
            border-color: rgba(16,16,17,.08);
        }

        .oauth-divider::before {
            border-top-color: rgba(16,16,17,.09);
        }

        .oauth-divider span {
            background: rgba(255,255,255,.96);
        }

        .scene-label {
            background: rgba(16,16,17,.82);
            border-color: rgba(200, 164, 78, .18);
            color: #f4e9c5;
        }

        .scene-text strong {
            color: #111;
        }

        .scene-text span {
            background: rgba(16,16,17,.10);
        }

        .print-scene.card .scene-object.main {
            background: linear-gradient(135deg, #ffffff, #f6f3ec 62%, #ece6d8);
        }

        .print-scene.card .scene-object.back {
            background: linear-gradient(135deg, #f8f7f3, #efe8d8 56%, #dbc488);
        }

        .print-scene.card .scene-accent,
        .print-scene.flyer .scene-accent {
            background:
                linear-gradient(140deg, rgba(200,164,78,.14), rgba(200,164,78,.03)),
                rgba(255,255,255,.72);
        }

        .print-scene.flyer .scene-object.main {
            background: linear-gradient(135deg, #ffffff, #f6f2ea 52%, #e7d8b0);
        }

        .print-scene.flyer .scene-object.mid {
            background: linear-gradient(135deg, #f8f7f4, #ece8de 54%, #ddd4c0);
        }

        .print-scene.banner .scene-object.main {
            background:
                linear-gradient(180deg, rgba(255,255,255,.10), rgba(255,255,255,0)),
                linear-gradient(145deg, #0d0d0f 0%, #1b1b20 55%, #5a4720 130%);
        }

        .print-scene.banner .scene-accent.ring {
            background:
                radial-gradient(circle at center, rgba(255,255,255,.18) 30%, transparent 31%),
                linear-gradient(140deg, rgba(200,164,78,.18), rgba(200,164,78,.05));
        }

        .print-scene.labels .scene-object.sheet {
            background: linear-gradient(135deg, #fff, #f8f6f1 60%, #efe8da);
        }

        .print-scene.labels .scene-object.sheet .grid-dots span {
            background:
                radial-gradient(circle at 40% 35%, rgba(200,164,78,.20), rgba(200,164,78,.04)),
                radial-gradient(circle at 70% 70%, rgba(16,16,17,.08), rgba(16,16,17,.02)),
                rgba(255,255,255,.88);
            border-color: rgba(16,16,17,.05);
        }

        .print-scene.labels .scene-object.roll {
            background:
                linear-gradient(180deg, rgba(255,255,255,.22), rgba(255,255,255,.03)),
                linear-gradient(145deg, #f4e5ae, #cfab4f 48%, #a77b22);
        }

        .swatch.c {
            background: linear-gradient(135deg, #ffffff, #ece8df);
        }

        .swatch.m {
            background: linear-gradient(135deg, #1b1b1d, #38343b);
        }

        .swatch.y {
            background: linear-gradient(135deg, #f0dfa3, #cfa44a);
        }

        .swatch.k {
            background: linear-gradient(135deg, #09090a, #242428);
        }

        .footer-shell {
            color: rgba(246, 242, 232, .92);
            border-color: rgba(200, 164, 78, .16);
            background:
                radial-gradient(circle at 12% 0%, rgba(200,164,78,.12), transparent 38%),
                radial-gradient(circle at 95% 10%, rgba(255,255,255,.03), transparent 45%),
                linear-gradient(180deg, #111214, #17181a);
            box-shadow: 0 18px 42px rgba(0,0,0,.22);
        }

        .footer-shell::after {
            background:
                linear-gradient(135deg, rgba(200,164,78,.10), rgba(200,164,78,0)),
                linear-gradient(315deg, rgba(255,255,255,.03), rgba(255,255,255,0));
        }

        .footer-shell .muted,
        .footer-col p,
        .footer-kpi span,
        .footer-bottom,
        .footer-signature .small {
            color: rgba(246, 242, 232, .62);
        }

        .footer-col,
        .footer-chip,
        .footer-kpi {
            background: rgba(255,255,255,.03);
            border-color: rgba(200, 164, 78, .10);
            box-shadow: none;
        }

        .footer-links a {
            color: rgba(255,255,255,.88);
        }

        .footer-links a:hover {
            color: #e4cd8f;
        }

        .footer-chip .mini-dot {
            background: #d1b469 !important;
            box-shadow: 0 0 0 3px rgba(200,164,78,.10) !important;
        }

        .footer-status .mono {
            background: rgba(255,255,255,.03);
            border-color: rgba(200, 164, 78, .10);
            color: rgba(246, 242, 232, .88);
        }

        .footer-signature strong {
            color: #f4e7bd;
        }

        .mobile-quickbar {
            border-color: rgba(200, 164, 78, .18);
            background: linear-gradient(180deg, rgba(14,14,16,.96), rgba(20,20,22,.92));
            box-shadow: 0 18px 34px rgba(0,0,0,.22);
        }

        .mobile-quickbar a {
            color: rgba(246, 242, 232, .72);
            background: rgba(255,255,255,.03);
            border-color: rgba(255,255,255,.03);
        }

        .mobile-quickbar a.active {
            color: #fff7e6;
            border-color: rgba(200, 164, 78, .16);
            background: rgba(255,255,255,.06);
        }

        .mobile-quickbar a.cta {
            color: #101011;
            background: linear-gradient(135deg, #f0dfa5, #caa24a);
            box-shadow: 0 10px 18px rgba(0,0,0,.16);
        }

        .mobile-quickbar .count {
            background: rgba(200,164,78,.14);
            color: #e8d193;
        }

        @media (max-width: 980px) {
            .header-panel {
                background: transparent;
                border: 0;
            }

            .nav-main .nav-link,
            .nav-actions .nav-link {
                color: #f4efe2;
                background: rgba(255,255,255,.04);
                border-color: rgba(200, 164, 78, .12);
            }

            .nav-actions .user-chip {
                color: #f4efe2;
            }
        }

        /* Stage 7 premium refinement: black canvas + editorial type + subtle motion */
        body {
            background:
                radial-gradient(circle at 16% 4%, rgba(200,164,78,.12), transparent 40%),
                radial-gradient(circle at 88% 14%, rgba(200,164,78,.06), transparent 45%),
                radial-gradient(circle at 50% 100%, rgba(255,255,255,.03), transparent 55%),
                linear-gradient(180deg, #09090a 0%, #0d0d0f 35%, #0b0b0d 100%);
        }

        body::before {
            opacity: .045;
            background-image:
                linear-gradient(rgba(255,255,255,.10) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.10) 1px, transparent 1px);
            background-size: 28px 28px;
            mask-image: radial-gradient(circle at center, black 28%, transparent 96%);
        }

        main.container {
            position: relative;
            padding-top: 18px;
            padding-bottom: 4px;
        }

        main.container::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(900px 420px at 20% 18%, rgba(200,164,78,.06), transparent 70%),
                radial-gradient(1000px 520px at 86% 20%, rgba(255,255,255,.03), transparent 75%);
            z-index: 0;
        }

        main.container > * {
            position: relative;
            z-index: 1;
        }

        h1, h2 {
            font-family: "Cormorant Garamond", "Fraunces", Georgia, serif;
            font-weight: 600;
            letter-spacing: -.012em;
            text-wrap: balance;
        }

        h3 {
            font-family: "Cormorant Garamond", "Fraunces", Georgia, serif;
            font-weight: 600;
            letter-spacing: -.006em;
        }

        .lead {
            color: color-mix(in srgb, var(--muted) 84%, white 16%);
            line-height: 1.62;
        }

        /* Section headings rendered directly on the black canvas (outside cards) */
        main.container > .stack-xl > .section-head .section-kicker,
        main.container > .stack-xl > .section-head .copy p,
        main.container > .stack-xl > .section-head .copy .muted {
            color: rgba(244, 239, 226, .70);
        }

        main.container > .stack-xl > .section-head .copy h2,
        main.container > .stack-xl > .section-head .copy h3 {
            color: #f7f3e9;
        }

        main.container > .stack-xl > .section-head .copy h2.text-gradient,
        main.container > .stack-xl > .section-head .copy h3.text-gradient {
            background: linear-gradient(135deg, #f6f0df 0%, #dbc27f 46%, #b98c2f 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .hero,
        .card,
        .glass-panel,
        .board-card,
        .metric-card,
        .hero-metric,
        .timeline-item,
        .process-step,
        .table-wrap {
            border-color: rgba(255,255,255,.08);
        }

        .hero {
            background:
                radial-gradient(circle at 87% 10%, rgba(200,164,78,.12), transparent 42%),
                radial-gradient(circle at 5% 100%, rgba(255,255,255,.03), transparent 48%),
                linear-gradient(180deg, rgba(250,250,247,.96), rgba(247,245,240,.90));
            box-shadow:
                0 18px 40px rgba(0,0,0,.16),
                0 1px 0 rgba(255,255,255,.45) inset;
        }

        .hero-pane::before {
            border-color: rgba(200,164,78,.10);
        }

        .card {
            background:
                linear-gradient(180deg, rgba(255,255,255,.99), rgba(252,251,248,.95));
            box-shadow:
                0 16px 34px rgba(0,0,0,.10),
                0 1px 0 rgba(255,255,255,.65) inset;
        }

        .surface-dark {
            background:
                radial-gradient(circle at 88% 10%, rgba(200,164,78,.18), transparent 42%),
                linear-gradient(160deg, #0b0b0d, #111114 55%, #0c0c0e);
            box-shadow:
                0 22px 46px rgba(0,0,0,.28),
                0 1px 0 rgba(255,255,255,.02) inset;
        }

        .product-card {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            transition:
                transform .28s cubic-bezier(.2,.8,.2,1),
                box-shadow .28s ease,
                border-color .28s ease,
                background-color .28s ease;
        }

        .product-card::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            border-radius: inherit;
            border: 1px solid rgba(200,164,78,.07);
            opacity: 0;
            transition: opacity .28s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow:
                0 22px 42px rgba(0,0,0,.14),
                0 1px 0 rgba(255,255,255,.65) inset;
            border-color: rgba(200,164,78,.14);
        }

        .product-card:hover::before {
            opacity: 1;
        }

        .product-media-frame {
            position: relative;
            padding: 8px;
            border-radius: 18px;
            background:
                linear-gradient(180deg, rgba(15,15,17,.98), rgba(24,24,27,.96));
            border: 1px solid rgba(200,164,78,.18);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.04),
                0 8px 18px rgba(0,0,0,.14);
            transition: border-color .28s ease, box-shadow .28s ease, transform .28s ease;
        }

        .product-media-frame::before {
            content: "";
            position: absolute;
            inset: 5px;
            border-radius: 14px;
            border: 1px solid rgba(200,164,78,.14);
            pointer-events: none;
        }

        .product-media-frame::after {
            content: "";
            position: absolute;
            inset: 1px;
            border-radius: 16px;
            background:
                radial-gradient(circle at 18% 14%, rgba(255,255,255,.06), transparent 42%),
                radial-gradient(circle at 82% 88%, rgba(200,164,78,.06), transparent 46%);
            pointer-events: none;
            opacity: .9;
        }

        .product-card:hover .product-media-frame {
            border-color: rgba(200,164,78,.28);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.05),
                0 12px 24px rgba(0,0,0,.18);
        }

        .product-card .product-thumb {
            min-height: 230px;
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,.12);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.25),
                inset 0 -24px 48px rgba(0,0,0,.04);
            transition: transform .3s cubic-bezier(.2,.8,.2,1), box-shadow .3s ease;
            overflow: hidden;
        }

        .product-card .product-thumb::before {
            inset: 10px;
            border-color: rgba(200,164,78,.18);
            border-style: solid;
            opacity: .75;
        }

        .product-card .product-thumb::after {
            width: 48px;
            height: 48px;
            top: 10px;
            right: 10px;
            border-radius: 12px;
            background:
                radial-gradient(circle at 30% 25%, rgba(255,255,255,.10), transparent 60%),
                linear-gradient(135deg, rgba(200,164,78,.18), rgba(200,164,78,.05));
            border: 1px solid rgba(200,164,78,.18);
            box-shadow: none;
        }

        .product-card:hover .product-thumb {
            transform: translateY(-2px) scale(1.01);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.28),
                inset 0 -28px 52px rgba(0,0,0,.05),
                0 10px 20px rgba(0,0,0,.08);
        }

        .product-brow {
            background: rgba(255,255,255,.88);
            border-color: rgba(200,164,78,.16);
            color: #3a311d;
            box-shadow: 0 4px 10px rgba(0,0,0,.04);
        }

        .product-card h3 {
            font-family: "Cormorant Garamond", "Fraunces", Georgia, serif;
            font-size: 1.18rem;
            line-height: 1.06;
            letter-spacing: -.01em;
        }

        .product-card .price {
            font-family: "Manrope", system-ui, sans-serif;
            font-weight: 800;
            color: #0f0f10;
        }

        .product-card .price small {
            color: #6f675c;
            font-weight: 600;
            letter-spacing: .03em;
        }

        .product-card-cta {
            color: #171719;
            transition: color .22s ease, transform .22s ease;
        }

        .product-card-cta .bullet {
            border-color: rgba(200,164,78,.22);
            background: linear-gradient(180deg, rgba(255,255,255,.98), rgba(248,244,235,.95));
            transition: transform .22s ease, border-color .22s ease, background .22s ease;
        }

        .product-card:hover .product-card-cta {
            color: #8c6a21;
            transform: translateX(2px);
        }

        .product-card:hover .product-card-cta .bullet {
            transform: translateX(2px);
            border-color: rgba(200,164,78,.34);
            background: linear-gradient(180deg, #fbf4de, #eddba7);
        }

        .category-tile,
        .spot-card,
        .footer-col,
        .glass-panel,
        .board-card,
        .metric-card,
        .hero-metric,
        .timeline-item {
            transition:
                transform .24s cubic-bezier(.2,.8,.2,1),
                box-shadow .24s ease,
                border-color .24s ease,
                background-color .24s ease;
        }

        .category-tile:hover,
        .spot-card:hover {
            transform: translateY(-4px);
            border-color: rgba(200,164,78,.18);
            box-shadow:
                0 18px 34px rgba(0,0,0,.12),
                0 1px 0 rgba(255,255,255,.55) inset;
        }

        .category-tile:hover .category-arrow {
            transform: translate(2px, -2px);
            border-color: rgba(200,164,78,.22);
            color: #8d6c22;
        }

        .category-arrow {
            transition: transform .22s ease, border-color .22s ease, color .22s ease;
        }

        .btn {
            transition:
                transform .18s cubic-bezier(.2,.8,.2,1),
                box-shadow .2s ease,
                border-color .2s ease,
                background-color .2s ease,
                color .2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .nav-link {
            transition:
                background-color .18s ease,
                border-color .18s ease,
                color .18s ease,
                transform .18s ease;
        }

        .nav-link:hover {
            transform: translateY(-1px);
        }

        .reveal-up {
            animation: revealUp .64s cubic-bezier(.18,.82,.22,1) both;
            transform-origin: 50% 100%;
            will-change: transform, opacity;
        }

        @keyframes revealUp {
            from {
                opacity: 0;
                transform: translateY(12px) scale(.992);
                filter: blur(2px);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
                filter: blur(0);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .product-card,
            .product-media-frame,
            .product-card .product-thumb,
            .btn,
            .nav-link,
            .category-tile,
            .spot-card,
            .reveal-up {
                transition: none !important;
                animation: none !important;
                transform: none !important;
                filter: none !important;
            }
        }

        @media (max-width: 980px) {
            body {
                background:
                    radial-gradient(circle at 30% 0%, rgba(200,164,78,.10), transparent 45%),
                    linear-gradient(180deg, #0a0a0c, #0d0d10);
            }

            main.container::before {
                background: radial-gradient(700px 280px at 50% 10%, rgba(200,164,78,.06), transparent 70%);
            }
        }

        /* Header fix: desktop/tablet wide breakpoints without stacked/overlapping menu */
        @media (min-width: 981px) {
            .header-panel .nav {
                background: transparent;
                border: 0;
                box-shadow: none;
                padding: 0;
                border-radius: 0;
                display: grid;
                grid-template-columns: minmax(0, 1fr) auto;
                align-items: center;
                gap: 8px;
            }

            .nav-main {
                min-width: 0;
                width: auto;
                flex-wrap: wrap;
            }

            .nav-actions {
                width: auto;
                min-width: 0;
                justify-content: flex-end;
                flex-wrap: wrap;
            }

            .nav-actions .inline-form {
                width: auto;
            }

            .nav-actions .btn,
            .nav-actions .nav-link,
            .nav-actions .user-chip {
                width: auto;
            }
        }

        @media (max-width: 1480px) and (min-width: 981px) {
            .header-panel .nav {
                grid-template-columns: minmax(0, 1fr) auto;
                align-items: center;
            }

            .nav-main {
                width: auto;
            }

            .nav-actions {
                width: auto;
                justify-content: flex-end;
            }

            .nav-actions .user-chip {
                display: none;
            }
        }

        @media (max-width: 1260px) and (min-width: 981px) {
            .header-panel .nav {
                grid-template-columns: 1fr;
                align-items: start;
                gap: 8px;
            }

            .nav-main,
            .nav-actions {
                width: 100%;
            }

            .nav-actions {
                justify-content: flex-start;
            }

            .header-checkout-btn {
                margin-left: auto;
            }
        }

        /* Header should not follow scroll */
        .site-header {
            position: relative;
            top: 0;
        }

        /* Light premium refinement (requested): clear base + gold accents */
        :root {
            --bg: #f5f1e7;
            --surface: #ffffff;
            --surface-2: #f4efe4;
            --ink: #161413;
            --muted: #6c655b;
            --line: rgba(22, 20, 19, .10);
            --brand: #c6a14a;
            --brand-2: #9c7728;
            --shadow: 0 20px 44px rgba(17, 15, 12, .10);
            --shadow-soft: 0 10px 24px rgba(17, 15, 12, .06);
        }

        body {
            background:
                radial-gradient(circle at 12% 2%, rgba(198, 161, 74, .10), transparent 42%),
                radial-gradient(circle at 88% 10%, rgba(17, 15, 12, .03), transparent 52%),
                radial-gradient(circle at 75% 92%, rgba(198, 161, 74, .05), transparent 44%),
                linear-gradient(180deg, #fbf9f4 0%, #f4f0e7 58%, #efeadf 100%);
            color: var(--ink);
        }

        body::before {
            opacity: .045;
            background-image:
                linear-gradient(rgba(22,20,19,.09) 1px, transparent 1px),
                linear-gradient(90deg, rgba(22,20,19,.09) 1px, transparent 1px);
            background-size: 28px 28px;
            mask-image: radial-gradient(circle at center, black 32%, transparent 95%);
        }

        main.container::before {
            background:
                radial-gradient(900px 420px at 18% 16%, rgba(198,161,74,.05), transparent 72%),
                radial-gradient(1000px 520px at 86% 16%, rgba(255,255,255,.06), transparent 78%);
        }

        .lead {
            color: color-mix(in srgb, var(--muted) 92%, black 8%);
        }

        main.container > .stack-xl > .section-head .section-kicker,
        main.container > .stack-xl > .section-head .copy p,
        main.container > .stack-xl > .section-head .copy .muted {
            color: rgba(92, 83, 72, .88);
        }

        main.container > .stack-xl > .section-head .copy h2,
        main.container > .stack-xl > .section-head .copy h3 {
            color: #1a1714;
        }

        main.container > .stack-xl > .section-head .copy h2.text-gradient,
        main.container > .stack-xl > .section-head .copy h3.text-gradient {
            background: linear-gradient(135deg, #181513 0%, #6f5820 44%, #c6a14a 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .site-header {
            background:
                linear-gradient(180deg, rgba(255,255,255,.90), rgba(255,255,255,.78)),
                linear-gradient(90deg, rgba(198,161,74,.07), rgba(198,161,74,0));
            border-color: rgba(198, 161, 74, .20);
            box-shadow: 0 14px 30px rgba(17,15,12,.08);
            backdrop-filter: blur(10px);
        }

        .site-header::before {
            background: linear-gradient(90deg, rgba(198,161,74,.35), rgba(235,214,157,.95), rgba(198,161,74,.35));
            opacity: .9;
        }

        .header-inner {
            position: relative;
            color: #191614;
        }

        .site-header .vertice-logo-text small {
            color: rgba(88, 79, 68, .78);
        }

        .header-meta-chip {
            color: #4e453b;
            background: rgba(255,255,255,.78);
            border-color: rgba(198, 161, 74, .16);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.92);
        }

        /* Make the "Pedido online, produção e retirada" line slimmer and visually top-aligned */
        .site-header::before {
            height: 1px;
            opacity: .75;
        }

        .header-meta-chip {
            position: absolute;
            top: -6px;
            left: 50%;
            transform: translateX(-50%);
            margin-left: 0;
            padding: 3px 9px;
            gap: 6px;
            min-height: 0;
            line-height: 1;
            font-size: .64rem;
            letter-spacing: .07em;
            border-radius: 999px;
            white-space: nowrap;
            z-index: 2;
            pointer-events: none;
            background: linear-gradient(180deg, rgba(255,255,255,.95), rgba(249,244,235,.90));
            border-color: rgba(198,161,74,.18);
            box-shadow:
                0 4px 10px rgba(17,15,12,.04),
                inset 0 1px 0 rgba(255,255,255,.92);
        }

        .header-meta-chip .dot {
            width: 5px;
            height: 5px;
            box-shadow: 0 0 0 2px rgba(198,161,74,.10);
        }

        @media (max-width: 1680px) {
            .header-meta-chip {
                top: -4px;
                font-size: .62rem;
                padding: 2px 8px;
            }
        }

        /* Refine top line + move "PRODUTOS" label upward on the search bar */
        .header-shell {
            gap: 4px;
        }

        .header-inner {
            padding-top: 6px;
            padding-bottom: 4px;
        }

        .header-panel {
            gap: 8px;
            padding-top: 3px;
            padding-bottom: 6px;
        }

        .header-meta-chip {
            top: -8px;
            padding: 1px 8px;
            min-height: 0;
            font-size: .58rem;
            line-height: 1;
            letter-spacing: .09em;
            border-radius: 0 0 999px 999px;
            border-top-color: transparent;
        }

        .header-meta-chip .dot {
            width: 4px;
            height: 4px;
            box-shadow: 0 0 0 2px rgba(198,161,74,.08);
        }

        .header-search {
            position: relative;
            min-height: 38px;
            padding: 3px 4px;
            border-radius: 12px;
            gap: 6px;
        }

        .header-search-kicker {
            position: absolute;
            top: -8px;
            left: 10px;
            margin: 0;
            padding: 2px 8px;
            border-radius: 999px;
            border: 1px solid rgba(198,161,74,.16);
            background: linear-gradient(180deg, rgba(255,255,255,.96), rgba(249,244,235,.92));
            box-shadow:
                0 4px 10px rgba(17,15,12,.03),
                inset 0 1px 0 rgba(255,255,255,.92);
            color: #574d40;
            letter-spacing: .12em;
            font-size: .56rem;
            line-height: 1;
        }

        .header-search-kicker::before {
            width: 4px;
            height: 4px;
        }

        .header-search input {
            padding-top: 5px;
            padding-bottom: 5px;
            font-size: .9rem;
        }

        .header-search .btn {
            min-height: 30px;
            padding: 6px 10px;
            border-radius: 10px;
            font-size: .74rem;
        }

        @media (max-width: 980px) {
            .header-search-kicker {
                position: static;
                border: 0;
                background: transparent;
                box-shadow: none;
                padding: 0 2px 0 6px;
                border-radius: 0;
                font-size: .62rem;
            }

            .header-search {
                min-height: 40px;
                padding: 4px;
            }

            .header-search input {
                padding-top: 6px;
                padding-bottom: 6px;
            }

            .header-meta-chip {
                top: -5px;
                font-size: .56rem;
            }
        }

        .header-panel {
            background: rgba(255,255,255,.62);
            border-color: rgba(22,20,19,.06);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.86),
                0 10px 22px rgba(17,15,12,.04);
            backdrop-filter: blur(6px);
        }

        .header-search {
            background: rgba(255,255,255,.84);
            border-color: rgba(22,20,19,.06);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.95);
        }

        .header-search-kicker {
            color: #51493f;
        }

        .header-search input {
            color: #191614;
        }

        .header-search input::placeholder {
            color: rgba(92,83,72,.72);
        }

        .nav-main {
            background: rgba(255,255,255,.86);
            border-color: rgba(22,20,19,.06);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.94);
        }

        .nav-main .nav-link,
        .nav-actions .nav-link {
            color: #3f3931;
            background: rgba(255,255,255,.82);
            border-color: rgba(22,20,19,.05);
        }

        .nav-link:hover {
            color: #191614;
            background: rgba(255,255,255,.98);
            border-color: rgba(198, 161, 74, .18);
            box-shadow: 0 8px 16px rgba(17,15,12,.04);
        }

        .nav-link.active,
        .nav-actions .nav-link.active,
        .nav-main .nav-link.active {
            color: #191614;
            background: rgba(255,255,255,.98);
            border-color: rgba(198, 161, 74, .20);
            box-shadow: 0 6px 14px rgba(17,15,12,.04);
        }

        .nav-link.active::after,
        .nav-main .nav-link.active::after {
            background: linear-gradient(90deg, rgba(198,161,74,.30), rgba(214,186,103,.95), rgba(198,161,74,.30));
        }

        .user-chip {
            background: rgba(255,255,255,.82);
            border-color: rgba(198, 161, 74, .14);
            color: #413a31;
        }

        .menu-toggle {
            color: #1a1714;
            background: rgba(255,255,255,.85);
            border-color: rgba(22,20,19,.08);
        }

        .btn-primary {
            color: #17130d;
            border: 1px solid rgba(198, 161, 74, .32);
            background:
                linear-gradient(180deg, rgba(255,255,255,.28), rgba(255,255,255,0)),
                linear-gradient(135deg, #ecd28c, #d3ad56 62%, #bd932f);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.42),
                0 10px 20px rgba(198,161,74,.16);
        }

        .btn-primary:hover {
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.5),
                0 14px 24px rgba(198,161,74,.20);
        }

        .hero,
        .card,
        .glass-panel,
        .board-card,
        .metric-card,
        .hero-metric,
        .timeline-item,
        .process-step,
        .table-wrap {
            border-color: rgba(22,20,19,.08);
        }

        .hero {
            background:
                radial-gradient(circle at 87% 10%, rgba(198,161,74,.12), transparent 42%),
                radial-gradient(circle at 5% 96%, rgba(22,20,19,.03), transparent 48%),
                linear-gradient(180deg, rgba(255,255,255,.96), rgba(250,247,241,.90));
            box-shadow:
                0 18px 38px rgba(17,15,12,.08),
                0 1px 0 rgba(255,255,255,.75) inset;
        }

        .card {
            background: linear-gradient(180deg, rgba(255,255,255,.98), rgba(253,251,246,.95));
            box-shadow:
                0 14px 28px rgba(17,15,12,.06),
                0 1px 0 rgba(255,255,255,.72) inset;
        }

        .footer-shell {
            color: #201c18;
            border-color: rgba(22,20,19,.08);
            background:
                radial-gradient(circle at 12% 0%, rgba(198,161,74,.10), transparent 38%),
                radial-gradient(circle at 95% 10%, rgba(22,20,19,.03), transparent 45%),
                linear-gradient(180deg, rgba(255,255,255,.97), rgba(247,243,236,.94));
            box-shadow:
                0 18px 38px rgba(17,15,12,.08),
                0 1px 0 rgba(255,255,255,.8) inset;
        }

        .footer-shell::after {
            background:
                linear-gradient(135deg, rgba(198,161,74,.08), rgba(198,161,74,0)),
                linear-gradient(315deg, rgba(22,20,19,.03), rgba(22,20,19,0));
        }

        .footer-shell .muted,
        .footer-col p,
        .footer-kpi span,
        .footer-bottom,
        .footer-signature .small {
            color: rgba(92,83,72,.84);
        }

        .footer-col,
        .footer-chip,
        .footer-kpi {
            background: rgba(255,255,255,.78);
            border-color: rgba(22,20,19,.06);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.86);
        }

        .footer-links a {
            color: #201c18;
        }

        .footer-links a:hover {
            color: #8d6c22;
        }

        .footer-status .mono {
            background: rgba(255,255,255,.86);
            border-color: rgba(198, 161, 74, .14);
            color: #4a433a;
        }

        .footer-signature strong {
            color: #6f5620;
        }

        .mobile-quickbar {
            border-color: rgba(22,20,19,.08);
            background: linear-gradient(180deg, rgba(255,255,255,.96), rgba(247,243,236,.92));
            box-shadow: 0 16px 28px rgba(17,15,12,.10);
        }

        .mobile-quickbar a {
            color: #4b433a;
            background: rgba(255,255,255,.9);
            border-color: rgba(22,20,19,.05);
        }

        .mobile-quickbar a.active {
            color: #1a1714;
            border-color: rgba(198, 161, 74, .16);
            background: rgba(255,255,255,.98);
        }

        .mobile-quickbar a.cta {
            color: #17130d;
            background: linear-gradient(135deg, #ecd28c, #d3ad56);
            box-shadow: 0 10px 18px rgba(198,161,74,.18);
        }

        .mobile-quickbar .count {
            background: rgba(198,161,74,.14);
            color: #7f6120;
        }

        .nav-main .nav-link,
        .nav-actions .nav-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
        }

        .nav-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
        }

        .nav-link-label {
            display: inline-block;
            line-height: 1;
            white-space: nowrap;
        }

        .nav-icon-svg {
            width: 14px;
            height: 14px;
            display: block;
            flex: 0 0 auto;
            opacity: .82;
            transition: opacity .18s ease, transform .18s ease, color .18s ease;
        }

        .nav-link:hover .nav-icon-svg,
        .nav-link.active .nav-icon-svg,
        .mobile-quickbar a:hover .nav-icon-svg,
        .mobile-quickbar a.active .nav-icon-svg {
            opacity: 1;
        }

        .nav-link.active .nav-icon-svg,
        .mobile-quickbar a.active .nav-icon-svg {
            color: #8d6c22;
        }

        .nav-link .badge {
            margin-left: 2px;
        }

        .mobile-quickbar a {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            text-align: center;
        }

        .mobile-quickbar a .nav-link-label {
            line-height: 1.05;
            font-size: .74rem;
        }

        .mobile-quickbar a .nav-icon-svg {
            width: 15px;
            height: 15px;
            opacity: .9;
        }

        .mobile-quickbar a.cta .nav-icon-svg,
        .header-checkout-btn .nav-icon-svg,
        .nav-action-btn .nav-icon-svg {
            opacity: 1;
        }

        @media (max-width: 1320px) {
            .nav-main .nav-link,
            .nav-actions .nav-link,
            .nav-action-btn {
                gap: 6px;
            }

            .nav-main .nav-link .nav-icon-svg,
            .nav-actions .nav-link .nav-icon-svg,
            .nav-action-btn .nav-icon-svg {
                width: 13px;
                height: 13px;
            }
        }

        @media (max-width: 980px) {
            body {
                background:
                    radial-gradient(circle at 30% 0%, rgba(198,161,74,.10), transparent 45%),
                    linear-gradient(180deg, #faf8f3, #f2ede2);
            }

            main.container::before {
                background: radial-gradient(700px 280px at 50% 10%, rgba(198,161,74,.05), transparent 70%);
            }

            .header-panel {
                background: transparent;
                border: 0;
                box-shadow: none;
                backdrop-filter: none;
            }

            .nav-main .nav-link,
            .nav-actions .nav-link {
                color: #1d1916;
                background: rgba(255,255,255,.88);
                border-color: rgba(22,20,19,.06);
            }

            .nav-actions .user-chip {
                color: #3f3931;
                background: rgba(255,255,255,.86);
            }
        }

        /* Final UI polish pass */
        :root {
            --ui-gutter: clamp(12px, 1.7vw, 26px);
            --ui-radius: 18px;
            --ui-radius-lg: 22px;
            --ui-shadow-soft: 0 12px 30px rgba(12, 10, 8, .07);
            --ui-shadow-lift: 0 20px 44px rgba(12, 10, 8, .10);
            --ui-border-soft: rgba(198, 161, 74, .14);
            --ui-surface-strong: rgba(255, 255, 255, .88);
        }

        .container {
            width: 100%;
            padding-inline: var(--ui-gutter);
        }

        body {
            line-height: 1.52;
            background:
                radial-gradient(1200px 520px at 14% -8%, rgba(198,161,74,.11), transparent 72%),
                radial-gradient(1200px 680px at 92% 10%, rgba(255,255,255,.04), transparent 78%),
                radial-gradient(900px 520px at 60% 105%, rgba(198,161,74,.05), transparent 72%),
                linear-gradient(180deg, #f8f4ec 0%, #f3ede1 44%, #efe5d6 100%);
        }

        main.container {
            padding-top: 18px;
            padding-bottom: 34px;
        }

        .site-header {
            top: 10px;
            z-index: 40;
            border-radius: 18px;
            border-color: rgba(255,255,255,.72);
            box-shadow:
                0 16px 38px rgba(12,10,8,.10),
                inset 0 1px 0 rgba(255,255,255,.56);
            backdrop-filter: blur(14px) saturate(130%);
            background:
                linear-gradient(180deg, rgba(255,255,255,.86), rgba(255,255,255,.72)),
                radial-gradient(circle at 12% 8%, rgba(198,161,74,.11), transparent 44%);
        }

        .header-shell {
            padding-block: 6px;
        }

        .header-inner {
            min-height: 66px;
            gap: 14px;
        }

        .header-panel {
            border-radius: 16px;
            border: 1px solid rgba(22,20,19,.06);
            background:
                linear-gradient(180deg, rgba(255,255,255,.84), rgba(255,255,255,.70)),
                radial-gradient(circle at 8% 0%, rgba(198,161,74,.08), transparent 42%);
            box-shadow:
                0 10px 24px rgba(12,10,8,.06),
                inset 0 1px 0 rgba(255,255,255,.65);
        }

        .header-meta-chip {
            border-color: rgba(198,161,74,.16);
            background:
                linear-gradient(180deg, rgba(255,255,255,.82), rgba(255,255,255,.62));
            box-shadow: inset 0 1px 0 rgba(255,255,255,.7);
        }

        .header-meta-chip .header-meta-icon {
            width: 14px;
            height: 14px;
            opacity: .86;
            color: #7f6120;
        }

        .menu-toggle {
            border-radius: 14px;
            border: 1px solid rgba(22,20,19,.08);
            background: linear-gradient(180deg, rgba(255,255,255,.92), rgba(255,255,255,.76));
            box-shadow: 0 8px 18px rgba(12,10,8,.06);
        }

        .menu-toggle:hover {
            border-color: rgba(198,161,74,.22);
            transform: translateY(-1px);
        }

        .header-search {
            border-radius: 16px;
            border-color: rgba(22,20,19,.08);
            background:
                linear-gradient(180deg, rgba(255,255,255,.88), rgba(255,255,255,.72));
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.65),
                0 10px 22px rgba(12,10,8,.05);
        }

        .header-search input {
            font-weight: 500;
            letter-spacing: .005em;
        }

        .header-search input::placeholder {
            color: rgba(68, 60, 52, .62);
        }

        .header-search-kicker {
            border-color: rgba(198,161,74,.18);
            background: rgba(198,161,74,.09);
            color: #6d531a;
        }

        .nav-main,
        .nav-actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            padding: 8px;
            border-radius: 16px;
            border: 1px solid rgba(22,20,19,.05);
            background:
                linear-gradient(180deg, rgba(255,255,255,.58), rgba(255,255,255,.42));
            box-shadow: inset 0 1px 0 rgba(255,255,255,.56);
        }

        .nav-group-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            min-height: 30px;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid rgba(198,161,74,.14);
            background:
                linear-gradient(180deg, rgba(255,255,255,.76), rgba(255,255,255,.60));
            color: rgba(69, 58, 48, .86);
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .11em;
            text-transform: uppercase;
            white-space: nowrap;
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.65),
                0 6px 12px rgba(12,10,8,.03);
        }

        .nav-group-pill .nav-icon-svg {
            width: 13px;
            height: 13px;
            opacity: .9;
            color: #8d6c22;
        }

        .nav-link,
        .user-chip {
            border-radius: 14px;
            border: 1px solid rgba(22,20,19,.05);
            background:
                linear-gradient(180deg, rgba(255,255,255,.78), rgba(255,255,255,.62));
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.62),
                0 6px 14px rgba(12,10,8,.04);
            transition:
                transform .18s ease,
                border-color .18s ease,
                box-shadow .18s ease,
                background-color .18s ease;
        }

        .nav-link:hover,
        .nav-link.active,
        .user-chip:hover {
            transform: translateY(-1px);
            border-color: rgba(198,161,74,.22);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.66),
                0 10px 18px rgba(12,10,8,.06);
        }

        .nav-link.active {
            background:
                linear-gradient(180deg, rgba(255,255,255,.90), rgba(255,255,255,.70)),
                radial-gradient(circle at 10% 0%, rgba(198,161,74,.08), transparent 36%);
        }

        .btn {
            min-height: 42px;
            border-radius: 14px;
            font-weight: 700;
            letter-spacing: .01em;
            box-shadow: 0 8px 18px rgba(12,10,8,.06);
            transition:
                transform .18s cubic-bezier(.2,.8,.2,1),
                box-shadow .18s ease,
                border-color .18s ease,
                background .18s ease,
                color .18s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 20px rgba(12,10,8,.08);
        }

        .btn-primary {
            border: 1px solid rgba(198,161,74,.24);
            background:
                linear-gradient(180deg, rgba(255,255,255,.20), rgba(255,255,255,0)),
                linear-gradient(135deg, #b88d30 0%, #d4af5f 54%, #f2d792 100%);
            color: #1c1409;
            box-shadow:
                0 12px 24px rgba(198,161,74,.20),
                inset 0 1px 0 rgba(255,255,255,.42);
        }

        .btn-primary:hover {
            box-shadow:
                0 16px 28px rgba(198,161,74,.24),
                inset 0 1px 0 rgba(255,255,255,.46);
        }

        .btn-secondary {
            border: 1px solid rgba(22,20,19,.08);
            background:
                linear-gradient(180deg, rgba(255,255,255,.92), rgba(255,255,255,.74));
            color: #1f1a16;
        }

        .btn-secondary:hover {
            border-color: rgba(198,161,74,.18);
            background:
                linear-gradient(180deg, rgba(255,255,255,.98), rgba(255,255,255,.80));
        }

        .btn-link {
            box-shadow: none;
        }

        .btn-link:hover {
            box-shadow: none;
            transform: translateY(-1px);
        }

        .card,
        .table-wrap,
        .metric-card,
        .hero-metric {
            border-radius: var(--ui-radius-lg);
            border: 1px solid rgba(198,161,74,.12);
            background:
                linear-gradient(180deg, rgba(255,255,255,.88), rgba(255,255,255,.74)),
                radial-gradient(circle at 88% 10%, rgba(198,161,74,.08), transparent 42%);
            box-shadow:
                var(--ui-shadow-soft),
                inset 0 1px 0 rgba(255,255,255,.66);
        }

        .card {
            overflow: clip;
            transition:
                transform .22s cubic-bezier(.2,.8,.2,1),
                box-shadow .22s ease,
                border-color .22s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            border-color: rgba(198,161,74,.18);
            box-shadow:
                var(--ui-shadow-lift),
                inset 0 1px 0 rgba(255,255,255,.72);
        }

        .card-pad {
            padding: 18px;
        }

        .stack-xl {
            gap: 28px;
        }

        .section-head {
            gap: 18px;
            margin-bottom: 2px;
            align-items: end;
        }

        .section-head .copy {
            gap: 10px;
        }

        .section-head .copy h2,
        .section-head .copy h3 {
            line-height: .96;
            letter-spacing: -.02em;
            text-wrap: balance;
        }

        .section-kicker {
            letter-spacing: .14em;
        }

        .lead,
        .muted {
            color: rgba(73, 64, 55, .82);
        }

        .badge,
        .pill,
        .chip {
            border-radius: 999px;
            border: 1px solid rgba(198,161,74,.14);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.58);
        }

        .ui-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            width: fit-content;
            max-width: 100%;
            border-radius: 999px;
            border: 1px solid rgba(22,20,19,.08);
            background:
                linear-gradient(180deg, rgba(255,255,255,.92), rgba(255,255,255,.78));
            color: #2a231d;
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.66),
                0 8px 14px rgba(12,10,8,.05);
            vertical-align: middle;
        }

        .ui-status-badge.size-sm {
            min-height: 28px;
            padding: 5px 10px;
            font-size: .76rem;
            font-weight: 700;
            line-height: 1;
        }

        .ui-status-badge.size-md {
            min-height: 32px;
            padding: 6px 12px;
            font-size: .82rem;
            font-weight: 700;
            line-height: 1;
        }

        .ui-status-badge .ui-status-badge-icon {
            width: 14px;
            height: 14px;
            flex: 0 0 auto;
            opacity: .95;
        }

        .ui-status-badge .ui-status-badge-label {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .ui-status-badge.tone-warning {
            border-color: rgba(180,106,0,.20);
            background: linear-gradient(180deg, rgba(255,246,226,.98), rgba(255,243,214,.88));
            color: #8d5a00;
        }

        .ui-status-badge.tone-success {
            border-color: rgba(15,138,95,.18);
            background: linear-gradient(180deg, rgba(237,255,248,.98), rgba(225,250,240,.90));
            color: #0f7b56;
        }

        .ui-status-badge.tone-info {
            border-color: rgba(15,93,245,.16);
            background: linear-gradient(180deg, rgba(240,246,255,.98), rgba(231,241,255,.90));
            color: #175ad1;
        }

        .ui-status-badge.tone-accent {
            border-color: rgba(198,161,74,.18);
            background: linear-gradient(180deg, rgba(255,252,243,.98), rgba(253,246,228,.90));
            color: #8d6c22;
        }

        .ui-status-badge.tone-danger {
            border-color: rgba(195,58,29,.18);
            background: linear-gradient(180deg, rgba(255,243,240,.98), rgba(255,233,228,.90));
            color: #b1361c;
        }

        .ui-status-badge.tone-neutral {
            border-color: rgba(22,20,19,.08);
            background: linear-gradient(180deg, rgba(248,247,245,.96), rgba(242,240,236,.88));
            color: #4a4036;
        }

        .grid {
            gap: 18px;
        }

        .input,
        .select,
        .textarea {
            border-radius: 14px;
            border: 1px solid rgba(22,20,19,.10);
            background:
                linear-gradient(180deg, rgba(255,255,255,.95), rgba(255,255,255,.80));
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.72),
                0 8px 16px rgba(12,10,8,.03);
        }

        .input:hover,
        .select:hover,
        .textarea:hover {
            border-color: rgba(198,161,74,.18);
        }

        .input:focus,
        .select:focus,
        .textarea:focus {
            border-color: rgba(198,161,74,.34);
            box-shadow:
                0 0 0 4px rgba(198,161,74,.10),
                inset 0 1px 0 rgba(255,255,255,.8);
        }

        .alert {
            border-radius: 16px;
            border: 1px solid rgba(22,20,19,.06);
            box-shadow:
                0 10px 20px rgba(12,10,8,.04),
                inset 0 1px 0 rgba(255,255,255,.55);
        }

        .table-wrap {
            overflow: hidden;
        }

        table {
            border-collapse: collapse;
        }

        th,
        td {
            border-color: rgba(22,20,19,.06);
        }

        tbody tr {
            transition: background-color .16s ease;
        }

        tbody tr:hover {
            background: rgba(198,161,74,.04);
        }

        .site-footer {
            margin-top: 18px;
            padding-bottom: 26px;
        }

        .footer-shell {
            border-radius: 24px;
            border: 1px solid rgba(198,161,74,.14);
            background:
                linear-gradient(180deg, rgba(255,255,255,.88), rgba(255,255,255,.72)),
                radial-gradient(circle at 10% 0%, rgba(198,161,74,.10), transparent 40%),
                radial-gradient(circle at 96% 10%, rgba(255,255,255,.12), transparent 45%);
            box-shadow:
                0 22px 42px rgba(12,10,8,.10),
                inset 0 1px 0 rgba(255,255,255,.62);
        }

        .footer-top,
        .footer-grid,
        .footer-bottom {
            gap: 16px;
        }

        .footer-col h3 {
            margin-bottom: 8px;
            letter-spacing: -.01em;
        }

        .footer-col p {
            color: rgba(73,64,55,.78);
        }

        .footer-links a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 3px 0;
            color: #2c241d;
            transition: color .16s ease, transform .16s ease;
        }

        .footer-links a::before {
            content: "";
            width: 5px;
            height: 5px;
            border-radius: 999px;
            background: rgba(198,161,74,.55);
            box-shadow: 0 0 0 3px rgba(198,161,74,.08);
        }

        .footer-links a:hover {
            color: #8d6c22;
            transform: translateX(2px);
        }

        .footer-chip,
        .footer-kpi {
            border-radius: 14px;
            border: 1px solid rgba(198,161,74,.12);
            background:
                linear-gradient(180deg, rgba(255,255,255,.82), rgba(255,255,255,.66));
            box-shadow: inset 0 1px 0 rgba(255,255,255,.62);
        }

        .footer-status-tags .mono {
            border-radius: 999px;
            border: 1px solid rgba(198,161,74,.14);
            background: rgba(255,255,255,.62);
            padding: 6px 10px;
        }

        .mobile-quickbar {
            left: max(10px, env(safe-area-inset-left));
            right: max(10px, env(safe-area-inset-right));
            bottom: calc(10px + env(safe-area-inset-bottom));
            border-radius: 18px;
            border: 1px solid rgba(198,161,74,.16);
            background:
                linear-gradient(180deg, rgba(255,255,255,.92), rgba(255,255,255,.82));
            box-shadow:
                0 16px 28px rgba(12,10,8,.14),
                inset 0 1px 0 rgba(255,255,255,.65);
            backdrop-filter: blur(14px) saturate(130%);
        }

        .mobile-quickbar a {
            border-radius: 12px;
            min-height: 52px;
            transition: transform .16s ease, background-color .16s ease;
        }

        .mobile-quickbar a:hover {
            transform: translateY(-1px);
            background: rgba(198,161,74,.06);
        }

        .mobile-quickbar a.active {
            background:
                linear-gradient(180deg, rgba(198,161,74,.13), rgba(198,161,74,.06));
            border: 1px solid rgba(198,161,74,.18);
        }

        .mobile-quickbar a.cta {
            background:
                linear-gradient(180deg, rgba(255,255,255,.98), rgba(255,255,255,.90));
            border: 1px solid rgba(198,161,74,.18);
        }

        .mobile-quickbar .count {
            box-shadow:
                0 6px 14px rgba(198,161,74,.18),
                inset 0 1px 0 rgba(255,255,255,.4);
        }

        :where(a, button, input, select, textarea):focus-visible {
            outline: 3px solid rgba(198,161,74,.26);
            outline-offset: 2px;
        }

        @media (max-width: 980px) {
            .container {
                padding-inline: clamp(10px, 3vw, 16px);
            }

            .site-header {
                top: 8px;
                border-radius: 16px;
            }

            .header-shell {
                padding-block: 4px;
            }

            .header-panel {
                border-radius: 14px;
                box-shadow:
                    0 8px 18px rgba(12,10,8,.06),
                    inset 0 1px 0 rgba(255,255,255,.6);
            }

            .nav-main,
            .nav-actions {
                padding: 6px;
                border-radius: 14px;
            }

            .nav-group-pill {
                display: none;
            }

            .card:hover {
                transform: none;
                box-shadow:
                    var(--ui-shadow-soft),
                    inset 0 1px 0 rgba(255,255,255,.66);
            }

            .footer-shell {
                border-radius: 20px;
            }

            main.container {
                padding-bottom: 92px;
            }
        }

        /* Layout refinement pass 2 */
        .header-panel .nav {
            gap: 10px;
        }

        .nav-main,
        .nav-actions {
            position: relative;
            gap: 7px;
            padding: 7px;
            border-color: rgba(198,161,74,.10);
            background:
                radial-gradient(circle at 8% 0%, rgba(198,161,74,.06), transparent 36%),
                linear-gradient(180deg, rgba(255,255,255,.62), rgba(255,255,255,.48));
        }

        .nav-link,
        .user-chip,
        .nav-action-btn.btn {
            position: relative;
            overflow: hidden;
        }

        .nav-link::after,
        .user-chip::after,
        .nav-action-btn.btn::after {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                linear-gradient(120deg, rgba(255,255,255,.26), transparent 28%, transparent 74%, rgba(198,161,74,.06));
            opacity: .72;
        }

        .nav-link:hover::after,
        .nav-link.active::after,
        .nav-action-btn.btn:hover::after {
            opacity: .95;
        }

        .nav-link .nav-icon-svg,
        .nav-action-btn .nav-icon-svg {
            width: 15px;
            height: 15px;
            border-radius: 7px;
            background: rgba(198,161,74,.08);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.58);
            padding: 2px;
        }

        .nav-link.active .nav-icon-svg {
            background: rgba(198,161,74,.14);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.62),
                0 4px 10px rgba(198,161,74,.10);
        }

        .nav-link .badge {
            min-width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 6px;
            margin-left: 4px;
            border-radius: 999px;
            border: 1px solid rgba(198,161,74,.18);
            background:
                linear-gradient(180deg, rgba(255,252,243,.96), rgba(253,245,226,.90));
            color: #7a5b1a;
            font-size: .68rem;
            font-weight: 800;
            line-height: 1;
            box-shadow:
                0 6px 10px rgba(12,10,8,.05),
                inset 0 1px 0 rgba(255,255,255,.72);
        }

        .user-chip {
            border: 1px solid rgba(198,161,74,.10);
            background:
                radial-gradient(circle at 12% 14%, rgba(198,161,74,.07), transparent 42%),
                linear-gradient(180deg, rgba(255,255,255,.80), rgba(255,255,255,.64));
            color: #2b241d;
            font-weight: 700;
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.66),
                0 8px 14px rgba(12,10,8,.04);
        }

        .header-search .btn {
            min-height: 36px;
            padding-inline: 12px;
            white-space: nowrap;
        }

        .header-search input {
            min-width: 0;
        }

        .badge {
            font-weight: 800;
            letter-spacing: .02em;
        }

        .badge-brand {
            border-color: rgba(198,161,74,.18);
            background:
                linear-gradient(180deg, rgba(255,252,243,.98), rgba(251,241,214,.90));
            color: #7a5b1a;
            box-shadow:
                0 6px 12px rgba(198,161,74,.12),
                inset 0 1px 0 rgba(255,255,255,.74);
        }

        .link-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            flex-wrap: wrap;
        }

        .pill-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .pill-list .pill,
        .pill-list .badge {
            box-shadow:
                0 6px 12px rgba(12,10,8,.04),
                inset 0 1px 0 rgba(255,255,255,.62);
        }

        .section-head .copy p {
            text-wrap: pretty;
        }

        /* Header search: keep inline with logo, reserve panel search for mobile only */
        .site-header {
            width: auto;
            margin-inline: var(--ui-gutter);
        }

        .header-shell {
            padding-inline: 0;
        }

        .header-inner {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-inline: 0;
        }

        .header-panel {
            padding-inline: 0;
        }

        .header-search-inline {
            flex: 1 1 460px;
            min-width: 220px;
            margin-left: 4px;
        }

        .header-search-panel {
            display: none;
        }

        .header-quick-actions {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-left: auto;
        }

        .header-quick-actions .header-quick-link:last-child {
            margin-right: 12px;
        }

        .header-quick-link {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            min-height: 38px;
            padding: 7px 11px;
            border-radius: 12px;
            border: 1px solid rgba(198,161,74,.18);
            background:
                radial-gradient(circle at 18% 0%, rgba(198,161,74,.18), transparent 52%),
                linear-gradient(180deg, rgba(255,255,255,.94), rgba(255,250,241,.88));
            color: #2d251d;
            font-size: .8rem;
            font-weight: 800;
            letter-spacing: .01em;
            box-shadow:
                0 8px 16px rgba(12,10,8,.06),
                inset 0 1px 0 rgba(255,255,255,.82);
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
            white-space: nowrap;
        }

        .header-quick-link .nav-icon-svg {
            width: 22px;
            height: 22px;
            border-radius: 11px;
            background: rgba(198,161,74,.14);
            padding: 3px;
            color: #7c5f1f;
            opacity: 1;
        }

        .header-quick-link .badge {
            min-width: 18px;
            height: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 5px;
            border-radius: 999px;
            border: 1px solid rgba(198,161,74,.22);
            background: rgba(255,255,255,.9);
            color: #7a5b1a;
            font-size: .66rem;
            line-height: 1;
        }

        .header-quick-link:hover,
        .header-quick-link.is-active {
            transform: translateY(-1px) scale(1.01);
            border-color: rgba(198,161,74,.3);
            box-shadow:
                0 12px 20px rgba(12,10,8,.08),
                inset 0 1px 0 rgba(255,255,255,.9);
        }

        .header-panel .nav {
            position: relative;
            gap: 12px;
        }

        .nav-main,
        .nav-actions {
            position: relative;
            border-color: rgba(198,161,74,.16);
            background:
                radial-gradient(circle at 8% -10%, rgba(198,161,74,.14), transparent 44%),
                linear-gradient(180deg, rgba(255,255,255,.82), rgba(255,255,255,.60));
        }

        .nav-main::before,
        .nav-actions::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            border-radius: inherit;
            background: linear-gradient(120deg, rgba(255,255,255,.24), transparent 36%, transparent 70%, rgba(198,161,74,.08));
            opacity: .8;
        }

        .header-panel .nav-link,
        .header-panel .nav-action-btn.btn {
            border-color: rgba(198,161,74,.12);
            background:
                linear-gradient(180deg, rgba(255,255,255,.95), rgba(255,251,245,.86));
        }

        .header-panel .nav-main,
        .header-panel .nav-actions {
            justify-content: center;
        }

        .header-panel .nav .nav-link {
            justify-content: center;
            text-align: center;
        }

        .header-panel .nav .nav-link-label {
            text-align: center;
        }

        .header-panel .nav-main .nav-group-pill .nav-icon-svg {
            width: 18px;
            height: 18px;
        }

        .header-panel .nav-link .nav-icon-svg,
        .header-panel .nav-action-btn .nav-icon-svg {
            width: 22px;
            height: 22px;
            border-radius: 11px;
            padding: 3px;
        }

        .header-panel .nav-main .nav-link:hover {
            transform: translateY(-2px) rotate(-.35deg);
        }

        .header-panel .nav-actions .nav-link:hover,
        .header-panel .nav-actions .nav-action-btn.btn:hover {
            transform: translateY(-2px) rotate(.35deg);
        }

        @media (min-width: 981px) {
            .header-panel .nav-link-cart,
            .header-panel .nav-link-access {
                display: none;
            }
        }

        @media (max-width: 980px) and (min-width: 681px) {
            .header-inner {
                justify-content: flex-start;
            }

            .header-search-inline {
                flex: 1 1 auto;
                min-width: 0;
            }

            .header-quick-actions {
                display: none;
            }
        }

        @media (max-width: 680px) {
            .header-search-inline {
                display: none;
            }

            .header-search-panel {
                display: flex;
                width: 100%;
            }

            .header-quick-actions {
                display: none;
            }
        }

        @media (max-width: 980px) {
            .site-header {
                margin-inline: clamp(10px, 3vw, 16px);
            }

            .nav-main,
            .nav-actions {
                gap: 6px;
                padding: 6px;
            }

            .nav-link .nav-icon-svg,
            .nav-action-btn .nav-icon-svg {
                width: 20px;
                height: 20px;
                padding: 2px;
            }

            .nav-link .badge {
                min-width: 18px;
                height: 18px;
                font-size: .64rem;
            }
        }
    </style>
    @stack('head')
</head>
<body>
    @php($cartSummary = app(\App\Services\CartService::class)->summary())
    @php($currentUser = auth()->user())
    <a href="#conteudo" class="skip-link">Pular para o conteúdo</a>
	    <header class="site-header" data-site-header data-nav-open="false">
	        <div class="container header-shell">
	            <div class="header-inner">
	                <a href="{{ route('home') }}" class="brand brand-vertice" aria-label="Ir para a home">
	                    @include('partials.vertice-logo', ['variant' => 'header'])
	                </a>

                <form class="header-search header-search-inline" method="GET" action="{{ route('catalog.index') }}" role="search" aria-label="Buscar no catálogo">
                    <span class="header-search-kicker">Produtos</span>
                    <input
	                        type="search"
	                        name="q"
	                        value="{{ (string) request('q', '') }}"
	                        placeholder="Buscar: cartão, flyer, banner, etiqueta..."
	                        aria-label="Buscar produtos"
	                    >
	                    @if(request()->filled('categoria'))
	                        <input type="hidden" name="categoria" value="{{ request('categoria') }}">
                    @endif
                    <button class="btn btn-secondary btn-sm" type="submit">Ir</button>
                </form>

                @if(!($currentUser?->is_admin))
                    <div class="header-quick-actions" aria-label="Acesso rápido">
                        @auth
                            <a class="header-quick-link {{ request()->routeIs('account.*') ? 'is-active' : '' }}" href="{{ route('account.dashboard') }}">
                                @include('partials.nav-icon', ['name' => 'account', 'class' => 'nav-icon'])
                                <span>Minha conta</span>
                            </a>
                        @else
                            <a class="header-quick-link {{ request()->routeIs('login*') ? 'is-active' : '' }}" href="{{ route('login') }}">
                                @include('partials.nav-icon', ['name' => 'login', 'class' => 'nav-icon'])
                                <span>Entrar</span>
                            </a>
                        @endauth
                        <a class="header-quick-link {{ request()->routeIs('cart.*') ? 'is-active' : '' }}" href="{{ route('cart.index') }}">
                            @include('partials.nav-icon', ['name' => 'cart', 'class' => 'nav-icon'])
                            <span>Carrinho</span>
                            @if(($cartSummary['count'] ?? 0) > 0)
                                <span class="badge">{{ $cartSummary['count'] }}</span>
                            @endif
                        </a>
                    </div>
                @endif

                <span class="header-meta-chip" aria-hidden="true">
                    @include('partials.nav-icon', ['name' => 'status-production', 'class' => 'header-meta-icon'])
                    <span class="dot"></span>
                    Pedido online, produção e retirada
                </span>

                <button
                    type="button"
                    class="menu-toggle"
                    data-menu-toggle
                    aria-expanded="false"
                    aria-controls="site-header-panel"
                    aria-label="Abrir menu"
                >
                    <span class="bars" aria-hidden="true"><span></span></span>
                    Menu
                </button>
	            </div>

	            <div class="header-panel" id="site-header-panel">
	                <form class="header-search header-search-panel" method="GET" action="{{ route('catalog.index') }}" role="search" aria-label="Buscar no catálogo">
	                    <span class="header-search-kicker">Produtos</span>
	                    <input
	                        type="search"
	                        name="q"
                        value="{{ (string) request('q', '') }}"
                        placeholder="Buscar: cartão, flyer, banner, etiqueta..."
                        aria-label="Buscar produtos"
                    >
                    @if(request()->filled('categoria'))
                        <input type="hidden" name="categoria" value="{{ request('categoria') }}">
                    @endif
                    <button class="btn btn-secondary btn-sm" type="submit">Ir</button>
                </form>

                <nav class="nav" aria-label="Navegação principal">
                    <div class="nav-main">
                        @auth
                            @if($currentUser?->is_admin)
                                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                    @include('partials.nav-icon', ['name' => 'panel', 'class' => 'nav-icon'])
                                    <span class="nav-link-label">Painel</span>
                                </a>
                                <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                                    @include('partials.nav-icon', ['name' => 'orders', 'class' => 'nav-icon'])
                                    <span class="nav-link-label">Pedidos</span>
                                </a>
                                <a class="nav-link {{ request()->routeIs('admin.catalog.*') ? 'active' : '' }}" href="{{ route('admin.catalog.index') }}">
                                    @include('partials.nav-icon', ['name' => 'cadastros', 'class' => 'nav-icon'])
                                    <span class="nav-link-label">Cadastros</span>
                                </a>
                                <a class="nav-link {{ request()->routeIs('catalog.*') ? 'active' : '' }}" href="{{ route('catalog.index') }}">
                                    @include('partials.nav-icon', ['name' => 'store', 'class' => 'nav-icon'])
                                    <span class="nav-link-label">Loja</span>
                                </a>
                            @else
                                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                    @include('partials.nav-icon', ['name' => 'home', 'class' => 'nav-icon'])
                                    <span class="nav-link-label">Início</span>
                                </a>
                                <a class="nav-link {{ request()->routeIs('catalog.*') ? 'active' : '' }}" href="{{ route('catalog.index') }}">
                                    @include('partials.nav-icon', ['name' => 'catalog', 'class' => 'nav-icon'])
                                    <span class="nav-link-label">Catálogo</span>
                                </a>
                                <a class="nav-link {{ request()->routeIs('pages.about') ? 'active' : '' }}" href="{{ route('pages.about') }}">
                                    @include('partials.nav-icon', ['name' => 'about', 'class' => 'nav-icon'])
                                    <span class="nav-link-label">Quem somos</span>
                                </a>
                                <a class="nav-link {{ request()->routeIs('pages.services') ? 'active' : '' }}" href="{{ route('pages.services') }}">
                                    @include('partials.nav-icon', ['name' => 'services', 'class' => 'nav-icon'])
                                    <span class="nav-link-label">Serviços</span>
                                </a>
                                <a class="nav-link {{ request()->routeIs('pages.portfolio') ? 'active' : '' }}" href="{{ route('pages.portfolio') }}">
                                    @include('partials.nav-icon', ['name' => 'portfolio', 'class' => 'nav-icon'])
                                    <span class="nav-link-label">Portfólio</span>
                                </a>
                                <a class="nav-link {{ request()->routeIs('pages.blog') ? 'active' : '' }}" href="{{ route('pages.blog') }}">
                                    @include('partials.nav-icon', ['name' => 'blog', 'class' => 'nav-icon'])
                                    <span class="nav-link-label">Blog</span>
                                </a>
                                <a class="nav-link {{ request()->routeIs('pages.quote') ? 'active' : '' }}" href="{{ route('pages.quote') }}">
                                    @include('partials.nav-icon', ['name' => 'quote', 'class' => 'nav-icon'])
                                    <span class="nav-link-label">Orçamento</span>
                                </a>
                                <a class="nav-link {{ request()->routeIs('pages.contact') ? 'active' : '' }}" href="{{ route('pages.contact') }}">
                                    @include('partials.nav-icon', ['name' => 'contact', 'class' => 'nav-icon'])
                                    <span class="nav-link-label">Contato</span>
                                </a>
                                <a class="nav-link nav-link-cart {{ request()->routeIs('cart.*') ? 'active' : '' }}" href="{{ route('cart.index') }}">
                                    @include('partials.nav-icon', ['name' => 'cart', 'class' => 'nav-icon'])
                                    <span class="nav-link-label">Carrinho</span>
                                    @if(($cartSummary['count'] ?? 0) > 0)
                                        <span class="badge">{{ $cartSummary['count'] }}</span>
                                    @endif
                                </a>
                            @endif
                        @else
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                @include('partials.nav-icon', ['name' => 'home', 'class' => 'nav-icon'])
                                <span class="nav-link-label">Início</span>
                            </a>
                            <a class="nav-link {{ request()->routeIs('catalog.*') ? 'active' : '' }}" href="{{ route('catalog.index') }}">
                                @include('partials.nav-icon', ['name' => 'catalog', 'class' => 'nav-icon'])
                                <span class="nav-link-label">Catálogo</span>
                            </a>
                            <a class="nav-link {{ request()->routeIs('pages.about') ? 'active' : '' }}" href="{{ route('pages.about') }}">
                                @include('partials.nav-icon', ['name' => 'about', 'class' => 'nav-icon'])
                                <span class="nav-link-label">Quem somos</span>
                            </a>
                            <a class="nav-link {{ request()->routeIs('pages.services') ? 'active' : '' }}" href="{{ route('pages.services') }}">
                                @include('partials.nav-icon', ['name' => 'services', 'class' => 'nav-icon'])
                                <span class="nav-link-label">Serviços</span>
                            </a>
                            <a class="nav-link {{ request()->routeIs('pages.portfolio') ? 'active' : '' }}" href="{{ route('pages.portfolio') }}">
                                @include('partials.nav-icon', ['name' => 'portfolio', 'class' => 'nav-icon'])
                                <span class="nav-link-label">Portfólio</span>
                            </a>
                            <a class="nav-link {{ request()->routeIs('pages.blog') ? 'active' : '' }}" href="{{ route('pages.blog') }}">
                                @include('partials.nav-icon', ['name' => 'blog', 'class' => 'nav-icon'])
                                <span class="nav-link-label">Blog</span>
                            </a>
                            <a class="nav-link {{ request()->routeIs('pages.quote') ? 'active' : '' }}" href="{{ route('pages.quote') }}">
                                @include('partials.nav-icon', ['name' => 'quote', 'class' => 'nav-icon'])
                                <span class="nav-link-label">Orçamento</span>
                            </a>
                            <a class="nav-link {{ request()->routeIs('pages.contact') ? 'active' : '' }}" href="{{ route('pages.contact') }}">
                                @include('partials.nav-icon', ['name' => 'contact', 'class' => 'nav-icon'])
                                <span class="nav-link-label">Contato</span>
                            </a>
                            <a class="nav-link nav-link-cart {{ request()->routeIs('cart.*') ? 'active' : '' }}" href="{{ route('cart.index') }}">
                                @include('partials.nav-icon', ['name' => 'cart', 'class' => 'nav-icon'])
                                <span class="nav-link-label">Carrinho</span>
                                @if(($cartSummary['count'] ?? 0) > 0)
                                    <span class="badge">{{ $cartSummary['count'] }}</span>
                                @endif
                            </a>
                        @endauth
                    </div>

                    <div class="nav-actions">
                        @auth
                            @if($currentUser?->is_admin)
                                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                    @include('partials.nav-icon', ['name' => 'home', 'class' => 'nav-icon'])
                                    <span class="nav-link-label">Home pública</span>
                                </a>
                            @else
                                <a class="nav-link nav-link-access {{ request()->routeIs('account.*') ? 'active' : '' }}" href="{{ route('account.dashboard') }}">
                                    @include('partials.nav-icon', ['name' => 'account', 'class' => 'nav-icon'])
                                    <span class="nav-link-label">Minha Conta</span>
                                </a>
                            @endif
                            <span class="user-chip" title="{{ $currentUser->email }}">
                                {{ \Illuminate\Support\Str::limit($currentUser->name, 18) }}
                            </span>
                            <form class="inline-form" method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-secondary btn-sm nav-action-btn">
                                    @include('partials.nav-icon', ['name' => 'logout', 'class' => 'nav-icon'])
                                    <span>Sair</span>
                                </button>
                            </form>
                        @else
                            <a class="nav-link nav-link-access {{ request()->routeIs('login*') ? 'active' : '' }}" href="{{ route('login') }}">
                                @include('partials.nav-icon', ['name' => 'login', 'class' => 'nav-icon'])
                                <span class="nav-link-label">Entrar</span>
                            </a>
                        @endauth
                    </div>
                </nav>
            </div>
        </div>
    </header>

    <main id="conteudo" class="container">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <strong>Revise os campos informados.</strong>
                <ul class="clean-list" style="margin-top:8px;">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="container site-footer">
        <section class="card footer-shell">
            <div class="footer-top">
                <div class="footer-brand">
                    <div class="footer-brand-head">
                        @include('partials.vertice-logo', ['variant' => 'footer'])
                    </div>
                    <div class="footer-badge-row" aria-label="Diferenciais da gráfica">
                        <span class="footer-chip"><span class="mini-dot" style="background: var(--brand); box-shadow: 0 0 0 3px rgba(195,58,29,.08);"></span> Produção sob demanda</span>
                        <span class="footer-chip"><span class="mini-dot"></span> Conferência de arquivo</span>
                        <span class="footer-chip"><span class="mini-dot" style="background:#0f8a5f; box-shadow: 0 0 0 3px rgba(15,138,95,.08);"></span> Entrega e retirada</span>
                        <span class="footer-chip"><span class="mini-dot" style="background:#111827; box-shadow: 0 0 0 3px rgba(17,24,39,.06);"></span> Atendimento comercial</span>
                    </div>
                    <p class="small muted">
                        Escolha o produto, configure tiragem e acabamento, finalize o pedido e acompanhe o andamento da produção.
                    </p>
                </div>

                <div class="footer-kpis">
                    <div class="footer-kpi">
                        <strong>Rápido</strong>
                        <span>Pedido online</span>
                    </div>
                    <div class="footer-kpi">
                        <strong>Suporte</strong>
                        <span>Pré-impressão</span>
                    </div>
                    <div class="footer-kpi">
                        <strong>{{ (int) ($cartSummary['count'] ?? 0) }}</strong>
                        <span>Item(ns) no carrinho</span>
                    </div>
                </div>
            </div>

            <div class="footer-grid">
                <section class="footer-col">
                    <h3>Produtos</h3>
                    <p>Linhas mais procuradas para compra rápida por categoria.</p>
                    <ul class="footer-links">
                        <li><a href="{{ route('catalog.index', ['categoria' => 'cartoes-e-papelaria']) }}">Cartões e papelaria</a></li>
                        <li><a href="{{ route('catalog.index', ['categoria' => 'promocionais']) }}">Flyers e promocionais</a></li>
                        <li><a href="{{ route('catalog.index', ['categoria' => 'comunicacao-visual']) }}">Banners e comunicação visual</a></li>
                        <li><a href="{{ route('catalog.index', ['categoria' => 'rotulos-e-etiquetas']) }}">Rótulos e etiquetas</a></li>
                    </ul>
                </section>

                <section class="footer-col">
                    <h3>Como comprar</h3>
                    <p>Fluxo pensado para orçamento e pedido com confirmação rápida.</p>
                    <ul class="footer-links">
                        <li><a href="{{ route('catalog.index') }}">Escolher produto</a></li>
                        <li><a href="{{ route('cart.index') }}">Revisar carrinho</a></li>
                        <li><a href="{{ route('checkout.index') }}">Dados e pagamento</a></li>
                        <li><a href="{{ auth()->check() ? route('account.dashboard') : route('login') }}">Acompanhar pedido</a></li>
                    </ul>
                </section>

                <section class="footer-col">
                    <h3>Arquivo e produção</h3>
                    <p>Procedimentos padrão para gráfica online e segurança na entrega.</p>
                    <ul class="footer-links">
                        <li><a href="{{ route('catalog.index') }}">Conferir especificações</a></li>
                        <li><a href="{{ route('checkout.index') }}">Informar observações de arte</a></li>
                        <li><a href="{{ auth()->check() ? route('account.dashboard') : route('login') }}">Status de produção e entrega</a></li>
                    </ul>
                </section>

                <section class="footer-col">
                    <h3>Atendimento</h3>
                    <p>Suporte para dúvidas de material, acabamento, prazo e envio.</p>
                    <ul class="footer-links">
                        <li><a href="{{ route('catalog.index') }}">Solicitar novo pedido</a></li>
                        <li><a href="{{ route('login') }}">Área do cliente</a></li>
                        <li><a href="{{ route('checkout.index') }}">Finalizar compra</a></li>
                    </ul>
                </section>
            </div>

            <div class="footer-bottom">
                <div class="footer-status">
                    <span class="footer-status-copy">Atendimento comercial, conferência de arquivo, produção, acabamento e expedição com acompanhamento online.</span>
                    <div class="footer-status-tags">
                        <span class="mono">Pedido online</span>
                        <span class="mono">Cliente acompanha status</span>
                    </div>
                </div>
                <div class="footer-signature">
                    <strong>URIAH CRIATIVA</strong>
                    <span class="small muted">Gráfica</span>
                </div>
            </div>
        </section>
    </footer>

    <nav class="mobile-quickbar" aria-label="Ações rápidas no mobile">
        @auth
            @if($currentUser?->is_admin)
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    @include('partials.nav-icon', ['name' => 'panel', 'class' => 'nav-icon'])
                    <span class="nav-link-label">Painel</span>
                </a>
                <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    @include('partials.nav-icon', ['name' => 'orders', 'class' => 'nav-icon'])
                    <span class="nav-link-label">Pedidos</span>
                </a>
                <a href="{{ route('admin.catalog.index') }}" class="{{ request()->routeIs('admin.catalog.*') ? 'active' : '' }}">
                    @include('partials.nav-icon', ['name' => 'cadastros', 'class' => 'nav-icon'])
                    <span class="nav-link-label">Cadastros</span>
                </a>
                <a href="{{ route('catalog.index') }}" class="cta">
                    @include('partials.nav-icon', ['name' => 'store', 'class' => 'nav-icon'])
                    <span class="nav-link-label">Loja</span>
                </a>
            @else
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
                    @include('partials.nav-icon', ['name' => 'home', 'class' => 'nav-icon'])
                    <span class="nav-link-label">Início</span>
                </a>
                <a href="{{ route('catalog.index') }}" class="{{ request()->routeIs('catalog.*') ? 'active' : '' }}">
                    @include('partials.nav-icon', ['name' => 'catalog', 'class' => 'nav-icon'])
                    <span class="nav-link-label">Catálogo</span>
                </a>
                <a href="{{ route('cart.index') }}" class="{{ request()->routeIs('cart.*') ? 'active' : '' }}">
                    @include('partials.nav-icon', ['name' => 'cart', 'class' => 'nav-icon'])
                    <span class="nav-link-label">Carrinho</span>
                    @if(($cartSummary['count'] ?? 0) > 0)
                        <span class="count">{{ $cartSummary['count'] }}</span>
                    @endif
                </a>
                <a href="{{ route('checkout.index') }}" class="cta">
                    @include('partials.nav-icon', ['name' => 'checkout', 'class' => 'nav-icon'])
                    <span class="nav-link-label">Checkout</span>
                </a>
            @endif
        @else
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
                @include('partials.nav-icon', ['name' => 'home', 'class' => 'nav-icon'])
                <span class="nav-link-label">Início</span>
            </a>
            <a href="{{ route('catalog.index') }}" class="{{ request()->routeIs('catalog.*') ? 'active' : '' }}">
                @include('partials.nav-icon', ['name' => 'catalog', 'class' => 'nav-icon'])
                <span class="nav-link-label">Catálogo</span>
            </a>
            <a href="{{ route('cart.index') }}" class="{{ request()->routeIs('cart.*') ? 'active' : '' }}">
                @include('partials.nav-icon', ['name' => 'cart', 'class' => 'nav-icon'])
                <span class="nav-link-label">Carrinho</span>
                @if(($cartSummary['count'] ?? 0) > 0)
                    <span class="count">{{ $cartSummary['count'] }}</span>
                @endif
            </a>
            <a href="{{ route('checkout.index') }}" class="cta">
                @include('partials.nav-icon', ['name' => 'checkout', 'class' => 'nav-icon'])
                <span class="nav-link-label">Checkout</span>
            </a>
        @endauth
    </nav>

    <script>
        (function () {
            const siteHeader = document.querySelector('[data-site-header]');
            const menuToggle = document.querySelector('[data-menu-toggle]');
            const headerPanel = document.getElementById('site-header-panel');

            if (siteHeader && menuToggle && headerPanel) {
                const isMobile = () => window.matchMedia('(max-width: 680px)').matches;
                const setOpen = (open) => {
                    siteHeader.setAttribute('data-nav-open', open ? 'true' : 'false');
                    menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                    menuToggle.setAttribute('aria-label', open ? 'Fechar menu' : 'Abrir menu');
                };

                setOpen(false);

                menuToggle.addEventListener('click', () => {
                    if (!isMobile()) return;
                    setOpen(siteHeader.getAttribute('data-nav-open') !== 'true');
                });

                headerPanel.addEventListener('click', (event) => {
                    if (!isMobile()) return;
                    const target = event.target;
                    if (!(target instanceof Element)) return;
                    if (target.closest('a')) {
                        setOpen(false);
                    }
                });

                window.addEventListener('resize', () => {
                    if (!isMobile()) setOpen(false);
                });
            }

            const sameAsBilling = document.getElementById('same_as_billing');
            if (!sameAsBilling) return;

            const fields = ['recipient_name','phone','zipcode','street','number','complement','district','city','state','country'];
            const sync = () => {
                if (!sameAsBilling.checked) return;
                for (const field of fields) {
                    const source = document.getElementById('billing_' + field);
                    const target = document.getElementById('shipping_' + field);
                    if (!source || !target) continue;
                    target.value = source.value;
                }
            };

            sameAsBilling.addEventListener('change', sync);
            for (const field of fields) {
                const source = document.getElementById('billing_' + field);
                if (!source) continue;
                source.addEventListener('input', sync);
            }
        })();
    </script>
    @stack('scripts')
</body>
</html>
