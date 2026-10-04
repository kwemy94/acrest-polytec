<?php

/*
| Données des filières et spécialités, reprises du site ACREST Polytechnique d'origine.
*/

return [
    'filieres' => [
        [
            'slug' => 'agriculture-elevage',
            'nom' => 'Agriculture et Élevage',
            'domaine' => 'Agropastoral',
            'description' => 'Produire, élever et valoriser les ressources agricoles et aquacoles.',
            'image' => 'images/filieres/agriculture-elevage.webp',
            'ordre' => 1,
        ],
        [
            'slug' => 'metiers-eau',
            'nom' => 'Métiers de l\'eau',
            'domaine' => 'Eau et Environnement',
            'description' => 'Concevoir et entretenir les ouvrages d\'approvisionnement, de traitement et d\'assainissement de l\'eau.',
            'image' => 'images/filieres/metiers-eau.webp',
            'ordre' => 2,
        ],
        [
            'slug' => 'sciences-environnementales',
            'nom' => 'Sciences environnementales',
            'domaine' => 'Eau et Environnement',
            'description' => 'Protéger les ressources naturelles et accompagner les projets dans le respect de l\'environnement.',
            'image' => 'images/filieres/sciences-environnementales.webp',
            'ordre' => 3,
        ],
        [
            'slug' => 'genie-electrique',
            'nom' => 'Génie électrique',
            'domaine' => 'Industrie et Technologies',
            'description' => 'Installer, produire et maintenir l\'énergie électrique, du réseau au solaire.',
            'image' => 'images/filieres/genie-electrique.webp',
            'ordre' => 4,
        ],
        [
            'slug' => 'genie-civil',
            'nom' => 'Génie civil',
            'domaine' => 'Industrie et Technologies',
            'description' => 'Bâtir, aménager et entretenir l\'habitat, les routes et les ouvrages.',
            'image' => 'images/filieres/genie-civil.webp',
            'ordre' => 5,
        ],
        [
            'slug' => 'genie-biologique',
            'nom' => 'Génie biologique',
            'domaine' => 'Industrie et Technologies',
            'description' => 'Transformer et conserver les produits agricoles locaux.',
            'image' => 'images/filieres/genie-biologique.webp',
            'ordre' => 6,
        ],
        [
            'slug' => 'genie-mecanique',
            'nom' => 'Génie mécanique et productique',
            'domaine' => 'Industrie et Technologies',
            'description' => 'Concevoir, fabriquer et réparer machines, structures et véhicules.',
            'image' => 'images/filieres/genie-mecanique.webp',
            'ordre' => 7,
        ],
        [
            'slug' => 'commerce-vente',
            'nom' => 'Commerce et Vente',
            'domaine' => 'Commerce et Gestion',
            'description' => 'Vendre, négocier et développer la relation client.',
            'image' => 'images/filieres/commerce-vente.webp',
            'ordre' => 8,
        ],
        [
            'slug' => 'gestion',
            'nom' => 'Gestion',
            'domaine' => 'Commerce et Gestion',
            'description' => 'Piloter les finances, les projets et la logistique des organisations.',
            'image' => 'images/filieres/gestion.webp',
            'ordre' => 9,
        ],
        [
            'slug' => 'arts-culture',
            'nom' => 'Arts et Métiers de la Culture',
            'domaine' => 'Arts et Culture',
            'description' => 'Valoriser le patrimoine artistique et les industries créatives.',
            'image' => 'images/filieres/arts-culture.webp',
            'ordre' => 10,
        ],
        [
            'slug' => 'medico-sanitaire',
            'nom' => 'Études médico-sanitaires',
            'domaine' => 'Santé',
            'description' => 'Soigner, accompagner et prévenir au service de la santé des populations.',
            'image' => 'images/filieres/medico-sanitaire.webp',
            'ordre' => 11,
        ],
        [
            'slug' => 'genie-informatique',
            'nom' => 'Génie informatique',
            'domaine' => 'TIC',
            'description' => 'Développer des logiciels et maintenir les systèmes informatiques.',
            'image' => 'images/filieres/genie-informatique.webp',
            'ordre' => 12,
        ],
    ],
    'specialites' => [
        [
            'nom' => 'Aquaculture',
            'slug' => 'aquaculture',
            'filiere' => 'agriculture-elevage',
            'resume' => 'Cette spécialité permet de maîtriser les milieux aquatiques naturels et artificiels, de mettre en œuvre la production agricole de l’écloserie à la transformation, d’en assurer le contrôle et le suivi, mais aussi procéder à l’étude et l’analyse du marché en veillant à la protection de l’environnement et au respect de la règlementation sanitaire et vétérinaire et aux dispositions relatives à la police des eaux et au code rural.',
            'sections' => [
                [
                    'title' => 'Compétences recherchées',
                    'blocks' => [
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences génériques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Maîtriser les outils informatiques de base',
                                'Développer une attitude professionnelle dans le respect de la déontologie et de l’éthique',
                                'Travailler en équipe en milieu de formation et en milieu de pratique professionnelle',
                                'Comprendre le fonctionnement des organisations',
                                'Travailler dans un environnement multiculturel',
                                'Créer et gérer une entreprise',
                                'Développer progressivement une autonomie d\'apprentissage afin de pouvoir poursuivre de façon continue son développement personnel et professionnel tout au long de sa carrière.',
                            ],
                        ],
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences spécifiques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Maîtriser les techniques et outils en production aquatique',
                                'Réaliser un projet d\'installation (bâtiment, canalisation, digue...)',
                                'Calculer un plan de financement en tenant compte des crédits et subvention',
                                'Choisir les espèces à élever en fonction des potentialités du milieu et des contraintes du site d\'élevage',
                                'Veiller en permanence à la protection de l’environnement',
                                'Conduire un système de production spécialisé (alimentation des animaux, contrôle des cycles de reproduction, opération de sélection, surveillance de l\'état sanitaire de l\'élevage, préparation à la vente)',
                                'Maîtriser l’élevage et la commercialisation des plantes et animaux aquatiques',
                                'Connaître les outils et méthodes dans les tâches de résolution des problèmes du secteur aquacole.',
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'Débouchés',
                    'blocks' => [
                        [
                            'type' => 'list',
                            'items' => [
                                'Conducteur de travaux',
                                'Technicien aquacole',
                                'Chef d’exploitation ou d’entreprise aquacole',
                                'Cadre en entreprise de production aquacole dans les secteurs du commerce, de la distribution des produits de la mer, de l’industrie de la transformation, de la pêche et du tourisme',
                                'Technicien de laboratoire de recherche et développement',
                                'Technicien conseiller ou technico-commercial',
                                'Gestionnaire d’une entreprise aquacole',
                                'S’installer à son compte comme pisciculteur ou conchyliculteur',
                                'Gérer sa propre exploitation',
                                'Travailler dans des secteurs plus singuliers comme la culture d’algues, l’élevage de crustacés, l’aquariophilie marine ou la pêche continentale ou en estuaire.',
                            ],
                        ],
                    ],
                ],
            ],
            'diplome_requis' => 'Cette filière accueille les étudiants titulaires du BAC, GCE/AL ou un diplôme équivalent.',
            'images' => [
                'images/specialites/aquaculture-1.webp',
                'images/specialites/aquaculture-2.webp',
            ],
            'icone' => 'images/icones/aquaculture.webp',
            'brochure' => 'documents/brochures/aquaculture.pdf',
            'ordre' => 1,
        ],
        [
            'nom' => 'Production animale',
            'slug' => 'production-animale',
            'filiere' => 'agriculture-elevage',
            'resume' => 'Cette spécialité forme des spécialistes de l\'élevage et de la filière animale, disposant de solides connaissances dans l\'ensemble des techniques de production animale mais également en biologie et en chimie. L\'étudiant apprend la conduite d\'élevage sous tous ses aspects : qualité de l\'alimentation, croissance des animaux, reproduction, manipulations et interventions sur les animaux, surveillance sanitaire, bien-être animal, conception des bâtiments...',
            'sections' => [
                [
                    'title' => 'Compétences recherchées',
                    'blocks' => [
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences génériques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Maîtriser les outils informatiques de base',
                                'Développer une attitude professionnelle dans le respect de la déontologie et de l’éthique',
                                'Travailler en équipe en milieu de formation et en milieu de pratique professionnelle',
                                'Comprendre le fonctionnement des organisations',
                                'Travailler dans un environnement multiculturel',
                                'Créer et gérer une entreprise',
                                'Développer progressivement une autonomie d\'apprentissage afin de pouvoir poursuivre de façon continue son développement personnel et professionnel tout au long de sa carrière.',
                            ],
                        ],
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences spécifiques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Fournir des conseils techniques à un chef d\'exploitation ou à une coopérative aussi bien dans la production en elle-même que dans la gestion et le fonctionnement d\'une exploitation agricole',
                                'Gérer une exploitation agricole avec un élevage qu’il apprendra à conduire : qualité de l\'alimentation, croissance, reproduction et bien-être des animaux, surveillance sanitaire, intervention et manipulation (insémination et soins, par exemple) des animaux',
                                'Réaliser des diagnostics techniques, financiers, régalementaires et environnementaux concernant l’élevage',
                                'Manipuler les machines et les équipements liés à l\'élevage et à la production.',
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'Débouchés',
                    'blocks' => [
                        [
                            'type' => 'list',
                            'items' => [
                                'Chef d’exploitation',
                                'Chef d\'équipe',
                                'Conducteur de travaux',
                                'Responsable d\'élevage',
                                'Conseiller technique ou commercial.',
                            ],
                        ],
                    ],
                ],
            ],
            'diplome_requis' => 'Cette filière accueille les étudiants titulaires du BAC, GCE/AL ou un diplôme équivalent.',
            'images' => [
                'images/specialites/production-animale-1.webp',
                'images/specialites/production-animale-2.webp',
            ],
            'icone' => 'images/icones/production-animale.webp',
            'brochure' => 'documents/brochures/production-animale.pdf',
            'ordre' => 2,
        ],
        [
            'nom' => 'Production végétale',
            'slug' => 'production-vegetale',
            'filiere' => 'agriculture-elevage',
            'resume' => 'Ce programme forme des spécialistes de l\'ensemble des domaines de la culture de plantes maraîchères, pérennes, légumineuses, fourragères, de céréales ou d\'oléagineux. L’enseignement accorde une place importante aux potentialités agronomiques du sol, des apports d’engrais et d’amendement, la biologie végétale, la physiologie de la reproduction, le fonctionnement et les processus de reproduction et de multiplication des végétaux et des semences',
            'sections' => [
                [
                    'title' => 'Compétences recherchées',
                    'blocks' => [
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences génériques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Maîtriser les outils informatiques de base',
                                'Développer une attitude professionnelle dans le respect de la déontologie et de l’éthique',
                                'Travailler en équipe en milieu de formation et en milieu de pratique professionnelle',
                                'Comprendre le fonctionnement des organisations',
                                'Travailler dans un environnement multiculturel',
                                'Créer et gérer une entreprise',
                                'Développer progressivement une autonomie d\'apprentissage afin de pouvoir poursuivre de façon continue son développement personnel et professionnel tout au long de sa carrière.',
                            ],
                        ],
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences spécifiques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Définir les objectifs de rendement, de qualité mais également le calendrier de production',
                                'Maîtriser les bases de la production végétale',
                                'Apporter un conseil averti aux agriculteurs en place',
                                'S’occuper de la refonte totale d\'un système de culture.',
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'Débouchés',
                    'blocks' => [
                        [
                            'type' => 'list',
                            'items' => [
                                'Chef d’exploitation ou chef de culture au sein d’une entreprise agricole',
                                'Conseiller agricole (Chambre d’agriculture, institut technique)',
                                'Responsable d’une unité d’approvisionnement (coopérative, Chambre d’agriculture ou production de semences)',
                                'Technicien sélectionneur ou expérimentateur dans un institut de recherche ou une firme semencière',
                                'Chargé de mission dans les organismes agricoles (CAPEF, Coopératives, Groupement des producteurs)',
                                'Assistant de recherche dans un laboratoire ou centre de recherche',
                                'Technicien de multiplication de semences.',
                            ],
                        ],
                    ],
                ],
            ],
            'diplome_requis' => 'Cette filière accueille les étudiants titulaires du BAC, GCE/AL ou un diplôme équivalent.',
            'images' => [
                'images/specialites/production-vegetale-1.webp',
                'images/specialites/production-vegetale-2.webp',
            ],
            'icone' => null,
            'brochure' => 'documents/brochures/production-vegetale.pdf',
            'ordre' => 3,
        ],
        [
            'nom' => 'Hydraulique, traitement des eaux et assainissement',
            'slug' => 'hydraulique-traitement-des-eaux-et-assainissement',
            'filiere' => 'metiers-eau',
            'resume' => 'Cette spécialité a pour objectif de former des techniciens supérieurs capables de travailler dans le secteur du traitement, du transport-distribution, de l’assainissement et de l’épuration des eaux. Ils veillent au bon fonctionnement des stations de production ou de dépollution d’eau et assurent les opérations de captage, de traitement et de distribution de l’eau destinée à la consommation ou à usage industriel. Ils participent aussi aux opérations de collecte, d’assainissement et d’épuration des eaux usées.',
            'sections' => [
                [
                    'title' => 'Compétences recherchées',
                    'blocks' => [
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences génériques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Maîtriser les outils informatiques de base',
                                'Développer une attitude professionnelle dans le respect de la déontologie et de l’éthique',
                                'Travailler en équipe en milieu de formation et en milieu de pratique professionnelle',
                                'Comprendre le fonctionnement des organisations',
                                'Travailler dans un environnement multiculturel',
                                'Utiliser des techniques de collecte et de traitement de données',
                                'Développer progressivement une autonomie d\'apprentissage afin de pouvoir poursuivre de façon continue son développement personnel et professionnel tout au long de sa carrière.',
                            ],
                        ],
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences spécifiques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Gestion technique des ouvrages (conduite et régulation des installations, exploitation des réseaux, maintenances)',
                                'Gestion de l’information',
                                'Etude et encadrement technique',
                                'Assurance de la qualité',
                                'Responsable d’une unité d’exploitation dans une grande compagnie.',
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'Débouchés',
                    'blocks' => [
                        [
                            'type' => 'list',
                            'items' => [
                                'Collectivités territoriales décentralisées',
                                'Sociétés distributrices ou utilisatrice d’eau',
                                'Bureaux d’études et des équipes de recherche',
                                'Fournisseurs de matériels et des administrations et agences spécialisées.',
                            ],
                        ],
                    ],
                ],
            ],
            'diplome_requis' => 'Cette filière accueille les étudiants titulaires du BAC, GCE/AL ou un diplôme équivalent.',
            'images' => [
                'images/specialites/hydraulique-traitement-des-eaux-et-assainissement-1.webp',
                'images/specialites/hydraulique-traitement-des-eaux-et-assainissement-2.webp',
                'images/specialites/hydraulique-traitement-des-eaux-et-assainissement-3.webp',
            ],
            'icone' => 'images/icones/hydraulique-traitement-des-eaux-et-assainissement.webp',
            'brochure' => null,
            'ordre' => 4,
        ],
        [
            'nom' => 'Gestion environnementale',
            'slug' => 'gestion-environnementale',
            'filiere' => 'sciences-environnementales',
            'resume' => 'Cette spécialité forme des techniciens supérieurs à même d’exercer toutes activités liées à l’entretien et à l’amélioration du cadre de vie et de l’environnement : la gestion de l’eau et des déchets, l’entretien des forêts, parcs et jardins, l’entretien des locaux, le développement des énergies renouvelables et la lutte contre la pollution atmosphérique. Ces professionnels assurent le maintien de la biodiversité des espèces, l’équilibre de l’ensemble des écosystèmes naturels, sensibilisent et éduquent le public sur les domaines de la propreté, de l\'hygiène des locaux et des équipements.',
            'sections' => [
                [
                    'title' => 'Compétences recherchées',
                    'blocks' => [
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences génériques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Maîtriser les outils informatiques de base',
                                'Développer une attitude professionnelle dans le respect de la déontologie et de l’éthique',
                                'Travailler en équipe en milieu de formation et en milieu de pratique professionnelle',
                                'Comprendre le fonctionnement des organisations',
                                'Travailler dans un environnement multiculturel',
                                'Créer et gérer une entreprise',
                                'Développer progressivement une autonomie d\'apprentissage afin de pouvoir poursuivre de façon continue son développement personnel et professionnel tout au long de sa carrière.',
                            ],
                        ],
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences spécifiques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Maitriser les caractéristiques des écosystèmes, les normes environnementales',
                                'Sensibiliser le public à la nature et à l\'environnement',
                                'Identifier les axes de progrès en matière de développement durable',
                                'Evaluer et prévenir les risques (santé, sécurité liés à l’activité professionnelle)',
                                'Elaborer et mettre en œuvre les plans d’actions correctives et préventives',
                                'Réaliser des prestations de services qui interviennent dans le domaine de la propreté de l’hygiène des locaux et des équipements, de la propreté urbaine et de la gestion des déchets',
                                'Diriger une opération exceptionnelle, urgente ou délicate (catastrophes écologiques, site difficilement accessible)',
                                'Organiser les chantiers d’assainissement',
                                'Mettre en place des actions de bio nettoyage, gestion des déchets',
                                'Avoir des connaissances en droit forestier, droit rural',
                                'Maîtriser la régalementation de la pêche, de la chasse, la classification des espèces animales.',
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'Débouchés',
                    'blocks' => [
                        [
                            'type' => 'list',
                            'items' => [
                                'Technicien-animateur en agroenvironnement',
                                'Conservateur d’espaces naturels',
                                'Technicien, cynégétique ou piscicole',
                                'Consultant environnement',
                                'Eco éducateur',
                                'Garde national de la chasse et de la faune sauvage',
                                'Garde pêche.',
                            ],
                        ],
                    ],
                ],
            ],
            'diplome_requis' => 'Cette filière accueille les étudiants titulaires du BAC, GCE/AL ou un diplôme équivalent.',
            'images' => [
                'images/specialites/gestion-environnementale-1.webp',
                'images/specialites/gestion-environnementale-2.webp',
                'images/specialites/gestion-environnementale-3.webp',
            ],
            'icone' => 'images/icones/gestion-environnementale.webp',
            'brochure' => 'documents/brochures/gestion-environnementale.pdf',
            'ordre' => 5,
        ],
        [
            'nom' => 'Électrotechnique',
            'slug' => 'electrotechnique',
            'filiere' => 'genie-electrique',
            'resume' => 'L\'énergie électrique est omniprésente dans les applications industrielles terminales et dans les services qui utilisent le procédés électriques. L\'électrotechnicien exerce ses ctivités dans l\'étude, la mise en oeuvre, l\'utilisation, la maintenance des équipements élctriques qui utilisent ussi bien des courants forts que des courants faibles. Il doit par ailleurs prendre en compte la sécurité des personnes et des biens. Avec l\'évolution des techniques et des nouvelles technologies liées à l\'electronique et à l\'informatique, il est souvent amené à intervenir sur des équipements de plus en plus sophistiqués.',
            'sections' => [
                [
                    'title' => 'Compétences & Débouchés',
                    'blocks' => [
                        [
                            'type' => 'text',
                            'text' => 'Le titulaire d’un diplôme de technicien supérieur en Génie Electrique option Electrotechnique a des connaissnces et des compétences nécessaires à l\'execice de son métier dans des applications aussi variées que:',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'L\'énergie électrique,',
                                'l\'électronique de puissance',
                                'l\'électronique,',
                                'l\'automatique,',
                                'les automatismes industriels,',
                                'l\'informatique industrielle.',
                            ],
                        ],
                        [
                            'type' => 'text',
                            'text' => 'Au sein d\'une équipe, le diplomé peut occuper les fonctions de responsbilité suivantes:',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Responsable des moyens techniques',
                                'Responsable de support technique',
                                'Technico-commercial',
                            ],
                        ],
                    ],
                ],
            ],
            'diplome_requis' => 'Cette filière accueille les étudiants titulaires du BAC C, D, E, F2, F3, GCE/AL ou un diplôme équivalent.',
            'images' => [
                'images/specialites/electrotechnique-1.webp',
                'images/specialites/electrotechnique-2.webp',
                'images/specialites/electrotechnique-3.webp',
            ],
            'icone' => 'images/icones/electrotechnique.webp',
            'brochure' => null,
            'ordre' => 6,
        ],
        [
            'nom' => 'Énergies renouvelables',
            'slug' => 'energies-renouvelables',
            'filiere' => 'genie-electrique',
            'resume' => 'Cette spécialité a pour objectif de former des experts dans la chaîne de la conception, la mise en service et la gestion d\'un système énergétique à partir des phénomènes naturels réguliers ou constant, ou des données de la nature (les astre, le soleil, la lune et la terre).',
            'sections' => [
                [
                    'title' => 'Compétences & Débouchés',
                    'blocks' => [
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences génétiques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Travailler en autonomie, collaborer en équipe',
                                'Anlyser, synthétiser un document professionnel',
                                'Participer à /Mener une démarche de gestion de projet.',
                            ],
                        ],
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences spécifiques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Faire une instllation électrique (de la production, réseaux de transport et distribution de l\'énergie)',
                                'Géner l\'aspect technico-économique des réseaux électriques',
                                'Mener et réaliser un projet',
                                'Effectuer les travaux d\'entretien et de maintenance dans les réseaux électriques.',
                            ],
                        ],
                        [
                            'type' => 'text',
                            'text' => 'Les débouchés sont nombreux et dans tout type d\'organisation:',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Chef de projet éolien et photovoltaïque',
                                'Commercial en énergies rénouvelable',
                                'Développeur de projets en énergies rénouvelables',
                                'Installateur de panneaux solaires photovoltaïques',
                                'Conseiller technique dans les agences de l\'énergie',
                                'Technicien du batiment en énergies rénouvelables.',
                            ],
                        ],
                    ],
                ],
            ],
            'diplome_requis' => 'Cette filière accueille les étudiants titulaires du BAC C, D, E, F2, F3, GCE/AL ou un diplôme équivalent.',
            'images' => [
                'images/specialites/energies-renouvelables-1.webp',
                'images/specialites/energies-renouvelables-2.webp',
                'images/specialites/energies-renouvelables-3.webp',
            ],
            'icone' => 'images/icones/energies-renouvelables.webp',
            'brochure' => null,
            'ordre' => 7,
        ],
        [
            'nom' => 'Bâtiment',
            'slug' => 'batiment',
            'filiere' => 'genie-civil',
            'resume' => 'Le BTS Bâtiment forme les étudiants à toutes les composantes de la construction, de l\'étude technique et financière du projet à la préparation d\'un budget prévisionnel, en passant par la conduite et le suivi du chantier.',
            'sections' => [
                [
                    'title' => 'Objectif du BTS Bâtiment',
                    'blocks' => [
                        [
                            'type' => 'text',
                            'text' => 'Le BTS Bâtiment amène ses titulaires à la maîtrise des compétences et techniques nécessaires à l\'accomplissement des travaux rencontrés dans le bâtiment, mais également à la compréhension des problèmes de droit, d\'économie et de gestion qui accompagnent les activités du bâtiment.',
                        ],
                        [
                            'type' => 'text',
                            'text' => 'Ses connaissances couvrent tant les nouvelles constructions que la réhabilitation de l\'existant (habitations privées et collectives, locaux commerciaux, installations publiques, ...).',
                        ],
                        [
                            'type' => 'text',
                            'text' => 'Les fonctions du diplômé du BTS Bâtiment vont de l\'étude technique et financière du projet à la préparation d\'un budget prévisionnel, en passant par la conduite et le suivi du chantier.',
                        ],
                    ],
                ],
                [
                    'title' => 'Débouchés',
                    'blocks' => [
                        [
                            'type' => 'subtitle',
                            'text' => 'Intégrer le marché du travail',
                        ],
                        [
                            'type' => 'text',
                            'text' => 'Le diplôme de BTS Bâtiment vous assure un emploi. Vous exercerez dans les entreprises du bâtiment, dans les cabinets d\'architectes, dans les bureaux d\'étude et d\'ingénieurie. Vous interviendrez en tant que Chef de projet, Chef d\'équipe puis après quelques années d\'expérience, Conducteur de travaux.',
                        ],
                        [
                            'type' => 'subtitle',
                            'text' => 'Poursuivre vos études',
                        ],
                        [
                            'type' => 'text',
                            'text' => 'Si votre souhait est de poursuivre vos études après votre BTS Bâtiment, il vous sera possible de reprendre un cycle universitaire, par exemple un MST en génie civil.',
                        ],
                    ],
                ],
            ],
            'diplome_requis' => 'Cette filière accueille les étudiants titulaires du BAC C, E, F4, GCE/AL ou un diplôme équivalent.',
            'images' => [
                'images/specialites/batiment-1.webp',
                'images/specialites/batiment-2.webp',
                'images/specialites/batiment-3.webp',
            ],
            'icone' => 'images/icones/batiment.webp',
            'brochure' => 'documents/brochures/batiment.pdf',
            'ordre' => 8,
        ],
        [
            'nom' => 'Travaux publics',
            'slug' => 'travaux-publics',
            'filiere' => 'genie-civil',
            'resume' => 'Les titulaires du BTS Travaux Publics participent à la réalisation des infrastructures et grands équipements d\'un pays: Routes, réseaux, chemins de fer, canaux, châteaux d\'eau, ports ... Ils peuvent aussi bien intervenir lors de la préparation des dossiers techniques qu\'en phase de rélisation et de suivi de chantier. Il est capable de remplir les fonctions suivantes: études; exploitation; préparation et réalisation. il veille aux impératifs de qualité, de coût, de sécurité.',
            'sections' => [
                [
                    'title' => 'Débouchés',
                    'blocks' => [
                        [
                            'type' => 'text',
                            'text' => 'Le titulaires du BTS Travaux publics intervient dans tous types de travaux publics comme:',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Les travaux routiers',
                                'Les travaux de canalisation',
                                'Consultant indépendant',
                                'Dessinateur BTP',
                                'Les terrassements généraux ou encore les travaux électriques.',
                            ],
                        ],
                        [
                            'type' => 'text',
                            'text' => 'Il exerce ses activités en tant que Chef de chantier, canalisateur ou Conducteur de travaux',
                        ],
                    ],
                ],
            ],
            'diplome_requis' => 'Cette filière accueille les étudiants titulaires du BAC C, E, F, GCE/AL ou un diplôme équivalent.',
            'images' => [
                'images/specialites/travaux-publics-1.webp',
                'images/specialites/travaux-publics-2.webp',
            ],
            'icone' => 'images/icones/travaux-publics.webp',
            'brochure' => 'documents/brochures/travaux-publics.pdf',
            'ordre' => 9,
        ],
        [
            'nom' => 'Urbanisme',
            'slug' => 'urbanisme',
            'filiere' => 'genie-civil',
            'resume' => 'Cette spécialité conduit à la formation des professionnels capables de concevoir et de conduire des actions cohérentes dans les domaines de l\'habitat, des équipements, des espaces publics et du développement communal ou intercommunal, en concertation avec les collectivités locales dont il est le conseil.',
            'sections' => [
                [
                    'title' => 'Objectif du BTS Urbanisme',
                    'blocks' => [
                        [
                            'type' => 'text',
                            'text' => 'Cette spécialité conduit à la formation des professionnels capables de concevoir et de conduire des actions cohérentes dans les domaines de l\'habitat, des équipements, des espaces publics et du développement communal ou intercommunal, en concertation avec les collectivités locales dont il est le conseil.',
                        ],
                    ],
                ],
                [
                    'title' => 'Compétences recherchées',
                    'blocks' => [
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences génériques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Travailler en autonomie, collaborer en équipe',
                                'Analyser, synthétiser un document (français, anglais)',
                                'Participer à /Mener une démarche de gestion de projet',
                                'Connaître et exploiter les réseaux professionnels et institutionnels des secteurs des travaux publics.',
                            ],
                        ],
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences spécifiques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Maîtriser les paramètres d\'études et de la conduite des travaux d\'aménagement urbain en bureau d\'étude et sur le site',
                                'Maîtriser l\'évolution démographique en vue de la précision des infrastructures et des équipements en milieu urbain',
                                'Sensibiliser et former à la résolution des problèmes de dégradation de l\'environnement',
                                'Exploiter les données d\'un site et produire les documents techniques en vue de la réalisation d\'un projet d\'aménagement',
                                'Maîtriser la faisabilité d\'un projet d\'aménagement urbain.',
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'Débouchés',
                    'blocks' => [
                        [
                            'type' => 'list',
                            'items' => [
                                'Chef de chantier',
                                'Conducteur de travaux',
                                'Dessinateur projecteur BTP',
                                'Responsble des prix',
                                'Chargé d\'affaires.',
                            ],
                        ],
                    ],
                ],
            ],
            'diplome_requis' => 'Cette filière accueille les étudiants titulaires du BAC C, D, E, F, GCE/AL ou un diplôme équivalent.',
            'images' => [
                'images/specialites/urbanisme-1.webp',
                'images/specialites/urbanisme-2.webp',
            ],
            'icone' => 'images/icones/urbanisme.webp',
            'brochure' => 'documents/brochures/urbanisme.pdf',
            'ordre' => 10,
        ],
        [
            'nom' => 'Menuiserie et ébénisterie',
            'slug' => 'menuiserie-et-ebenisterie',
            'filiere' => 'genie-civil',
            'resume' => 'Cette spécialité a pour objectif de former des techniciens supérieurs capables de développer, d\'industrialiser des produits à base de bois et de résoudre les problèmes techniques liés à leur mise en oeuvre. Ils exercent leur métier en atelier ou sur chantier, aussi bien en construction neuve qu\'en réhabitation ou en agencement. Ils travaillent le bois, ses dérivés et les matériaux associés (aluminium, produits verriers, mattières plastiques ...).',
            'sections' => [
                [
                    'title' => 'Compétences recherchées',
                    'blocks' => [
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences génériques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Travailler en autonomie, collaborer en équipe',
                                'Analyser, synthétiser un document (français, anglais)',
                                'Participer à /Mener une démarche de gestion de projet',
                                'Connaître et exploiter les réseaux professionnels et institutionnels des secteurs des travaux public.',
                            ],
                        ],
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences spécifiques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Résoudre les problèmes techniques liés à la mise en oeuvre du bois et ses dérivés',
                                'Participer aux études nécessaires à l\'industrialistion et assurer les missions telles que la gestion et la production, l\'organisation et planification, la gestion et l\'amélioration de la qualité, la valorisation des ressources humaines en production',
                                'Réaliser des ouvrages de menuiserie en bâtiment (escaliers, fermetures, cloisons, revêtements de sol, revêtement muraux...), d\'agencement (magasins, salles de bains...), d\'aménagent intérieur (mobilier, placards...) et de mobilier urbain (kiosques, aires de jeux...).',
                                'Exploiter les données d\'un site et produire les documents techniques en vue de la réalisation d\'un projet d\'aménagement',
                                'Maîtriser la faisabilité d\'un projet d\'aménagement urbain.',
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'Débouchés',
                    'blocks' => [
                        [
                            'type' => 'subtitle',
                            'text' => 'Cette spécialité vous offre la possibilité d\'être',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Agenceur/ceuse de cuisines et salles de bains',
                                'Responsable de scierie',
                                'Responsable d\'ordonancement',
                                'Technicien/ne de fabrication de mobilier et de menuiserie',
                                'Assistant qualité.',
                            ],
                        ],
                    ],
                ],
            ],
            'diplome_requis' => 'Cette filière accueille les étudiants titulaires du BAC A, C, D, ACC, CGE, ACA ou un diplôme équivalent.',
            'images' => [
                'images/specialites/menuiserie-et-ebenisterie-1.webp',
                'images/specialites/menuiserie-et-ebenisterie-2.webp',
            ],
            'icone' => 'images/icones/menuiserie-et-ebenisterie.webp',
            'brochure' => 'documents/brochures/menuiserie-et-ebenisterie.pdf',
            'ordre' => 11,
        ],
        [
            'nom' => 'Industrie alimentaire',
            'slug' => 'industrie-alimentaire',
            'filiere' => 'genie-biologique',
            'resume' => 'Cette spécialité vise à former des professionnels capables de transformer et de conserver les matières premières locales tout comme d’évaluer le danger microbiologique associé à la production des aliments.',
            'sections' => [
                [
                    'title' => 'Compétences recherchées',
                    'blocks' => [
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences génériques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Travailler en autonomie, collaborer en équipe',
                                'Analyser, synthétiser un document professionnel (français, anglais)',
                                'Communiquer à l’oral, à l’écrit, en entreprise ou extérieur (français, anglais)',
                                'Participer à /Mener une démarche de gestion de projet',
                                'Connaître et exploiter les réseaux professionnels et institutionnels des secteurs biologiques.',
                            ],
                        ],
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences spécifiques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Mettre en œuvre et contrôler les opérations de transformation, de fabrication des produits alimentaires ou biologiques',
                                'Gérer et planifier l’ensemble des moyens humains et matériels dans un contexte d’hygiène et de sécurité',
                                'Surveiller la qualité des matières premières et des produits tout au long des transformations',
                                'Définir de nouveaux équipements ou procédés pour optimiser le processus de qualités des produits',
                                'Prendre en charge ou participer à la démarche qualité de l’entreprise (animation, certification ISO, transformation, audit,...).',
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'Débouchés',
                    'blocks' => [
                        [
                            'type' => 'list',
                            'items' => [
                                'Industrie alimentaire',
                                'Industrie pharmaceutique',
                                'Industrie cosmétique.',
                            ],
                        ],
                    ],
                ],
            ],
            'diplome_requis' => 'Cette filière accueille les étudiants titulaires du BAC, GCE/AL ou un diplôme équivalent.',
            'images' => [
                'images/specialites/industrie-alimentaire-1.webp',
                'images/specialites/industrie-alimentaire-2.webp',
                'images/specialites/industrie-alimentaire-3.webp',
            ],
            'icone' => 'images/icones/industrie-alimentaire.webp',
            'brochure' => null,
            'ordre' => 12,
        ],
        [
            'nom' => 'Construction métallique',
            'slug' => 'construction-metallique',
            'filiere' => 'genie-mecanique',
            'resume' => 'Les titulaires du BTS option CM participent à la réalisation par assemblage d\'ouvrages métalliques diverse',
            'sections' => [
                [
                    'title' => 'Débouchés',
                    'blocks' => [
                        [
                            'type' => 'text',
                            'text' => 'Le titulaire de ce BTS participe à la réalisation par assemblage d\'ouvrage métalliques divers: ponts, pylônes, voies ferrées, écluses, vannes de barrage, apportements dans les ports, silos... Il travail en bureau d\'étude, à l\'telier et sur le chantier au moment du montage.',
                        ],
                        [
                            'type' => 'text',
                            'text' => 'Si votre souhait est de poursuivre vos études, vous pourrez intégrer une année de spécialisation ou une licence de technologie, option construction métalliques',
                        ],
                    ],
                ],
            ],
            'diplome_requis' => 'Cette filière accueille les étudiants titulaires du BAC C, E, F2, F3, GCE/AL ou un diplôme équivalent.',
            'images' => [
                'images/specialites/construction-metallique-1.webp',
                'images/specialites/construction-metallique-2.webp',
            ],
            'icone' => 'images/icones/construction-metallique.webp',
            'brochure' => null,
            'ordre' => 13,
        ],
        [
            'nom' => 'Construction et fabrication mécaniques',
            'slug' => 'construction-et-fabrication-mecaniques',
            'filiere' => 'genie-mecanique',
            'resume' => 'Option : Fabrication mécanique Cette spécialité a pour objectif de conférer des aptitudes à l production des équipements mécaniques, et à la conception technique de diverse composants mécniques. Elle permet ussi de procéder u contrôle de leur qualité. Objectifs',
            'sections' => [
                [
                    'title' => 'Objectifs',
                    'blocks' => [
                        [
                            'type' => 'list',
                            'items' => [
                                'Choisir une méthode optimale de production',
                                'Conduire une étude de fabication complexe',
                                'Maitriser les outils de TIC appliqués à la Fabrication Mécanique',
                                'Entretenir une chine pneumatique automatisée de production',
                                'Gerer un projet d\'analyse de fbrication mécanique',
                                'Ecrire les progrmmes informatiques pour les machines de fabrication mécanique à commande numérique',
                                'Maitriser le processus d\'élaboration des matériaux, etc.',
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'Débouchés',
                    'blocks' => [
                        [
                            'type' => 'list',
                            'items' => [
                                'Technicien en fabrication mécanique',
                                'Dessinateur concepteur',
                                'Technicien métallurgiste',
                                'Technicien sidérugiste.',
                            ],
                        ],
                    ],
                ],
            ],
            'diplome_requis' => 'Cette filière accueille les étudiants titulaires du BAC C, D, E, F ou un diplôme équivalent.',
            'images' => [
                'images/specialites/construction-et-fabrication-mecaniques-1.webp',
                'images/specialites/construction-et-fabrication-mecaniques-2.webp',
                'images/specialites/construction-et-fabrication-mecaniques-3.webp',
            ],
            'icone' => 'images/icones/construction-et-fabrication-mecaniques.webp',
            'brochure' => null,
            'ordre' => 14,
        ],
        [
            'nom' => 'Mécanique et électronique automobile',
            'slug' => 'mecanique-et-electronique-automobile',
            'filiere' => 'genie-mecanique',
            'resume' => 'Option : Maintenance après-Vente Automobile Le Technicien Supérieur en Maintenance et après-vente automobile est un généraliste qui satisfait ses clients en tant que technicien, conseiller, commerçant, gestionnaire d’activités et animateur des ressources humaines. Il pourra faire un diagnostic, maintenance et réparations complexes, tout comme l\'utilisation de logiciels d\'exploitation dédiés. Garant de la qualité du service après vente, il est également à l\'aise avec la réglémentation technique et environnementale. Objectifs',
            'sections' => [
                [
                    'title' => 'Objectifs',
                    'blocks' => [
                        [
                            'type' => 'list',
                            'items' => [
                                'Acquérir des compétences techniques en maintenance automobile',
                                'Acquérir des compétences en gestion et organisation après-vente',
                                'Bien mener la gestion des activités commerciales',
                                'Acquérir des compétences en gestion des pièces de rechange',
                                'Diagnostiquer des pannes mécaniques',
                                'Maîtriser les techniques et les outils de conception des systèmes Mécaniques, électriques et électroniques',
                                'Accueil Physique et téléphonique du client',
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'Débouchés',
                    'blocks' => [
                        [
                            'type' => 'list',
                            'items' => [
                                'Chef de garage',
                                'Manager de l\'après-vente',
                                'Réceptionnaire après-vente',
                                'Réceptionnaire d’atelier',
                                'Chef d’équipe',
                                'Chef d’atelier',
                                'Technicien de diagnostic',
                                'Conseiller technique',
                                'Mécanicien',
                                'Chef d\'équipe maintenance de ligne',
                                'Chef d\'atelier mécanique',
                                'Responsable réparationautomobile',
                                'Assistant après-vente automobile...',
                            ],
                        ],
                    ],
                ],
            ],
            'diplome_requis' => 'Cette filière accueille les étudiants titulaires du BAC C, D, E, F, MA, MAV ou un diplôme équivalent.',
            'images' => [
                'images/specialites/mecanique-et-electronique-automobile-1.webp',
                'images/specialites/mecanique-et-electronique-automobile-2.webp',
            ],
            'icone' => 'images/icones/mecanique-et-electronique-automobile.webp',
            'brochure' => null,
            'ordre' => 15,
        ],
        [
            'nom' => 'Marketing, commerce et vente',
            'slug' => 'marketing-commerce-et-vente',
            'filiere' => 'commerce-vente',
            'resume' => 'Cette spécialité vise à répondre à un besoin exprimé par les entreprises : s\'entourer des commerciaux outillés, parfaitement imprégnés de la logique marketing, sensibilisés sur les besoins changeants du consommateur et orientés vers le développement des ventes dans un environnement caractérisé par la concurrence.',
            'sections' => [
                [
                    'title' => 'Compétences recherchées',
                    'blocks' => [
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences génériques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'La compréhension de l\'économie internationale',
                                'La maîtrise d\'autres langues',
                                'L\'aptitude à la négociation',
                                'La compréhension de l\'environnement professionnel',
                                'Etre capable de travailler sous pression',
                                'L\'adaptabilité et polyvalence.',
                            ],
                        ],
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences spécifiques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Comprendre la logique marketing',
                                'Comprendre les défis à relever par l\'entreprise dans un environnement de concurrence',
                                'Comprendre les déterminants du succès commercial de l\'entreprise',
                                'Vendre de manière durable et rentable',
                                'Conduire une équipe commerciale vers l\'atteinte des objectifs',
                                'Faire la veille concurrentielle pour accroitre les ventes',
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'Débouchés',
                    'blocks' => [
                        [
                            'type' => 'list',
                            'items' => [
                                'Animateur des ventes',
                                'Attaché de la clientèle',
                                'Représentant commercial',
                                'Responsable des ventes.',
                            ],
                        ],
                    ],
                ],
            ],
            'diplome_requis' => 'Cette filière accueille les étudiants titulaires du BAC A, B, C, E, D, F, G ou un diplôme équivalent.',
            'images' => [
                'images/specialites/marketing-commerce-et-vente-1.webp',
                'images/specialites/marketing-commerce-et-vente-2.webp',
                'images/specialites/marketing-commerce-et-vente-3.webp',
            ],
            'icone' => 'images/icones/marketing-commerce-et-vente.webp',
            'brochure' => null,
            'ordre' => 16,
        ],
        [
            'nom' => 'Comptabilité et gestion des entreprises',
            'slug' => 'comptabilite-et-gestion-des-entreprises',
            'filiere' => 'gestion',
            'resume' => 'La spécialité Comptabilité et gestion des entreprises a pour but de munir les étudiants des connaissances et savoir-faire leur permettant de traduire de manière comptable, toutes les opérations commerciales ou financières et d\'établir les documents correspondants, d\'analyser les informations dont ils disposent pour préparer les décisions de gestion.',
            'sections' => [
                [
                    'title' => 'Compétences recherchées',
                    'blocks' => [
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences génériques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Avoir une bonne compréhension de l\'environnement économique et des entreprises',
                                'Maîtriser l\'outil informatique',
                                'Maîtriser la communication écrite et orale',
                                'Etre capable de diriger une équipe de travail.',
                            ],
                        ],
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences spécifiques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Réaliser la gestion des opérations comptables, fiscales et sociales (tenue des livres comptables, élaboration des états financiers, ...)',
                                'Analyser la rentabilité des activités de l\'organisation',
                                'Elaborer les budgets et suivre leur exécution',
                                'Centraliser, organiser et redresser les comptabilités des entreprises',
                                'Collaborer efficacement avec le chef dans la gestion de l\'entreprise',
                                'Contrôler et planifier la production',
                                'Maitriser les logiciels de base de la comptabilité.',
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'Débouchés',
                    'blocks' => [
                        [
                            'type' => 'list',
                            'items' => [
                                'Comptable en entreprise (PME)',
                                'Collaborateur comptable en cabinet',
                                'Assistant comptable dans les grandes entreprises',
                                'Gestionnaire de la paie',
                                'Responsable comptable',
                                'Trésorier',
                                'Contrôleur de gestion ; Etc.',
                            ],
                        ],
                    ],
                ],
            ],
            'diplome_requis' => 'Baccalauréat ABI, A, ACA, ACC, C, D, E, TI, F2, F3, F5, F8, CG, ESF, IH, HO, TO, SES, FIG, GCE A/L ou tout diplôme équivalent',
            'images' => [
                'images/specialites/comptabilite-et-gestion-des-entreprises-1.webp',
            ],
            'icone' => 'images/icones/comptabilite-et-gestion-des-entreprises.webp',
            'brochure' => null,
            'ordre' => 17,
        ],
        [
            'nom' => 'Comptabilité et finances publiques',
            'slug' => 'comptabilite-et-finances-publiques',
            'filiere' => 'gestion',
            'resume' => 'Le programme détaillé de cette spécialité est disponible auprès du service de la scolarité.',
            'sections' => [],
            'diplome_requis' => 'Baccalauréat, GCE A/L ou diplôme équivalent.',
            'images' => [
                'images/specialites/comptabilite-et-finances-publiques-1.webp',
            ],
            'icone' => 'images/icones/comptabilite-et-finances-publiques.webp',
            'brochure' => null,
            'ordre' => 18,
        ],
        [
            'nom' => 'Gestion des projets',
            'slug' => 'gestion-des-projets',
            'filiere' => 'gestion',
            'resume' => 'La spécialité Gestion des projets vise à former des techniciens capables de conduire des projets d\'entreprises de tout secteur (industriel, service, commercial, technologique, culturel), à travers le développement de la compréhension de l\'entreprise et l\'acquisition des connaissances théoriques et compétences pratiques du management de projet.',
            'sections' => [
                [
                    'title' => 'Compétences recherchées',
                    'blocks' => [
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences génériques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Avoir une bonne compréhension de l\'environnement économique et des entreprises',
                                'Maîtriser l\'outil informatique',
                                'Maîtriser la communication écrite et orale',
                                'Etre capable de diriger une équipe de travail.',
                            ],
                        ],
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences spécifiques',
                        ],
                        [
                            'type' => 'text',
                            'text' => 'Innover, créer, améliorer un projet, un produit ou un procédé :',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Identifier les partenariats locaux, nationaux et internationaux',
                                'Réaliser une veille technologique ou concurrentielle',
                            ],
                        ],
                        [
                            'type' => 'subtitle',
                            'text' => 'Coordonner un projet',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Réaliser une pré-étude',
                                'Définir les objectifs opérationnels d’un projet',
                                'Identifier les outils de travail',
                                'Constituer des dossiers techniques',
                            ],
                        ],
                        [
                            'type' => 'subtitle',
                            'text' => 'Rechercher et traiter l\'information',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Identifier les sources à exploiter',
                                'Analyser et synthétiser les informations trouvées.',
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'Débouchés',
                    'blocks' => [
                        [
                            'type' => 'list',
                            'items' => [
                                'Assistant au chef de projet',
                                'Assistant marketing',
                                'Responsable de la communication',
                                'Planificateur de projet.',
                            ],
                        ],
                    ],
                ],
            ],
            'diplome_requis' => 'Baccalauréat ABI, A, ACA, ACC, C, D, E, TI, F2, F3, F5, F8, CG, ESF, IH, HO, TO, SES, FIG, GCE A/L ou tout diplôme équivalent',
            'images' => [
                'images/specialites/gestion-des-projets-1.webp',
            ],
            'icone' => 'images/icones/gestion-des-projets.webp',
            'brochure' => null,
            'ordre' => 19,
        ],
        [
            'nom' => 'Gestion logistique et transport',
            'slug' => 'gestion-logistique-et-transport',
            'filiere' => 'gestion',
            'resume' => 'La spécialité Gestion logistique et transport vise à former des experts dans l\'organisation et le management des opérations de transport et des prestations logistiques sur les marchés locaux, régionaux, nationaux et internationaux en tenant compte de la complémentarité des modes de transport et du développement durable. Ils maîtrisent à cet effet les langues étrangères, la communication et les techniques de négociation, les techniques de gestion et d\'optimisation des flux de marchandise, la gestion des entrepôts ou des plates-formes ainsi que l\'exploitation des réseaux de transports urbains.',
            'sections' => [
                [
                    'title' => 'Compétences recherchées',
                    'blocks' => [
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences génériques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Avoir des connaissances générales de l\'environnement social et économique national, régional et international',
                                'Faire preuve de rigueur dans l\'organisation du travail et une capacité de réactivité et de créativité',
                                'Avoir une connaissance générale des langues étrangères, l\'anglais en particulier',
                                'Avoir le sens de la négociation, des relations commerciales et de la vente, comme de l\'après- vente.',
                            ],
                        ],
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences spécifiques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Assurer le pilotage d\'une chaine logistique',
                                'Connaître un large éventail des techniques liées à l\'exploitation de la chaine logistique (entreposage, manutention, transitique, productique, transport, etc.)',
                                'Faciliter et coordonner l\'échange entre les acteurs internes de l\'entreprise',
                                'Contribuer à la résolution rapide des problèmes entre fournisseurs et clients',
                                'Gérer le changement et promouvoir des solutions nécessaires à l\'adhésion des partenaires',
                                'Avoir une connaissance de la mercatique afin de cerner les attentes des consommateurs et à concevoir le meilleur compromis entre efficacité et qualité dans un contexte concurrentiel',
                                'Concevoir des structures adaptables, en interaction permanente avec les multiples composantes de l\'environnement',
                                'Mettre en œuvre des méthodes à la fois souples et rationnelles, pour matérialiser son action et permettre la régulation des flux à travers le développement d\'un système logistique et des réseaux d\'information performants',
                                'Avoir des connaissances en gestion comptable et financière ainsi qu\'en contrôle de gestion (plus centrées sur des outils de contrôle en temps réel que sur les méthodes comptables)',
                                'Connaître les outils nécessaires à l\'optimisation de la qualité et de la sécurité des flux physiques et informationnels',
                                'Prévoir des alternatives performantes en cas de perturbations des flux en cours',
                                'Pouvoir utiliser des logiciels spécifiques, contribuer à leur choix par l\'entreprise et favoriser leur exploitation',
                                'Avoir des connaissances en gestion comptable et financière ainsi qu\'en contrôle de gestion.',
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'Débouchés',
                    'blocks' => [
                        [
                            'type' => 'list',
                            'items' => [
                                'Responsable des unités de transport',
                                'Gestionnaire des stocks et des approvisionnements',
                                'Gestionnaire des réseaux d\'entrepôts et des plates-formes',
                                'Manutentionnaire',
                                'Transitaire et prestataire logistique',
                                'Commissionnaire agrée en douane',
                                'Agent contrôleur de la SGS',
                                'Employé au Guichet unique.',
                            ],
                        ],
                    ],
                ],
            ],
            'diplome_requis' => 'Baccalauréat ABI, A, ACA, ACC, C, D, E, TI, F2, F3, F5, F8, CG, ESF, IH, HO, TO, SES, FIG, GCE A/L ou tout diplôme équivalent',
            'images' => [
                'images/specialites/gestion-logistique-et-transport-1.webp',
                'images/specialites/gestion-logistique-et-transport-2.webp',
            ],
            'icone' => 'images/icones/gestion-logistique-et-transport.webp',
            'brochure' => null,
            'ordre' => 20,
        ],
        [
            'nom' => 'Microfinance',
            'slug' => 'microfinance',
            'filiere' => 'gestion',
            'resume' => 'Cette formation vise à combler le déficit en nombre et en qualité en matière de ressources humaines dont les établissements de crédit ont besoin pour la conduite de leurs activités. En outre, elle fournit aux entreprises, quel que soit leur domaine d\'activité, des collaborateurs pouvant leur permettre de tirer le maximum des opportunités que leur offre le système financier dans son évolution, son expansion et son arrimage à la finance mondiale.',
            'sections' => [
                [
                    'title' => 'Compétences recherchées',
                    'blocks' => [
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences génériques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Avoir une bonne compréhension de l\'environnement professionnel et économique',
                                'Maîtriser la communication écrite et orale',
                                'Etre apte à la vente et à la négociation commerciale',
                                'Maîtriser le cadre juridique de l\'activité et être apte à l\'analyse des règles fiscales applicables',
                                'Maîtriser les TIC applicable à la banque',
                                'Etre capable de prendre du recul face à une problématique donnée et de trouver la solution appropriée respectant à la fois l\'attente du client et la politique commerciale de son établissement',
                                'Faire preuve d\'adaptabilité et de polyvalence.',
                            ],
                        ],
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences spécifiques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Construire et développer une relation de confiance, personnalisée avec chaque client, dans le cadre de la politique commerciale arrêtée par son établissement',
                                'Développer quantitativement et qualitativement un fonds de commerce, notamment en améliorant le taux d\'équipement des clients en produits et services',
                                'Contribuer, par son action personnelle, à un accueil de qualité',
                                'Gérer et développer quantitativement et qualitativement un portefeuille de clientèle de professionnels dans le cadre de la politique commerciale arrêtée par son établissement',
                                'Contribuer au développement de son établissement par son action commerciale auprès des clients professionnels',
                                'Développer l\'approche globale des clients professionnels',
                                'Ouvrir et gérer les comptes',
                                'Distribuer les produits et services attachés aux comptes',
                                'Promouvoir et utiliser les technologies de transmission des informations',
                                'Distribuer les produits d\'épargne bancaires et non bancaires et de gestion de trésorerie',
                                'Distribuer les produits liés à l\'épargne financière et notamment ceux dits de gestion collective',
                                'Promouvoir les crédits à la consommation, des crédits immobiliers aux particuliers et montage des dossiers',
                                'Promouvoir les modes de financement du cycle d\'exploitation et des investissements des entreprises et montage des dossiers',
                                'Promouvoir les produits d\'assurance (Bancassurance)',
                                'Le suivi et gestion des risques clients',
                                'Conduire une analyse économique et financière de la situation du client, évaluation et suivi des risques.',
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'Débouchés',
                    'blocks' => [
                        [
                            'type' => 'list',
                            'items' => [
                                'Conseiller en microcrédit',
                                'Chargé de clientèle',
                                'Caissier ou guichetier',
                                'Fondé de pouvoir d\'EMF',
                                'Analyste de microcrédit; Etc.',
                            ],
                        ],
                    ],
                ],
            ],
            'diplome_requis' => 'Baccalauréat ABI, A, ACA, ACC, C, D, E, TI, F2, F3, F5, F8, CG, ESF, IH, HO, TO, SES, FIG, GCE A/L ou tout diplôme équivalent',
            'images' => [
                'images/specialites/microfinance-1.webp',
                'images/specialites/microfinance-2.webp',
            ],
            'icone' => 'images/icones/microfinance.webp',
            'brochure' => null,
            'ordre' => 21,
        ],
        [
            'nom' => 'Gestion des collectivités territoriales',
            'slug' => 'gestion-des-collectivites-territoriales',
            'filiere' => 'gestion',
            'resume' => 'Option Comptabilité et finances publiques Cette spécialité vise à former des techniciens supérieurs chargés de la gestion des communes, départements, régions et groupements à travers des méthodes novatrices de management et de financement tels que : l\'analyse financière et fiscale rétrospective et prospective ; la stratégie intercommunale et la mutualisation des moyens ; la coopération décentralisée ; le choix des investissements ; la communication financière ; la consolidation des comptes et des risques ; l\'évaluation des délégations de service public ; la gestion des dettes et des trésoreries avec des enjeux juridico-financiers des emprunts ; l\'automatisation des programmes d\'engagement et de règlement financiers.',
            'sections' => [
                [
                    'title' => 'Compétences recherchées',
                    'blocks' => [
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences génériques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Manager une équipe',
                                'Maîtriser les outils techniques managériales',
                                'Disposer d\'une aisance rédactionnelle',
                                'Savoir communiquer oralement en français et en anglais',
                                'Etre capable de travailler en équipe et en autonomie.',
                            ],
                        ],
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences spécifiques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Collecter, traiter, enregistrer toutes les informations à caractère financier et comptable des Administrations Publiques en général et des collectivités territoriales décentralisées en particulier',
                                'Rassembler tous les agrégats nécessaires à la confection des budgets de CTD ainsi que leur suivi périodique',
                                'Participer à l\'élaboration du budget de la collectivité territoriale et à son contrôle',
                                'Maitriser les conséquences financières des politiques d\'investissement et de financement des collectivités territoriales',
                                'Accompagner un projet dans ses aspects juridiques, financiers, des ressources humaines, d\'évaluation, et des TIC',
                                'Prendre en charge la politique de commande publique dans le cadre des marchés publics.',
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'Débouchés',
                    'blocks' => [
                        [
                            'type' => 'list',
                            'items' => [
                                'Comptables publics',
                                'Trésoriers payeurs',
                                'Caissiers',
                                'Receveur des finances',
                            ],
                        ],
                    ],
                ],
            ],
            'diplome_requis' => 'Baccalauréat ABI, A, ACA, ACC, C, D, E, TI, F2, F3, F5, F8, CG, ESF, IH, HO, TO, SES, FIG, GCE A/L ou tout diplôme équivalent',
            'images' => [
                'images/specialites/gestion-des-collectivites-territoriales-1.webp',
                'images/specialites/gestion-des-collectivites-territoriales-2.webp',
            ],
            'icone' => 'images/icones/gestion-des-collectivites-territoriales.webp',
            'brochure' => null,
            'ordre' => 22,
        ],
        [
            'nom' => 'Production cinématographique',
            'slug' => 'production-cinematographique',
            'filiere' => 'arts-culture',
            'resume' => 'Le programme détaillé de cette spécialité est disponible auprès du service de la scolarité.',
            'sections' => [],
            'diplome_requis' => 'Baccalauréat, GCE A/L ou diplôme équivalent.',
            'images' => [
                'images/specialites/production-cinematographique-1.webp',
            ],
            'icone' => null,
            'brochure' => null,
            'ordre' => 23,
        ],
        [
            'nom' => 'Industrie du textile et de l\'habillement',
            'slug' => 'industrie-du-textile-et-de-l-habillement',
            'filiere' => 'arts-culture',
            'resume' => 'Option : Industrie de l’habillement Le BTS "Industrie de l\'habillement" a pour objectif de former des créateurs des modèles de vêtements et d\'accessoires pour le prêt-à-porter, la haute couture et dans tous les secteurs de l\'industrie et du commerce liés à la création de mode. Le designer de mode suit toutes les étapes d\'un projet, de sa conception à sa création. À partir d\'une commande, il émet des hypothèses de travail puis procède à un choix conceptuel intégrant les données du marché. Ce choix aboutit ensuite à la réalisation d\'un prototype. Le Métier de designer de mode, au-delà ses activités de création donne de la valeur ajoutée aux textiles, il stimule la consommation du textile et la sauvegarde des emplois dans l\'agriculture pour les fibres textiles naturelles et l\'industrie chimique pour les fibres artificielles ou synthétiques.',
            'sections' => [
                [
                    'title' => 'Compétences recherchées',
                    'blocks' => [
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences génériques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Animer et manager une équipe',
                                'Former le personnel, gérer les ressources humaines',
                                'Communiquer dans un cadre professionnel en français anglais (oral/écrit)',
                                'Comprendre le fonctionnement des organisations',
                                'Créer et gérer une entreprise',
                                'Gérer un projet',
                                'Maîtriser l\'outil informatique de base',
                                'Participer à l\'élaboration du budget',
                                'Planifier et suivre des travaux',
                                'Développer la créativité, l\'esprit d\'analyse, la capacité de communication.',
                            ],
                        ],
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences spécifiques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Déterminer les lignes, les matières et les coloris de collections de vêtements, de tissus ou d\'accessoires',
                                'Intervenir dans des domaines tels que l\'environnement de la maison (arts de la table, tissus d\'ameublement), l\'industrie automobile ou les cosmétiques',
                                'Réaliser des cahiers de tendances',
                                'Effectuer des achats pour une boutique',
                                'Réaliser des books et des catalogues de vente',
                                'Concevoir des fibres et des textures.',
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'Débouchés',
                    'blocks' => [
                        [
                            'type' => 'list',
                            'items' => [
                                'Designer textile',
                                'Modéliste',
                                'Styliste dans les secteurs du vêtement, des accessoires, des arts de la table, de créateur textile mode et ameublement',
                                'Styliste-Modéliste',
                                'Costumier du spectacle (cinéma, théâtre, opéra.)',
                                'Attaché de presse de mode',
                                'Collaborateur de studio',
                                'Conseiller vestimentaire.',
                            ],
                        ],
                    ],
                ],
            ],
            'diplome_requis' => 'Cette filière accueille les étudiants titulaires du BAC, GCE/AL ou un diplôme équivalent.',
            'images' => [
                'images/specialites/industrie-du-textile-et-de-l-habillement-1.webp',
                'images/specialites/industrie-du-textile-et-de-l-habillement-2.webp',
            ],
            'icone' => 'images/icones/industrie-du-textile-et-de-l-habillement.webp',
            'brochure' => null,
            'ordre' => 24,
        ],
        [
            'nom' => 'Sage-femme',
            'slug' => 'sage-femme',
            'filiere' => 'medico-sanitaire',
            'resume' => 'Le BTS spécialité Sage-femme vise à former des professionnelles de santé ayant pour mission d’accompagner les femmes enceintes tout au long de leur grossesse, de l’établissement du diagnostic jusqu’au jour de l’accouchement, d’animer les séances de préparation à l’accouchement et assurer seule l’accouchement, de s’occuper du nouveau-né et si nécessaire d’accomplir les gestes de réanimation et surveiller le rétablissement de la mère. La Sage-femme assure le suivi gynécologique de la femme (prescription et pose contraceptifs, réduction périnéale et IVG médicamenteuse).',
            'sections' => [
                [
                    'title' => 'Débouchés',
                    'blocks' => [
                        [
                            'type' => 'text',
                            'text' => 'Responsable du personnel cuisinier, chef de cuisine, gestionnaire de stocks',
                        ],
                    ],
                ],
            ],
            'diplome_requis' => 'Baccalauréat ABI, A, ACA, ACC, C, D, E, TI, F2, F3, F5, F8, CG, ESF, IH, HO, TO, SES, FIG, GCE A/L ou tout diplôme équivalent',
            'images' => [
                'images/specialites/sage-femme-1.webp',
                'images/specialites/sage-femme-2.webp',
            ],
            'icone' => 'images/icones/sage-femme.webp',
            'brochure' => null,
            'ordre' => 25,
        ],
        [
            'nom' => 'Sciences infirmières',
            'slug' => 'sciences-infirmieres',
            'filiere' => 'medico-sanitaire',
            'resume' => 'Cette formation vise à mettre sur le marché de l’emploi des professionnels capables d’analyser une situation de santé, de prendre des décisions dans les limites de leur compétence et de mener des interventions seuls ou équipe multidisciplinaire.',
            'sections' => [
                [
                    'title' => 'Débouchés',
                    'blocks' => [
                        [
                            'type' => 'text',
                            'text' => 'la possibilité de travailler dans les Hôpitaux et cliniques publiques et privées ; les ONG (Organisations Non Gouvernementales) ; Secteur agroalimentaire ; Médecine du travail ; Recherche et formation ; Auto-emploi.',
                        ],
                    ],
                ],
            ],
            'diplome_requis' => 'Baccalauréat ABI, A, ACA, ACC, C, D, E, TI, F2, F3, F5, F8, CG, ESF, IH, HO, TO, SES, FIG, GCE A/L ou tout diplôme équivalent',
            'images' => [
                'images/specialites/sciences-infirmieres-1.webp',
            ],
            'icone' => 'images/icones/sciences-infirmieres.webp',
            'brochure' => null,
            'ordre' => 26,
        ],
        [
            'nom' => 'Odontostomatologie',
            'slug' => 'odontostomatologie',
            'filiere' => 'medico-sanitaire',
            'resume' => 'Cette spécialité vise à former des thérapeutes dentaires hautement qualifiés capables de fournir des soins dentaires holistiques de haute qualité dans les cliniques au niveau local, national ou international. Elle cible les jeunes hommes et femmes qui ont la vocation et sont prêts non seulement à gagner leur vie mais aussi et surtout à sauver des vies.',
            'sections' => [
                [
                    'title' => 'Compétences recherchées',
                    'blocks' => [
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences génériques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Etre responsable et réflexif',
                                'Disposer de capacités relationnelles',
                                'Disposer d\'une confiance et d\'une assurance avérées',
                                'Avoir la capacité critique et de questionnement',
                                'Développer une éthique et une normale professionnelles',
                                'Proactivité et aptitude à prendre l\'initiative',
                                'Avoir la pensée critique',
                                'Etre capable de maintenir les normes morales professionnelles',
                                'Disposer de compétences organisationnelles',
                                'Faire des compromis et être à l\'écoute',
                                'Améliorer la productivité grâce à la gestion du temps.',
                            ],
                        ],
                        [
                            'type' => 'subtitle',
                            'text' => 'Compétences spécifiques',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Fabrication et instrumentation de la denture',
                                'Soins dentaires pour patients',
                                'Administration de l\'anesthésie locale en dentisterie',
                                'Fabrication de denture',
                                'Procédures chirurgicales dentaires',
                                'Hygiène buccale',
                                'Prise de l\'histoire de la maladie (Interrogatoire)',
                                'Gestion des urgences dentaires à l\'hôpital. P. Ex. Aspiration du corps étranger, syncope, etc.',
                                'Aider le chirurgien dentaire à mener des procédures dentaires majeures, par exemple, l\'immobilisation de la mandibule fracturée',
                                'Prise de radiographies intra-orales',
                                'Fabrication de prothèses complètes et partielles',
                                'Manipulation des instruments',
                                'Fonctionnement des équipements auxiliaires, y compris les produits chimiques des unités de rayons X',
                                'Commande d\'instruments de remplacement, fournitures et équipements.',
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'Débouchés',
                    'blocks' => [
                        [
                            'type' => 'list',
                            'items' => [
                                'Prothésiste dentaire',
                                'Assistant Médecin dentaire.',
                            ],
                        ],
                    ],
                ],
            ],
            'diplome_requis' => 'Baccalauréat ABI, A, ACA, ACC, C, D, E, TI, F2, F3, F5, F8, CG, ESF, IH, HO, TO, SES, FIG, GCE A/L ou tout diplôme équivalent',
            'images' => [
                'images/specialites/odontostomatologie-1.webp',
                'images/specialites/odontostomatologie-2.webp',
            ],
            'icone' => 'images/icones/odontostomatologie.webp',
            'brochure' => null,
            'ordre' => 27,
        ],
        [
            'nom' => 'Génie logiciel',
            'slug' => 'genie-logiciel',
            'filiere' => 'genie-informatique',
            'resume' => 'Former des spécialistes capables d\'étudier les besoins des organisations, de les analyser avec Merise et UML et de développer des applications.',
            'sections' => [
                [
                    'title' => 'Objectifs',
                    'blocks' => [
                        [
                            'type' => 'list',
                            'items' => [
                                'Etudier les besoins dans divers domaines',
                                'Analyser à travers les méthodes Merise et UML',
                                'Développer des applications informatique (logiciels)',
                            ],
                        ],
                    ],
                ],
                [
                    'title' => 'Débouchés',
                    'blocks' => [
                        [
                            'type' => 'text',
                            'text' => 'Les débouchés sont nombreux et dans tout type d\'organisation:',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Employé au niveau d\'agent de maîtrise/cadre moyen',
                                'Entrepreneur',
                                'Consultant indépendant.',
                            ],
                        ],
                        [
                            'type' => 'subtitle',
                            'text' => 'Les compétences permettront de devenir',
                        ],
                        [
                            'type' => 'list',
                            'items' => [
                                'Chef de projet Informatique',
                                'Consultant logiciel',
                                'Développeur d\'applications',
                                'Administrateur des systèmes',
                            ],
                        ],
                    ],
                ],
            ],
            'diplome_requis' => 'Cette filière accueille les étudiants titulaires du BAC C, D, E, GCE/AL ou un diplôme équivalent.',
            'images' => [
                'images/specialites/genie-logiciel-1.webp',
                'images/specialites/genie-logiciel-2.webp',
            ],
            'icone' => 'images/icones/genie-logiciel.webp',
            'brochure' => 'documents/brochures/genie-logiciel.pdf',
            'ordre' => 28,
        ],
        [
            'nom' => 'Maintenance des systèmes informatiques',
            'slug' => 'maintenance-des-systemes-informatiques',
            'filiere' => 'genie-informatique',
            'resume' => 'Le BTS en Maintenance des Systèmes Informatique dote l’étudiant des compétences pour assurer l’intégrité, la sécurité et la pérennité d’un système informatique (ordinateursmaintenance logicielle et matérielle, réseaux informatiques) au sein d’une entreprise. Diagnostiquer et localiser la panne ou l’anomalie. Conseiller et apporter une assistance technique à l’utilisateur. Installer, configurer et administre un parc informatique (ordinateurs terminaux mobiles, périphériques, système d\'exploitation et logiciels).',
            'sections' => [
                [
                    'title' => 'Débouchés',
                    'blocks' => [
                        [
                            'type' => 'text',
                            'text' => 'Le titulaire d’un BTS en Maintenance des Systèmes Informatiques peut exercer en tant que : Agent de maintenance, Chef d’équipe, Responsable maintenance de la cellule informatique, Consultant en informatique, Distributeur du matériel informatique, Responsable de La maintenance informatique et bureautique.',
                        ],
                    ],
                ],
            ],
            'diplome_requis' => 'Cette filière accueille les étudiants titulaires du BAC C, D, TI, E, F2, F3, GCE/AL ou un diplôme équivalent.',
            'images' => [
                'images/specialites/maintenance-des-systemes-informatiques-1.webp',
                'images/specialites/maintenance-des-systemes-informatiques-2.webp',
            ],
            'icone' => 'images/icones/maintenance-des-systemes-informatiques.webp',
            'brochure' => 'documents/brochures/maintenance-des-systemes-informatiques.pdf',
            'ordre' => 29,
        ],
    ],
];
