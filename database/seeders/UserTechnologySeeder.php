<?php

namespace Database\Seeders;

use App\Models\Technology;
use App\Models\User;
use App\Models\UserTechnology;
use Illuminate\Database\Seeder;

class UserTechnologySeeder extends Seeder
{
    public function run(): void
    {
        $developers = User::query()
            ->whereHas('profile')
            ->with('profile')
            ->get();

        $technologies = Technology::all()->keyBy('name');

        $developerTechnologies = [
            'Alexandre Martin' => [
                ['name' => 'PHP', 'level' => 'expert', 'years' => 8, 'primary' => true],
                ['name' => 'Laravel', 'level' => 'expert', 'years' => 6, 'primary' => false],
                ['name' => 'Vue.js', 'level' => 'advanced', 'years' => 4, 'primary' => false],
                ['name' => 'PostgreSQL', 'level' => 'advanced', 'years' => 5, 'primary' => false],
                ['name' => 'Docker', 'level' => 'intermediate', 'years' => 2, 'primary' => false],
            ],

            'Sophie Bernard' => [
                ['name' => 'Python', 'level' => 'expert', 'years' => 7, 'primary' => true],
                ['name' => 'Django', 'level' => 'advanced', 'years' => 5, 'primary' => false],
                ['name' => 'PostgreSQL', 'level' => 'advanced', 'years' => 4, 'primary' => false],
                ['name' => 'Docker', 'level' => 'intermediate', 'years' => 2, 'primary' => false],
            ],

            'Thomas Dubois' => [
                ['name' => 'JavaScript', 'level' => 'expert', 'years' => 8, 'primary' => true],
                ['name' => 'React', 'level' => 'expert', 'years' => 6, 'primary' => false],
                ['name' => 'TypeScript', 'level' => 'advanced', 'years' => 4, 'primary' => false],
                ['name' => 'Node.js', 'level' => 'advanced', 'years' => 5, 'primary' => false],
            ],

            'Emma Laurent' => [
                ['name' => 'Java', 'level' => 'expert', 'years' => 9, 'primary' => true],
                ['name' => 'Spring Boot', 'level' => 'expert', 'years' => 6, 'primary' => false],
                ['name' => 'PostgreSQL', 'level' => 'advanced', 'years' => 5, 'primary' => false],
                ['name' => 'Docker', 'level' => 'advanced', 'years' => 3, 'primary' => false],
            ],

            'Lucas Petit' => [
                ['name' => 'Dart', 'level' => 'expert', 'years' => 5, 'primary' => true],
                ['name' => 'Flutter', 'level' => 'expert', 'years' => 5, 'primary' => false],
                ['name' => 'Firebase', 'level' => 'advanced', 'years' => 3, 'primary' => false],
                ['name' => 'Git', 'level' => 'advanced', 'years' => 5, 'primary' => false],
            ],

            'Camille Moreau' => [
                ['name' => 'TypeScript', 'level' => 'expert', 'years' => 6, 'primary' => true],
                ['name' => 'Angular', 'level' => 'expert', 'years' => 5, 'primary' => false],
                ['name' => 'HTML', 'level' => 'advanced', 'years' => 7, 'primary' => false],
                ['name' => 'CSS', 'level' => 'advanced', 'years' => 7, 'primary' => false],
            ],

            'Nicolas Simon' => [
                ['name' => 'C#', 'level' => 'expert', 'years' => 8, 'primary' => true],
                ['name' => '.NET', 'level' => 'expert', 'years' => 6, 'primary' => false],
                ['name' => 'SQL', 'level' => 'advanced', 'years' => 5, 'primary' => false],
                ['name' => 'Docker', 'level' => 'intermediate', 'years' => 2, 'primary' => false],
            ],

            'Olga Petrova' => [
                ['name' => 'Python', 'level' => 'expert', 'years' => 7, 'primary' => true],
                ['name' => 'Pandas', 'level' => 'expert', 'years' => 5, 'primary' => false],
                ['name' => 'NumPy', 'level' => 'advanced', 'years' => 5, 'primary' => false],
                ['name' => 'Scikit-learn', 'level' => 'advanced', 'years' => 4, 'primary' => false],
            ],

            'Karim Benali' => [
                ['name' => 'PHP', 'level' => 'expert', 'years' => 7, 'primary' => true],
                ['name' => 'Symfony', 'level' => 'expert', 'years' => 5, 'primary' => false],
                ['name' => 'MySQL', 'level' => 'advanced', 'years' => 5, 'primary' => false],
                ['name' => 'Redis', 'level' => 'intermediate', 'years' => 2, 'primary' => false],
            ],

            'Julie Lefèvre' => [
                ['name' => 'JavaScript', 'level' => 'expert', 'years' => 6, 'primary' => true],
                ['name' => 'Vue.js', 'level' => 'expert', 'years' => 4, 'primary' => false],
                ['name' => 'Tailwind CSS', 'level' => 'advanced', 'years' => 3, 'primary' => false],
                ['name' => 'Git', 'level' => 'advanced', 'years' => 5, 'primary' => false],
            ],

            'Maxime Roux' => [
                ['name' => 'Go', 'level' => 'expert', 'years' => 6, 'primary' => true],
                ['name' => 'Docker', 'level' => 'advanced', 'years' => 4, 'primary' => false],
                ['name' => 'Kubernetes', 'level' => 'intermediate', 'years' => 2, 'primary' => false],
                ['name' => 'Linux', 'level' => 'advanced', 'years' => 5, 'primary' => false],
            ],

            'Anna Müller' => [
                ['name' => 'Kotlin', 'level' => 'expert', 'years' => 6, 'primary' => true],
                ['name' => 'Android', 'level' => 'expert', 'years' => 5, 'primary' => false],
                ['name' => 'SQL', 'level' => 'advanced', 'years' => 4, 'primary' => false],
                ['name' => 'Git', 'level' => 'advanced', 'years' => 5, 'primary' => false],
            ],

            'David Garcia' => [
                ['name' => 'JavaScript', 'level' => 'expert', 'years' => 7, 'primary' => true],
                ['name' => 'Node.js', 'level' => 'expert', 'years' => 5, 'primary' => false],
                ['name' => 'Express.js', 'level' => 'advanced', 'years' => 4, 'primary' => false],
                ['name' => 'MongoDB', 'level' => 'advanced', 'years' => 4, 'primary' => false],
            ],

            'Fatou Diop' => [
                ['name' => 'Python', 'level' => 'expert', 'years' => 6, 'primary' => true],
                ['name' => 'FastAPI', 'level' => 'advanced', 'years' => 3, 'primary' => false],
                ['name' => 'PostgreSQL', 'level' => 'advanced', 'years' => 4, 'primary' => false],
                ['name' => 'Docker', 'level' => 'intermediate', 'years' => 2, 'primary' => false],
            ],

            'Ivan Smirnov' => [
                ['name' => 'Rust', 'level' => 'expert', 'years' => 5, 'primary' => true],
                ['name' => 'C++', 'level' => 'advanced', 'years' => 4, 'primary' => false],
                ['name' => 'Linux', 'level' => 'advanced', 'years' => 5, 'primary' => false],
                ['name' => 'Git', 'level' => 'advanced', 'years' => 4, 'primary' => false],
            ],

            'Claire Fontaine' => [
                ['name' => 'HTML', 'level' => 'expert', 'years' => 7, 'primary' => true],
                ['name' => 'CSS', 'level' => 'expert', 'years' => 7, 'primary' => false],
                ['name' => 'JavaScript', 'level' => 'advanced', 'years' => 5, 'primary' => false],
                ['name' => 'Bootstrap', 'level' => 'advanced', 'years' => 3, 'primary' => false],
            ],

            'Mohamed Hassan' => [
                ['name' => 'C', 'level' => 'expert', 'years' => 8, 'primary' => true],
                ['name' => 'C++', 'level' => 'expert', 'years' => 6, 'primary' => false],
                ['name' => 'Linux', 'level' => 'advanced', 'years' => 5, 'primary' => false],
                ['name' => 'Git', 'level' => 'advanced', 'years' => 5, 'primary' => false],
            ],

            'Elena Ivanova' => [
                ['name' => 'Swift', 'level' => 'expert', 'years' => 6, 'primary' => true],
                ['name' => 'iOS', 'level' => 'expert', 'years' => 5, 'primary' => false],
                ['name' => 'Git', 'level' => 'advanced', 'years' => 4, 'primary' => false],
            ],

            'Antoine Girard' => [
                ['name' => 'TypeScript', 'level' => 'expert', 'years' => 6, 'primary' => true],
                ['name' => 'React', 'level' => 'advanced', 'years' => 4, 'primary' => false],
                ['name' => 'Node.js', 'level' => 'advanced', 'years' => 4, 'primary' => false],
                ['name' => 'PostgreSQL', 'level' => 'advanced', 'years' => 3, 'primary' => false],
            ],

            'Sarah Johnson' => [
                ['name' => 'Python', 'level' => 'expert', 'years' => 8, 'primary' => true],
                ['name' => 'TensorFlow', 'level' => 'expert', 'years' => 5, 'primary' => false],
                ['name' => 'PyTorch', 'level' => 'advanced', 'years' => 4, 'primary' => false],
                ['name' => 'Scikit-learn', 'level' => 'advanced', 'years' => 5, 'primary' => false],
            ],
        ];

        $createdOrUpdated = 0;

        foreach ($developers as $developer) {
            $assignments = $developerTechnologies[$developer->name] ?? [];

            foreach ($assignments as $assignment) {
                $technology = $technologies->get($assignment['name']);

                if (! $technology) {
                    $this->command?->warn(
                        "Technologie introuvable : {$assignment['name']}"
                    );

                    continue;
                }

                UserTechnology::updateOrCreate(
                    [
                        'user_id' => $developer->id,
                        'technology_id' => $technology->id,
                    ],
                    [
                        'skill_level' => $assignment['level'],
                        'years_experience' => $assignment['years'],
                        'is_primary' => $assignment['primary'],
                    ],
                );

                $createdOrUpdated++;
            }
        }

        $this->command?->info(
            "{$createdOrUpdated} associations de technologies traitées."
        );
    }
}
