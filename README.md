# ACREST Polytechnique — Laravel 12

Refonte du site [acrest-php](https://github.com/kwemy94/acrest-php) de l'Institut Supérieur ACREST Polytechnique Da Vinci (Bangang, Ouest-Cameroun) : site vitrine des formations, inscription en ligne en 5 étapes, paiement Mobile Money et espace d'administration.

## Prérequis

- PHP **8.2** ou plus (8.2, 8.3 et 8.4 sont compatibles), avec les extensions `pdo_mysql` (ou `pdo_sqlite`), `mbstring`, `xml`, `curl`, `fileinfo`, `openssl`, `zip`
- Composer 2
- MySQL 5.7+ / MariaDB 10.4+ (ou SQLite pour un essai)

Aucun Node.js n'est nécessaire : Bootstrap 5.3, Bootstrap Icons et les polices sont fournis dans `public/vendor`. Le site fonctionne donc sans CDN.

## Installation rapide

**Linux / macOS**
```bash
./install.sh
```

**Windows (WAMP, Laragon, XAMPP)**
```bat
install.bat
```

Le script :
1. installe les dépendances ;
2. crée le fichier `.env` ;
3. génère la clé ;
4. crée les tables et importe les 12 filières et 29 spécialités ;
5. crée le compte administrateur.

## Installation manuelle

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env        # puis renseignez DB_DATABASE, DB_USERNAME, DB_PASSWORD
php artisan key:generate
php artisan migrate --seed  # tables + filières/spécialités + administrateur
php artisan serve           # http://localhost:8000
```

Pour un **essai sans MySQL** :
1. Dans `.env`, mettez `DB_CONNECTION=sqlite` et supprimez les lignes `DB_HOST` à `DB_PASSWORD`.
2. Créez le fichier vide `database/database.sqlite`.
3. Lancez `php artisan migrate --seed`.

**Données fictives** pour tester l'administration (nécessite les dépendances de développement, donc un `composer install` sans `--no-dev`) :
```bash
php artisan db:seed --class=DemoSeeder
```

## Administration

- **Adresse** : `/admin/connexion`
- **Compte créé par défaut** : `admin@acrest-polytechnique.cm` / `ChangezMoi2026`. Ces valeurs se modifient dans `.env` avant le seed (`ADMIN_EMAIL`, `ADMIN_PASSWORD`).
- **Changez le mot de passe** dès la première connexion (menu « Mon compte »).
- **Créer ou réinitialiser un administrateur** : `php artisan acrest:admin`

## Configuration (`.env`)

| Variable | Rôle |
|---|---|
| `FRAIS_INSCRIPTION` | Montant des frais en FCFA (25 000 par défaut, **à ajuster**) |
| `MTN_MOMO_NUMERO`, `ORANGE_MONEY_NUMERO` | Numéros marchands affichés aux candidats (laisser vide pour masquer un opérateur) |
| `ACREST_EMAIL`, `ACREST_TELEPHONE`, `ACREST_FACEBOOK` | Coordonnées affichées sur le site |
| `MAIL_*` | Serveur d'envoi des e-mails (code d'inscription). En `MAIL_MAILER=log`, les e-mails sont écrits dans `storage/logs` |

La liste des diplômes et des pays se trouve dans `config/acrest.php`. Les photos de la galerie sont listées dans `config/galerie.php`.

## Architecture

```
app/
├── Enums/                      StatutInscription, StatutPaiement, OperateurPaiement, Sexe
├── Repositories/
│   ├── Contracts/              Interfaces (RepositoryInterface, FiliereRepositoryInterface, ...)
│   └── Eloquent/               Implémentations (BaseRepository, FiliereRepository, ...)
├── Services/
│   ├── Inscription/            Etape (enum), InscriptionWizard (session), InscriptionService, GenerateurCode
│   └── Paiement/               PaiementService, Contracts/PasserellePaiementInterface, Passerelles/DeclarationManuelle
├── Http/
│   ├── Controllers/            Contrôleurs fins : ils délèguent aux services et repositories
│   │   └── Admin/
│   └── Requests/               Une Form Request par étape du formulaire
├── Providers/RepositoryServiceProvider.php   Liaisons interface → implémentation
└── Models/                     Filiere, Specialite, Inscription, Paiement, NewsletterAbonne
database/seeders/data/formations.php          Contenu des filières et spécialités (repris du site d'origine)
```

Les contrôleurs ne dépendent que des **interfaces**. Pour changer d'implémentation, modifiez uniquement `RepositoryServiceProvider` : par exemple une autre source de données, ou une API de paiement (MTN MoMo API, Orange Money Web Payment, CinetPay…) en implémentant `PasserellePaiementInterface`.

## Parcours candidat

1. **Inscription** (`/inscription`) en 5 étapes : identité, coordonnées, diplôme, choix de formation, vérification.
   - Chaque étape est validée côté navigateur et côté serveur.
   - Les étapes déjà remplies restent modifiables.
2. **Code d'inscription** unique, par exemple `ISAP-26-K7M4QX`, affiché sur un reçu imprimable et envoyé par e-mail.
3. **Paiement** (`/paiement`) : le candidat envoie les frais par Mobile Money, puis déclare la référence de transaction.
4. **Suivi** (`/suivi`) avec le code et l'e-mail. En cas d'oubli, le code est renvoyé par e-mail.

## Paiements

Le site d'origine appelait une API MTN qui n'existe plus. Le mode par défaut fonctionne désormais ainsi :

1. Le candidat **déclare** son paiement en indiquant l'opérateur, le numéro et la référence du SMS.
2. Le paiement apparaît « En cours de vérification » dans *Administration → Paiements*.
3. L'agent compare avec l'historique du compte marchand, puis **confirme** ou **rejette** le paiement. En cas de rejet, le motif est visible par le candidat.

Une même référence ne peut être déclarée qu'une fois, et un dossier déjà payé ou en cours de vérification ne peut pas être payé de nouveau.

## Tests

```bash
composer install            # avec les dépendances de développement
php artisan test
```

13 tests couvrent :
- les pages publiques ;
- le parcours d'inscription complet, avec ses règles de validation ;
- l'accès au dossier ;
- les paiements ;
- l'administration et la newsletter.

Ils passent sous SQLite et MySQL.

## Mise en production

- `.env` : `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://votre-domaine`
- Faites pointer le serveur web vers le dossier `public/`.
- Exécutez `php artisan config:cache && php artisan route:cache && php artisan view:cache`.
- Les dossiers `storage/` et `bootstrap/cache/` doivent être accessibles en écriture.
- Configurez un vrai serveur SMTP (`MAIL_*`) pour l'envoi des codes.
