@props(['statut'])
<span {{ $attributes->merge(['class' => 'statut statut-'.$statut->couleur()]) }}>
    <i class="bi bi-circle-fill" style="font-size:.5rem"></i> {{ $statut->libelle() }}
</span>
