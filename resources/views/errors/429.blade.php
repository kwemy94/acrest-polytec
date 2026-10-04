@extends('layouts.app')
@section('titre', 'Trop de tentatives')

@section('contenu')
<section class="section">
    <div class="container text-center" style="max-width: 560px">
        <h1 class="h2">Trop de tentatives en peu de temps</h1>
        <p class="text-gris">Pour protéger les dossiers des candidats, patientez une minute avant de réessayer.</p>
        <a href="{{ url()->previous() }}" class="btn btn-primary">Revenir en arrière</a>
    </div>
</section>
@endsection
