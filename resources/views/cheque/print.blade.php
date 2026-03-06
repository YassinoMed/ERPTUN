@extends('layouts.admin')

@section('page-title')
    {{__('Cheque Print')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('cheques.index')}}">{{__('Cheques')}}</a></li>
    <li class="breadcrumb-item">{{__('Print')}}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex">
        <button type="button" class="btn btn-sm btn-primary cheque-print-button" onclick="window.print()">
            <i class="ti ti-printer"></i>
        </button>
    </div>
@endsection

@push('css-page')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Source+Sans+3:wght@400;600;700&display=swap');

        :root {
            --cheque-bg-1: #bfe7df;
            --cheque-bg-2: #d9f4ee;
            --cheque-border: #2e7d73;
            --cheque-border-soft: rgba(46, 125, 115, 0.4);
            --cheque-ink: #0f2f2b;
            --cheque-ink-soft: rgba(15, 47, 43, 0.7);
            --cheque-accent: #1c9a8c;
            --cheque-stamp: rgba(26, 132, 121, 0.15);
        }

        .cheque-print-wrapper {
            display: flex;
            justify-content: center;
            padding: 12px 0 24px;
        }

        .cheque-canvas {
            width: min(100%, 980px);
            aspect-ratio: 210 / 100;
            position: relative;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 12px 40px rgba(15, 47, 43, 0.12);
            background: #ffffff;
        }

        .cheque-svg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            display: block;
        }

        .cheque-layer {
            position: absolute;
            inset: 0;
            z-index: 2;
            pointer-events: none;
        }

        .cheque-field,
        .cheque-label {
            position: absolute;
            color: var(--cheque-ink);
            line-height: 1.2;
            letter-spacing: 0.2px;
            font-family: 'Source Sans 3', 'Segoe UI', sans-serif;
        }

        .cheque-label {
            font-size: clamp(10px, 1.1vw, 12px);
            color: var(--cheque-ink-soft);
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .cheque-field {
            font-size: clamp(12px, 1.3vw, 15px);
            font-weight: 600;
        }

        .cheque-field.multiline {
            white-space: normal;
        }

        .cheque-field.ar {
            font-family: 'Amiri', 'Noto Naskh Arabic', 'Tahoma', serif;
            font-weight: 700;
            direction: rtl;
        }

        .cheque-bank-name {
            top: 6%;
            right: 5.714%;
            text-align: right;
            max-width: 38.095%;
        }

        .cheque-bank-agency {
            top: 13.5%;
            right: 5.714%;
            text-align: right;
            max-width: 38.095%;
            font-size: clamp(11px, 1.2vw, 13px);
            color: var(--cheque-ink-soft);
        }

        .cheque-number {
            top: 12%;
            left: 38.095%;
            min-width: 19.048%;
            text-align: center;
        }

        .cheque-payee {
            top: 24%;
            left: 8.571%;
            max-width: 61.905%;
        }

        .cheque-payee-ar {
            top: 21.5%;
            right: 8.571%;
            text-align: right;
            font-size: clamp(12px, 1.4vw, 16px);
        }

        .cheque-amount-words {
            top: 34%;
            left: 8.571%;
            width: 71.428%;
            font-size: clamp(11px, 1.2vw, 14px);
            font-weight: 500;
        }

        .cheque-amount {
            top: 34%;
            right: 5.714%;
            min-width: 16.667%;
            text-align: right;
            font-size: clamp(12px, 1.4vw, 16px);
        }

        .cheque-date {
            top: 62%;
            right: 8.571%;
            min-width: 19.048%;
            text-align: right;
            font-weight: 600;
        }

        .cheque-expiration-date {
            top: 68%;
            right: 8.571%;
            min-width: 19.048%;
            text-align: right;
            font-weight: 600;
        }

        .cheque-place-issue {
            top: 74%;
            right: 8.571%;
            min-width: 19.048%;
            text-align: right;
            font-weight: 600;
        }

        .cheque-bank-account {
            top: 58%;
            left: 8.571%;
            max-width: 57.143%;
            font-size: clamp(11px, 1.2vw, 13px);
        }

        .cheque-rib {
            top: 68%;
            left: 8.571%;
            max-width: 57.143%;
            letter-spacing: 1.5px;
        }

        .cheque-chequebook {
            top: 77%;
            left: 8.571%;
            max-width: 57.143%;
            font-size: clamp(10px, 1.1vw, 12px);
            color: var(--cheque-ink-soft);
        }

        .cheque-signature {
            top: 56%;
            right: 8.571%;
            width: 26.19%;
            height: 20%;
            border: 1.5px solid var(--cheque-border);
            border-radius: 4px;
        }

        .cheque-ceiling {
            top: 6%;
            left: 8.571%;
            max-width: 28.571%;
            font-size: clamp(11px, 1.2vw, 13px);
            color: var(--cheque-accent);
        }

        .cheque-ceiling-ar {
            top: 9%;
            left: 8.571%;
            max-width: 28.571%;
            font-size: clamp(11px, 1.2vw, 13px);
            color: var(--cheque-accent);
            direction: rtl;
        }

        .cheque-payable-to {
            top: 45%;
            left: 8.571%;
            max-width: 30%;
        }

        .cheque-address {
            top: 50%;
            left: 8.571%;
            max-width: 30%;
        }

        .cheque-street {
            top: 55%;
            left: 8.571%;
            max-width: 30%;
        }

        .cheque-phone {
            top: 60%;
            left: 8.571%;
            max-width: 30%;
        }

        .cheque-qr-placeholder {
            top: 6%;
            right: 45%;
            width: 8%;
            height: 16%;
            background: #000000;
            color: #ffffff;
            text-align: center;
            font-size: clamp(8px, 0.9vw, 10px);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cheque-print-button {
            background: linear-gradient(135deg, #3db6a8 0%, #1c9a8c 100%);
            border: none;
            color: #ffffff;
            box-shadow: 0 8px 20px rgba(28, 154, 140, 0.25);
        }

        .cheque-style-guide {
            margin: 16px auto 0;
            max-width: 980px;
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e6efe9;
            box-shadow: 0 10px 24px rgba(15, 47, 43, 0.08);
            padding: 16px 20px;
            font-family: 'Source Sans 3', 'Segoe UI', sans-serif;
            color: #163a34;
        }

        .cheque-style-guide h5 {
            margin: 0 0 8px;
            font-weight: 700;
        }

        .cheque-style-guide ul {
            margin: 0;
            padding-left: 18px;
        }

        @media print {
            .pc-sidebar,
            .pc-header,
            .page-header,
            .action-btn-col,
            .breadcrumb,
            .loader-bg,
            .toast,
            .modal,
            .card,
            .cheque-style-guide {
                display: none !important;
            }
            .dash-container,
            .dash-content {
                margin: 0 !important;
                padding: 0 !important;
            }
            .cheque-canvas {
                width: 210mm;
                height: 100mm;
                box-shadow: none;
                border-radius: 0;
                border: none;
            }
        }
    </style>
@endpush

@section('content')
    <div class="cheque-print-wrapper">
        <div class="cheque-canvas">
            <svg class="cheque-svg" viewBox="0 0 2100 1000" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <defs>
                    <linearGradient id="chequeBg" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0" stop-color="#bfe7df"/>
                        <stop offset="1" stop-color="#d9f4ee"/>
                    </linearGradient>
                    <linearGradient id="chequeHeader" x1="0" y1="0" x2="1" y2="0">
                        <stop offset="0" stop-color="#a6e0d6"/>
                        <stop offset="1" stop-color="#bfece4"/>
                    </linearGradient>
                </defs>
                <rect x="0" y="0" width="2100" height="1000" rx="28" fill="url(#chequeBg)"/>
                <rect x="18" y="18" width="2064" height="964" rx="24" fill="none" stroke="#2e7d73" stroke-width="6"/>
                <rect x="60" y="60" width="1980" height="140" rx="18" fill="url(#chequeHeader)" stroke="#2e7d73" stroke-width="3"/>
                <polygon points="60,60 260,60 160,200 60,200" fill="#a4ddd2" stroke="#2e7d73" stroke-width="3"/>
                <circle cx="200" cy="120" r="44" fill="rgba(26,132,121,0.15)" stroke="#2e7d73" stroke-width="2"/>
                <rect x="60" y="250" width="1980" height="6" fill="#2e7d73" opacity="0.35"/>
                <rect x="60" y="760" width="1980" height="6" fill="#2e7d73" opacity="0.25"/>

                <rect x="90" y="560" width="520" height="260" rx="16" fill="rgba(255,255,255,0.4)" stroke="#2e7d73" stroke-width="3"/>
                <rect x="1460" y="540" width="520" height="240" rx="16" fill="rgba(255,255,255,0.55)" stroke="#2e7d73" stroke-width="3"/>
                <rect x="90" y="330" width="1220" height="120" rx="12" fill="rgba(255,255,255,0.4)" stroke="#2e7d73" stroke-width="2"/>
                <rect x="1460" y="330" width="520" height="120" rx="12" fill="rgba(255,255,255,0.5)" stroke="#2e7d73" stroke-width="2"/>

                <rect x="60" y="60" width="120" height="880" fill="#8fc7bc" opacity="0.35"/>
                <rect x="1920" y="60" width="120" height="880" fill="#8fc7bc" opacity="0.2"/>
                <line x1="120" y1="410" x2="1280" y2="410" stroke="#2e7d73" stroke-width="2" stroke-dasharray="8 6" opacity="0.5"/>
                <line x1="1500" y1="410" x2="1960" y2="410" stroke="#2e7d73" stroke-width="2" stroke-dasharray="8 6" opacity="0.5"/>
                <line x1="120" y1="690" x2="580" y2="690" stroke="#2e7d73" stroke-width="2" stroke-dasharray="6 6" opacity="0.5"/>
                <line x1="120" y1="450" x2="1280" y2="450" stroke="#2e7d73" stroke-width="1.5" stroke-dasharray="4 6" opacity="0.35"/>
                <line x1="120" y1="490" x2="1280" y2="490" stroke="#2e7d73" stroke-width="1.5" stroke-dasharray="4 6" opacity="0.35"/>
                <rect x="160" y="540" width="420" height="160" fill="none" stroke="#2e7d73" stroke-width="1.5" opacity="0.35"/>
                <line x1="160" y1="572" x2="580" y2="572" stroke="#2e7d73" stroke-width="1" opacity="0.35"/>
                <line x1="160" y1="604" x2="580" y2="604" stroke="#2e7d73" stroke-width="1" opacity="0.35"/>
                <line x1="160" y1="636" x2="580" y2="636" stroke="#2e7d73" stroke-width="1" opacity="0.35"/>
                <line x1="160" y1="668" x2="580" y2="668" stroke="#2e7d73" stroke-width="1" opacity="0.35"/>

                <!-- QR Code Placeholder in SVG -->
                <rect x="1500" y="60" width="120" height="120" fill="#000000"/>
                <text x="1560" y="120" text-anchor="middle" font-size="20" font-family="Source Sans 3, Arial, sans-serif" fill="#ffffff">SCAN ME</text>

                <text x="1060" y="125" text-anchor="middle" font-size="46" font-family="Source Sans 3, Arial, sans-serif" fill="#1a6a61" letter-spacing="2">CHÈQUE</text>
                <text x="1060" y="175" text-anchor="middle" font-size="28" font-family="Amiri, serif" fill="#1a6a61">نموذج شيك للواجهة فقط</text>

                <text x="320" y="145" font-size="22" font-family="Source Sans 3, Arial, sans-serif" fill="#1a6a61">Chèque N°</text>
                <text x="120" y="210" font-size="16" font-family="Source Sans 3, Arial, sans-serif" fill="#1a6a61">Non endossable</text>
                <text x="1600" y="145" font-size="22" font-family="Source Sans 3, Arial, sans-serif" fill="#1a6a61">BANQUE</text>
                <text x="1860" y="145" text-anchor="end" font-size="18" font-family="Source Sans 3, Arial, sans-serif" fill="#1a6a61">EXEMPLE UI</text>

                <text x="120" y="310" font-size="22" font-family="Source Sans 3, Arial, sans-serif" fill="#1a6a61">À l'ordre de</text>
                <text x="1680" y="310" text-anchor="middle" font-size="22" font-family="Amiri, serif" fill="#1a6a61">أمر</text>

                <!-- Additional fields labels -->
                <text x="120" y="450" font-size="18" font-family="Source Sans 3, Arial, sans-serif" fill="#1a6a61">Payable à</text>
                <text x="120" y="500" font-size="18" font-family="Source Sans 3, Arial, sans-serif" fill="#1a6a61">Adresse</text>
                <text x="120" y="550" font-size="18" font-family="Source Sans 3, Arial, sans-serif" fill="#1a6a61">Rue</text>
                <text x="120" y="600" font-size="18" font-family="Source Sans 3, Arial, sans-serif" fill="#1a6a61">Tél.</text>

                <text x="120" y="540" font-size="20" font-family="Source Sans 3, Arial, sans-serif" fill="#1a6a61">Numéro de compte</text>
                <text x="120" y="620" font-size="20" font-family="Source Sans 3, Arial, sans-serif" fill="#1a6a61">RIB</text>
                <text x="120" y="720" font-size="20" font-family="Source Sans 3, Arial, sans-serif" fill="#1a6a61">Chéquier</text>

                <text x="1520" y="530" font-size="20" font-family="Source Sans 3, Arial, sans-serif" fill="#1a6a61">Signature</text>
                <text x="1060" y="820" text-anchor="middle" font-size="18" font-family="Source Sans 3, Arial, sans-serif" fill="#1a6a61">Payable contre ce chèque uniquement</text>
                <text x="1700" y="310" font-size="20" font-family="Source Sans 3, Arial, sans-serif" fill="#1a6a61">Montant</text>
                <text x="1620" y="760" font-size="20" font-family="Source Sans 3, Arial, sans-serif" fill="#1a6a61">Date</text>

                <!-- Expiration date label -->
                <text x="1620" y="820" font-size="20" font-family="Source Sans 3, Arial, sans-serif" fill="#1a6a61">Date d'expiration</text>

                <!-- Place of issue label -->
                <text x="1620" y="880" font-size="20" font-family="Source Sans 3, Arial, sans-serif" fill="#1a6a61">Lieu d'émission</text>

                <!-- Ceiling label -->
                <text x="120" y="120" font-size="22" font-family="Source Sans 3, Arial, sans-serif" fill="#1a6a61">Plafond du chèque: 30 000 TND</text>
                <text x="120" y="160" font-size="22" font-family="Amiri, serif" fill="#1a6a61">سقف الشيك: 30 000 د.ت</text>

                <text x="1050" y="900" text-anchor="middle" font-size="110" font-family="Source Sans 3, Arial, sans-serif" fill="rgba(15,47,43,0.08)" letter-spacing="6">SPECIMEN</text>
                <text x="1060" y="960" text-anchor="middle" font-size="14" font-family="Source Sans 3, Arial, sans-serif" fill="rgba(15,47,43,0.5)">Maquette UI non officielle</text>
            </svg>

            <div class="cheque-layer">
                <div class="cheque-field cheque-bank-name">{{ $cheque->bank_name ?? '' }}</div>
                <div class="cheque-field cheque-bank-agency">{{ $cheque->bank_agency ?? '' }}</div>
                <div class="cheque-field cheque-number">{{ $cheque->cheque_number ?? '' }}</div>
                <div class="cheque-field cheque-payee">{{ $cheque->beneficiary_name ?? '' }}</div>
                <div class="cheque-field cheque-payee-ar ar">{{ $cheque->beneficiary_name ?? '' }}</div>
                <div class="cheque-field cheque-amount-words multiline">{{ $cheque->amount_text ?? '' }}</div>
                <div class="cheque-field cheque-amount">{{ \Auth::user()->priceFormat($cheque->amount) }} {{ $cheque->currency }}</div>
                <div class="cheque-field cheque-date">{{ \Auth::user()->dateFormat($cheque->issue_date) }}</div>
                <div class="cheque-field cheque-expiration-date">{{ \Auth::user()->dateFormat($cheque->expiration_date ?? '') }}</div>
                <div class="cheque-field cheque-place-issue">{{ $cheque->place_of_issue ?? 'Tunis' }}</div>
                <div class="cheque-field cheque-bank-account">{{ $cheque->bank_account ?? '' }}</div>
                <div class="cheque-field cheque-rib">{{ $cheque->rib ?? '' }}</div>
                <div class="cheque-field cheque-chequebook">{{ $cheque->chequebook_number ?? '' }}</div>
                <div class="cheque-field cheque-signature"></div>
                <div class="cheque-field cheque-ceiling">Plafond du chèque : 30 000 TND</div>
                <div class="cheque-field cheque-ceiling-ar ar">سقف الشيك: 30 000 د.ت</div>
                <div class="cheque-field cheque-payable-to">{{ $cheque->payable_to ?? '' }}</div>
                <div class="cheque-field cheque-address">{{ $cheque->address ?? '' }}</div>
                <div class="cheque-field cheque-street">{{ $cheque->street ?? '' }}</div>
                <div class="cheque-field cheque-phone">{{ $cheque->phone ?? '' }}</div>
                <!-- QR Placeholder (optional, since in SVG) -->
                <div class="cheque-qr-placeholder">QR Code</div>
            </div>
        </div>
    </div>

    <section class="cheque-style-guide">
        <h5>Guide de style — Chèque tunisien</h5>
        <ul>
            <li>Palette: #bfe7df → #d9f4ee (dégradé), accents #2e7d73, encre #0f2f2b.</li>
            <li>Typographie: Source Sans 3 (latin, 400–700), Amiri (arabe, 700), tracking 0.2–0.8px.</li>
            <li>Effets: bordure 6px, encadrés avec 40–55% d’opacité, ombre 0 12px 40px.</li>
            <li>Grille: format 210×100 (ratio 2.1:1), positions en pourcentage pour rendu responsive.</li>
            <li>Usage: gabarit fictif UI, sans éléments de sécurité ni références officielles. Mis à jour pour le nouveau modèle 2025 avec QR, plafond, expiration.</li>
        </ul>
    </section>
@endsection