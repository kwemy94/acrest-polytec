<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\FiliereRepositoryInterface;
use App\Repositories\Contracts\SpecialiteRepositoryInterface;
use Illuminate\View\View;

class SpecialiteController extends Controller
{
    public function __construct(
        private readonly SpecialiteRepositoryInterface $specialites,
        private readonly FiliereRepositoryInterface $filieres,
    ) {
    }

    public function index(): View
    {
        return view('specialites.index', ['filieres' => $this->filieres->activesAvecSpecialites()]);
    }

    public function show(string $slug): View
    {
        $specialite = $this->specialites->findBySlug($slug) ?? abort(404);

        return view('specialites.show', [
            'specialite' => $specialite,
            'voisines' => $this->specialites->voisines($specialite),
        ]);
    }
}
