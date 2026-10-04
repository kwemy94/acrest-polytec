@extends('layouts.app')
@section('titre', $specialite->nom)
@section('description', Str::limit($specialite->resume, 155))

@section('contenu')
@include('partials.entete', [
    'titre' => $specialite->nom,
    'intro' => null,
    'image' => $specialite->couverture(),
    'ariane' => ['Filières' => route('filieres.index'), $specialite->filiere->nom => route('filieres.show', $specialite->filiere->slug), $specialite->nom => null],
])

<section class="section">
    <div class="container">
        <div class="row g-5">
            <article class="col-lg-8 contenu">
                @if ($specialite->resume)
                    <p class="lead texte mb-4">{{ $specialite->resume }}</p>
                @endif

                @foreach ($specialite->sections ?? [] as $section)
                    <h2>{{ $section['title'] }}</h2>
                    @foreach ($section['blocks'] as $bloc)
                        @switch($bloc['type'])
                            @case('subtitle')
                                <h3>{{ $bloc['text'] }}</h3>
                                @break
                            @case('list')
                                <ul>
                                    @foreach ($bloc['items'] as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                                @break
                            @default
                                <p class="texte">{{ $bloc['text'] }}</p>
                        @endswitch
                    @endforeach
                @endforeach

                @if (count($specialite->images ?? []) > 0)
                    <h2>En images</h2>
                    <div class="row g-3">
                        @foreach ($specialite->images as $i => $image)
                            <div class="{{ count($specialite->images) === 1 ? 'col-12' : 'col-sm-6' }}">
                                <button type="button" class="photo ratio-photo d-block w-100 border-0 p-0" data-bs-toggle="modal" data-bs-target="#modal-photo" data-photo="{{ asset($image) }}" data-legende="{{ $specialite->nom }}" aria-label="Agrandir la photo {{ $i + 1 }}">
                                    <img src="{{ asset($image) }}" alt="{{ $specialite->nom }} — photo {{ $i + 1 }}" loading="lazy">
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </article>

            <aside class="col-lg-4">
                <div class="sticky-encart d-flex flex-column gap-3">
                    <div class="encart">
                        <h2 class="h5"><i class="bi bi-mortarboard me-1 text-foret"></i> Diplôme requis</h2>
                        <p class="mb-3">{{ $specialite->diplome_requis }}</p>
                        <h2 class="h5"><i class="bi bi-clock me-1 text-foret"></i> Durée</h2>
                        <p class="mb-0">2 ans, BTS (bac + 2)</p>
                    </div>
                    <div class="encart encart-foret">
                        <h2 class="h5">Candidater à cette spécialité</h2>
                        <p class="text-white-50 small">Vous la sélectionnerez à l'étape « Formation » du formulaire.</p>
                        <a href="{{ route('inscription.debut') }}" class="btn btn-soleil w-100">Commencer mon inscription</a>
                        @if ($specialite->brochure)
                            <a href="{{ asset($specialite->brochure) }}" class="btn btn-outline-light w-100 mt-2" target="_blank" rel="noopener"><i class="bi bi-download me-1"></i> Brochure (PDF)</a>
                        @endif
                    </div>
                    @if ($voisines->isNotEmpty())
                        <div>
                            <h2 class="h6 text-gris mb-2">Dans la même filière</h2>
                            <div class="d-flex flex-column gap-2">
                                @foreach ($voisines as $v)
                                    @include('partials.ligne-specialite', ['specialite' => $v])
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
