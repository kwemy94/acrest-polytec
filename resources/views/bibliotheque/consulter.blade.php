@extends('layouts.app')
@section('titre', 'Consultation — '.$ressource->document->titre)

@php
    $source = route('bibliotheque.ressources.fichier', $ressource);
    // Sans droit de téléchargement, on masque la barre d'outils du lecteur PDF du navigateur.
    $sourcePdf = $source.($telechargeable ? '' : '#toolbar=0&navpanes=0');
@endphp

@section('contenu')
<section class="section-sm">
    <div class="container-fluid px-3 px-lg-4">
        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
            <a href="{{ route('bibliotheque.show', $ressource->document) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-arrow-left me-1"></i>Retour à la notice</a>
            <div class="me-auto">
                <h1 class="h5 mb-0">{{ $ressource->document->titre }}</h1>
                <div class="small text-gris">{{ $ressource->libelle }} · {{ $ressource->document->noms_auteurs }}</div>
            </div>
            @if ($telechargeable)
                <a href="{{ route('bibliotheque.ressources.telecharger', $ressource) }}" class="btn btn-sm btn-soleil"><i class="bi bi-download me-1"></i>Télécharger</a>
            @else
                <span class="small text-gris"><i class="bi bi-lock me-1"></i>Consultation uniquement</span>
            @endif
        </div>

        @switch($ressource->type)
            @case(\App\Enums\TypeRessource::Pdf)
                <iframe src="{{ $sourcePdf }}" title="{{ $ressource->document->titre }}" class="w-100 border rounded bg-white" style="height: calc(100vh - 190px); min-height: 520px"></iframe>
                @break
            @case(\App\Enums\TypeRessource::Audio)
                <div class="encart text-center p-5">
                    <i class="bi bi-music-note-beamed fs-1 text-foret"></i>
                    <audio controls class="w-100 mt-3" src="{{ $source }}" @unless ($telechargeable) controlsList="nodownload" oncontextmenu="return false" @endunless></audio>
                </div>
                @break
            @case(\App\Enums\TypeRessource::Video)
                <video controls class="w-100 rounded bg-dark" style="max-height: calc(100vh - 190px)" src="{{ $source }}" @unless ($telechargeable) controlsList="nodownload" oncontextmenu="return false" @endunless></video>
                @break
        @endswitch
    </div>
</section>
@endsection
