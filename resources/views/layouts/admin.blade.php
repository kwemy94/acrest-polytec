<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('titre', 'Administration') — {{ config('acrest.nom') }}</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link href="{{ asset('vendor/fonts/fonts.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/app.css') }}?v=5" rel="stylesheet">
</head>
<body class="admin-body">
@php
    $admin = auth()->user()->estAdmin();
    $circulation = app(\App\Repositories\Contracts\EmpruntRepositoryInterface::class)->compteurs();
    $aTraiter = ($circulation['demandes'] + $circulation['retards']) ?: null;

    // Pages de l'espace bibliothèque : elles ont leur propre menu latéral.
    // Le bibliothécaire n'a accès qu'à cet espace : il voit toujours ce menu.
    $espaceBibliotheque = ! $admin || request()->routeIs(
        'admin.bibliotheque', 'admin.documents.*', 'admin.exemplaires.*', 'admin.localisations.*',
        'admin.adherents.*', 'admin.emprunts.*', 'admin.ressources.*', 'admin.journal', 'admin.referentiels',
    );

    // [route active, route cible, icône, libellé, pastille]
    $sections = $espaceBibliotheque
        ? array_filter([
            '' => [
                ['admin.bibliotheque', 'admin.bibliotheque', 'bi-speedometer2', 'Tableau de bord', null],
            ],
            'Catalogue' => [
                ['admin.documents.*', 'admin.documents.index', 'bi-book', 'Documents', null],
                ['admin.exemplaires.*', 'admin.exemplaires.index', 'bi-upc', 'Exemplaires', null],
                ['admin.localisations.*', 'admin.localisations.index', 'bi-diagram-3', 'Localisations', null],
            ],
            'Circulation' => [
                ['admin.emprunts.create', 'admin.emprunts.create', 'bi-box-arrow-right', 'Nouveau prêt', null],
                ['admin.emprunts.retours', 'admin.emprunts.retours', 'bi-box-arrow-in-left', 'Retours', null],
                ['admin.emprunts.index', 'admin.emprunts.index', 'bi-arrow-left-right', 'Prêts et demandes', $aTraiter],
                ['admin.adherents.*', 'admin.adherents.index', 'bi-person-vcard', 'Adhérents', null],
            ],
            'Suivi' => [
                ['admin.journal', 'admin.journal', 'bi-clock-history', 'Historique', null],
                $admin ? ['admin.referentiels', 'admin.referentiels', 'bi-sliders', 'Types, catégories, prêt', null] : null,
            ],
            'Compte' => $admin ? [] : [
                ['admin.compte', 'admin.compte', 'bi-shield-lock', 'Mon profil', null],
            ],
        ])
        : [
            '' => [
                ['admin.dashboard', 'admin.dashboard', 'bi-speedometer2', 'Tableau de bord', null],
                ['admin.bibliotheque', 'admin.bibliotheque', 'bi-book-half', 'Bibliothèque', $aTraiter],
            ],
            'Établissement' => [
                ['admin.inscriptions.*', 'admin.inscriptions.index', 'bi-people', 'Inscriptions', null],
                ['admin.paiements.*', 'admin.paiements.index', 'bi-cash-coin', 'Paiements', app(\App\Repositories\Contracts\PaiementRepositoryInterface::class)->compterEnAttente()],
                ['admin.newsletter.*', 'admin.newsletter.index', 'bi-envelope-paper', 'Newsletter', null],
            ],
            'Administration' => [
                ['admin.utilisateurs.*', 'admin.utilisateurs.index', 'bi-person-gear', 'Utilisateurs', null],
                ['admin.compte', 'admin.compte', 'bi-shield-lock', 'Mon profil', null],
            ],
        ];
@endphp
<aside class="admin-sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="admin-menu" aria-label="Menu d'administration">
    <div class="offcanvas-header">
        <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="offcanvas" data-bs-target="#admin-menu" aria-label="Fermer"></button>
    </div>
    <div class="d-flex flex-column h-100 p-3 overflow-hidden">
        <a href="{{ route($espaceBibliotheque ? 'admin.bibliotheque' : 'admin.dashboard') }}" class="marque mb-3 text-white">
            @include('partials.logo', ['taille' => 40, 'fond' => true])
            <span><span class="marque-nom text-white">ACREST</span><span class="marque-sous text-white-50">{{ $espaceBibliotheque ? 'Bibliothèque' : 'Administration' }}</span></span>
        </a>
        @if ($espaceBibliotheque && $admin)
            <a href="{{ route('admin.dashboard') }}" class="nav-link sidebar-retour mb-2"><i class="bi bi-arrow-left"></i> Administration générale</a>
        @endif
        <nav class="nav flex-column gap-1 menu-lateral">
            @foreach ($sections as $titre => $liens)
                @if ($titre !== '')
                    <div class="small text-uppercase text-white-50 fw-semibold mt-3 mb-1 px-3" style="font-size:.7rem;letter-spacing:.08em">{{ $titre }}</div>
                @endif
                @foreach (array_filter($liens) as [$actif, $cible, $icone, $libelle, $badge])
                    <a class="nav-link {{ request()->routeIs($actif) ? 'active' : '' }}" href="{{ route($cible) }}" @if (request()->routeIs($actif)) aria-current="page" @endif>
                        <i class="bi {{ $icone }}"></i> {{ $libelle }}
                        @if ($badge)<span class="badge rounded-pill text-bg-warning">{{ $badge }}</span>@endif
                    </a>
                @endforeach
            @endforeach
        </nav>
        <div class="flex-shrink-0 pt-3 mt-2 border-top border-light border-opacity-10">
            <a href="{{ route('accueil') }}" class="nav-link" target="_blank"><i class="bi bi-box-arrow-up-right"></i> Voir le site</a>
            <form method="post" action="{{ route('admin.logout') }}">
                @csrf
                <button class="nav-link w-100 border-0 bg-transparent text-start"><i class="bi bi-box-arrow-left"></i> Se déconnecter</button>
            </form>
        </div>
    </div>
</aside>

<div class="admin-main">
    <header class="admin-topbar px-3 px-lg-4 py-3 d-flex align-items-center gap-3">
        <button class="btn btn-outline-primary btn-sm d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#admin-menu" aria-controls="admin-menu" aria-label="Ouvrir le menu"><i class="bi bi-list"></i></button>
        <h1 class="h4 mb-0">@yield('titre', 'Administration')</h1>
        <span class="ms-auto small text-gris d-none d-sm-inline"><i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }} · {{ auth()->user()->role->libelle() }}</span>
    </header>
    <main class="p-3 p-lg-4">
        @include('partials.flash')
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        @endif
        @yield('contenu')
    </main>
</div>

<script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}?v=1"></script>
</body>
</html>
