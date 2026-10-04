@extends('mail.layout')
@section('entete', 'Bibliothèque ACREST Polytechnique')

@use('App\Enums\EvenementEmprunt', 'E')
@php
    $e = $emprunt;
    $doc = $e->document;
    $date = fn ($d) => $d?->translatedFormat('l d F Y');
    $encart = 'background:#FFF6DB;border-left:4px solid #F2B21B;border-radius:6px;padding:12px 16px;margin:20px 0';
    $bouton = 'background:#F2B21B;color:#142019;text-decoration:none;font-weight:700;padding:12px 22px;border-radius:8px';
@endphp

@section('contenu')
<p style="margin-top:0">Bonjour {{ $e->inscription->prenom ?: $e->inscription->nom }},</p>

@switch($evenement)
    @case(E::Demande)
        <p>Nous avons bien reçu votre demande d'emprunt. La bibliothèque va la traiter et vous recevrez un e-mail dès qu'un exemplaire sera mis de côté pour vous.</p>
        @break
    @case(E::Reserve)
        <p>Bonne nouvelle : un exemplaire est mis de côté pour vous.</p>
        <p style="{{ $encart }}">Retirez-le au comptoir de la bibliothèque <strong>avant le {{ $date($e->retirer_avant) }}</strong> ({{ config('acrest.bibliotheque.horaires') }}), muni de votre code d'inscription <strong>{{ $e->inscription->code }}</strong>. Passé ce délai, la réservation sera annulée.</p>
        @break
    @case(E::Refuse)
        <p>Votre demande d'emprunt n'a pas pu être acceptée.</p>
        @if ($e->motif)<p style="{{ $encart }}"><strong>Motif :</strong> {{ $e->motif }}</p>@endif
        @break
    @case(E::Remis)
        <p>Votre emprunt est enregistré. Merci de prendre soin du document.</p>
        <p style="{{ $encart }}">Date de retour : <strong>{{ $date($e->date_retour_prevue) }}</strong></p>
        @break
    @case(E::Prolonge)
        <p>Votre emprunt a été prolongé.</p>
        <p style="{{ $encart }}">Nouvelle date de retour : <strong>{{ $date($e->date_retour_prevue) }}</strong></p>
        @break
    @case(E::Rendu)
        <p>Le retour de votre document a bien été enregistré le {{ $date($e->date_retour) }}. Merci !</p>
        @if ($e->joursDeRetard())<p style="{{ $encart }}">Ce document a été rendu avec {{ $e->joursDeRetard() }} jour(s) de retard. Pensez à respecter les dates de retour pour que chacun puisse en profiter.</p>@endif
        @break
    @case(E::Rappel)
        <p>Petit rappel : le document ci-dessous est à rapporter bientôt.</p>
        <p style="{{ $encart }}">Date de retour : <strong>{{ $date($e->date_retour_prevue) }}</strong></p>
        @if ($e->peutEtreProlonge())<p>Besoin de plus de temps ? Vous pouvez demander une prolongation depuis votre espace, si personne n'attend ce document.</p>@endif
        @break
    @case(E::Retard)
        <p>Le document ci-dessous devait être rendu le <strong>{{ $date($e->date_retour_prevue) }}</strong>. Il a aujourd'hui <strong>{{ $e->joursDeRetard() }} jour(s) de retard</strong>.</p>
        <p style="{{ $encart }}">Merci de le rapporter au plus vite à la bibliothèque. Tant qu'il n'est pas rendu, vous ne pouvez pas faire de nouvelle demande.</p>
        @break
    @case(E::Expire)
        <p>Le document mis de côté pour vous n'a pas été retiré à temps : la réservation est annulée et l'exemplaire remis à disposition des autres étudiants. Vous pouvez refaire une demande depuis le catalogue.</p>
        @break
@endswitch

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #DDE6DF;border-radius:8px;margin:20px 0">
    <tr><td style="padding:14px 16px">
        <div style="font-weight:700;font-size:16px">{{ $doc->titre }}</div>
        <div style="color:#5C6B62;font-size:14px">{{ $doc->auteurs }}{{ $doc->cote ? ' · Cote '.$doc->cote : '' }}</div>
        @if ($e->exemplaire)<div style="color:#5C6B62;font-size:14px">Exemplaire {{ $e->exemplaire->code }}</div>@endif
    </td></tr>
</table>

<p style="text-align:center;margin:28px 0">
    <a href="{{ route('bibliotheque.emprunts') }}" style="{{ $bouton }}">Voir mes emprunts</a>
</p>
@endsection

@section('pied', 'Bibliothèque ouverte '.config('acrest.bibliotheque.horaires').'. Contact : '.config('acrest.contact.telephone').' — '.config('acrest.contact.email'))
