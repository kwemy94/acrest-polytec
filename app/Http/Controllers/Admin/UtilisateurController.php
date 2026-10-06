<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Bibliotheque\Journal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/** Comptes du personnel (administrateurs et bibliothécaires). Réservé aux administrateurs. */
class UtilisateurController extends Controller
{
    public function __construct(private readonly Journal $journal)
    {
    }

    public function index(): View
    {
        return view('admin.utilisateurs.index', ['utilisateurs' => User::orderBy('name')->get()]);
    }

    public function create(): View
    {
        return view('admin.utilisateurs.form', ['utilisateur' => new User(['role' => Role::Bibliothecaire])]);
    }

    private function valider(Request $request, ?User $utilisateur = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($utilisateur)],
            'role' => ['required', Rule::enum(Role::class)],
            'actif' => ['boolean'],
            'password' => [$utilisateur ? 'nullable' : 'required', 'confirmed', Password::min(8)],
        ], [], ['name' => 'nom', 'password' => 'mot de passe', 'role' => 'rôle']);
    }

    public function store(Request $request): RedirectResponse
    {
        $utilisateur = User::create([...$this->valider($request), 'actif' => $request->boolean('actif', true)]);
        $this->journal->enregistrer('administration', $utilisateur, "Création du compte {$utilisateur->email} ({$utilisateur->role->libelle()})");

        return redirect()->route('admin.utilisateurs.index')->with('succes', "Compte de {$utilisateur->name} créé.");
    }

    public function edit(User $utilisateur): View
    {
        return view('admin.utilisateurs.form', compact('utilisateur'));
    }

    public function update(Request $request, User $utilisateur): RedirectResponse
    {
        $donnees = $this->valider($request, $utilisateur);
        $donnees['actif'] = $request->boolean('actif');
        if (empty($donnees['password'])) {
            unset($donnees['password']);
        }

        // On ne peut pas se retirer à soi-même les droits d'administration ni se désactiver.
        if ($utilisateur->is($request->user()) && ($donnees['role'] !== Role::Admin->value || ! $donnees['actif'])) {
            return back()->withErrors(['role' => 'Vous ne pouvez pas retirer vos propres droits d\'administrateur ni désactiver votre compte.']);
        }

        $utilisateur->update($donnees);
        $this->journal->enregistrer('administration', $utilisateur, "Modification du compte {$utilisateur->email}",
            ['champs' => collect($utilisateur->getChanges())->except(['updated_at', 'password', 'remember_token'])->keys()->all()]);

        return redirect()->route('admin.utilisateurs.index')->with('succes', 'Compte mis à jour.');
    }
}
