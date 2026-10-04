<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('titre', 'Administration') — {{ config('acrest.nom') }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link href="{{ asset('vendor/fonts/fonts.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/app.css') }}?v=1" rel="stylesheet">
</head>
<body class="admin-body">
@php
    $enAttente = app(\App\Repositories\Contracts\PaiementRepositoryInterface::class)->compterEnAttente();
    $liens = [
        ['admin.dashboard', 'bi-speedometer2', 'Tableau de bord', null],
        ['admin.inscriptions.*', 'bi-people', 'Inscriptions', null],
        ['admin.paiements.*', 'bi-cash-coin', 'Paiements', $enAttente],
        ['admin.newsletter.*', 'bi-envelope-paper', 'Newsletter', null],
        ['admin.compte', 'bi-shield-lock', 'Mon compte', null],
    ];
@endphp
<aside class="admin-sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="admin-menu" aria-label="Menu d'administration">
    <div class="offcanvas-header">
        <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="offcanvas" data-bs-target="#admin-menu" aria-label="Fermer"></button>
    </div>
    <div class="d-flex flex-column h-100 p-3">
        <a href="{{ route('admin.dashboard') }}" class="marque mb-4 text-white">
            @include('partials.logo', ['taille' => 36])
            <span><span class="marque-nom text-white">ACREST</span><span class="marque-sous text-white-50">Administration</span></span>
        </a>
        <nav class="nav flex-column gap-1">
            @foreach ($liens as [$route, $icone, $libelle, $badge])
                <a class="nav-link {{ request()->routeIs($route) ? 'active' : '' }}" href="{{ route(str_ends_with($route, '.*') ? str_replace('.*', '.index', $route) : $route) }}">
                    <i class="bi {{ $icone }}"></i> {{ $libelle }}
                    @if ($badge)<span class="badge rounded-pill text-bg-warning">{{ $badge }}</span>@endif
                </a>
            @endforeach
        </nav>
        <div class="mt-auto pt-4">
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
        <span class="ms-auto small text-gris d-none d-sm-inline"><i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}</span>
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
