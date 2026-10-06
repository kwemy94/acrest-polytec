@extends('layouts.admin')
@section('titre', 'Utilisateurs')

@section('contenu')
<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-gris small mb-0">Les administrateurs gèrent tout le site ; les bibliothécaires n'accèdent qu'à la bibliothèque.</p>
    <a href="{{ route('admin.utilisateurs.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Nouvel utilisateur</a>
</div>
<div class="admin-carte">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Nom</th><th>E-mail</th><th>Rôle</th><th>État</th><th></th></tr></thead>
            <tbody>
                @foreach ($utilisateurs as $u)
                    <tr>
                        <td class="fw-semibold">{{ $u->name }}@if ($u->is(auth()->user())) <span class="small text-gris">(vous)</span>@endif</td>
                        <td class="small">{{ $u->email }}</td>
                        <td><span class="badge {{ $u->estAdmin() ? 'text-bg-primary' : 'text-bg-light border' }}">{{ $u->role->libelle() }}</span></td>
                        <td>@if ($u->actif)<span class="statut statut-success">Actif</span>@else<span class="statut statut-neutre">Désactivé</span>@endif</td>
                        <td class="text-end"><a href="{{ route('admin.utilisateurs.edit', $u) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
