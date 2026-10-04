@php $v = fn ($champ, $defaut = null) => old($champ, $valeurs[$champ] ?? $defaut); @endphp
<form method="post" action="{{ route('inscription.identite') }}" class="needs-validation" novalidate>
    @csrf
    <div class="row g-3">
        <div class="col-md-6">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" id="nom" name="nom" value="{{ $v('nom') }}" class="form-control text-uppercase @error('nom') is-invalid @enderror" required maxlength="100" autocomplete="family-name" autofocus>
            <div class="invalid-feedback">@error('nom'){{ $message }}@else Saisissez votre nom. @enderror</div>
        </div>
        <div class="col-md-6">
            <label for="prenom" class="form-label">Prénom(s) <span class="facultatif">facultatif</span></label>
            <input type="text" id="prenom" name="prenom" value="{{ $v('prenom') }}" class="form-control @error('prenom') is-invalid @enderror" maxlength="100" autocomplete="given-name">
            @error('prenom')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <span class="form-label d-block" id="label-sexe">Sexe</span>
            <div class="choix-sexe @error('sexe') is-invalid @enderror" role="radiogroup" aria-labelledby="label-sexe">
                @foreach (\App\Enums\Sexe::cases() as $sexe)
                    <div>
                        <input type="radio" name="sexe" id="sexe-{{ $sexe->value }}" value="{{ $sexe->value }}" @checked($v('sexe') === $sexe->value) required>
                        <label for="sexe-{{ $sexe->value }}"><i class="bi {{ $sexe === \App\Enums\Sexe::Masculin ? 'bi-gender-male' : 'bi-gender-female' }}"></i> {{ $sexe->libelle() }}</label>
                    </div>
                @endforeach
            </div>
            @error('sexe')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label for="date_naissance" class="form-label">Date de naissance</label>
            <input type="date" id="date_naissance" name="date_naissance" value="{{ $v('date_naissance') }}" class="form-control @error('date_naissance') is-invalid @enderror" required max="{{ now()->subYears(14)->toDateString() }}" min="1940-01-02" autocomplete="bday">
            <div class="invalid-feedback">@error('date_naissance'){{ $message }}@else Indiquez votre date de naissance. @enderror</div>
        </div>

        <div class="col-md-6">
            <label for="lieu_naissance" class="form-label">Lieu de naissance</label>
            <input type="text" id="lieu_naissance" name="lieu_naissance" value="{{ $v('lieu_naissance') }}" class="form-control @error('lieu_naissance') is-invalid @enderror" required maxlength="100" placeholder="Ville ou village">
            <div class="invalid-feedback">@error('lieu_naissance'){{ $message }}@else Indiquez votre lieu de naissance. @enderror</div>
        </div>
        <div class="col-md-6">
            <label for="pays" class="form-label">Nationalité</label>
            <select id="pays" name="pays" class="form-select @error('pays') is-invalid @enderror" required autocomplete="country-name">
                <option value="">Choisissez un pays</option>
                @foreach (config('acrest.pays') as $continent => $pays)
                    <optgroup label="{{ $continent }}">
                        @foreach ($pays as $p)
                            <option value="{{ $p }}" @selected($v('pays', 'Cameroun') === $p)>{{ $p }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            <div class="invalid-feedback">@error('pays'){{ $message }}@else Choisissez votre nationalité. @enderror</div>
        </div>

        <div class="col-12">
            <label for="cni" class="form-label">Numéro de CNI ou de passeport</label>
            <input type="text" id="cni" name="cni" value="{{ $v('cni') }}" class="form-control text-uppercase @error('cni') is-invalid @enderror" required minlength="5" maxlength="30" inputmode="text" aria-describedby="cni-aide">
            <div id="cni-aide" class="form-text">Ce numéro, avec votre e-mail, permet de retrouver votre code si vous le perdez.</div>
            <div class="invalid-feedback">
                @error('cni'){{ $message }} @if (str_contains($message, 'existe déjà'))<a href="{{ route('dossier.retrouver') }}">Retrouver mon code</a>@endif @else Saisissez le numéro de votre pièce d'identité. @enderror
            </div>
        </div>
    </div>
    @include('inscription.etapes._actions')
</form>
