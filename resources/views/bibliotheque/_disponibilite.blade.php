{{-- Disponibilité d'un document. Paramètre : document (chargé avec avecDisponibilite()) --}}
@if ($document->consultation_sur_place)
    <span class="statut statut-neutre"><i class="bi bi-building" style="font-size:.8rem"></i> Consultation sur place</span>
@elseif ($document->exemplaires_count === 0)
    @unless ($document->numeriques_count)
        <span class="statut statut-neutre"><i class="bi bi-circle-fill" style="font-size:.5rem"></i> Aucun exemplaire</span>
    @endunless
@elseif ($document->disponibles_count > 0)
    <span class="statut statut-success"><i class="bi bi-circle-fill" style="font-size:.5rem"></i> {{ $document->disponibles_count }} disponible{{ $document->disponibles_count > 1 ? 's' : '' }} sur {{ $document->exemplaires_count }}</span>
@else
    <span class="statut statut-warning"><i class="bi bi-circle-fill" style="font-size:.5rem"></i> Aucun disponible ({{ $document->exemplaires_count }} ex.)</span>
@endif
@if ($document->numeriques_count)
    <span class="statut statut-info"><i class="bi bi-file-earmark-arrow-down" style="font-size:.8rem"></i> Numérique</span>
@endif
