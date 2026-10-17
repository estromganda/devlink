<?php

namespace Database\Seeders;

use App\Models\Technology;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TechnologySeeder extends Seeder
{
    public function run(): void
    {
        $technologies = [
            // Langages de programmation
            ['name' => 'PHP', 'category' => 'Langage'],
            ['name' => 'JavaScript', 'category' => 'Langage'],
            ['name' => 'TypeScript', 'category' => 'Langage'],
            ['name' => 'Python', 'category' => 'Langage'],
            ['name' => 'Java', 'category' => 'Langage'],
            ['name' => 'C', 'slug' => 'c-lang', 'category' => 'Langage'],
            ['name' => 'C++', 'slug' => 'cpp', 'category' => 'Langage'],
            ['name' => 'C#', 'slug' => 'csharp', 'category' => 'Langage'],
            ['name' => 'Go', 'category' => 'Langage'],
            ['name' => 'Rust', 'category' => 'Langage'],
            ['name' => 'Kotlin', 'category' => 'Langage'],
            ['name' => 'Swift', 'category' => 'Langage'],
            ['name' => 'Dart', 'category' => 'Langage'],
            ['name' => 'SQL', 'category' => 'Langage'],

            // Frontend
            ['name' => 'Vue.js', 'category' => 'Frontend'],
            ['name' => 'React', 'category' => 'Frontend'],
            ['name' => 'Angular', 'category' => 'Frontend'],
            ['name' => 'HTML', 'category' => 'Frontend'],
            ['name' => 'CSS', 'category' => 'Frontend'],
            ['name' => 'Tailwind CSS', 'category' => 'Frontend'],
            ['name' => 'Bootstrap', 'category' => 'Frontend'],

            // Backend et frameworks
            ['name' => 'Laravel', 'category' => 'Backend'],
            ['name' => 'Symfony', 'category' => 'Backend'],
            ['name' => 'Node.js', 'category' => 'Backend'],
            ['name' => 'Express.js', 'category' => 'Backend'],
            ['name' => 'Django', 'category' => 'Backend'],
            ['name' => 'FastAPI', 'category' => 'Backend'],
            ['name' => 'Spring Boot', 'category' => 'Backend'],
            ['name' => '.NET', 'category' => 'Backend'],

            // Bases de données
            ['name' => 'PostgreSQL', 'category' => 'Base de données'],
            ['name' => 'MySQL', 'category' => 'Base de données'],
            ['name' => 'MariaDB', 'category' => 'Base de données'],
            ['name' => 'SQLite', 'category' => 'Base de données'],
            ['name' => 'MongoDB', 'category' => 'Base de données'],
            ['name' => 'Redis', 'category' => 'Base de données'],

            // Mobile
            ['name' => 'Flutter', 'category' => 'Mobile'],
            ['name' => 'React Native', 'category' => 'Mobile'],
            ['name' => 'Android', 'category' => 'Mobile'],
            ['name' => 'iOS', 'category' => 'Mobile'],

            // DevOps et outils
            ['name' => 'Docker', 'category' => 'DevOps'],
            ['name' => 'Kubernetes', 'category' => 'DevOps'],
            ['name' => 'Git', 'category' => 'Outils'],
            ['name' => 'GitHub Actions', 'category' => 'DevOps'],
            ['name' => 'Linux', 'category' => 'Système'],
            ['name' => 'Nginx', 'category' => 'DevOps'],

            // Data et intelligence artificielle
            ['name' => 'Pandas', 'category' => 'Data'],
            ['name' => 'NumPy', 'category' => 'Data'],
            ['name' => 'Scikit-learn', 'category' => 'Machine Learning'],
            ['name' => 'TensorFlow', 'category' => 'Machine Learning'],
            ['name' => 'PyTorch', 'category' => 'Machine Learning'],
        ];

        foreach ($technologies as $data) {
            $slug = $data['slug'] ?? Str::slug($data['name']);
            Technology::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $data['name'],
                    'category' => $data['category'],
                ],
            );
        }

        $this->command?->info(
            count($technologies) . ' technologies traitées.'
        );
    }
}
