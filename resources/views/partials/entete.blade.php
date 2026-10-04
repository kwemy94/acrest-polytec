{{-- En-tête des pages intérieures. Paramètres : titre, intro, image (facultatif), ariane (tableau libellé => url) --}}
<header class="entete {{ !empty($image) ? 'entete-photo' : '' }}" @if (!empty($image)) style="--entete-img: url('{{ asset($image) }}')" @endif>
    <div class="container position-relative">
        @if (!empty($ariane))
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('accueil') }}">Accueil</a></li>
                    @foreach ($ariane as $libelle => $url)
                        @if ($url)
                            <li class="breadcrumb-item"><a href="{{ $url }}">{{ $libelle }}</a></li>
                        @else
                            <li class="breadcrumb-item active" aria-current="page">{{ $libelle }}</li>
                        @endif
                    @endforeach
                </ol>
            </nav>
        @endif
        <h1 class="mb-3">{{ $titre }}</h1>
        @if (!empty($intro))
            <p class="lead texte mb-0">{{ $intro }}</p>
        @endif
        {{ $slot ?? '' }}
    </div>
</header>
