{{-- Bandeau de l'adhérent connecté. Paramètre : lecteur (ou null) --}}
<div class="d-flex flex-wrap align-items-center gap-2 p-3 rounded bg-white border mb-4">
    @if ($lecteur)
        <i class="bi bi-person-badge text-foret fs-5"></i>
        <span>Connecté : <strong>{{ $lecteur->nom_complet }}</strong> <span class="text-gris small">({{ $lecteur->matricule }})</span></span>
        @unless ($lecteur->estActif())
            <x-statut :statut="$lecteur->statutEffectif()" class="small" />
        @endunless
        <div class="ms-auto d-flex gap-2">
            <a href="{{ route('bibliotheque.emprunts') }}" class="btn btn-sm btn-primary"><i class="bi bi-bookmark-check me-1"></i>Mes emprunts</a>
            <form method="post" action="{{ route('bibliotheque.deconnexion') }}">
                @csrf
                <button class="btn btn-sm btn-outline-primary">Se déconnecter</button>
            </form>
        </div>
    @else
        <i class="bi bi-info-circle text-foret fs-5"></i>
        <span>Adhérent de la bibliothèque ? Identifiez-vous pour emprunter et accéder aux documents numériques.</span>
        <a href="{{ route('bibliotheque.connexion') }}" class="btn btn-sm btn-primary ms-auto"><i class="bi bi-box-arrow-in-right me-1"></i>Espace adhérent</a>
    @endif
</div>
