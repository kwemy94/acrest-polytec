<a href="{{ route('specialites.show', $specialite->slug) }}" class="ligne-spe">
    @if ($specialite->icone)
        <img src="{{ asset($specialite->icone) }}" alt="" loading="lazy">
    @else
        <span class="icone-vide" aria-hidden="true"><i class="bi bi-mortarboard"></i></span>
    @endif
    <span>
        <strong>{{ $specialite->nom }}</strong>
        @isset($sousTitre)<small>{{ $sousTitre }}</small>@endisset
    </span>
    <i class="bi bi-chevron-right" aria-hidden="true"></i>
</a>
