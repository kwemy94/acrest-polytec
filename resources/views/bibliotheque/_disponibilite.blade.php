{{-- Disponibilité d'un document. Paramètre : document (avec disponibles_count et en_circulation_count) --}}
@if ($document->consultation_sur_place)
    <span class="statut statut-neutre"><i class="bi bi-building" style="font-size:.8rem"></i> Consultation sur place</span>
@elseif ($document->en_circulation_count === 0)
    @unless ($document->estNumerique())
        <span class="statut statut-neutre"><i class="bi bi-circle-fill" style="font-size:.5rem"></i> Indisponible</span>
    @endunless
@elseif ($document->disponibles_count > 0)
    <span class="statut statut-success"><i class="bi bi-circle-fill" style="font-size:.5rem"></i> {{ $document->disponibles_count }} disponible{{ $document->disponibles_count > 1 ? 's' : '' }} sur {{ $document->en_circulation_count }}</span>
@else
    <span class="statut statut-warning"><i class="bi bi-circle-fill" style="font-size:.5rem"></i> Tous empruntés</span>
@endif
