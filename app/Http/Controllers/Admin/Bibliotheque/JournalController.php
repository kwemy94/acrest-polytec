<?php

namespace App\Http\Controllers\Admin\Bibliotheque;

use App\Http\Controllers\Controller;
use App\Models\JournalActivite;
use App\Models\User;
use App\Services\Bibliotheque\Journal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Historique des opérations : qui, quoi, quand, sur quel élément. */
class JournalController extends Controller
{
    public function index(Request $request): View
    {
        $filtres = $request->only(['q', 'action', 'user', 'du', 'au']);

        $activites = JournalActivite::with(['user', 'adherent', 'sujet'])
            ->when($filtres['q'] ?? null, fn (Builder $q, string $t) => $q->where('description', 'like', "%{$t}%"))
            ->when($filtres['action'] ?? null, fn (Builder $q, string $a) => $q->where('action', $a))
            ->when($filtres['user'] ?? null, fn (Builder $q, $u) => $q->where('user_id', $u))
            ->when($filtres['du'] ?? null, fn (Builder $q, string $d) => $q->whereDate('created_at', '>=', $d))
            ->when($filtres['au'] ?? null, fn (Builder $q, string $d) => $q->whereDate('created_at', '<=', $d))
            ->latest('id')
            ->paginate(40)
            ->withQueryString();

        return view('admin.bibliotheque.journal.index', [
            'activites' => $activites,
            'filtres' => $filtres,
            'actions' => Journal::ACTIONS,
            'utilisateurs' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }
}
