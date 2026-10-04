<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MotDePasseRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CompteController extends Controller
{
    public function edit(): View
    {
        return view('admin.compte');
    }

    public function update(MotDePasseRequest $request): RedirectResponse
    {
        $request->user()->update(['password' => $request->validated('password')]);

        return back()->with('succes', 'Mot de passe modifié.');
    }
}
