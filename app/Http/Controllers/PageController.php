<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    public function acrest(): View
    {
        return view('pages.acrest');
    }

    public function technologies(): View
    {
        return view('pages.technologies', ['galerie' => config('galerie')]);
    }

    public function logement(): View
    {
        return view('pages.logement');
    }
}
