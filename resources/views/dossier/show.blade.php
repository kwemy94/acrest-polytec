@extends('layouts.app')
@section('titre', 'Dossier '.$inscription->code)

@section('contenu')
@php
    $montant = config('acrest.paiement.frais_inscription');
    $dernier = $inscription->paiements->first();
@endphp
<div class="inscription-fond pt-4 pt-md-5 pb-5">
    <div class="container" style="max-width: 860px">
        <div class="carte-form">
            <div class="carte-form-corps">
                @if (session('nouvelle_inscription'))
                    <div class="text-center mb-4">
                        <div class="d-inline-grid rounded-circle bg-success-subtle text-success mb-3" style="width:64px;height:64px;place-items:center;font-size:2rem"><i class="bi bi-check2"></i></div>
                        <h1 class="h2 mb-2">Votre dossier est enregistré</h1>
                        <p class="text-gris mb-0">Notez votre code d'inscription : il vous sera demandé pour payer et suivre votre dossier.</p>
                    </div>
                @else
                    <h1 class="h2 mb-3">Mon dossier d'inscription</h1>
                @endif

                @include('partials.flash')

                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 p-3 rounded bg-light mb-4">
                    <div>
                        <div class="small text-gris mb-1">Code d'inscription</div>
                        <div class="code-inscription">{{ $inscription->code }}</div>
                    </div>
                    <div class="d-flex gap-2 no-print">
                        <button type="button" class="btn btn-outline-primary btn-sm" data-copier="{{ $inscription->code }}"><i class="bi bi-clipboard"></i> Copier</button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.print()"><i class="bi bi-printer"></i> Imprimer le reçu</button>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="encart h-100">
                            <div class="small text-gris mb-2">État du dossier</div>
                            <x-statut :statut="$inscription->statut" />
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="encart h-100">
                            <div class="small text-gris mb-2">Frais d'inscription</div>
                            @if ($dernier)
                                <x-statut :statut="$dernier->statut" />
                                @if ($dernier->statut === \App\Enums\StatutPaiement::Rejete && $dernier->note)
                                    <p class="small mt-2 mb-0"><strong>Motif :</strong> {{ $dernier->note }}</p>
                                @endif
                            @else
                                <span class="statut statut-danger"><i class="bi bi-circle-fill" style="font-size:.5rem"></i> Non payés</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if (! $inscription->estPayee() && ! $inscription->aPaiementEnCours())
                    <div class="encart encart-foret mb-4 no-print">
                        <h2 class="h4">Prochaine étape : payer les frais</h2>
                        <p class="text-white-50">Réglez {{ number_format($montant, 0, ',', ' ') }} FCFA par Mobile Money pour que votre dossier soit étudié.</p>
                        <a href="{{ route('paiement.create', ['code' => $inscription->code]) }}" class="btn btn-soleil">Payer maintenant</a>
                    </div>
                @endif

                <div class="recap">
                    <div class="recap-entete"><h2>Candidat</h2></div>
                    <dl>
                        <dt>Nom et prénom</dt><dd>{{ $inscription->nom_complet }}</dd>
                        <dt>Né(e) le</dt><dd>{{ $inscription->date_naissance->translatedFormat('d F Y') }} à {{ $inscription->lieu_naissance }}</dd>
                        <dt>Téléphone</dt><dd>{{ $inscription->telephone }}</dd>
                        <dt>E-mail</dt><dd>{{ $inscription->email }}</dd>
                        <dt>Diplôme</dt><dd>{{ $inscription->diplome }} {{ $inscription->option_diplome ? '— '.$inscription->option_diplome : '' }}</dd>
                        <dt>Inscrit(e) le</dt><dd>{{ $inscription->created_at->translatedFormat('d F Y à H:i') }}</dd>
                    </dl>
                </div>
                <div class="recap">
                    <div class="recap-entete"><h2>Choix de formation</h2></div>
                    <dl>
                        @foreach ($inscription->specialites as $s)
                            <dt>Choix {{ $s->pivot->rang }}</dt>
                            <dd>{{ $s->nom }} <span class="fw-normal text-gris">· {{ $s->filiere->nom }}</span></dd>
                        @endforeach
                    </dl>
                </div>

                @if ($inscription->paiements->isNotEmpty())
                    <div class="recap">
                        <div class="recap-entete"><h2>Paiements déclarés</h2></div>
                        <div class="table-responsive">
                            <table class="table mb-0 align-middle">
                                <thead><tr><th>Date</th><th>Opérateur</th><th>Référence</th><th>Montant</th><th>État</th></tr></thead>
                                <tbody>
                                    @foreach ($inscription->paiements as $p)
                                        <tr>
                                            <td>{{ $p->created_at->format('d/m/Y') }}</td>
                                            <td>{{ $p->operateur->libelle() }}</td>
                                            <td><code>{{ $p->reference }}</code></td>
                                            <td>{{ number_format($p->montant, 0, ',', ' ') }} FCFA</td>
                                            <td><x-statut :statut="$p->statut" /></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <p class="small text-gris mt-4 mb-0">Une question ? Contactez la scolarité au {{ config('acrest.contact.telephone') }} ou à {{ config('acrest.contact.email') }} en indiquant votre code.</p>
            </div>
        </div>
    </div>
</div>
@endsection
