@extends('layouts.admin')
@section('titre', 'Emprunts')

@use('App\Enums\StatutEmprunt', 'S')

@section('contenu')
<div class="row g-3 mb-3">
    @foreach ([
        ['demande', 'bi-inbox', 'Demandes à traiter', $compteurs['demandes'], ['statut' => 'demande'], 'text-warning'],
        ['reserve', 'bi-bookmark-check', 'À retirer', $compteurs['reserves'], ['statut' => 'reserve'], ''],
        ['en_cours', 'bi-book-half', 'Prêts en cours', $compteurs['en_cours'], ['statut' => 'en_cours'], ''],
        ['retard', 'bi-exclamation-triangle', 'En retard', $compteurs['retards'], ['retard' => 1], 'text-danger'],
    ] as [$cle, $icone, $libelle, $nombre, $lien, $classe])
        <div class="col-6 col-xl-3">
            <a href="{{ route('admin.emprunts.index', $lien) }}" class="admin-carte p-3 d-block text-decoration-none text-reset h-100">
                <div class="small text-gris mb-2"><i class="bi {{ $icone }} me-1"></i>{{ $libelle }}</div>
                <div class="admin-chiffre {{ $nombre ? $classe : '' }}">{{ $nombre }}</div>
            </a>
        </div>
    @endforeach
</div>

<form method="get" class="admin-carte p-3 mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-5">
            <label for="q" class="form-label small">Recherche</label>
            <input type="search" id="q" name="q" value="{{ $filtres['q'] ?? '' }}" class="form-control" placeholder="Titre, code-barres, code ou nom de l'étudiant…">
        </div>
        <div class="col-md-3">
            <label for="statut" class="form-label small">État</label>
            <select id="statut" name="statut" class="form-select">
                <option value="">Tous</option>
                @foreach ($statuts as $s)<option value="{{ $s->value }}" @selected(($filtres['statut'] ?? '') === $s->value)>{{ $s->libelle() }}</option>@endforeach
            </select>
        </div>
        <div class="col-6 col-md-2"><button class="btn btn-outline-primary w-100"><i class="bi bi-funnel"></i> Filtrer</button></div>
        <div class="col-6 col-md-2"><a href="{{ route('admin.emprunts.create') }}" class="btn btn-primary w-100"><i class="bi bi-upc-scan"></i> Prêt guichet</a></div>
    </div>
</form>

<div class="admin-carte">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Étudiant</th><th>Document</th><th>Dates</th><th>État</th><th class="text-end">Action</th></tr></thead>
            <tbody>
                @forelse ($emprunts as $e)
                    <tr>
                        <td>
                            @if ($e->inscription)
                                <a href="{{ route('admin.inscriptions.show', $e->inscription) }}" class="fw-semibold">{{ $e->inscription->nom_complet }}</a>
                                <div class="small text-gris">{{ $e->inscription->code }}</div>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.documents.show', $e->document) }}">{{ $e->document->titre }}</a>
                            <div class="small text-gris">{{ $e->exemplaire ? 'Ex. '.$e->exemplaire->code : 'Exemplaire non attribué' }}</div>
                            @if ($e->message)<div class="small fst-italic mt-1">« {{ $e->message }} »</div>@endif
                        </td>
                        <td class="small text-nowrap">
                            Demandé le {{ $e->created_at->format('d/m/Y') }}
                            @if ($e->statut === S::Reserve)<div>À retirer avant le <strong>{{ $e->retirer_avant->format('d/m/Y') }}</strong></div>@endif
                            @if ($e->date_pret)<div>Prêté le {{ $e->date_pret->format('d/m/Y') }}</div>@endif
                            @if ($e->date_retour_prevue && $e->statut === S::EnCours)<div>Retour prévu <strong>{{ $e->date_retour_prevue->format('d/m/Y') }}</strong></div>@endif
                            @if ($e->date_retour)<div>Rendu le {{ $e->date_retour->format('d/m/Y') }}</div>@endif
                        </td>
                        <td>
                            <x-statut :statut="$e->statut" />
                            @if ($e->estEnRetard())<div class="small text-danger fw-semibold mt-1">{{ $e->joursDeRetard() }} j de retard</div>@endif
                            @if ($e->motif)<div class="small text-gris mt-1">{{ $e->motif }}</div>@endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex flex-wrap gap-1 justify-content-end">
                                @if ($e->statut === S::Demande)
                                    <form method="post" action="{{ route('admin.emprunts.traiter', [$e, 'valider']) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-primary" title="Mettre un exemplaire de côté et prévenir l'étudiant"><i class="bi bi-check-lg"></i> Accepter</button>
                                    </form>
                                @endif
                                @if (in_array($e->statut, [S::Demande, S::Reserve], true))
                                    <form method="post" action="{{ route('admin.emprunts.traiter', [$e, 'remettre']) }}" data-confirmer="Remettre « {{ $e->document->titre }} » à {{ $e->inscription?->nom_complet }} ?">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-soleil" title="L'étudiant est au guichet"><i class="bi bi-box-arrow-right"></i> Remettre</button>
                                    </form>
                                    <button class="btn btn-sm btn-outline-danger" type="button" data-bs-toggle="collapse" data-bs-target="#refus-{{ $e->id }}" aria-expanded="false" title="Refuser"><i class="bi bi-x-lg"></i></button>
                                @endif
                                @if ($e->statut === S::EnCours)
                                    <form method="post" action="{{ route('admin.emprunts.traiter', [$e, 'retour']) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-primary"><i class="bi bi-box-arrow-in-left"></i> Retour</button>
                                    </form>
                                    @if ($e->peutEtreProlonge())
                                        <form method="post" action="{{ route('admin.emprunts.traiter', [$e, 'prolonger']) }}">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-sm btn-outline-primary" title="Prolonger"><i class="bi bi-calendar-plus"></i></button>
                                        </form>
                                    @endif
                                    <form method="post" action="{{ route('admin.emprunts.traiter', [$e, 'retour']) }}" data-confirmer="Enregistrer le retour et mettre l'exemplaire hors prêt (abîmé ou perdu) ?">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="hors_service" value="1">
                                        <button class="btn btn-sm btn-outline-secondary" title="Retour abîmé / perdu"><i class="bi bi-heartbreak"></i></button>
                                    </form>
                                @endif
                                @if (! $e->statut->estActif() && $e->agent)
                                    <span class="small text-gris">{{ $e->agent->name }}</span>
                                @endif
                            </div>
                            @if (in_array($e->statut, [S::Demande, S::Reserve], true))
                                <form method="post" action="{{ route('admin.emprunts.traiter', [$e, 'refuser']) }}" class="collapse mt-2 text-start" id="refus-{{ $e->id }}">
                                    @csrf @method('PATCH')
                                    <label for="motif-{{ $e->id }}" class="form-label small">Motif du refus (envoyé à l'étudiant)</label>
                                    <textarea id="motif-{{ $e->id }}" name="motif" rows="2" class="form-control form-control-sm mb-2" required maxlength="500" placeholder="Ex. : ouvrage réservé aux enseignants ce semestre"></textarea>
                                    <button class="btn btn-sm btn-danger">Refuser la demande</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-4 text-gris">Aucun emprunt ne correspond à ces critères.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($emprunts->hasPages())<div class="p-3">{{ $emprunts->links() }}</div>@endif
</div>
@endsection
