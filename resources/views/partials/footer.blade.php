<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <a class="marque mb-3" href="{{ route('accueil') }}">
                    @include('partials.logo', ['taille' => 52, 'fond' => true])
                    <span class="marque-nom text-white">ACREST Polytechnique</span>
                </a>
                <p class="texte-court">{{ config('acrest.nom_complet') }}. {{ config('acrest.slogan') }}.</p>
                <p class="mb-0"><i class="bi bi-geo-alt me-1"></i> {{ config('acrest.localisation') }}</p>
            </div>
            <div class="col-6 col-lg-2">
                <h2>Formations</h2>
                <ul class="list-unstyled liens">
                    <li><a href="{{ route('filieres.index') }}">Filières</a></li>
                    <li><a href="{{ route('specialites.index') }}">Spécialités</a></li>
                    <li><a href="{{ asset('documents/programme-formation.pdf') }}" target="_blank" rel="noopener">Programme (PDF)</a></li>
                    <li><a href="{{ route('logement') }}">Logement</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h2>Candidats</h2>
                <ul class="list-unstyled liens">
                    <li><a href="{{ route('inscription.debut') }}">S'inscrire</a></li>
                    <li><a href="{{ route('paiement.create') }}">Payer les frais</a></li>
                    <li><a href="{{ route('dossier.recherche') }}">Suivre mon dossier</a></li>
                    <li><a href="{{ route('dossier.retrouver') }}">Code oublié</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h2>Contact</h2>
                <ul class="list-unstyled liens">
                    <li><i class="bi bi-envelope me-2"></i><a href="mailto:{{ config('acrest.contact.email') }}">{{ config('acrest.contact.email') }}</a></li>
                    <li><i class="bi bi-telephone me-2"></i><a href="tel:{{ preg_replace('/\s+/', '', config('acrest.contact.telephone')) }}">{{ config('acrest.contact.telephone') }}</a></li>
                    <li><i class="bi bi-facebook me-2"></i><a href="{{ config('acrest.contact.facebook') }}" target="_blank" rel="noopener">Facebook</a></li>
                </ul>

                <h2 class="mt-4" id="newsletter-titre">Recevoir nos actualités</h2>
                @if (session('newsletter'))
                    <p class="text-white"><i class="bi bi-check-circle me-1"></i> {{ session('newsletter') }}</p>
                @endif
                <form method="post" action="{{ route('newsletter') }}" class="needs-validation" novalidate aria-labelledby="newsletter-titre">
                    @csrf
                    <div class="input-group">
                        <label for="email_newsletter" class="visually-hidden">Adresse e-mail</label>
                        <input type="email" id="email_newsletter" name="email_newsletter" class="form-control @error('email_newsletter', 'newsletter') is-invalid @enderror" placeholder="votre@email.com" required autocomplete="email">
                        <button class="btn btn-soleil" type="submit">S'abonner</button>
                    </div>
                    @error('email_newsletter', 'newsletter')<div class="text-warning small mt-1">{{ $message }}</div>@enderror
                </form>
            </div>
        </div>
        <div class="bas d-flex flex-column flex-md-row justify-content-between gap-2">
            <span>© {{ date('Y') }} ACREST Polytechnique — Centre africain des technologies appropriées et des énergies renouvelables</span>
            <a href="{{ route('admin.login') }}" class="opacity-75">Espace administration</a>
        </div>
    </div>
</footer>
