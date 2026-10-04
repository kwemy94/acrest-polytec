@php $v = fn ($champ) => old($champ, $valeurs[$champ] ?? null); @endphp
<form method="post" action="{{ route('inscription.diplome') }}" class="needs-validation" novalidate>
    @csrf
    <div class="row g-3">
        <div class="col-md-6">
            <label for="diplome" class="form-label">Diplôme d'admission</label>
            <select id="diplome" name="diplome" class="form-select @error('diplome') is-invalid @enderror" required autofocus>
                <option value="">Choisissez votre diplôme</option>
                @foreach (config('acrest.diplomes') as $d)
                    <option value="{{ $d }}" @selected($v('diplome') === $d)>{{ $d }}</option>
                @endforeach
            </select>
            <div class="invalid-feedback">@error('diplome'){{ $message }}@else Choisissez votre diplôme. @enderror</div>
        </div>
        <div class="col-md-6" id="bloc-serie">
            <label for="option_diplome" class="form-label">Série ou option <span class="facultatif">facultatif</span></label>
            <input type="text" id="option_diplome" name="option_diplome" value="{{ $v('option_diplome') }}" class="form-control @error('option_diplome') is-invalid @enderror" maxlength="50" placeholder="Ex. : C, D, A4, TI, F3">
            @error('option_diplome')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label for="annee_obtention" class="form-label">Année d'obtention <span class="facultatif">facultatif</span></label>
            <input type="number" id="annee_obtention" name="annee_obtention" value="{{ $v('annee_obtention') }}" class="form-control @error('annee_obtention') is-invalid @enderror" min="1980" max="{{ now()->year }}" inputmode="numeric" placeholder="{{ now()->year }}">
            <div class="invalid-feedback">@error('annee_obtention'){{ $message }}@else Saisissez une année entre 1980 et {{ now()->year }}. @enderror</div>
        </div>
    </div>
    <div class="alert alert-info mt-4 mb-0">
        <i class="bi bi-info-circle me-1"></i>
        Le diplôme requis varie selon la spécialité ; il est indiqué sur la page de chaque <a href="{{ route('specialites.index') }}" target="_blank">spécialité</a>.
    </div>
    @include('inscription.etapes._actions')
</form>
