@echo off
REM Installation dACREST Polytechnique (Windows : WAMP, Laragon, XAMPP)
chcp 65001 >nul
cd /d "%~dp0"

where php >nul 2>nul || (echo PHP 8.2+ est requis dans le PATH. & pause & exit /b 1)
where composer >nul 2>nul || (echo Composer est requis : https://getcomposer.org & pause & exit /b 1)

echo ==^> Installation des dependances
call composer install --no-dev --optimize-autoloader --no-interaction || (pause & exit /b 1)

if not exist .env (
    copy .env.example .env >nul
    php artisan key:generate --force
    echo.
    echo Fichier .env cree. Ouvrez-le pour renseigner DB_DATABASE, DB_USERNAME et DB_PASSWORD,
    echo creez la base dans phpMyAdmin, puis appuyez sur une touche.
    pause
)

echo ==^> Creation des tables et import des formations
php artisan migrate --seed --force || (pause & exit /b 1)

echo.
echo Installation terminee.
echo   Lancer le site : php artisan serve  puis http://localhost:8000
echo   Administration : http://localhost:8000/admin/connexion
pause
