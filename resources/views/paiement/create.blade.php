@extends('layouts.app')
@section('titre', 'Payer les frais d\'inscription')

@section('contenu')
@php $numeros = config('acrest.paiement.numeros'); @endphp
<div class="inscription-fond pt-5 pb-5">
    <div class="container" style="max-width: 920px">
        <div class="carte-form">
            <div class="carte-form-corps">
                <header class="etape-entete">
                    <i class="bi bi-phone" aria-hidden="true"></i>
                    <div>
                        <h1>Payer les frais d'inscription</h1>
                        <p>{{ number_format($montant, 0, ',', ' ') }} FCFA, par MTN Mobile Money ou Orange Money.</p>
                    </div>
                </header>

                @include('partials.flash')
                @error('paiement')
                    <div class="alert alert-danger" role="alert"><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $message }}</div>
                @enderror

                <div class="row g-4">
                    <div class="col-lg-5">
                        <div class="encart h-100">
                            <h2 class="h5 mb-3">1. Envoyez le montant</h2>
                            <p class="mb-2">Depuis votre téléphone, transférez <strong>{{ number_format($montant, 0, ',', ' ') }} FCFA</strong> à :</p>
                            @foreach ($operateurs as $op)
                                @if (!empty($numeros[$op->value]))
                                    <div class="mb-3">
                                        <div class="small text-gris">{{ $op->libelle() }} — composez {{ $op->ussd() }}</div>
                                        <div class="numero-marchand">{{ $numeros[$op->value] }}</div>
                                    </div>
                                @endif
                            @endforeach
                            <p class="small text-gris mb-0">Bénéficiaire : {{ config('acrest.paiement.beneficiaire') }}. Conservez le SMS de confirmation : il contient la référence de la transaction.</p>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <h2 class="h5 mb-3">2. Déclarez votre paiement</h2>
                        <form method="post" action="{{ route('paiement.store') }}" class="needs-validation" novalidate>
                            @csrf
                            <div class="mb-3">
                                <label for="code" class="form-label">Code d'inscription</label>
                                <input type="text" id="code" name="code" value="{{ old('code', $code) }}" class="form-control text-uppercase @error('code') is-invalid @enderror" placeholder="ISAP-26-XXXXXX" required autocomplete="off">
                                <div class="invalid-feedback">@error('code'){{ $message }}@else Saisissez votre code d'inscription. @enderror</div>
                                <div class="form-text"><a href="{{ route('dossier.retrouver') }}">Code oublié ?</a></div>
                            </div>

                            <fieldset class="mb-3">
                                <legend class="form-label fs-6">Opérateur utilisé</legend>
                                <div class="row g-2">
                                    @foreach ($operateurs as $op)
                                        <div class="col-sm-6 operateur op-{{ $op->value }}">
                                            <input type="radio" name="operateur" id="op-{{ $op->value }}" value="{{ $op->value }}" @checked(old('operateur', 'mtn_momo') === $op->value) required>
                                            <label for="op-{{ $op->value }}"><span class="pastille-op">{{ $op === \App\Enums\OperateurPaiement::MtnMomo ? 'MTN' : 'OM' }}</span> {{ $op->libelle() }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('operateur')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </fieldset>

                            <div class="mb-3">
                                <label for="telephone" class="form-label">Numéro qui a effectué le paiement</label>
                                <input type="tel" id="telephone" name="telephone" value="{{ old('telephone') }}" class="form-control @error('telephone') is-invalid @enderror" required inputmode="tel" pattern="[\d\s+.\-()]{8,20}" placeholder="6 77 00 00 00">
                                <div class="invalid-feedback">@error('telephone'){{ $message }}@else Saisissez un numéro valide. @enderror</div>
                            </div>
                            <div class="mb-4">
                                <label for="reference" class="form-label">Référence de la transaction</label>
                                <input type="text" id="reference" name="reference" value="{{ old('reference') }}" class="form-control text-uppercase @error('reference') is-invalid @enderror" required minlength="6" maxlength="50" autocomplete="off" aria-describedby="reference-aide">
                                <div id="reference-aide" class="form-text">Elle figure dans le SMS de confirmation (« ID de transaction » ou « Réf »).</div>
                                <div class="invalid-feedback">@error('reference'){{ $message }}@else Saisissez la référence reçue par SMS. @enderror</div>
                            </div>
                            <button type="submit" class="btn btn-soleil btn-lg w-100" data-chargement="Envoi…">Déclarer mon paiement</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
