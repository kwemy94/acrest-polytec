<!DOCTYPE html>
<html lang="fr">
<body style="margin:0;background:#F6F8F4;font-family:Segoe UI,Arial,sans-serif;color:#142019">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:24px 12px">
    <tr><td align="center">
        <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="max-width:560px;background:#fff;border-radius:12px;overflow:hidden">
            <tr><td style="background:#1D4535;color:#fff;padding:16px 28px;font-size:20px;font-weight:700">
                <table role="presentation" cellpadding="0" cellspacing="0"><tr>
                    <td style="background:#fff;border-radius:8px;padding:4px 6px"><img src="{{ asset('images/acrest_logo_mail.png') }}" alt="ACREST" width="41" height="48" style="display:block"></td>
                    <td style="padding-left:14px;color:#fff;font-size:20px;font-weight:700">ACREST Polytechnique</td>
                </tr></table>
            </td></tr>
            <tr><td style="padding:28px">
                <p style="margin-top:0">Bonjour {{ $inscription->prenom ?: $inscription->nom }},</p>
                <p>Votre dossier d'inscription est enregistré. Voici votre code :</p>
                <p style="font-size:26px;font-weight:800;letter-spacing:2px;background:#FFF6DB;border:2px dashed #F2B21B;border-radius:10px;padding:12px;text-align:center">{{ $inscription->code }}</p>
                <p>Il vous sera demandé pour payer les frais d'inscription ({{ number_format(config('acrest.paiement.frais_inscription'), 0, ',', ' ') }} FCFA) et pour suivre votre dossier.</p>
                <p style="text-align:center;margin:28px 0">
                    <a href="{{ route('paiement.create', ['code' => $inscription->code]) }}" style="background:#F2B21B;color:#142019;text-decoration:none;font-weight:700;padding:12px 22px;border-radius:8px">Payer les frais</a>
                </p>
                <p style="color:#5C6B62;font-size:14px">Suivi du dossier : {{ route('dossier.recherche') }}<br>Contact : {{ config('acrest.contact.telephone') }} — {{ config('acrest.contact.email') }}</p>
            </td></tr>
        </table>
    </td></tr>
</table>
</body>
</html>
