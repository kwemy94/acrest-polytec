<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\NewsletterRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsletterController extends Controller
{
    public function __construct(private readonly NewsletterRepositoryInterface $newsletter)
    {
    }

    public function index(Request $request): View
    {
        return view('admin.newsletter.index', [
            'abonnes' => $this->newsletter->rechercher($request->query('q')),
            'q' => $request->query('q'),
        ]);
    }

    public function export(): StreamedResponse
    {
        $emails = $this->newsletter->emails();

        return response()->streamDownload(function () use ($emails) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['E-mail'], ';');
            foreach ($emails as $email) {
                fputcsv($out, [$email], ';');
            }
            fclose($out);
        }, 'newsletter-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
