/* ACREST Polytechnique — comportements de l'interface (sans dépendance hormis Bootstrap). */
(function () {
    'use strict';

    /* 1. Validation côté navigateur avant envoi (le serveur revalide toujours). */
    document.querySelectorAll('form.needs-validation').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                form.classList.add('was-validated');
                var premier = form.querySelector(':invalid');
                if (premier) { premier.focus({ preventScroll: false }); }
                return;
            }
            // Empêche le double envoi
            form.querySelectorAll('[type=submit]').forEach(function (btn) {
                btn.disabled = true;
                if (btn.dataset.chargement) {
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>' + btn.dataset.chargement;
                }
            });
        });
    });

    /* 2. Listes dépendantes filière → spécialité, sans doublon entre les choix. */
    var source = document.getElementById('catalogue-formations');
    if (source) {
        var catalogue = JSON.parse(source.textContent);
        var blocs = Array.prototype.slice.call(document.querySelectorAll('[data-choix]'));

        var specialitesDe = function (filiereId) {
            var f = catalogue.find(function (x) { return String(x.id) === String(filiereId); });
            return f ? f.specialites : [];
        };

        var choisies = function (sauf) {
            return blocs.filter(function (b) { return b !== sauf; })
                .map(function (b) { return b.querySelector('[data-specialite]').value; })
                .filter(Boolean);
        };

        var remplir = function (bloc, garder) {
            var selFiliere = bloc.querySelector('[data-filiere]');
            var selSpe = bloc.querySelector('[data-specialite]');
            var actuelle = garder ? selSpe.value || selSpe.dataset.valeur : '';
            var dejaPrises = choisies(bloc);
            var liste = specialitesDe(selFiliere.value);

            selSpe.innerHTML = '';
            var vide = document.createElement('option');
            vide.value = '';
            vide.textContent = selFiliere.value ? 'Choisissez une spécialité' : 'Choisissez d\'abord une filière';
            selSpe.appendChild(vide);

            liste.forEach(function (s) {
                var opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = s.nom;
                if (dejaPrises.indexOf(String(s.id)) !== -1) {
                    opt.disabled = true;
                    opt.textContent += ' (déjà choisie)';
                }
                if (String(s.id) === String(actuelle) && !opt.disabled) { opt.selected = true; }
                selSpe.appendChild(opt);
            });
            selSpe.disabled = !selFiliere.value;
            if (bloc.dataset.obligatoire !== undefined) { selSpe.required = true; }
            else { selSpe.required = !!selFiliere.value; }
        };

        var rafraichirTous = function (sauf) {
            blocs.forEach(function (b) { if (b !== sauf) { remplir(b, true); } });
        };

        blocs.forEach(function (bloc) {
            remplir(bloc, true);
            bloc.querySelector('[data-filiere]').addEventListener('change', function () {
                remplir(bloc, false);
                rafraichirTous(bloc);
            });
            bloc.querySelector('[data-specialite]').addEventListener('change', function () { rafraichirTous(bloc); });

            var retirer = bloc.querySelector('[data-retirer]');
            if (retirer) {
                retirer.addEventListener('click', function () {
                    bloc.querySelector('[data-filiere]').value = '';
                    bloc.querySelector('[data-specialite]').dataset.valeur = '';
                    remplir(bloc, false);
                    rafraichirTous(bloc);
                });
            }
        });
    }

    /* 3. Champ « série » visible seulement pour les diplômes qui en ont une. */
    var diplome = document.getElementById('diplome');
    var blocSerie = document.getElementById('bloc-serie');
    if (diplome && blocSerie) {
        var sansSerie = ['BEPC', 'CAP', 'DOCTORAT'];
        var majSerie = function () { blocSerie.hidden = sansSerie.indexOf(diplome.value) !== -1; };
        diplome.addEventListener('change', majSerie);
        majSerie();
    }

    /* 4. Agrandissement des photos de la galerie. */
    var modal = document.getElementById('modal-photo');
    if (modal) {
        modal.addEventListener('show.bs.modal', function (event) {
            var bouton = event.relatedTarget;
            modal.querySelector('img').src = bouton.dataset.photo;
            modal.querySelector('img').alt = bouton.dataset.legende || '';
            modal.querySelector('.modal-title').textContent = bouton.dataset.legende || '';
        });
    }

    /* 5. Copier le code d'inscription. */
    document.querySelectorAll('[data-copier]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!navigator.clipboard) { return; }
            navigator.clipboard.writeText(btn.dataset.copier).then(function () {
                var texte = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check2"></i> Copié';
                setTimeout(function () { btn.innerHTML = texte; }, 2000);
            });
        });
    });

    /* 6. Confirmation des actions sensibles (administration). */
    document.querySelectorAll('form[data-confirmer]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!window.confirm(form.dataset.confirmer)) { e.preventDefault(); }
        });
    });
})();
