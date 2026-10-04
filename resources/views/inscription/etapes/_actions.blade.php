<div class="actions-form">
    @if ($etape->precedente())
        <a href="{{ route('inscription.etape', $etape->precedente()->value) }}" class="btn btn-outline-primary"><i class="bi bi-arrow-left me-1"></i> {{ $etape->precedente()->titre() }}</a>
    @else
        <a href="{{ route('accueil') }}" class="btn btn-lien">Annuler</a>
    @endif

    @if (request('retour') === 'verification' || old('retour_verification'))
        <input type="hidden" name="retour_verification" value="1">
        <button type="submit" class="btn btn-primary btn-lg" data-chargement="Enregistrement…">Enregistrer et revenir à la vérification</button>
    @else
        <button type="submit" class="btn btn-primary btn-lg" data-chargement="Enregistrement…">
            Continuer : {{ $etape->suivante()?->titre() }} <i class="bi bi-arrow-right ms-1"></i>
        </button>
    @endif
</div>
