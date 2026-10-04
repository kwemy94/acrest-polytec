@php
    $choixEnregistres = $valeurs['choix'] ?? [];
    $val = fn ($rang, $cle) => old("choix.$rang.$cle", $choixEnregistres[$rang][$cle] ?? '');
@endphp
<script type="application/json" id="catalogue-formations">@json($catalogue)</script>

<form method="post" action="{{ route('inscription.formation') }}" class="needs-validation" novalidate>
    @csrf
    @foreach ([1, 2, 3] as $rang)
        <fieldset class="bloc-choix {{ $rang > 1 ? 'optionnel' : '' }}" data-choix @if ($rang === 1) data-obligatoire @endif>
            <legend class="bloc-choix-titre">
                <span class="rang">{{ $rang }}</span>
                {{ $rang === 1 ? 'Premier choix' : ($rang === 2 ? 'Deuxième choix' : 'Troisième choix') }}
                @if ($rang > 1)<span class="facultatif fw-normal small text-gris">facultatif</span>@endif
            </legend>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="filiere-{{ $rang }}" class="form-label">Filière</label>
                    <select id="filiere-{{ $rang }}" name="choix[{{ $rang }}][filiere]" class="form-select @error("choix.$rang.filiere") is-invalid @enderror" data-filiere @if ($rang === 1) required @endif>
                        <option value="">Choisissez une filière</option>
                        @foreach ($catalogue as $f)
                            <option value="{{ $f['id'] }}" @selected((string) $val($rang, 'filiere') === (string) $f['id'])>{{ $f['nom'] }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">@error("choix.$rang.filiere"){{ $message }}@else Choisissez une filière. @enderror</div>
                </div>
                <div class="col-md-6">
                    <label for="specialite-{{ $rang }}" class="form-label">Spécialité</label>
                    <select id="specialite-{{ $rang }}" name="choix[{{ $rang }}][specialite]" class="form-select @error("choix.$rang.specialite") is-invalid @enderror" data-specialite data-valeur="{{ $val($rang, 'specialite') }}" @if ($rang === 1) required @endif>
                        {{-- Sans JavaScript : toutes les spécialités, groupées par filière --}}
                        <option value="">Choisissez une spécialité</option>
                        @foreach ($catalogue as $f)
                            <optgroup label="{{ $f['nom'] }}">
                                @foreach ($f['specialites'] as $s)
                                    <option value="{{ $s['id'] }}" @selected((string) $val($rang, 'specialite') === (string) $s['id'])>{{ $s['nom'] }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">@error("choix.$rang.specialite"){{ $message }}@else Choisissez une spécialité. @enderror</div>
                </div>
            </div>
            @if ($rang > 1)
                <button type="button" class="btn btn-lien small mt-2" data-retirer><i class="bi bi-x-circle me-1"></i>Retirer ce choix</button>
            @endif
        </fieldset>
    @endforeach
    <p class="form-text mt-3 mb-0"><i class="bi bi-lightbulb me-1"></i> Vos choix sont étudiés dans l'ordre : mettez en premier la spécialité qui vous intéresse le plus.</p>
    @include('inscription.etapes._actions')
</form>
