@extends('mail.layout')
@section('entete', 'Bibliothèque — nouvelle demande')

@section('contenu')
<p style="margin-top:0">Une nouvelle demande d'emprunt attend d'être traitée.</p>
<table role="presentation" width="100%" cellpadding="6" cellspacing="0" style="font-size:15px">
    <tr><td style="color:#5C6B62;width:140px">Document</td><td><strong>{{ $emprunt->document->titre }}</strong></td></tr>
    <tr><td style="color:#5C6B62">Auteur(s)</td><td>{{ $emprunt->document->auteurs }}</td></tr>
    <tr><td style="color:#5C6B62">Étudiant</td><td>{{ $emprunt->inscription->nom_complet }} ({{ $emprunt->inscription->code }})</td></tr>
    @if ($emprunt->message)<tr><td style="color:#5C6B62">Message</td><td>{{ $emprunt->message }}</td></tr>@endif
</table>
<p style="text-align:center;margin:28px 0">
    <a href="{{ route('admin.emprunts.index', ['statut' => 'demande']) }}" style="background:#F2B21B;color:#142019;text-decoration:none;font-weight:700;padding:12px 22px;border-radius:8px">Traiter les demandes</a>
</p>
@endsection
