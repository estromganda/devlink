<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DeveloperSeeder extends Seeder
{
    public function run(): void
    {
        $developers = [
            [
                'name' => 'Alexandre Martin',
                'email' => 'alexandre.martin@example.test',
                'headline' => 'Développeur Full-Stack Laravel & Vue.js',
                'bio' => 'Développeur passionné par la création d’applications web modernes. Je travaille principalement avec Laravel, Vue.js et PostgreSQL. Je recherche des développeurs pour collaborer sur des projets open source.',
                'location' => 'Paris, France',
                'website' => 'https://example.com',
                'github_url' => 'https://github.com',
                'linkedin_url' => 'https://www.linkedin.com',
            ],
            [
                'name' => 'Sophie Bernard',
                'email' => 'sophie.bernard@example.test',
                'headline' => 'Développeuse Frontend Vue.js',
                'bio' => 'Je conçois des interfaces utilisateur accessibles, performantes et agréables à utiliser. J’aime travailler avec Vue.js, TypeScript et Tailwind CSS.',
                'location' => 'Lyon, France',
                'website' => null,
                'github_url' => 'https://github.com',
                'linkedin_url' => 'https://www.linkedin.com',
            ],
            [
                'name' => 'Thomas Dubois',
                'email' => 'thomas.dubois@example.test',
                'headline' => 'Développeur Backend PHP',
                'bio' => 'Spécialisé dans le développement backend, les API REST et l’architecture logicielle. J’utilise Laravel et PostgreSQL pour construire des applications fiables.',
                'location' => 'Bruxelles, Belgique',
                'website' => null,
                'github_url' => 'https://github.com',
                'linkedin_url' => null,
            ],
            [
                'name' => 'Emma Laurent',
                'email' => 'emma.laurent@example.test',
                'headline' => 'Développeuse mobile Flutter',
                'bio' => 'Je développe des applications mobiles multiplateformes avec Flutter et Dart. Je m’intéresse particulièrement à Firebase, aux notifications push et aux expériences mobiles fluides.',
                'location' => 'Genève, Suisse',
                'website' => 'https://example.com',
                'github_url' => 'https://github.com',
                'linkedin_url' => 'https://www.linkedin.com',
            ],
            [
                'name' => 'Lucas Petit',
                'email' => 'lucas.petit@example.test',
                'headline' => 'Ingénieur DevOps & Cloud',
                'bio' => 'Passionné par Linux, Docker, l’intégration continue et le déploiement automatisé. Je cherche à rendre les environnements de développement plus simples et plus fiables.',
                'location' => 'Montréal, Canada',
                'website' => null,
                'github_url' => 'https://github.com',
                'linkedin_url' => 'https://www.linkedin.com',
            ],
            [
                'name' => 'Camille Moreau',
                'email' => 'camille.moreau@example.test',
                'headline' => 'Développeuse Python & Data',
                'bio' => 'Je travaille sur des applications Python, le traitement des données et l’automatisation. Je m’intéresse également au machine learning et à la visualisation des données.',
                'location' => 'Toulouse, France',
                'website' => 'https://example.com',
                'github_url' => 'https://github.com',
                'linkedin_url' => null,
            ],
            [
                'name' => 'Nicolas Simon',
                'email' => 'nicolas.simon@example.test',
                'headline' => 'Développeur React & TypeScript',
                'bio' => 'Développeur frontend orienté composants réutilisables, performances et expérience utilisateur. J’aime collaborer avec les équipes backend pour concevoir des produits cohérents.',
                'location' => 'Bordeaux, France',
                'website' => null,
                'github_url' => 'https://github.com',
                'linkedin_url' => 'https://www.linkedin.com',
            ],
            [
                'name' => 'Olga Petrova',
                'email' => 'olga.petrova@example.test',
                'headline' => 'Développeuse Java & Spring Boot',
                'bio' => 'Je conçois des services backend et des API avec Java et Spring Boot. Je m’intéresse à la conception de systèmes distribués, aux tests automatisés et à la qualité du code.',
                'location' => 'Vienne, Autriche',
                'website' => null,
                'github_url' => 'https://github.com',
                'linkedin_url' => 'https://www.linkedin.com',
            ],
            [
                'name' => 'Karim Benali',
                'email' => 'karim.benali@example.test',
                'headline' => 'Développeur Full-Stack JavaScript',
                'bio' => 'Je construis des applications web avec Node.js, Express, Vue.js et MongoDB. J’aime transformer des idées en produits fonctionnels et travailler en équipe.',
                'location' => 'Casablanca, Maroc',
                'website' => 'https://example.com',
                'github_url' => 'https://github.com',
                'linkedin_url' => null,
            ],
            [
                'name' => 'Julie Lefèvre',
                'email' => 'julie.lefevre@example.test',
                'headline' => 'UI/UX Designer & Développeuse Frontend',
                'bio' => 'À la croisée du design et du développement, je crée des interfaces intuitives et les intègre avec HTML, CSS, JavaScript et Vue.js.',
                'location' => 'Nantes, France',
                'website' => 'https://example.com',
                'github_url' => 'https://github.com',
                'linkedin_url' => 'https://www.linkedin.com',
            ],
            [
                'name' => 'Maxime Roux',
                'email' => 'maxime.roux@example.test',
                'headline' => 'Développeur Laravel & API REST',
                'bio' => 'Je développe des applications métier avec Laravel. Mes sujets favoris sont la conception des API, la sécurité, les tests et l’optimisation des requêtes SQL.',
                'location' => 'Lille, France',
                'website' => null,
                'github_url' => 'https://github.com',
                'linkedin_url' => 'https://www.linkedin.com',
            ],
            [
                'name' => 'Anna Müller',
                'email' => 'anna.mueller@example.test',
                'headline' => 'Ingénieure en cybersécurité',
                'bio' => 'Je m’intéresse à la sécurité des applications web, à l’authentification, à la gestion des accès et à l’analyse des vulnérabilités. Je souhaite échanger avec des développeurs soucieux de la sécurité.',
                'location' => 'Berlin, Allemagne',
                'website' => null,
                'github_url' => 'https://github.com',
                'linkedin_url' => 'https://www.linkedin.com',
            ],
            [
                'name' => 'David Garcia',
                'email' => 'david.garcia@example.test',
                'headline' => 'Développeur iOS & Swift',
                'bio' => 'Je crée des applications iOS avec Swift et SwiftUI. Je m’intéresse à l’architecture mobile, à l’accessibilité et aux bonnes pratiques de développement.',
                'location' => 'Madrid, Espagne',
                'website' => 'https://example.com',
                'github_url' => 'https://github.com',
                'linkedin_url' => null,
            ],
            [
                'name' => 'Fatou Diop',
                'email' => 'fatou.diop@example.test',
                'headline' => 'Développeuse Web & PHP',
                'bio' => 'Je développe des applications web avec PHP, Laravel, JavaScript et MySQL. J’aime apprendre de nouvelles technologies et participer à des projets collaboratifs.',
                'location' => 'Dakar, Sénégal',
                'website' => null,
                'github_url' => 'https://github.com',
                'linkedin_url' => 'https://www.linkedin.com',
            ],
            [
                'name' => 'Ivan Smirnov',
                'email' => 'ivan.smirnov@example.test',
                'headline' => 'Développeur C++ & systèmes',
                'bio' => 'Je travaille sur des logiciels systèmes et des applications performantes en C++. Je m’intéresse à Linux, à la programmation concurrente et à l’optimisation mémoire.',
                'location' => 'Prague, Tchéquie',
                'website' => null,
                'github_url' => 'https://github.com',
                'linkedin_url' => null,
            ],
            [
                'name' => 'Claire Fontaine',
                'email' => 'claire.fontaine@example.test',
                'headline' => 'Développeuse spécialisée en tests automatisés',
                'bio' => 'Je contribue à améliorer la fiabilité des applications grâce aux tests unitaires, aux tests fonctionnels et à l’intégration continue. Je travaille notamment avec PHP et JavaScript.',
                'location' => 'Strasbourg, France',
                'website' => null,
                'github_url' => 'https://github.com',
                'linkedin_url' => 'https://www.linkedin.com',
            ],
            [
                'name' => 'Mohamed Hassan',
                'email' => 'mohamed.hassan@example.test',
                'headline' => 'Développeur Python & Django',
                'bio' => 'Je conçois des applications web et des services backend avec Python et Django. Je m’intéresse aux API, à PostgreSQL et à la conception de logiciels maintenables.',
                'location' => 'Le Caire, Égypte',
                'website' => 'https://example.com',
                'github_url' => 'https://github.com',
                'linkedin_url' => null,
            ],
            [
                'name' => 'Elena Ivanova',
                'email' => 'elena.ivanova@example.test',
                'headline' => 'Développeuse Kotlin & Android',
                'bio' => 'Je développe des applications Android avec Kotlin. Je m’intéresse à Jetpack Compose, à l’architecture applicative et à la qualité des interfaces mobiles.',
                'location' => 'Sofia, Bulgarie',
                'website' => null,
                'github_url' => 'https://github.com',
                'linkedin_url' => 'https://www.linkedin.com',
            ],
            [
                'name' => 'Antoine Girard',
                'email' => 'antoine.girard@example.test',
                'headline' => 'Architecte logiciel & PostgreSQL',
                'bio' => 'J’aide à concevoir des applications robustes et évolutives. Mes domaines de prédilection sont la modélisation des données, PostgreSQL, les API et les architectures backend.',
                'location' => 'Lausanne, Suisse',
                'website' => 'https://example.com',
                'github_url' => 'https://github.com',
                'linkedin_url' => 'https://www.linkedin.com',
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@example.test',
                'headline' => 'Développeuse Full-Stack & Open Source',
                'bio' => 'Je participe à des projets open source et développe des applications web avec TypeScript, Node.js et React. Je suis toujours intéressée par de nouvelles collaborations.',
                'location' => 'Londres, Royaume-Uni',
                'website' => 'https://example.com',
                'github_url' => 'https://github.com',
                'linkedin_url' => 'https://www.linkedin.com',
            ],
        ];

        foreach ($developers as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('DevLinkDemo2026!'),
                    'email_verified_at' => now(),
                ],
            );

            $user->profile()->updateOrCreate(
                [],
                [
                    'headline' => $data['headline'],
                    'bio' => $data['bio'],
                    'location' => $data['location'],
                    'website' => $data['website'],
                    'github_url' => $data['github_url'],
                    'linkedin_url' => $data['linkedin_url'],
                ],
            );
        }

        $this->command?->info(
            count($developers) . ' profils de démonstration traités.'
        );
    }
}
