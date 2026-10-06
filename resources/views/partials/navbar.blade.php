<nav class="navbar navbar-expand-lg site-nav sticky-top" aria-label="Navigation principale">
    <div class="container">
        <a class="marque navbar-brand" href="{{ route('accueil') }}">
            @include('partials.logo', ['taille' => 48])
            <span>
                <span class="marque-nom">ACREST Polytechnique</span>
                <span class="marque-sous d-none d-sm-block">Institut supérieur Da Vinci</span>
            </span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#menu" aria-controls="menu" aria-expanded="false" aria-label="Ouvrir le menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1 py-3 py-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('filieres.*', 'specialites.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Formations</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item fw-semibold" href="{{ route('filieres.index') }}">Toutes les filières</a></li>
                        <li><a class="dropdown-item fw-semibold" href="{{ route('specialites.index') }}">Toutes les spécialités</a></li>
                        @if (count($menuFilieres))
                            <li><hr class="dropdown-divider"></li>
                            @foreach ($menuFilieres as $f)
                                <li><a class="dropdown-item" href="{{ route('filieres.show', $f['slug']) }}">{{ $f['nom'] }}</a></li>
                            @endforeach
                        @endif
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('acrest') ? 'active' : '' }}" href="{{ route('acrest') }}">ACREST</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('technologies') ? 'active' : '' }}" href="{{ route('technologies') }}">Technologies</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('logement') ? 'active' : '' }}" href="{{ route('logement') }}">Logement</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('bibliotheque.*') ? 'active' : '' }}" href="{{ route('bibliotheque.index') }}">Bibliothèque</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('dossier.*', 'paiement.*') ? 'active' : '' }}" href="{{ route('dossier.recherche') }}">Mon dossier</a></li>
                <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                    <a class="btn btn-soleil w-100" href="{{ route('inscription.debut') }}"><i class="bi bi-pencil-square me-1"></i> S'inscrire</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
