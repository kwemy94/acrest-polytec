<!DOCTYPE html>
<html lang="fr">
<body style="margin:0;background:#F6F8F4;font-family:Segoe UI,Arial,sans-serif;color:#142019">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:24px 12px">
    <tr><td align="center">
        <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="max-width:560px;background:#fff;border-radius:12px;overflow:hidden">
            <tr><td style="background:#1D4535;color:#fff;padding:20px 28px;font-size:20px;font-weight:700">@yield('entete', 'ACREST Polytechnique')</td></tr>
            <tr><td style="padding:28px">
                @yield('contenu')
                <p style="color:#5C6B62;font-size:14px;margin-bottom:0">@yield('pied', 'Contact : '.config('acrest.contact.telephone').' — '.config('acrest.contact.email'))</p>
            </td></tr>
        </table>
    </td></tr>
</table>
</body>
</html>
