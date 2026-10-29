<?php

namespace Database\Seeders;

use App\Models\Community;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommunitySeeder extends Seeder
{
    /**
     * Créer des communautés de démonstration
     * et leurs membres.
     */
    public function run(): void
    {
        $communityData = [
            [
                'name' => 'Laravel Developers',
                'slug' => 'laravel-developers',
                'description' => 'Une communauté francophone dédiée à Laravel, Eloquent, Artisan et à la construction d’applications web modernes.',
                'creator' => 'Alexandre Martin',
                'is_private' => false,
            ],
            [
                'name' => 'Vue.js Developers',
                'slug' => 'vuejs-developers',
                'description' => 'Échangeons sur Vue.js, Composition API, Pinia, Vue Router et les bonnes pratiques du développement frontend.',
                'creator' => 'Sophie Bernard',
                'is_private' => false,
            ],
            [
                'name' => 'Python & Data Science',
                'slug' => 'python-data-science',
                'description' => 'Python, analyse de données, machine learning, intelligence artificielle et partage de projets.',
                'creator' => 'Thomas Dubois',
                'is_private' => false,
            ],
            [
                'name' => 'PostgreSQL Community',
                'slug' => 'postgresql-community',
                'description' => 'Discussions autour de PostgreSQL, de la conception des bases de données, des performances et de la sécurité.',
                'creator' => 'Emma Laurent',
                'is_private' => false,
            ],
            [
                'name' => 'DevOps & Cloud',
                'slug' => 'devops-cloud',
                'description' => 'Docker, CI/CD, Linux, cloud computing, automatisation et déploiement d’applications.',
                'creator' => 'Lucas Petit',
                'is_private' => false,
            ],
            [
                'name' => 'Open Source Builders',
                'slug' => 'open-source-builders',
                'description' => 'Une communauté pour présenter des projets open source, trouver des contributeurs et collaborer.',
                'creator' => 'Camille Moreau',
                'is_private' => false,
            ],
            [
                'name' => 'Mobile Developers',
                'slug' => 'mobile-developers',
                'description' => 'Flutter, développement Android et iOS, architectures mobiles, tests et publication d’applications.',
                'creator' => 'Nicolas Simon',
                'is_private' => false,
            ],
            [
                'name' => 'Java & Spring Boot',
                'slug' => 'java-spring-boot',
                'description' => 'Développement Java, Spring Boot, API REST, architectures backend et applications d’entreprise.',
                'creator' => 'Olga Petrova',
                'is_private' => false,
            ],
            [
                'name' => 'Web Security Lab',
                'slug' => 'web-security-lab',
                'description' => 'Sécurité des applications web, authentification, protection des API et bonnes pratiques de développement sécurisé.',
                'creator' => 'Karim Benali',
                'is_private' => false,
            ],
            [
                'name' => 'React & TypeScript',
                'slug' => 'react-typescript',
                'description' => 'React, TypeScript, composants réutilisables, gestion d’état et qualité du code frontend.',
                'creator' => 'Julie Lefèvre',
                'is_private' => false,
            ],
            [
                'name' => 'Startup Developers',
                'slug' => 'startup-developers',
                'description' => 'Construire un produit numérique, choisir son architecture, livrer un MVP et collaborer en équipe.',
                'creator' => 'Maxime Roux',
                'is_private' => true,
            ],
            [
                'name' => 'Architecture & Clean Code',
                'slug' => 'architecture-clean-code',
                'description' => 'Architecture logicielle, principes SOLID, tests automatisés, refactoring et maintenabilité.',
                'creator' => 'Anna Müller',
                'is_private' => false,
            ],
        ];

        $users = User::query()
            ->orderBy('id')
            ->get();

        if ($users->isEmpty()) {
            $this->command?->warn(
                'Aucun utilisateur trouvé. Exécutez DeveloperSeeder avant CommunitySeeder.'
            );

            return;
        }

        foreach ($communityData as $index => $data) {
            $creator = $users->firstWhere('name', $data['creator']);

            if (! $creator) {
                $this->command?->warn(
                    "Créateur introuvable : {$data['creator']}. Communauté ignorée : {$data['name']}."
                );

                continue;
            }

            $community = Community::firstOrCreate(
                ['slug' => $data['slug']],
                [
                    'creator_id' => $creator->id,
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'is_private' => $data['is_private'],
                    'is_active' => true,
                ]
            );

            // Garantir que le créateur est membre et administrateur.
            $community->users()->syncWithoutDetaching([
                $community->creator_id => [
                    'role' => 'admin',
                    'joined_at' => now(),
                ],
            ]);

            // Ajouter quelques membres de démonstration.
            $members = [];

            $memberCount = min(4, max(0, $users->count() - 1));

            for ($offset = 0; $offset < $memberCount; $offset++) {
                $member = $users[($index + $offset) % $users->count()];

                if ($member->id === $community->creator_id) {
                    $member = $users->first(
                        fn(User $user) =>
                        $user->id !== $community->creator_id
                    );
                }

                if ($member && $member->id !== $community->creator_id) {
                    $members[$member->id] = [
                        'role' => 'member',
                        'joined_at' => now()->subDays($offset + 1),
                    ];
                }
            }

            if ($members !== []) {
                $community->users()->syncWithoutDetaching($members);
            }

            $this->command?->info(
                "Communauté traitée : {$community->name}"
            );
        }

        $this->command?->info(
            'Initialisation des communautés terminée.'
        );
    }
}
