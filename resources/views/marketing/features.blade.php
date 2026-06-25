@extends('layouts.marketing')
@section('title', 'Fonctionnalités — OPESBooks')
@section('description', 'Comptabilité SYSCOHADA, facturation TVA, DSF, DGI, paie CNPS/DIPE, CRM, projets, IA, mobile money et plus.')

@section('content')
<section class="max-w-5xl mx-auto px-5 py-16">
    <div class="text-center">
        <h1 class="text-3xl md:text-5xl font-black">Une plateforme complète</h1>
        <p class="text-white/60 mt-4 max-w-2xl mx-auto">De la saisie comptable à la déclaration fiscale, en passant par la paie, le CRM et l'IA — tout est intégré et pensé pour le Cameroun.</p>
    </div>

    @php $modules = [
        ['Comptabilité SYSCOHADA','Plan comptable OHADA révisé, journaux, grand livre, balance, clôture d\'exercice.'],
        ['Facturation & TVA','Factures et avoirs PDF, TVA 19,25% (17,5% + CAC), devis, bons de commande et de livraison.'],
        ['Fiscalité DGI','Export DSF / D10, moniteur DGI, certification MECeF, patente camerounaise.'],
        ['Paie & Social','Bulletins de paie, cotisations CNPS, IRPP/DIPE, bordereaux.'],
        ['Trésorerie & Banque','Import de relevés, rapprochement bancaire, prévisionnel de trésorerie.'],
        ['CRM & Projets','Pipeline commercial, relances WhatsApp, comptabilité et rentabilité par projet.'],
        ['Intelligence Artificielle','Catégorisation automatique, contrôle DSF, détection d\'anomalies, assistant.'],
        ['Mobile Money & API','MTN MoMo, Orange Money, API REST + webhooks pour vos intégrations.'],
        ['Hors ligne & Multi-société','Saisie sans connexion, synchronisation automatique, gestion multi-entreprises.'],
    ]; @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-12">
        @foreach($modules as $m)
        <div class="glass rounded-xl p-6">
            <div class="font-black text-white">{{ $m[0] }}</div>
            <p class="text-white/50 text-sm mt-1.5 leading-relaxed">{{ $m[1] }}</p>
        </div>
        @endforeach
    </div>
    <div class="text-center mt-12">
        <a href="/login" class="inline-block px-6 py-3.5 rounded-xl text-sm font-black text-[#010048] bg-gold hover:bg-gold-light transition">Essayer gratuitement →</a>
    </div>
</section>
@endsection
