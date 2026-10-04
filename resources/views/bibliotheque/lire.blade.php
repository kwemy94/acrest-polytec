@extends('layouts.app')
@section('titre', 'Lecture — '.$document->titre)

@php
    // Sans téléchargement autorisé, on masque la barre d'outils du lecteur PDF du navigateur.
    $source = route('bibliotheque.pdf', $document).($document->telechargeable ? '' : '#toolbar=0&navpanes=0');
@endphp

@section('contenu')
<section class="section-sm">
    <div class="container-fluid px-3 px-lg-4">
        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
            <a href="{{ route('bibliotheque.show', $document) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-arrow-left me-1"></i>Retour à la notice</a>
            <div class="me-auto">
                <h1 class="h5 mb-0">{{ $document->titre }}</h1>
                <div class="small text-gris">{{ $document->auteurs }} · {{ $document->langue_libelle }}</div>
            </div>
            @if ($document->telechargeable || auth()->check())
                <a href="{{ route('bibliotheque.telecharger', $document) }}" class="btn btn-sm btn-soleil"><i class="bi bi-download me-1"></i>Télécharger</a>
            @else
                <span class="small text-gris"><i class="bi bi-lock me-1"></i>Lecture en ligne uniquement</span>
            @endif
        </div>

        <iframe src="{{ $source }}" title="{{ $document->titre }}" class="w-100 border rounded bg-white" style="height: calc(100vh - 190px); min-height: 520px"
                @unless ($document->telechargeable) oncontextmenu="return false" @endunless></iframe>

        <p class="small text-gris mt-2 mb-0">
            Le document ne s'affiche pas ? Votre navigateur n'intègre peut-être pas de lecteur PDF :
            @if ($document->telechargeable)
                <a href="{{ route('bibliotheque.telecharger', $document) }}">téléchargez-le</a>.
            @else
                essayez avec un navigateur récent (Chrome, Firefox, Edge) ou consultez-le à la bibliothèque.
            @endif
        </p>
    </div>
</section>
@endsection
