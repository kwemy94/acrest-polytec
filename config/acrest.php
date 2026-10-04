<?php

return [

    'nom' => 'ACREST Polytechnique',
    'nom_complet' => "Institut Supérieur ACREST Polytechnique Da Vinci",
    'slogan' => "Formation aux métiers des énergies renouvelables et des technologies climatiques appliquées",
    'localisation' => "Tchuelekouet, Bangang — Bamboutos, Ouest-Cameroun",

    'contact' => [
        'email' => env('ACREST_EMAIL', 'info.acrest@gmail.com'),
        'telephone' => env('ACREST_TELEPHONE', '+237 677 86 06 38'),
        'facebook' => env('ACREST_FACEBOOK', '#'),
    ],

    /*
    | Frais de préinscription (FCFA) et numéros marchands Mobile Money
    | sur lesquels les candidats envoient leur paiement.
    */
    'paiement' => [
        'frais_inscription' => (int) env('FRAIS_INSCRIPTION', 25000),
        'devise' => 'FCFA',
        'numeros' => [
            'mtn_momo' => env('MTN_MOMO_NUMERO', '677 86 06 38'),
            'orange_money' => env('ORANGE_MONEY_NUMERO', ''),
        ],
        'beneficiaire' => env('PAIEMENT_BENEFICIAIRE', 'ACREST Polytechnique'),
    ],

    'diplomes' => ['BACC', 'GCE A/L', 'PROBATOIRE', 'GCE O/L', 'BEPC', 'CAP', 'BTS', 'LICENCE', 'MASTER', 'DOCTORAT'],

    'nombre_choix' => 3,

    'pays' => [
        'Afrique' => ['Afrique du Sud', 'Algérie', 'Angola', 'Bénin', 'Botswana', 'Burkina Faso', 'Burundi', 'Cameroun', 'Cap-Vert', 'Centrafrique', 'Comores', 'Congo', 'Côte d\'Ivoire', 'Djibouti', 'Égypte', 'Érythrée', 'Eswatini', 'Éthiopie', 'Gabon', 'Gambie', 'Ghana', 'Guinée', 'Guinée équatoriale', 'Guinée-Bissau', 'Kenya', 'Lesotho', 'Liberia', 'Libye', 'Madagascar', 'Malawi', 'Mali', 'Maroc', 'Maurice', 'Mauritanie', 'Mozambique', 'Namibie', 'Niger', 'Nigeria', 'Ouganda', 'RD Congo', 'Rwanda', 'Sao Tomé-et-Principe', 'Sénégal', 'Seychelles', 'Sierra Leone', 'Somalie', 'Soudan', 'Soudan du Sud', 'Tanzanie', 'Tchad', 'Togo', 'Tunisie', 'Zambie', 'Zimbabwe'],
        'Amériques' => ['Argentine', 'Brésil', 'Canada', 'Chili', 'Colombie', 'Cuba', 'États-Unis', 'Haïti', 'Mexique', 'Pérou', 'Venezuela'],
        'Asie' => ['Arabie saoudite', 'Chine', 'Corée du Sud', 'Émirats arabes unis', 'Inde', 'Indonésie', 'Japon', 'Liban', 'Philippines', 'Qatar', 'Turquie', 'Viêt Nam'],
        'Europe' => ['Allemagne', 'Belgique', 'Espagne', 'France', 'Italie', 'Pays-Bas', 'Portugal', 'Royaume-Uni', 'Suisse'],
    ],
];
