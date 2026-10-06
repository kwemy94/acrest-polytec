<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Connexion — Administration {{ config('acrest.nom') }}</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link href="{{ asset('vendor/fonts/fonts.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/app.css') }}?v=5" rel="stylesheet">
</head>
<body class="login-fond d-flex align-items-center py-5">
    <div class="container" style="max-width: 440px">
        <div class="carte-form">
            <div class="carte-form-corps">
                <div class="marque mb-4">
                    @include('partials.logo', ['taille' => 56])
                    <span><span class="marque-nom">ACREST Polytechnique</span><span class="marque-sous">Espace administration</span></span>
                </div>
                <h1 class="h3 mb-4">Connexion</h1>
                <form method="post" action="{{ route('admin.login.store') }}" class="needs-validation" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse e-mail</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus autocomplete="username">
                        <div class="invalid-feedback">@error('email'){{ $message }}@else Saisissez votre adresse e-mail. @enderror</div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" id="password" name="password" class="form-control" required autocomplete="current-password">
                        <div class="invalid-feedback">Saisissez votre mot de passe.</div>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                        <label class="form-check-label" for="remember">Rester connecté sur cet appareil</label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100" data-chargement="Connexion…">Se connecter</button>
                </form>
                <a href="{{ route('accueil') }}" class="d-inline-block mt-4 small"><i class="bi bi-arrow-left me-1"></i>Retour au site</a>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/js/app.js') }}?v=1"></script>
</body>
</html>
