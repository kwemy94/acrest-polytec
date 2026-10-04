@extends('layouts.admin')
@section('titre', 'Newsletter')

@section('contenu')
<div class="admin-carte">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-3">
        <form method="get" class="d-flex gap-2">
            <label for="q" class="visually-hidden">Rechercher</label>
            <input type="search" id="q" name="q" value="{{ $q }}" class="form-control" placeholder="Rechercher une adresse">
            <button class="btn btn-primary"><i class="bi bi-search"></i></button>
        </form>
        <a href="{{ route('admin.newsletter.export') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-filetype-csv me-1"></i>Exporter toutes les adresses</a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Adresse e-mail</th><th>Inscrite le</th></tr></thead>
            <tbody>
                @forelse ($abonnes as $a)
                    <tr><td><a href="mailto:{{ $a->email }}">{{ $a->email }}</a></td><td class="small">{{ $a->created_at->format('d/m/Y') }}</td></tr>
                @empty
                    <tr><td colspan="2" class="p-4 text-gris">Aucun abonné{{ $q ? ' pour cette recherche' : ' pour le moment' }}.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($abonnes->hasPages())<div class="p-3">{{ $abonnes->links() }}</div>@endif
</div>
@endsection
