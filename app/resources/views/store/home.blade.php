@extends('layouts.store')

@section('title', 'Gráfica Uriah Criativa | Loja online de impressos')
@section('meta_description', 'Loja online da sua gráfica com catálogo de impressos, tiragens e checkout rápido.')
@section('canonical_url', route('home'))
@section('og_type', 'website')

@php
    $homeOgImage = optional($heroBanners->first(fn ($banner) => filled($banner->background_image_url)))->background_image_url;
@endphp

@if($homeOgImage)
    @section('og_image', $homeOgImage)
@endif

@push('head')
    <style>
        .home-banner-rotator {
            margin: 4px 0 22px;
            position: relative;
        }

        .home-banner-shell {
            position: relative;
            overflow: hidden;
            border-radius: 22px;
            border: 1px solid rgba(212, 173, 88, .18);
            background:
                radial-gradient(circle at 15% 10%, rgba(212, 173, 88, .10), transparent 45%),
                radial-gradient(circle at 90% 85%, rgba(212, 173, 88, .08), transparent 50%),
                linear-gradient(180deg, rgba(10, 10, 11, .96), rgba(8, 8, 9, .98));
            box-shadow:
                0 28px 70px rgba(0, 0, 0, .45),
                inset 0 1px 0 rgba(255, 255, 255, .03),
                inset 0 -1px 0 rgba(212, 173, 88, .08);
        }

        .home-banner-shell::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                linear-gradient(90deg, rgba(212, 173, 88, .12), rgba(212, 173, 88, 0) 22%, rgba(212, 173, 88, 0) 78%, rgba(212, 173, 88, .08));
            opacity: .85;
        }

        .home-banner-stage {
            position: relative;
            min-height: 354px;
            transition: height .28s ease;
        }

        .home-banner-slide {
            position: absolute;
            inset: 0;
            display: grid;
            grid-template-columns: minmax(0, 1.08fr) minmax(0, .92fr);
            gap: 18px;
            padding: 20px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(8px);
            transition:
                opacity .42s ease,
                transform .42s ease,
                visibility 0s linear .42s;
            color: #f5f0e4;
            --banner-accent: #d5ad5e;
            --banner-accent-2: #9e7b2d;
            --banner-soft: rgba(212, 173, 88, .14);
            --banner-soft-2: rgba(212, 173, 88, .07);
            --banner-border: rgba(212, 173, 88, .20);
            --banner-copy-muted: rgba(245, 239, 228, .72);
            --banner-surface: rgba(255, 255, 255, .04);
            --banner-surface-2: rgba(255, 255, 255, .03);
        }

        .home-banner-slide.is-active {
            opacity: 1;
            visibility: visible;
            transform: none;
            transition-delay: 0s;
            z-index: 1;
        }

        .home-banner-slide::after {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at 0% 0%, var(--banner-soft), transparent 45%),
                radial-gradient(circle at 100% 100%, var(--banner-soft-2), transparent 52%);
            opacity: .95;
        }

        .home-banner-slide[data-theme="obsidian"] {
            --banner-accent: #d5af65;
            --banner-accent-2: #c08f36;
            --banner-soft: rgba(199, 153, 58, .11);
            --banner-soft-2: rgba(120, 84, 24, .10);
            --banner-border: rgba(212, 173, 88, .17);
            --banner-copy-muted: rgba(245, 239, 228, .72);
            --banner-surface: rgba(255,255,255,.03);
            --banner-surface-2: rgba(255,255,255,.02);
        }

        .home-banner-slide[data-theme="ivory"] {
            color: #f9f4ea;
            --banner-accent: #ecc87b;
            --banner-accent-2: #c99835;
            --banner-soft: rgba(236, 200, 123, .14);
            --banner-soft-2: rgba(236, 200, 123, .08);
            --banner-border: rgba(236, 200, 123, .23);
            --banner-copy-muted: rgba(249, 244, 234, .76);
            --banner-surface: rgba(255,255,255,.045);
            --banner-surface-2: rgba(255,255,255,.03);
        }

        .home-banner-copy,
        .home-banner-side {
            position: relative;
            z-index: 1;
        }

        .home-banner-copy {
            display: grid;
            grid-template-rows: auto auto auto;
            align-content: space-between;
            gap: 16px;
        }

        .home-banner-copy-top {
            display: grid;
            gap: 10px;
        }

        .home-banner-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            padding: 7px 11px;
            border-radius: 999px;
            border: 1px solid var(--banner-border);
            background: linear-gradient(180deg, rgba(255,255,255,.045), rgba(255,255,255,.015));
            color: var(--banner-copy-muted);
            font-size: .75rem;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.03);
        }

        .home-banner-kicker::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: var(--banner-accent);
            box-shadow: 0 0 0 4px rgba(212, 173, 88, .10);
        }

        .home-banner-slide h2 {
            margin: 0;
            font-family: "Cormorant Garamond", Georgia, serif;
            font-weight: 700;
            font-size: clamp(1.7rem, 2.8vw, 2.7rem);
            line-height: .97;
            letter-spacing: -.02em;
            color: #fbf8f1;
            text-wrap: balance;
        }

        .home-banner-subheadline {
            margin: 0;
            color: var(--banner-accent);
            font-weight: 700;
            letter-spacing: .01em;
            font-size: .98rem;
        }

        .home-banner-description {
            margin: 0;
            color: var(--banner-copy-muted);
            line-height: 1.55;
            max-width: 62ch;
        }

        .home-banner-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        .home-banner-actions .btn {
            min-height: 42px;
        }

        .home-banner-actions .btn-primary {
            background:
                linear-gradient(180deg, rgba(255,255,255,.16), rgba(255,255,255,0)),
                linear-gradient(135deg, #b88d2f, #dcb866 62%, #f1d58c);
            color: #13110d;
            border: 1px solid rgba(236, 200, 123, .36);
            box-shadow:
                0 12px 26px rgba(212, 173, 88, .18),
                inset 0 1px 0 rgba(255,255,255,.35);
        }

        .home-banner-actions .btn-primary:hover {
            box-shadow:
                0 15px 28px rgba(212, 173, 88, .24),
                inset 0 1px 0 rgba(255,255,255,.4);
        }

        .home-banner-actions .btn-secondary {
            background: rgba(255,255,255,.02);
            border-color: var(--banner-border);
            color: #f6f0e2;
        }

        .home-banner-actions .btn-secondary:hover {
            background: rgba(255,255,255,.06);
            border-color: rgba(212, 173, 88, .28);
        }

        .home-banner-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .home-banner-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.06);
            background: rgba(255,255,255,.02);
            color: rgba(245, 239, 228, .75);
            font-size: .76rem;
            font-weight: 600;
        }

        .home-banner-tag::before {
            content: "";
            width: 5px;
            height: 5px;
            border-radius: 999px;
            background: var(--banner-accent);
            opacity: .95;
        }

        .home-banner-side {
            display: grid;
            gap: 10px;
            align-content: start;
        }

        .home-banner-art {
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            min-height: 210px;
            border: 1px solid var(--banner-border);
            background:
                radial-gradient(circle at 84% 14%, rgba(255,255,255,.08), transparent 45%),
                radial-gradient(circle at 12% 88%, var(--banner-soft), transparent 58%),
                linear-gradient(145deg, rgba(255,255,255,.03), rgba(255,255,255,.01));
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.04),
                0 16px 28px rgba(0,0,0,.26);
            display: grid;
            align-items: end;
        }

        .home-banner-art.has-image {
            background:
                linear-gradient(180deg, rgba(7,7,8,.15), rgba(7,7,8,.75) 65%, rgba(7,7,8,.9)),
                var(--banner-image, none);
            background-position: center;
            background-size: cover;
        }

        .home-banner-art::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                linear-gradient(135deg, rgba(212,173,88,.18), rgba(212,173,88,0) 35%),
                radial-gradient(circle at 95% 8%, rgba(255,255,255,.10), transparent 35%);
            mix-blend-mode: screen;
            opacity: .8;
        }

        .home-banner-art-copy {
            position: relative;
            z-index: 1;
            padding: 14px;
            display: grid;
            gap: 8px;
            background: linear-gradient(180deg, rgba(0,0,0,0), rgba(0,0,0,.42) 32%, rgba(0,0,0,.62));
        }

        .home-banner-art-label {
            color: rgba(245, 239, 228, .68);
            text-transform: uppercase;
            letter-spacing: .11em;
            font-size: .72rem;
            font-weight: 800;
        }

        .home-banner-art strong {
            color: #fff9ef;
            font-size: 1.02rem;
            line-height: 1.12;
            text-wrap: balance;
        }

        .home-banner-operational-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .home-banner-operational-card {
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,.06);
            background: linear-gradient(180deg, var(--banner-surface), var(--banner-surface-2));
            padding: 12px;
            display: grid;
            gap: 4px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.03);
        }

        .home-banner-operational-card span {
            color: rgba(245, 239, 228, .62);
            font-size: .72rem;
            letter-spacing: .11em;
            font-weight: 800;
            text-transform: uppercase;
        }

        .home-banner-operational-card strong {
            color: #fbf8f1;
            font-size: .9rem;
            line-height: 1.15;
        }

        .home-banner-controls {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 0 16px 14px;
            margin-top: -6px;
        }

        .home-banner-dots {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .home-banner-dot {
            border: 0;
            padding: 0;
            width: 26px;
            height: 8px;
            border-radius: 999px;
            cursor: pointer;
            background: rgba(255,255,255,.13);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.04);
            transition: width .22s ease, background-color .22s ease, transform .16s ease;
        }

        .home-banner-dot:hover { transform: translateY(-1px); }

        .home-banner-dot[aria-current="true"] {
            width: 42px;
            background: linear-gradient(90deg, rgba(236, 200, 123, .85), rgba(212, 173, 88, .45));
        }

        .home-banner-nav {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .home-banner-nav button {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.07);
            background: rgba(255,255,255,.03);
            color: #f6f0e2;
            display: grid;
            place-items: center;
            cursor: pointer;
            transition: transform .16s ease, border-color .16s ease, background-color .16s ease;
        }

        .home-banner-nav button:hover {
            transform: translateY(-1px);
            border-color: rgba(212, 173, 88, .24);
            background: rgba(255,255,255,.06);
        }

        .home-banner-counter {
            min-width: 56px;
            text-align: center;
            color: rgba(245, 239, 228, .7);
            font-size: .8rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .home-banner-counter strong {
            color: var(--gold-2, #e6c677);
            margin-right: 2px;
        }

        /* Light premium identity for homepage banner */
        .home-banner-shell {
            border-color: rgba(198, 161, 74, .18);
            background:
                radial-gradient(circle at 15% 8%, rgba(198, 161, 74, .12), transparent 42%),
                radial-gradient(circle at 94% 88%, rgba(22, 20, 19, .03), transparent 52%),
                linear-gradient(180deg, rgba(255,255,255,.98), rgba(248,244,236,.95));
            box-shadow:
                0 20px 42px rgba(17, 15, 12, .09),
                inset 0 1px 0 rgba(255,255,255,.88),
                inset 0 -1px 0 rgba(198, 161, 74, .07);
        }

        .home-banner-shell::before {
            background:
                linear-gradient(90deg, rgba(198,161,74,.11), rgba(198,161,74,0) 20%, rgba(198,161,74,0) 80%, rgba(198,161,74,.08));
            opacity: .95;
        }

        .home-banner-slide {
            color: #1a1714;
            --banner-accent: #c6a14a;
            --banner-accent-2: #9f7b2c;
            --banner-soft: rgba(198, 161, 74, .12);
            --banner-soft-2: rgba(198, 161, 74, .06);
            --banner-border: rgba(22, 20, 19, .08);
            --banner-copy-muted: rgba(84, 76, 67, .88);
            --banner-surface: rgba(255,255,255,.88);
            --banner-surface-2: rgba(251,248,241,.95);
        }

        .home-banner-slide[data-theme="obsidian"] {
            --banner-accent: #b68931;
            --banner-accent-2: #8a6722;
            --banner-soft: rgba(182, 137, 49, .10);
            --banner-soft-2: rgba(182, 137, 49, .05);
            --banner-border: rgba(22, 20, 19, .08);
            --banner-copy-muted: rgba(84, 76, 67, .88);
            --banner-surface: rgba(255,255,255,.90);
            --banner-surface-2: rgba(249,245,238,.94);
        }

        .home-banner-slide[data-theme="ivory"] {
            color: #181512;
            --banner-accent: #cba353;
            --banner-accent-2: #9f7b2c;
            --banner-soft: rgba(203, 163, 83, .12);
            --banner-soft-2: rgba(203, 163, 83, .06);
            --banner-border: rgba(22,20,19,.07);
            --banner-copy-muted: rgba(86, 78, 69, .88);
            --banner-surface: rgba(255,255,255,.94);
            --banner-surface-2: rgba(250,246,239,.97);
        }

        .home-banner-slide::after {
            background:
                radial-gradient(circle at 0% 0%, var(--banner-soft), transparent 45%),
                radial-gradient(circle at 100% 100%, var(--banner-soft-2), transparent 52%);
            opacity: .92;
        }

        .home-banner-kicker {
            background: linear-gradient(180deg, rgba(255,255,255,.92), rgba(250,246,239,.88));
            color: #5b503f;
            border-color: rgba(198, 161, 74, .16);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.9),
                0 6px 12px rgba(17,15,12,.03);
        }

        .home-banner-slide h2 {
            color: #171411;
        }

        .home-banner-subheadline {
            color: #8a6822;
        }

        .home-banner-description {
            color: var(--banner-copy-muted);
        }

        .home-banner-actions .btn-secondary {
            background: rgba(255,255,255,.92);
            border-color: rgba(22,20,19,.08);
            color: #1a1714;
        }

        .home-banner-actions .btn-secondary:hover {
            background: #fff;
            border-color: rgba(198,161,74,.18);
        }

        .home-banner-tag {
            border-color: rgba(22,20,19,.06);
            background: rgba(255,255,255,.82);
            color: #534b41;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.86);
        }

        .home-banner-art {
            border-color: rgba(22,20,19,.08);
            background:
                radial-gradient(circle at 84% 14%, rgba(255,255,255,.75), transparent 45%),
                radial-gradient(circle at 12% 88%, var(--banner-soft), transparent 58%),
                linear-gradient(145deg, rgba(255,255,255,.98), rgba(248,244,236,.94));
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.88),
                0 10px 22px rgba(17,15,12,.06);
        }

        .home-banner-art.has-image {
            background:
                linear-gradient(180deg, rgba(255,255,255,.08), rgba(255,255,255,.18) 35%, rgba(15,15,16,.58) 100%),
                var(--banner-image, none);
            background-position: center;
            background-size: cover;
        }

        .home-banner-art::before {
            background:
                linear-gradient(135deg, rgba(198,161,74,.14), rgba(198,161,74,0) 35%),
                radial-gradient(circle at 95% 8%, rgba(255,255,255,.55), transparent 35%);
            opacity: .75;
            mix-blend-mode: normal;
        }

        .home-banner-art-copy {
            background: linear-gradient(180deg, rgba(255,255,255,0), rgba(255,255,255,.18) 28%, rgba(12,12,13,.55));
        }

        .home-banner-art-label {
            color: rgba(247, 242, 233, .78);
        }

        .home-banner-operational-card {
            border-color: rgba(22,20,19,.06);
            background: linear-gradient(180deg, rgba(255,255,255,.92), rgba(250,246,239,.95));
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.88),
                0 8px 14px rgba(17,15,12,.03);
        }

        .home-banner-operational-card span {
            color: rgba(92, 83, 72, .86);
        }

        .home-banner-operational-card strong {
            color: #1b1714;
        }

        .home-banner-dot {
            background: rgba(22,20,19,.10);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.35);
        }

        .home-banner-dot[aria-current="true"] {
            background: linear-gradient(90deg, rgba(212,176,93,.95), rgba(198,161,74,.50));
        }

        .home-banner-nav button {
            border-color: rgba(22,20,19,.07);
            background: rgba(255,255,255,.90);
            color: #1c1916;
            box-shadow: 0 8px 16px rgba(17,15,12,.04);
        }

        .home-banner-nav button:hover {
            border-color: rgba(198,161,74,.18);
            background: #fff;
        }

        .home-banner-counter {
            color: rgba(92,83,72,.86);
        }

        .home-banner-counter strong {
            color: #8d6c22;
        }

        /* Banner layout: text block on top + full-width photo below */
        .home-banner-stage {
            min-height: 560px;
        }

        .home-banner-slide {
            grid-template-columns: 1fr;
            gap: 12px;
            padding: 16px;
        }

        .home-banner-side {
            position: relative;
            gap: 12px;
        }

        .home-banner-art {
            width: 100%;
            min-height: 300px;
            aspect-ratio: 16 / 6.3;
        }

        .home-banner-copy {
            position: relative;
            top: auto;
            left: auto;
            width: 100%;
            max-width: none;
            z-index: 2;
            padding: 14px 16px;
            border-radius: 18px;
            border: 1px solid rgba(22, 20, 19, .07);
            background:
                linear-gradient(180deg, rgba(255,255,255,.94), rgba(251,247,240,.88));
            box-shadow:
                0 10px 22px rgba(17,15,12,.06),
                inset 0 1px 0 rgba(255,255,255,.9);
            backdrop-filter: blur(4px);
            align-content: start;
            gap: 12px;
            justify-items: start;
            text-align: left;
        }

        .home-banner-copy-top {
            gap: 8px;
            width: min(100%, 760px);
        }

        .home-banner-copy h2 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .home-banner-subheadline {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .home-banner-description {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            max-width: none;
        }

        .home-banner-actions {
            gap: 8px;
            width: min(100%, 760px);
        }

        .home-banner-actions .btn {
            min-height: 40px;
            padding-block: 9px;
        }

        .home-banner-tags {
            gap: 6px;
            width: min(100%, 760px);
        }

        .home-banner-tag {
            font-size: .72rem;
            padding: 5px 9px;
        }

        .home-banner-slide[data-text-side="right"] .home-banner-copy {
            justify-items: end;
            text-align: right;
        }

        .home-banner-slide[data-text-side="right"] .home-banner-kicker {
            margin-left: auto;
        }

        .home-banner-slide[data-text-side="right"] .home-banner-actions {
            justify-content: flex-end;
        }

        .home-banner-slide[data-text-side="right"] .home-banner-tags {
            justify-content: flex-end;
        }

        .home-banner-operational-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        @media (max-width: 980px) {
            .home-banner-stage {
                min-height: 620px;
            }

            .home-banner-slide {
                grid-template-columns: 1fr;
                gap: 12px;
                padding: 16px;
            }

            .home-banner-copy {
                position: relative;
                width: 100%;
                max-width: none;
                padding: 12px 14px;
                gap: 14px;
                justify-items: start !important;
                text-align: left !important;
            }

            .home-banner-slide h2 {
                font-size: clamp(1.45rem, 6vw, 2.15rem);
                -webkit-line-clamp: unset;
            }

            .home-banner-description {
                font-size: .94rem;
                -webkit-line-clamp: unset;
            }

            .home-banner-art {
                min-height: 240px;
                aspect-ratio: 16 / 8.4;
            }

            .home-banner-actions,
            .home-banner-tags,
            .home-banner-copy-top {
                width: 100%;
            }

            .home-banner-operational-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .home-banner-controls {
                align-items: flex-start;
                flex-direction: column;
                gap: 10px;
                padding: 0 14px 12px;
            }
        }

        @media (max-width: 560px) {
            .home-banner-stage {
                min-height: 700px;
            }

            .home-banner-actions {
                display: grid;
                grid-template-columns: 1fr;
            }

            .home-banner-actions .btn {
                width: 100%;
            }

            .home-banner-operational-grid {
                grid-template-columns: 1fr;
            }

            .home-banner-art {
                min-height: 210px;
                aspect-ratio: 16 / 10.6;
            }

            .home-banner-copy {
                gap: 10px;
            }

            .home-banner-subheadline {
                -webkit-line-clamp: 3;
            }

            .home-banner-description {
                -webkit-line-clamp: 4;
            }

            .home-banner-dot {
                width: 18px;
                height: 7px;
            }

            .home-banner-dot[aria-current="true"] {
                width: 30px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .home-banner-stage,
            .home-banner-slide,
            .home-banner-dot,
            .home-banner-nav button {
                transition: none !important;
            }
        }

        /* Home page final polish */
        .home-banner-rotator {
            margin: 8px 0 28px;
        }

        .home-banner-shell {
            border-radius: 24px;
            border: 1px solid rgba(198, 161, 74, .18);
            box-shadow:
                0 26px 60px rgba(9, 9, 9, .22),
                inset 0 1px 0 rgba(255,255,255,.04),
                inset 0 -1px 0 rgba(198,161,74,.10);
            background:
                radial-gradient(circle at 12% 10%, rgba(198,161,74,.12), transparent 42%),
                radial-gradient(circle at 92% 88%, rgba(255,255,255,.04), transparent 50%),
                linear-gradient(180deg, rgba(12,12,13,.98), rgba(8,8,9,.99));
        }

        .home-banner-shell::before {
            opacity: .72;
            background:
                linear-gradient(90deg, rgba(198,161,74,.12), rgba(198,161,74,0) 24%, rgba(198,161,74,0) 76%, rgba(198,161,74,.09)),
                radial-gradient(circle at 50% 0%, rgba(255,255,255,.05), transparent 52%);
        }

        .home-banner-slide {
            gap: 14px;
            padding: 18px;
        }

        .home-banner-copy {
            border-color: rgba(198,161,74,.12);
            border-radius: 20px;
            background:
                linear-gradient(180deg, rgba(255,255,255,.95), rgba(250,245,236,.89)),
                radial-gradient(circle at 8% 6%, rgba(198,161,74,.08), transparent 42%);
            box-shadow:
                0 14px 28px rgba(17,15,12,.08),
                inset 0 1px 0 rgba(255,255,255,.9);
            padding: 16px 18px;
            gap: 14px;
        }

        .home-banner-copy-top {
            gap: 9px;
        }

        .home-banner-kicker {
            border-color: rgba(198,161,74,.18);
            background:
                linear-gradient(180deg, rgba(255,255,255,.9), rgba(255,255,255,.72));
            color: #6f5418;
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.75),
                0 6px 14px rgba(17,15,12,.04);
        }

        .home-banner-kicker::before {
            box-shadow: 0 0 0 5px rgba(198,161,74,.10);
        }

        .home-banner-copy h2 {
            line-height: .95;
            letter-spacing: -.025em;
            color: #1b1611;
        }

        .home-banner-subheadline {
            color: #8f6a1c;
        }

        .home-banner-description {
            color: rgba(47, 39, 31, .78);
            line-height: 1.58;
        }

        .home-banner-actions .btn {
            min-height: 42px;
            border-radius: 13px;
        }

        .home-banner-tags {
            gap: 7px;
        }

        .home-banner-tag {
            border-color: rgba(198,161,74,.14);
            background:
                linear-gradient(180deg, rgba(255,255,255,.88), rgba(255,255,255,.72));
            color: #56473b;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.62);
        }

        .home-banner-side {
            gap: 12px;
        }

        .home-banner-operational-grid {
            gap: 12px;
        }

        .home-banner-operational-card {
            border-radius: 15px;
            border-color: rgba(198,161,74,.14);
            background:
                linear-gradient(180deg, rgba(255,255,255,.08), rgba(255,255,255,.03)),
                radial-gradient(circle at 88% 10%, rgba(198,161,74,.10), transparent 42%);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.05),
                0 10px 16px rgba(0,0,0,.12);
            transition:
                transform .2s cubic-bezier(.2,.8,.2,1),
                border-color .2s ease,
                box-shadow .2s ease;
        }

        .home-banner-operational-card:hover {
            transform: translateY(-2px);
            border-color: rgba(198,161,74,.22);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.06),
                0 14px 22px rgba(0,0,0,.16);
        }

        .home-banner-operational-card span {
            color: rgba(245, 239, 228, .66);
        }

        .home-banner-art {
            border-radius: 18px;
            border-color: rgba(198,161,74,.16);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.05),
                0 18px 26px rgba(0,0,0,.20);
            min-height: 320px;
        }

        .home-banner-art::after {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,0) 32%),
                radial-gradient(circle at 12% 10%, rgba(198,161,74,.14), transparent 42%);
        }

        .home-banner-art-copy {
            padding: 16px;
            background: linear-gradient(180deg, rgba(0,0,0,0), rgba(0,0,0,.46) 34%, rgba(0,0,0,.72));
        }

        .home-banner-art-label {
            color: rgba(255, 243, 214, .76);
        }

        .home-banner-art strong {
            font-size: 1.08rem;
            line-height: 1.14;
        }

        .home-banner-controls {
            padding: 4px 16px 16px;
            margin-top: 0;
            gap: 12px;
        }

        .home-banner-dot {
            height: 9px;
            background: rgba(255,255,255,.16);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.08),
                0 4px 10px rgba(0,0,0,.16);
        }

        .home-banner-dot[aria-current="true"] {
            background: linear-gradient(90deg, rgba(242, 215, 146, .95), rgba(198, 161, 74, .50));
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.30),
                0 6px 14px rgba(198,161,74,.22);
        }

        .home-banner-nav button {
            width: 36px;
            height: 36px;
            border-color: rgba(255,255,255,.08);
            background:
                linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.03));
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.04),
                0 8px 14px rgba(0,0,0,.16);
        }

        .home-banner-nav button:hover {
            border-color: rgba(198,161,74,.24);
            background:
                linear-gradient(180deg, rgba(255,255,255,.09), rgba(255,255,255,.04));
        }

        .home-banner-counter {
            min-width: 64px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.06);
            background: rgba(255,255,255,.03);
            padding: 6px 10px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.03);
        }

        @media (max-width: 980px) {
            .home-banner-shell {
                border-radius: 20px;
            }

            .home-banner-slide {
                padding: 14px;
                gap: 12px;
            }

            .home-banner-copy {
                padding: 14px;
            }

            .home-banner-art {
                min-height: 250px;
            }

            .home-banner-controls {
                padding: 2px 14px 14px;
            }

            .home-banner-operational-card:hover {
                transform: none;
            }
        }

        @media (max-width: 560px) {
            .home-banner-shell {
                border-radius: 18px;
            }

            .home-banner-copy {
                border-radius: 16px;
                padding: 12px;
            }

            .home-banner-art {
                min-height: 220px;
            }

            .home-banner-controls {
                gap: 10px;
            }
        }

        /* Simple full-width uploaded banner (requested) */
        .home-banner-rotator {
            --home-banner-height: clamp(220px, 30vw, 520px);
            width: calc(100% + (var(--ui-gutter, 16px) * 2));
            margin-left: calc(var(--ui-gutter, 16px) * -1);
            margin-right: calc(var(--ui-gutter, 16px) * -1);
            margin-top: 0;
            margin-bottom: 24px;
        }

        .home-banner-shell {
            border: 0;
            border-radius: 0;
            background: transparent;
            box-shadow: none;
            overflow: hidden;
        }

        .home-banner-shell::before,
        .home-banner-slide::after {
            display: none;
        }

        .home-banner-stage {
            min-height: 0 !important;
            transition: height .2s ease;
        }

        .home-banner-slide {
            display: block;
            padding: 0;
            gap: 0;
            background: transparent;
            color: inherit;
            transform: none;
        }

        .home-banner-slide.is-active {
            position: relative;
            inset: auto;
        }

        .home-banner-media {
            display: block;
            width: 100%;
            line-height: 0;
            background: #111;
            height: var(--home-banner-height);
            max-height: min(72vh, 620px);
            overflow: hidden;
        }

        .home-banner-image {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        .home-banner-fallback {
            min-height: var(--home-banner-height);
            display: grid;
            justify-items: center;
            align-content: center;
            gap: 10px;
            padding: 20px;
            text-align: center;
            line-height: 1.2;
            color: #fbf8f1;
            background:
                radial-gradient(circle at 15% 15%, rgba(198,161,74,.18), transparent 45%),
                radial-gradient(circle at 85% 85%, rgba(255,255,255,.06), transparent 45%),
                linear-gradient(180deg, #161515, #0e0e0f);
        }

        .home-banner-fallback-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 30px;
            padding: 5px 10px;
            border-radius: 999px;
            border: 1px solid rgba(198,161,74,.20);
            background: rgba(255,255,255,.04);
            color: rgba(255,244,220,.88);
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .home-banner-fallback strong {
            font-family: "Cormorant Garamond", Georgia, serif;
            font-size: clamp(1.6rem, 3vw, 2.7rem);
            line-height: .95;
            letter-spacing: -.02em;
            max-width: 18ch;
            text-wrap: balance;
        }

        .home-banner-fallback-subtitle {
            color: rgba(245,239,228,.78);
            max-width: 60ch;
            line-height: 1.4;
        }

        .home-banner-controls {
            margin-top: 0;
            padding: 10px clamp(12px, 2.2vw, 24px) 0;
            justify-content: center;
            gap: 14px;
        }

        .home-banner-dots {
            gap: 7px;
            justify-content: center;
        }

        .home-banner-dot {
            width: 20px;
            height: 7px;
            background: rgba(17, 15, 12, .14);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.45);
        }

        .home-banner-dot[aria-current="true"] {
            width: 34px;
            background: linear-gradient(90deg, rgba(198,161,74,.95), rgba(198,161,74,.45));
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.45),
                0 4px 10px rgba(198,161,74,.18);
        }

        .home-banner-nav {
            gap: 6px;
        }

        .home-banner-nav button {
            width: 32px;
            height: 32px;
            border-radius: 999px;
            border: 1px solid rgba(22,20,19,.08);
            background: rgba(255,255,255,.92);
            color: #1d1916;
            box-shadow: 0 8px 16px rgba(12,10,8,.06);
        }

        .home-banner-nav button:hover {
            border-color: rgba(198,161,74,.20);
            background: #fff;
        }

        .home-banner-counter {
            min-width: 56px;
            border-radius: 999px;
            border: 1px solid rgba(22,20,19,.06);
            background: rgba(255,255,255,.92);
            color: rgba(29,25,22,.72);
            padding: 5px 9px;
            box-shadow: 0 8px 16px rgba(12,10,8,.05);
        }

        .home-banner-counter strong {
            color: #6f5418;
        }

        @media (max-width: 980px) {
            .home-banner-rotator {
                --home-banner-height: clamp(190px, 34vw, 360px);
                margin-bottom: 20px;
            }

            .home-banner-controls {
                padding-top: 8px;
                gap: 10px;
            }
        }

        @media (max-width: 560px) {
            .home-banner-rotator {
                --home-banner-height: clamp(150px, 44vw, 240px);
            }

            .home-banner-controls {
                align-items: center;
                flex-direction: column;
                gap: 8px;
                padding-inline: 10px;
            }

            .home-banner-dot {
                width: 16px;
                height: 6px;
            }

            .home-banner-dot[aria-current="true"] {
                width: 28px;
            }
        }

        /* Home layout refinement */
        .home-section {
            margin-bottom: 30px;
        }

        .home-below-banner-grid {
            display: grid;
            grid-template-columns: 320px minmax(0, 1fr);
            gap: 16px;
            align-items: stretch;
            margin-bottom: 30px;
        }

        .home-category-links-panel {
            position: relative;
            overflow: hidden;
            display: grid;
            gap: 14px;
            border-radius: 22px;
            border: 1px solid rgba(198,161,74,.14);
            background:
                radial-gradient(circle at 92% 8%, rgba(198,161,74,.09), transparent 42%),
                radial-gradient(circle at 8% 92%, rgba(255,255,255,.14), transparent 50%),
                linear-gradient(180deg, rgba(255,255,255,.90), rgba(255,255,255,.80));
            box-shadow:
                0 18px 36px rgba(12,10,8,.08),
                inset 0 1px 0 rgba(255,255,255,.70);
        }

        .home-category-links-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: linear-gradient(140deg, rgba(198,161,74,.05), transparent 26%, transparent 72%, rgba(198,161,74,.05));
        }

        .home-category-links-panel > * {
            position: relative;
            z-index: 1;
        }

        .home-category-links-head {
            display: grid;
            gap: 6px;
        }

        .home-category-links-head h2 {
            margin: 0;
            font-size: 1.14rem;
            line-height: 1.02;
        }

        .home-category-links-nav {
            display: grid;
            gap: 8px;
        }

        .home-category-link {
            position: relative;
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            align-items: center;
            gap: 10px;
            padding: 10px 11px;
            border-radius: 14px;
            border: 1px solid rgba(22,20,19,.06);
            background:
                linear-gradient(180deg, rgba(255,255,255,.94), rgba(255,255,255,.84));
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.86),
                0 6px 12px rgba(12,10,8,.03);
            transition:
                transform .18s ease,
                border-color .18s ease,
                box-shadow .18s ease,
                background .18s ease;
        }

        .home-category-link:hover {
            transform: translateY(-2px);
            border-color: rgba(198,161,74,.18);
            background:
                linear-gradient(180deg, rgba(255,255,255,.98), rgba(250,245,235,.92));
            box-shadow:
                0 10px 18px rgba(12,10,8,.05),
                inset 0 1px 0 rgba(255,255,255,.9);
        }

        .home-category-link-index {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(198,161,74,.18);
            background:
                linear-gradient(180deg, rgba(255,255,255,.96), rgba(248,242,231,.92));
            color: #7a5b1a;
            font-size: .72rem;
            font-weight: 800;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.88);
        }

        .home-category-link-copy {
            min-width: 0;
            display: grid;
            gap: 2px;
        }

        .home-category-link-copy strong {
            color: #1f1a16;
            font-size: .88rem;
            line-height: 1.1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .home-category-link-copy span {
            color: rgba(92,83,72,.82);
            font-size: .72rem;
            line-height: 1.15;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .home-category-link-arrow {
            width: 26px;
            height: 26px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(198,161,74,.14);
            background: rgba(255,255,255,.86);
            color: #7a5b1a;
            font-weight: 800;
            transition: transform .18s ease, border-color .18s ease, background .18s ease;
        }

        .home-category-link:hover .home-category-link-arrow {
            transform: translateX(2px);
            border-color: rgba(198,161,74,.20);
            background: rgba(255,251,242,.94);
        }

        .home-category-links-panel .btn {
            width: 100%;
            justify-content: center;
        }

        .home-category-links-tip {
            padding: 10px 11px;
            border-radius: 14px;
            border: 1px dashed rgba(198,161,74,.18);
            background: rgba(255,255,255,.72);
            color: rgba(92,83,72,.9);
            font-size: .78rem;
            line-height: 1.35;
        }

        .home-category-callout {
            position: relative;
            overflow: hidden;
            display: grid;
            align-content: center;
            gap: 14px;
            min-height: 100%;
            border-radius: 24px;
            border: 1px solid rgba(198,161,74,.14);
            background:
                radial-gradient(circle at 10% 10%, rgba(198,161,74,.10), transparent 46%),
                radial-gradient(circle at 90% 88%, rgba(255,255,255,.18), transparent 52%),
                linear-gradient(135deg, rgba(255,255,255,.86), rgba(255,255,255,.74));
            box-shadow:
                0 18px 36px rgba(12,10,8,.07),
                inset 0 1px 0 rgba(255,255,255,.70);
        }

        .home-category-callout::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                linear-gradient(115deg, rgba(198,161,74,.05), transparent 32%, transparent 68%, rgba(198,161,74,.04));
        }

        .home-category-callout > * {
            position: relative;
            z-index: 1;
        }

        .home-category-callout-head {
            display: grid;
            gap: 8px;
            max-width: 64ch;
        }

        .home-category-callout-head h2 {
            margin: 0;
            line-height: .96;
            letter-spacing: -.02em;
        }

        .home-category-callout-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .home-category-callout-pills .pill {
            padding: 7px 10px;
            font-size: .78rem;
        }

        .home-category-callout-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        @media (max-width: 980px) {
            .home-below-banner-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .home-category-links-panel,
            .home-category-callout {
                border-radius: 20px;
            }
        }

        @media (max-width: 640px) {
            .home-category-link-copy span {
                -webkit-line-clamp: 1;
            }

            .home-category-callout-actions {
                display: grid;
                grid-template-columns: 1fr;
            }

            .home-category-callout-actions .btn {
                width: 100%;
                justify-content: center;
            }
        }

        .home-section-shell {
            position: relative;
            overflow: hidden;
            padding: clamp(16px, 2vw, 24px);
            border-radius: 24px;
            border: 1px solid rgba(198, 161, 74, .14);
            background:
                radial-gradient(circle at 92% 8%, rgba(198,161,74,.08), transparent 42%),
                radial-gradient(circle at 8% 92%, rgba(255,255,255,.18), transparent 52%),
                linear-gradient(180deg, rgba(255,255,255,.82), rgba(255,255,255,.70));
            box-shadow:
                0 22px 44px rgba(12, 10, 8, .08),
                inset 0 1px 0 rgba(255,255,255,.62);
        }

        .home-section-shell::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                linear-gradient(135deg, rgba(198,161,74,.06), transparent 28%, transparent 72%, rgba(198,161,74,.04));
            opacity: .95;
        }

        .home-section-shell > * {
            position: relative;
            z-index: 1;
        }

        .home-section-shell .section-head {
            margin-bottom: 2px;
        }

        .home-section-shell .section-head .copy {
            max-width: 74ch;
        }

        .home-section-shell .section-head .copy p {
            max-width: 64ch;
            line-height: 1.5;
        }

        .home-section-shell .section-head .btn.btn-secondary {
            border-color: rgba(22,20,19,.08);
            background:
                linear-gradient(180deg, rgba(255,255,255,.96), rgba(255,255,255,.82));
            box-shadow:
                0 8px 16px rgba(12,10,8,.05),
                inset 0 1px 0 rgba(255,255,255,.72);
        }

        .home-section-shell .grid > .card:not(.product-card) {
            border-color: rgba(198,161,74,.12);
            background:
                radial-gradient(circle at 92% 8%, rgba(198,161,74,.06), transparent 36%),
                linear-gradient(180deg, rgba(255,255,255,.94), rgba(255,255,255,.84));
            box-shadow:
                0 14px 28px rgba(12,10,8,.06),
                inset 0 1px 0 rgba(255,255,255,.7);
        }

        .home-section-shell .grid > .card:not(.product-card):hover {
            border-color: rgba(198,161,74,.18);
            box-shadow:
                0 18px 34px rgba(12,10,8,.08),
                inset 0 1px 0 rgba(255,255,255,.74);
        }

        .home-section-applications .grid.grid-3 > .card {
            position: relative;
            min-height: 100%;
        }

        .home-section-applications .grid.grid-3 > .card::after {
            content: "";
            position: absolute;
            top: 0;
            left: 18px;
            right: 18px;
            height: 2px;
            border-radius: 999px;
            background: linear-gradient(90deg, rgba(198,161,74,.9), rgba(198,161,74,.18));
            opacity: .85;
        }

        .home-section-applications .link-row {
            align-items: center;
            gap: 10px;
        }

        .home-section-applications .link-row .tiny {
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: rgba(73,64,55,.68);
        }

        .home-section-applications .grid.grid-2 > .card {
            border-radius: 16px;
            border: 1px solid rgba(198,161,74,.10);
            background:
                linear-gradient(180deg, rgba(255,255,255,.90), rgba(255,255,255,.78));
            box-shadow:
                0 8px 16px rgba(12,10,8,.04),
                inset 0 1px 0 rgba(255,255,255,.62);
        }

        .home-section-applications .grid.grid-2 > .card:hover {
            transform: none;
            box-shadow:
                0 8px 16px rgba(12,10,8,.04),
                inset 0 1px 0 rgba(255,255,255,.62);
        }

        .home-section-operation .spot-grid {
            gap: 14px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .home-section-operation .spot-card {
            min-height: 164px;
            grid-template-rows: auto auto auto 1fr;
            gap: 9px;
            padding: 16px;
            border-radius: 18px;
            border: 1px solid rgba(198,161,74,.12);
            background:
                radial-gradient(circle at 100% 0%, rgba(198,161,74,.10), transparent 46%),
                linear-gradient(180deg, rgba(255,255,255,.94), rgba(255,255,255,.82));
            box-shadow:
                0 14px 28px rgba(12,10,8,.06),
                inset 0 1px 0 rgba(255,255,255,.70);
        }

        .home-section-operation .spot-card::before {
            inset: auto -18px -18px auto;
            width: 110px;
            height: 110px;
            background: radial-gradient(circle, rgba(198,161,74,.14), rgba(198,161,74,0));
        }

        .home-section-operation .spot-card::after {
            content: "";
            position: absolute;
            inset: 10px;
            border-radius: 14px;
            border: 1px dashed rgba(198,161,74,.12);
            pointer-events: none;
        }

        .home-section-operation .spot-step {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            width: fit-content;
            padding: 4px 8px;
            border-radius: 999px;
            border: 1px solid rgba(198,161,74,.16);
            background: rgba(255,255,255,.82);
            color: #76591b;
            font-size: .68rem;
            font-weight: 800;
            letter-spacing: .09em;
            text-transform: uppercase;
        }

        .home-section-operation .spot-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            border: 1px solid rgba(198,161,74,.16);
            background:
                linear-gradient(180deg, rgba(255,255,255,.96), rgba(255,255,255,.80));
            color: #8d6c22;
            box-shadow:
                0 8px 16px rgba(12,10,8,.05),
                inset 0 1px 0 rgba(255,255,255,.78);
            position: relative;
            z-index: 1;
        }

        .home-section-operation .spot-icon .nav-icon-svg {
            width: 18px;
            height: 18px;
            opacity: 1;
        }

        .home-section-operation .spot-card h3 {
            font-size: 1.06rem;
            line-height: 1.06;
            letter-spacing: -.01em;
        }

        .home-section-operation .spot-card p {
            margin: 0;
            line-height: 1.45;
            color: rgba(73,64,55,.78);
        }

        .home-section-operation .spot-card:hover .spot-icon {
            transform: translateY(-1px);
            box-shadow:
                0 10px 18px rgba(12,10,8,.07),
                inset 0 1px 0 rgba(255,255,255,.82);
        }

        .home-section-categories .category-tile {
            min-height: 206px;
            gap: 16px;
            padding: 16px;
            border-radius: 18px;
            border-color: rgba(198,161,74,.12);
            background:
                radial-gradient(circle at 96% 6%, rgba(198,161,74,.08), transparent 42%),
                radial-gradient(circle at 8% 90%, rgba(15,93,245,.04), transparent 44%),
                linear-gradient(180deg, rgba(255,255,255,.95), rgba(255,255,255,.84));
        }

        .home-section-categories .category-index {
            border-color: rgba(198,161,74,.16);
            background:
                linear-gradient(180deg, rgba(255,255,255,.96), rgba(255,255,255,.82));
            color: #6f5418;
            box-shadow:
                0 8px 16px rgba(12,10,8,.04),
                inset 0 1px 0 rgba(255,255,255,.78);
        }

        .home-section-categories .category-footer {
            padding-top: 10px;
            border-top: 1px dashed rgba(198,161,74,.14);
            color: rgba(73,64,55,.76);
        }

        .home-section-categories .category-arrow {
            width: 34px;
            height: 34px;
            border-color: rgba(198,161,74,.15);
            background:
                linear-gradient(180deg, rgba(255,255,255,.96), rgba(255,255,255,.82));
            box-shadow:
                0 8px 14px rgba(12,10,8,.04),
                inset 0 1px 0 rgba(255,255,255,.72);
        }

        .home-section-showcase .grid.grid-4 {
            gap: 20px;
        }

        .home-process-shell {
            position: relative;
            overflow: hidden;
            margin-bottom: 28px;
            border-radius: 26px;
            padding: clamp(18px, 2vw, 24px);
        }

        .home-process-shell::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at 8% 12%, rgba(255,255,255,.05), transparent 42%),
                linear-gradient(120deg, rgba(198,161,74,.06), transparent 30%, transparent 74%, rgba(198,161,74,.06));
        }

        .home-process-shell > * {
            position: relative;
            z-index: 1;
        }

        .home-process-shell .grid.grid-4 > .card {
            border-radius: 18px;
            backdrop-filter: blur(8px);
            transition: transform .2s ease, border-color .2s ease, background .2s ease;
        }

        .home-process-shell .grid.grid-4 > .card:hover {
            transform: translateY(-2px);
            border-color: rgba(198,161,74,.18);
            background: rgba(255,255,255,.08) !important;
        }

        @media (max-width: 980px) {
            .home-section-shell {
                padding: 14px;
                border-radius: 20px;
            }

            .home-section-operation .spot-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .home-section-categories .category-tile {
                min-height: 184px;
            }

            .home-process-shell {
                border-radius: 22px;
                padding: 14px;
            }
        }

        @media (max-width: 640px) {
            .home-section-operation .spot-grid {
                grid-template-columns: 1fr;
            }

            .home-section-shell .section-head .copy p {
                max-width: 100%;
            }

            .home-section-categories .category-tile {
                min-height: 170px;
            }
        }
    </style>
@endpush

@section('content')
    @if($heroBanners->isNotEmpty())
        <section class="home-banner-rotator reveal-up" data-home-banner-rotator data-interval="7000" aria-label="Banners em destaque da Uriah Criativa">
            <div class="home-banner-shell">
                <div class="home-banner-stage" data-home-banner-stage>
                    @foreach($heroBanners as $banner)
                        <article
                            class="home-banner-slide {{ $loop->first ? 'is-active' : '' }}"
                            data-banner-slide
                            data-theme="{{ $banner->theme ?: 'gold' }}"
                            data-text-side="{{ data_get($banner->metadata, 'text_side') === 'right' ? 'right' : 'left' }}"
                            aria-hidden="{{ $loop->first ? 'false' : 'true' }}"
                            aria-label="Banner {{ $loop->iteration }} de {{ $heroBanners->count() }}"
                        >
                            @php
                                $bannerTitle = trim($banner->headline ?: $banner->name ?: 'Banner promocional');
                                $bannerSubtitle = trim($banner->subheadline ?: '');
                            @endphp

                            @if($banner->cta_url)
                                <a href="{{ $banner->cta_url }}" class="home-banner-media">
                            @else
                                <div class="home-banner-media">
                            @endif
                                @if($banner->background_image_url)
                                    <img
                                        class="home-banner-image"
                                        src="{{ $banner->background_image_url }}"
                                        alt="{{ $bannerTitle }}"
                                        decoding="async"
                                        @if($loop->first)
                                            fetchpriority="high"
                                        @else
                                            loading="lazy"
                                        @endif
                                    >
                                @else
                                    <div class="home-banner-fallback">
                                        @if($banner->badge)
                                            <span class="home-banner-fallback-badge">{{ $banner->badge }}</span>
                                        @endif
                                        <strong>{{ $bannerTitle }}</strong>
                                        @if($bannerSubtitle)
                                            <span class="home-banner-fallback-subtitle">{{ $bannerSubtitle }}</span>
                                        @endif
                                    </div>
                                @endif
                            @if($banner->cta_url)
                                </a>
                            @else
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>

                @if($heroBanners->count() > 1)
                    <div class="home-banner-controls">
                        <div class="home-banner-dots" role="tablist" aria-label="Selecionar banner">
                            @foreach($heroBanners as $banner)
                                <button
                                    type="button"
                                    class="home-banner-dot"
                                    data-banner-dot
                                    data-index="{{ $loop->index }}"
                                    aria-label="Ir para banner {{ $loop->iteration }}"
                                    aria-current="{{ $loop->first ? 'true' : 'false' }}"
                                ></button>
                            @endforeach
                        </div>

                        <div class="home-banner-nav">
                            <button type="button" data-banner-prev aria-label="Banner anterior">
                                <span aria-hidden="true">‹</span>
                            </button>
                            <div class="home-banner-counter" aria-live="polite">
                                <strong data-banner-current>1</strong>/<span>{{ $heroBanners->count() }}</span>
                            </div>
                            <button type="button" data-banner-next aria-label="Próximo banner">
                                <span aria-hidden="true">›</span>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    @endif

    @if($categories->isNotEmpty())
        <section class="home-below-banner-grid" aria-label="Atalhos de categorias">
            <aside class="card card-pad home-category-links-panel reveal-up">
                <div class="home-category-links-head">
                    <span class="section-kicker">Categorias</span>
                    <h2>Links rápidos</h2>
                    <p class="small muted">Clique em uma categoria para abrir a lista de produtos no catálogo.</p>
                </div>

                <nav class="home-category-links-nav" aria-label="Categorias da loja">
                    @foreach($categories as $category)
                        <a href="{{ route('catalog.index', ['categoria' => $category->slug]) }}" class="home-category-link">
                            <span class="home-category-link-index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="home-category-link-copy">
                                <strong>{{ $category->name }}</strong>
                                <span>{{ $category->description ?: 'Abrir categoria e ver produtos disponíveis.' }}</span>
                            </span>
                            <span class="home-category-link-arrow" aria-hidden="true">→</span>
                        </a>
                    @endforeach
                </nav>

                <div class="home-category-links-tip">
                    Dica: você pode entrar por categoria e depois usar a busca para encontrar o produto exato.
                </div>

                <a href="{{ route('catalog.index') }}" class="btn btn-primary">Ver catálogo completo</a>
            </aside>

            <div class="card card-pad home-category-callout reveal-up">
                <div class="home-category-callout-head">
                    <span class="section-kicker">Navegação rápida</span>
                    <h2>Escolha a categoria no lado esquerdo e encontre o produto mais rápido</h2>
                    <p class="muted">Organizamos os links para facilitar o caminho de compra: categoria, produto, configuração, pedido e checkout.</p>
                </div>

                <div class="home-category-callout-pills">
                    <span class="pill">Categorias em destaque</span>
                    <span class="pill">{{ $categories->count() }} atalhos rápidos</span>
                    <span class="pill">Catálogo em português</span>
                </div>

                <div class="home-category-callout-actions">
                    <a href="{{ route('catalog.index') }}" class="btn btn-secondary">Abrir catálogo</a>
                    <a href="{{ route('checkout.index') }}" class="btn btn-primary">Ir para checkout</a>
                </div>
            </div>
        </section>
    @endif

    <section class="stack-xl home-section home-section-shell home-section-applications">
        <div class="section-head">
            <div class="copy">
                <span class="section-kicker">Aplicações</span>
                <h2>Estruture a loja por contexto de uso, não só por tipo de peça</h2>
                <p class="muted">Uma vitrine premium vende melhor quando fala a linguagem do cliente final: campanha, PDV, evento e papelaria corporativa.</p>
            </div>
            <div>
                <a href="{{ route('catalog.index') }}" class="btn btn-secondary">Ver catálogo completo</a>
            </div>
        </div>

        <div class="grid grid-3">
            <article class="card card-pad stack reveal-up">
                <div class="link-row">
                    <span class="badge badge-brand">Varejo & PDV</span>
                    <span class="tiny muted">Campanhas</span>
                </div>
                @include('store.partials.print-mockup', ['categorySlug' => 'promocionais', 'title' => 'Flyer Oferta'])
                <p class="small muted">Flyers, folders e materiais promocionais com foco em giro rápido e ações sazonais.</p>
                <a href="{{ route('catalog.index', ['categoria' => 'promocionais']) }}" class="btn btn-secondary btn-sm">Explorar promocionais</a>
            </article>

            <article class="card card-pad stack reveal-up">
                <div class="link-row">
                    <span class="badge">Eventos</span>
                    <span class="tiny muted">Comunicação visual</span>
                </div>
                @include('store.partials.print-mockup', ['categorySlug' => 'comunicacao-visual', 'title' => 'Banner Evento'])
                <p class="small muted">Banners, faixas e peças de presença para ativações, pontos de venda e feiras.</p>
                <a href="{{ route('catalog.index', ['categoria' => 'comunicacao-visual']) }}" class="btn btn-secondary btn-sm">Explorar comunicação visual</a>
            </article>

            <article class="card card-pad stack reveal-up">
                <div class="link-row">
                    <span class="badge">Marcas</span>
                    <span class="tiny muted">Papelaria & rótulos</span>
                </div>
                <div class="grid grid-2" style="gap:10px;">
                    <div class="card" style="padding:10px; box-shadow:none;">
                        @include('store.partials.print-mockup', ['categorySlug' => 'cartoes-e-papelaria', 'title' => 'Cartão'])
                    </div>
                    <div class="card" style="padding:10px; box-shadow:none;">
                        @include('store.partials.print-mockup', ['categorySlug' => 'rotulos-e-etiquetas', 'title' => 'Rótulos'])
                    </div>
                </div>
                <p class="small muted">Papelaria premium e rótulos para reforçar identidade visual e percepção de qualidade.</p>
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <a href="{{ route('catalog.index', ['categoria' => 'cartoes-e-papelaria']) }}" class="btn btn-secondary btn-sm">Papelaria</a>
                    <a href="{{ route('catalog.index', ['categoria' => 'rotulos-e-etiquetas']) }}" class="btn btn-secondary btn-sm">Rótulos</a>
                </div>
            </article>
        </div>
    </section>

    <section class="stack-xl home-section home-section-shell home-section-operation">
        <div class="section-head">
            <div class="copy">
                <span class="section-kicker">Operação da gráfica</span>
                <h2>Fluxo pensado para orçamento, conferência e produção sem atrito</h2>
                <p class="muted">A loja foi organizada para facilitar compra rápida sem perder os procedimentos padrão de uma gráfica online.</p>
            </div>
        </div>

        <div class="spot-grid">
            <article class="spot-card reveal-up">
                <span class="spot-step">Etapa 01</span>
                <div class="spot-icon" aria-hidden="true">
                    @include('partials.nav-icon', ['name' => 'catalog'])
                </div>
                <h3>Configuração por tiragem</h3>
                <p>Escolha material, quantidade, acabamento e prazo com leitura rápida de preço.</p>
            </article>
            <article class="spot-card reveal-up">
                <span class="spot-step">Etapa 02</span>
                <div class="spot-icon" aria-hidden="true">
                    @include('partials.nav-icon', ['name' => 'status-billing'])
                </div>
                <h3>Pedido e cobrança</h3>
                <p>Resumo claro dos itens, dados de cobrança e criação rápida do pedido.</p>
            </article>
            <article class="spot-card reveal-up">
                <span class="spot-step">Etapa 03</span>
                <div class="spot-icon" aria-hidden="true">
                    @include('partials.nav-icon', ['name' => 'status-review'])
                </div>
                <h3>Conferência de arquivo</h3>
                <p>Espaço para observações de arte e fluxo preparado para validação técnica.</p>
            </article>
            <article class="spot-card reveal-up">
                <span class="spot-step">Etapa 04</span>
                <div class="spot-icon" aria-hidden="true">
                    @include('partials.nav-icon', ['name' => 'orders'])
                </div>
                <h3>Acompanhamento do cliente</h3>
                <p>Área de pedidos para consultar status de pagamento, produção e entrega.</p>
            </article>
        </div>
    </section>

    <section class="stack-xl home-section home-section-shell home-section-categories">
        <div class="section-head">
            <div class="copy">
                <span class="section-kicker">Categorias</span>
                <h2>Linhas de produto organizadas para escalar catálogo</h2>
                <p class="muted">Estruture sua oferta por família de materiais e tipo de aplicação comercial.</p>
            </div>
            <div>
                <a href="{{ route('catalog.index') }}" class="btn btn-secondary">Ver todos os produtos</a>
            </div>
        </div>

        <div class="grid grid-4">
            @forelse ($categories as $category)
                <a href="{{ route('catalog.index', ['categoria' => $category->slug]) }}" class="card card-pad category-tile reveal-up">
                    <span class="category-index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <div class="stack" style="gap:8px;">
                        <h3>{{ $category->name }}</h3>
                        <p class="small muted">{{ $category->description ?: 'Linha de produtos com configuração e produção sob demanda.' }}</p>
                    </div>
                    <div class="category-footer">
                        <span>Explorar categoria</span>
                        <span class="category-arrow">↗</span>
                    </div>
                </a>
            @empty
                <div class="card card-pad">
                    <p class="muted">Nenhuma categoria cadastrada ainda. Rode o seed para preencher a loja.</p>
                </div>
            @endforelse
        </div>
    </section>

    <section class="stack-xl home-section home-section-shell home-section-showcase">
        <div class="section-head">
            <div class="copy">
                <span class="section-kicker">Vitrine</span>
                <h2>Produtos em destaque com visual de catálogo moderno</h2>
                <p class="muted">Cards com leitura rápida de prazo e preço para acelerar navegação e decisão.</p>
            </div>
        </div>

        <div class="grid grid-4">
            @forelse ($featuredProducts as $product)
                @include('store.partials.product-card', ['product' => $product])
            @empty
                <div class="card card-pad">
                    <p class="muted">Nenhum produto em destaque ainda. Rode `php artisan db:seed` dentro do container.</p>
                </div>
            @endforelse
        </div>
    </section>

    <section class="card card-pad surface-dark stack-xl home-process-shell">
        <div class="section-head">
            <div class="copy">
                <span class="section-kicker" style="color: rgba(248,245,239,.72);">Processos da gráfica</span>
                <h2 style="color:#f8f5ef;">Etapas e serviços padrão para operar online</h2>
                <p class="muted">Comunicação e procedimento claros ajudam o cliente a comprar com segurança e evitam retrabalho na produção.</p>
            </div>
        </div>

        <div class="grid grid-4">
            <div class="card card-pad" style="background: rgba(255,255,255,.05); border-color: rgba(255,255,255,.08); box-shadow:none;">
                <h3 style="color:#f8f5ef;">Cobrança</h3>
                <p class="small muted">PIX, cartão, boleto e confirmação para liberação da produção.</p>
            </div>
            <div class="card card-pad" style="background: rgba(255,255,255,.05); border-color: rgba(255,255,255,.08); box-shadow:none;">
                <h3 style="color:#f8f5ef;">Arquivo</h3>
                <p class="small muted">Conferência técnica, ajustes e aprovação antes da impressão.</p>
            </div>
            <div class="card card-pad" style="background: rgba(255,255,255,.05); border-color: rgba(255,255,255,.08); box-shadow:none;">
                <h3 style="color:#f8f5ef;">Produção</h3>
                <p class="small muted">Impressão, acabamento e controle de prazo por pedido.</p>
            </div>
            <div class="card card-pad" style="background: rgba(255,255,255,.05); border-color: rgba(255,255,255,.08); box-shadow:none;">
                <h3 style="color:#f8f5ef;">Entrega</h3>
                <p class="small muted">Retirada local ou expedição com acompanhamento ao cliente.</p>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            const rotators = document.querySelectorAll('[data-home-banner-rotator]');
            if (!rotators.length) return;

            const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            rotators.forEach((rotator) => {
                const slides = Array.from(rotator.querySelectorAll('[data-banner-slide]'));
                if (!slides.length) return;

                const stage = rotator.querySelector('[data-home-banner-stage]');
                const dots = Array.from(rotator.querySelectorAll('[data-banner-dot]'));
                const prevButton = rotator.querySelector('[data-banner-prev]');
                const nextButton = rotator.querySelector('[data-banner-next]');
                const currentEl = rotator.querySelector('[data-banner-current]');

                let index = Math.max(0, slides.findIndex((slide) => slide.classList.contains('is-active')));
                if (index < 0) index = 0;

                let timer = null;
                let paused = false;
                const intervalMs = Number(rotator.getAttribute('data-interval') || 7000);

                const syncHeight = () => {
                    if (!stage) return;
                    const active = slides[index];
                    if (!active) return;
                    const activeHeight = Math.max(active.scrollHeight || 0, active.offsetHeight || 0);
                    const nextHeight = Math.max(activeHeight, parseInt(getComputedStyle(stage).minHeight, 10) || 0);
                    stage.style.height = `${nextHeight}px`;
                };

                const updateUi = () => {
                    slides.forEach((slide, slideIndex) => {
                        const isActive = slideIndex === index;
                        slide.classList.toggle('is-active', isActive);
                        slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
                    });

                    dots.forEach((dot, dotIndex) => {
                        dot.setAttribute('aria-current', dotIndex === index ? 'true' : 'false');
                    });

                    if (currentEl) {
                        currentEl.textContent = String(index + 1);
                    }

                    requestAnimationFrame(syncHeight);
                };

                rotator.querySelectorAll('.home-banner-image').forEach((img) => {
                    if (img.complete) return;
                    img.addEventListener('load', syncHeight);
                    img.addEventListener('error', syncHeight);
                });

                const goTo = (nextIndex) => {
                    if (slides.length <= 1) return;
                    index = (nextIndex + slides.length) % slides.length;
                    updateUi();
                };

                const stopAuto = () => {
                    if (timer) {
                        clearInterval(timer);
                        timer = null;
                    }
                };

                const startAuto = () => {
                    if (prefersReducedMotion || slides.length <= 1 || paused) return;
                    stopAuto();
                    timer = setInterval(() => goTo(index + 1), intervalMs);
                };

                prevButton?.addEventListener('click', () => {
                    goTo(index - 1);
                    startAuto();
                });

                nextButton?.addEventListener('click', () => {
                    goTo(index + 1);
                    startAuto();
                });

                dots.forEach((dot) => {
                    dot.addEventListener('click', () => {
                        const target = Number(dot.getAttribute('data-index') || 0);
                        goTo(target);
                        startAuto();
                    });
                });

                rotator.addEventListener('mouseenter', () => {
                    paused = true;
                    stopAuto();
                });

                rotator.addEventListener('mouseleave', () => {
                    paused = false;
                    startAuto();
                });

                rotator.addEventListener('focusin', () => {
                    paused = true;
                    stopAuto();
                });

                rotator.addEventListener('focusout', (event) => {
                    const nextFocused = event.relatedTarget;
                    if (nextFocused instanceof Node && rotator.contains(nextFocused)) return;
                    paused = false;
                    startAuto();
                });

                document.addEventListener('visibilitychange', () => {
                    if (document.hidden) {
                        stopAuto();
                    } else {
                        startAuto();
                    }
                });

                window.addEventListener('resize', syncHeight, { passive: true });
                window.addEventListener('load', syncHeight, { once: true });

                updateUi();
                startAuto();
            });
        })();
    </script>
@endpush
