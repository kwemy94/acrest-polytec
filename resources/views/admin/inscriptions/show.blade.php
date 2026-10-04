@extends('layouts.admin')
@section('titre', 'Dossier '.$inscription->code)

@section('contenu')
<a href="{{ route('admin.inscriptions.index') }}" class="small d-inline-block mb-3"><i class="bi bi-arrow-left me-1"></i>Toutes les inscriptions</a>
<div class="row g-3">
    <div class="col-xl-8">
        <div class="admin-carte p-3 p-lg-4 mb-3">
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                <div>
                    <h2 class="h3 mb-1">{{ $inscription->nom_complet }}</h2>
                    <span class="text-gris">Inscrit(e) le {{ $inscription->created_at->translatedFormat('d F Y à H:i') }}</span>
                </div>
                <x-statut :statut="$inscription->statut" class="align-self-start" />
            </div>
            <div class="recap">
                <div class="recap-entete"><h3 class="h6 mb-0">Identité</h3></div>
                <dl>
                    <dt>Sexe</dt><dd>{{ $inscription->sexe?->libelle() }}</dd>
                    <dt>Naissance</dt><dd>{{ $inscription->date_naissance->format('d/m/Y') }} à {{ $inscription->lieu_naissance }} ({{ $inscription->date_naissance->age }} ans)</dd>
                    <dt>Nationalité</dt><dd>{{ $inscription->pays }}</dd>
                    <dt>CNI / passeport</dt><dd>{{ $inscription->cni }}</dd>
                </dl>
            </div>
            <div class="recap">
                <div class="recap-entete"><h3 class="h6 mb-0">Coordonnées</h3></div>
                <dl>
                    <dt>Téléphone</dt><dd><a href="tel:{{ $inscription->telephone }}">{{ $inscription->telephone }}</a></dd>
                    <dt>E-mail</dt><dd><a href="mailto:{{ $inscription->email }}">{{ $inscription->email }}</a></dd>
                    <dt>Père</dt><dd>{{ $inscription->nom_pere ?: '—' }}</dd>
                    <dt>Mère</dt><dd>{{ $inscription->nom_mere }}</dd>
                    <dt>Contact parent</dt><dd><a href="tel:{{ $inscription->contact_parent }}">{{ $inscription->contact_parent }}</a></dd>
                </dl>
            </div>
            <div class="recap">
                <div class="recap-entete"><h3 class="h6 mb-0">Diplôme et choix</h3></div>
                <dl>
                    <dt>Diplôme</dt><dd>{{ $inscription->diplome }} {{ $inscription->option_diplome ? '— '.$inscription->option_diplome : '' }} {{ $inscription->annee_obtention ? '('.$inscription->annee_obtention.')' : '' }}</dd>
                    @foreach ($inscription->specialites as $s)
                        <dt>Choix {{ $s->pivot->rang }}</dt><dd>{{ $s->nom }} <span class="fw-normal text-gris">· {{ $s->filiere->nom }}</span></dd>
                    @endforeach
                </dl>
            </div>
        </div>

        <div class="admin-carte">
            <h2 class="h5 p-3 p-lg-4 pb-0 mb-3">Paiements</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Date</th><th>Opérateur</th><th>Référence</th><th>Montant</th><th>État</th></tr></thead>
                    <tbody>
                        @forelse ($inscription->paiements as $p)
                            <tr>
                                <td class="small">{{ $p->created_at->format('d/m/Y H:i') }}</td>
                                <td class="small">{{ $p->operateur->libelle() }}<div class="text-gris">{{ $p->telephone }}</div></td>
                                <td><code>{{ $p->reference }}</code></td>
                                <td class="text-nowrap">{{ number_format($p->montant, 0, ',', ' ') }} FCFA</td>
                                <td><x-statut :statut="$p->statut" />@if ($p->agent)<div class="small text-gris mt-1">par {{ $p->agent->name }}</div>@endif</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-4 text-gris">Aucun paiement déclaré pour ce dossier.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($inscription->paiements->contains(fn ($p) => $p->statut === \App\Enums\StatutPaiement::EnAttente))
                <div class="p-3"><a href="{{ route('admin.paiements.index', ['q' => $inscription->code]) }}" class="btn btn-sm btn-soleil">Vérifier le paiement en attente</a></div>
            @endif
        </div>
    </div>

    <div class="col-xl-4">
        <div class="admin-carte p-3 p-lg-4 mb-3">
            <h2 class="h5 mb-3">Décision sur le dossier</h2>
            @if (! $inscription->estPayee())
                <div class="alert alert-warning small">Les frais de ce dossier ne sont pas encore confirmés.</div>
            @endif
            <form method="post" action="{{ route('admin.inscriptions.statut', $inscription) }}" class="d-grid gap-2">
                @csrf @method('PATCH')
                @foreach (\App\Enums\StatutInscription::cases() as $s)
                    @continue($s === $inscription->statut)
                    <button name="statut" value="{{ $s->value }}" class="btn {{ $s === \App\Enums\StatutInscription::Validee ? 'btn-primary' : 'btn-outline-primary' }}">
                        {{ $s === \App\Enums\StatutInscription::EnAttente ? 'Remettre en attente' : ($s === \App\Enums\StatutInscription::Validee ? 'Valider le dossier' : 'Rejeter le dossier') }}
                    </button>
                @endforeach
            </form>
        </div>
        <div class="admin-carte p-3 p-lg-4">
            <h2 class="h6 mb-3">Autres actions</h2>
            <form method="post" action="{{ route('admin.inscriptions.destroy', $inscription) }}" data-confirmer="Supprimer définitivement le dossier {{ $inscription->code }} ?">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i>Supprimer le dossier</button>
            </form>
        </div>
    </div>
</div>
@endsection
