#!/usr/bin/env bash
# Installation d'ACREST Polytechnique (Linux / macOS)
set -e
cd "$(dirname "$0")"

command -v php >/dev/null || { echo "PHP 8.2+ est requis."; exit 1; }
command -v composer >/dev/null || { echo "Composer est requis : https://getcomposer.org"; exit 1; }
php -r 'exit(version_compare(PHP_VERSION, "8.2.0", ">=") ? 0 : 1);' || { echo "PHP 8.2 ou plus est requis (version actuelle : $(php -r 'echo PHP_VERSION;'))."; exit 1; }

echo "==> Installation des dépendances"
composer install --no-dev --optimize-autoloader --no-interaction

if [ ! -f .env ]; then
    cp .env.example .env
    echo
    read -r -p "Utiliser MySQL ? (o/N, N = SQLite pour un essai) " mysql
    if [[ "$mysql" =~ ^[oOyY] ]]; then
        read -r -p "Base de données [acrest_polytechnique] : " db; db=${db:-acrest_polytechnique}
        read -r -p "Utilisateur [root] : " user; user=${user:-root}
        read -r -s -p "Mot de passe : " pass; echo
        sed -i.bak -e "s/^DB_DATABASE=.*/DB_DATABASE=$db/" -e "s/^DB_USERNAME=.*/DB_USERNAME=$user/" -e "s/^DB_PASSWORD=.*/DB_PASSWORD=$pass/" .env
    else
        sed -i.bak -e "s/^DB_CONNECTION=.*/DB_CONNECTION=sqlite/" -e "/^DB_HOST=/d" -e "/^DB_PORT=/d" -e "/^DB_DATABASE=/d" -e "/^DB_USERNAME=/d" -e "/^DB_PASSWORD=/d" .env
        touch database/database.sqlite
    fi
    rm -f .env.bak
    php artisan key:generate --force
fi

echo "==> Création des tables et import des formations"
php artisan migrate --seed --force

chmod -R ug+rw storage bootstrap/cache 2>/dev/null || true

echo
echo "Installation terminée."
echo "  Lancer le site : php artisan serve   puis ouvrir http://localhost:8000"
echo "  Administration : http://localhost:8000/admin/connexion"
echo "  Compte         : voir ADMIN_EMAIL / ADMIN_PASSWORD dans .env (changez le mot de passe après connexion)"
