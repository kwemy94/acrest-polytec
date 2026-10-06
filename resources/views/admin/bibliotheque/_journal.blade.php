{{-- Liste d'opérations du journal. Paramètre : activites --}}
<div class="table-responsive">
    <table class="table align-middle mb-0">
        <thead><tr><th>Date</th><th>Opération</th><th>Par</th></tr></thead>
        <tbody>
            @forelse ($activites as $a)
                <tr>
                    <td class="small text-nowrap">{{ $a->created_at->format('d/m/Y H:i') }}</td>
                    <td class="small">
                        <span class="badge text-bg-light border me-1">{{ \App\Services\Bibliotheque\Journal::ACTIONS[$a->action] ?? $a->action }}</span>
                        {{ $a->description }}
                        @if (! empty($a->proprietes['motif']))<div class="text-gris">Motif : {{ $a->proprietes['motif'] }}</div>@endif
                        @if (! empty($a->proprietes['note']))<div class="text-gris">Note : {{ $a->proprietes['note'] }}</div>@endif
                    </td>
                    <td class="small text-nowrap">{{ $a->user?->name ?? ($a->adherent ? 'Adhérent '.$a->adherent->matricule : 'Système') }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="p-4 text-gris">Aucune opération enregistrée.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
