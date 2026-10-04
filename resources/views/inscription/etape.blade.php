@extends('layouts.app')
@section('titre', 'Inscription — '.$etape->titre())

@section('contenu')
@php
    $total = count($etapes);
    $progression = ($etape->numero() - 1) / ($total - 1);
@endphp
<div class="inscription-fond pt-4 pt-md-5 pb-5">
    <div class="container" style="max-width: 860px">
        {{-- Stepper : les étapes déjà remplies restent cliquables pour corriger --}}
        <nav aria-label="Progression de l'inscription">
            <ol class="stepper" style="--progression: {{ $progression }}">
                @foreach ($etapes as $e)
                    @php
                        $classe = $e === $etape ? 'courante' : ($wizard->estCompletee($e) ? 'faite' : '');
                        $cliquable = $e !== $etape && $wizard->estAccessible($e);
                    @endphp
                    <li class="{{ $classe }}">
                        @if ($cliquable)
                            <a class="pastille" href="{{ route('inscription.etape', $e->value) }}" aria-label="Étape {{ $e->numero() }} : {{ $e->titre() }}">
                                <i class="bi {{ $wizard->estCompletee($e) ? 'bi-check-lg' : $e->icone() }}"></i>
                            </a>
                        @else
                            <span class="pastille" @if ($e === $etape) aria-current="step" @endif>
                                <i class="bi {{ $e->icone() }}"></i>
                            </span>
                        @endif
                        <span class="libelle">{{ $e->titre() }}</span>
                    </li>
                @endforeach
            </ol>
        </nav>

        <div class="carte-form mt-4 mt-sm-5">
            <div class="carte-form-corps">
                <header class="etape-entete">
                    <i class="bi {{ $etape->icone() }}" aria-hidden="true"></i>
                    <div>
                        <span class="etape-compteur">Étape {{ $etape->numero() }} sur {{ $total }}</span>
                        <h1>{{ $etape->titre() }}</h1>
                        <p>{{ $etape->description() }}</p>
                    </div>
                </header>

                @include('partials.flash')

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <strong>{{ $errors->count() > 1 ? $errors->count().' champs sont à corriger.' : 'Un champ est à corriger.' }}</strong>
                        Les erreurs sont indiquées sous chaque champ.
                    </div>
                @endif

                @include('inscription.etapes.'.$etape->value)
            </div>
        </div>

        <p class="text-center text-gris small mt-4 no-print">
            Vos réponses sont conservées pendant la saisie.
            <a href="{{ route('inscription.recommencer') }}" class="ms-1">Tout recommencer</a>
        </p>
    </div>
</div>
@endsection
