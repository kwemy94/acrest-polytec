<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('titre')@yield('titre') — @endif{{ config('acrest.nom') }}</title>
    <meta name="description" content="@yield('description', config('acrest.nom_complet').' : '.config('acrest.slogan').'.')">
    <meta name="theme-color" content="#1D4535">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    <link href="{{ asset('vendor/fonts/fonts.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/app.css') }}?v=1" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <a class="visually-hidden-focusable position-absolute top-0 start-0 m-2 btn btn-soleil" href="#contenu">Aller au contenu</a>

    @include('partials.navbar')

    <main id="contenu">
        @yield('contenu')
    </main>

    @include('partials.footer')

    <div class="modal fade modal-photo" id="modal-photo" tabindex="-1" aria-labelledby="modal-photo-titre" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header border-0 px-0">
                    <h2 class="modal-title" id="modal-photo-titre"></h2>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <img src="" alt="" class="w-100">
            </div>
        </div>
    </div>

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}?v=1"></script>
    @stack('scripts')
</body>
</html>
