<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsletterRequest;
use App\Repositories\Contracts\NewsletterRepositoryInterface;
use Illuminate\Http\RedirectResponse;

class NewsletterController extends Controller
{
    public function __invoke(NewsletterRequest $request, NewsletterRepositoryInterface $newsletter): RedirectResponse
    {
        $nouveau = $newsletter->abonner($request->validated('email_newsletter'));

        return back()->with('newsletter', $nouveau
            ? 'Inscription confirmée. Vous recevrez nos actualités.'
            : 'Cette adresse est déjà inscrite à la newsletter.');
    }
}
