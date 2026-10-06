{{-- Logo ACREST. Paramètres : taille (hauteur en px), fond (true sur un fond sombre : pastille blanche pour la lisibilité) --}}
@php $hauteur = $taille ?? 40; @endphp
<span class="logo {{ ! empty($fond) ? 'logo-pastille' : '' }}">
    <img src="{{ asset('images/acrest_logo.avif') }}" alt="" width="{{ (int) round($hauteur * 463 / 539) }}" height="{{ $hauteur }}" decoding="async">
</span>
