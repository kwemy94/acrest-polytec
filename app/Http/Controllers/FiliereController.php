<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\FiliereRepositoryInterface;
use Illuminate\View\View;

class FiliereController extends Controller
{
    public function __construct(private readonly FiliereRepositoryInterface $filieres)
    {
    }

    public function index(): View
    {
        return view('filieres.index', ['domaines' => $this->filieres->parDomaine()]);
    }

    public function show(string $slug): View
    {
        $filiere = $this->filieres->findBySlug($slug) ?? abort(404);

        return view('filieres.show', compact('filiere'));
    }
}
