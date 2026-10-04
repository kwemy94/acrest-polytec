<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\FiliereRepositoryInterface;
use App\Repositories\Contracts\SpecialiteRepositoryInterface;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private readonly FiliereRepositoryInterface $filieres,
        private readonly SpecialiteRepositoryInterface $specialites,
    ) {
    }

    public function __invoke(): View
    {
        return view('pages.accueil', [
            'filieres' => $this->filieres->activesAvecSpecialites(),
            'nombreSpecialites' => $this->specialites->actives()->count(),
            'galerie' => config('galerie'),
        ]);
    }
}
