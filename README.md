# devColab

**devColab** est une plateforme communautaire destinée aux développeurs,
conçue par et pour les développeurs. Elle a pour objectif de leur
permettre de se rencontrer, d'échanger, de poser des questions, de
partager leurs connaissances, de présenter leurs projets et de
collaborer.

La plateforme vise une expérience complète pour **discuter, apprendre,
partager, découvrir et construire ensemble**.

## Sommaire

-   [Fonctionnalités prévues](#fonctionnalités-prévues)
-   [Stack technique](#stack-technique)
-   [Prérequis](#prérequis)
-   [Installation](#installation)
-   [Configuration](#configuration)
-   [Base de données](#base-de-données)
-   [Structure du projet](#structure-du-projet)
-   [Développement](#développement)
-   [Tests](#tests)
-   [Stockage des fichiers](#stockage-des-fichiers)
-   [Gestion des erreurs](#gestion-des-erreurs)
-   [Conventions](#conventions)
-   [État du projet](#état-du-projet)

## Fonctionnalités prévues

-   **Profils développeurs** : présentation des membres et de leurs
    compétences.
-   **Technologies** : catalogue des langages, frameworks et outils
    utilisés.
-   **Publications** : partage de contenus, questions et discussions.
-   **Commentaires, réactions et réponses** : interactions autour des
    publications.
-   **Communautés** : création de groupes publics ou privés et adhésion.
-   **Projets** : présentation de réalisations et recherche de
    collaborateurs.
-   **Offres de collaboration** : propositions et suivi des
    collaborations.
-   **Messagerie** : conversations individuelles ou collectives.
-   **Signalements** : remontée de contenus ou comportements
    problématiques.

Cette liste représente le périmètre fonctionnel prévu ; la disponibilité
de chaque fonctionnalité dépend de son état réel d'implémentation.

## Stack technique

  Élément                        Technologie
  ------------------------------ ----------------
  Backend                        Laravel 13
  Langage backend                PHP
  Frontend                       Vue.js
  Intégration frontend/backend   Inertia.js
  Base de données                PostgreSQL
  Temps réel / broadcasting      Laravel Reverb
  Bundler                        Vite
  Dépendances PHP                Composer
  Dépendances JavaScript         npm
  Tests                          Pest

Le projet a été initialisé avec le **Vue Starter Kit de Laravel** et le
système d'authentification intégré. Le support des équipes du starter
kit n'a pas été activé.

## Prérequis

-   PHP compatible avec Laravel 13 et les extensions nécessaires ;
-   Composer ;
-   Node.js et npm ;
-   PostgreSQL ;
-   Git.

Vérifier les installations :

``` bash
php -v
composer --version
node -v
npm -v
psql --version
```

## Installation

Cloner le dépôt et entrer dans le répertoire :

``` bash
git clone <URL_DU_DEPOT>
cd devColab
```

Installer les dépendances :

``` bash
composer install
npm install
```

Créer le fichier d'environnement et générer la clé :

``` bash
cp .env.example .env
php artisan key:generate
```

Configurer PostgreSQL dans `.env`, puis exécuter les migrations :

``` bash
php artisan migrate
```

Compiler les assets :

``` bash
npm run build
```

Pour le développement, lancer deux terminaux.

Terminal 1 :

``` bash
php artisan serve
```

Terminal 2 :

``` bash
npm run dev
```

L'application est généralement accessible à `http://localhost:8000`.

## Configuration

Exemple de configuration de base dans `.env` :

``` dotenv
APP_NAME=devColab
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=UTC
APP_LOCALE=fr

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=devcolab
DB_USERNAME=<UTILISATEUR_POSTGRESQL>
DB_PASSWORD=<MOT_DE_PASSE>

BROADCAST_CONNECTION=reverb
```

Adapter les identifiants PostgreSQL à l'environnement local. Ne jamais
versionner `.env` ni les secrets qu'il contient.

En production, utiliser notamment :

``` dotenv
APP_ENV=production
APP_DEBUG=false
```

Les paramètres complets de Reverb, du courrier électronique, des files
d'attente et du cache doivent être définis selon l'environnement de
déploiement.

## Base de données

Le projet utilise PostgreSQL. Le schéma métier comprend notamment :

-   utilisateurs et profils ;
-   technologies et associations utilisateur-technologie ;
-   communautés et membres ;
-   publications, tags, commentaires, réactions et réponses ;
-   projets et membres des projets ;
-   offres de collaboration ;
-   conversations, membres et messages ;
-   signalements.

Les migrations Laravel sont la référence pour faire évoluer le schéma.

Consulter leur état :

``` bash
php artisan migrate:status
```

Consulter la base :

``` bash
php artisan db:show
```

**Attention :** `php artisan migrate:fresh` supprime toutes les tables
avant de les recréer. Cette commande est réservée aux environnements où
les données peuvent être supprimées.

## Structure du projet

``` text
app/
├── Http/
│   ├── Controllers/
│   └── Middleware/
└── Models/

database/
├── factories/
├── migrations/
└── seeders/

resources/
├── js/
│   ├── components/
│   ├── layouts/
│   └── pages/
└── views/

routes/
├── web.php
├── channels.php
└── console.php
```

Les pages Vue sont organisées par domaine fonctionnel dans
`resources/js/pages/`. Les contrôleurs sont créés au fur et à mesure de
l'implémentation des modules.

## Développement

Commandes utiles :

``` bash
# Lister les routes
php artisan route:list

# Vérifier les migrations
php artisan migrate:status

# Nettoyer les caches Laravel
php artisan optimize:clear

# Compiler les assets
npm run build
```

Pour le développement frontend, utiliser `npm run dev`. Pour le serveur
backend, utiliser `php artisan serve`.

## Tests

Le projet utilise Pest :

``` bash
php artisan test
```

Les scripts de formatage et de vérification disponibles sont ceux
définis dans `composer.json` et `package.json`.

## Stockage des fichiers

Pour rendre les fichiers du disque `public` accessibles, créer le lien
symbolique si nécessaire :

``` bash
php artisan storage:link
```

En base de données, conserver des chemins relatifs au disque de stockage
plutôt que des chemins absolus propres à une machine.

## Gestion des erreurs

Le projet prévoit une page d'erreur Inertia/Vue commune pour les
principaux codes HTTP :

  Code   Signification
  ------ -----------------------
  403    Accès refusé
  404    Ressource introuvable
  419    Session expirée
  429    Trop de requêtes
  500    Erreur interne
  503    Service indisponible

La page Vue est prévue dans `resources/js/pages/Errors/Http.vue`. Le
rendu des exceptions est configuré dans `bootstrap/app.php`.

En production, `APP_DEBUG` doit être désactivé afin de ne pas exposer
les détails techniques des exceptions.

## Conventions

-   Développer les fonctionnalités module par module.
-   Créer les contrôleurs au moment de l'implémentation du module
    concerné.
-   Faire évoluer le schéma PostgreSQL avec les migrations Laravel.
-   Définir et utiliser les relations Eloquent.
-   Utiliser Inertia.js pour transmettre les données du backend aux
    pages Vue.
-   Privilégier les classes de thème sémantiques (`bg-background`,
    `text-foreground`, `bg-card`, `text-muted-foreground`, `bg-primary`,
    etc.) pour conserver la cohérence des thèmes clair et sombre.
-   Ne jamais exposer de secrets dans le dépôt.

## État du projet

Le socle Laravel 13, le Vue Starter Kit et la connexion PostgreSQL ont
été mis en place. Les migrations et les modèles des principaux domaines
métier ont été créés.

Le développement se poursuit progressivement, module par module. Cette
documentation devra être mise à jour à mesure que les fonctionnalités et
les procédures de déploiement seront stabilisées.

------------------------------------------------------------------------

**devColab** --- Par les développeurs, pour les développeurs.
