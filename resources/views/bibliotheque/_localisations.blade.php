{{-- Localisations distinctes des exemplaires d'un document. Paramètre : document (exemplaires chargés) --}}
@php
    $plan = app(\App\Services\Bibliotheque\PlanBibliotheque::class);
    $chemins = $document->exemplaires->pluck('localisation_id')->filter()->unique()->map(fn ($id) => $plan->chemin($id, ' / '))->filter()->values();
@endphp
@if ($chemins->isNotEmpty())
    <div class="small text-gris"><i class="bi bi-geo-alt me-1"></i>{{ $chemins->take(2)->join(' · ') }}{{ $chemins->count() > 2 ? ' · +'.($chemins->count() - 2) : '' }}</div>
@endif
