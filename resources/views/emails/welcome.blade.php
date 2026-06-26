@extends('emails.layout')

@section('content')
<p style="font-size:18px;color:#fff;font-weight:700;margin:0 0 12px;">Bienvenue, {{ $user->name }} !</p>
<p>Votre espace <strong style="color:#fff;">OPESBooks</strong> est prêt. Vous bénéficiez d'une période d'essai pour découvrir la comptabilité SYSCOHADA, la facturation TVA et la télétransmission DGI.</p>
<p>Commencez en quelques minutes : complétez votre profil, ajoutez un client et créez votre première facture.</p>
@endsection

@section('action')
<a href="{{ url('/onboarding') }}" style="display:inline-block;background:#F59E0B;color:#0B1120;text-decoration:none;font-weight:700;padding:12px 22px;border-radius:8px;font-size:14px;">Configurer mon espace</a>
@endsection
