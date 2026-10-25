<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();

            $table->string('name', 80)->unique();
            $table->string('slug', 100)->unique();

            $table->timestamps();
        });

        DB::table('tags')->insert([
            // Langages de programmation
            ['name' => 'PHP', 'slug' => 'php', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'JavaScript', 'slug' => 'javascript', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'TypeScript', 'slug' => 'typescript', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Python', 'slug' => 'python', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Java', 'slug' => 'java', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'C', 'slug' => 'c', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'C++', 'slug' => 'cpp', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'C#', 'slug' => 'csharp', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Go', 'slug' => 'go', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rust', 'slug' => 'rust', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kotlin', 'slug' => 'kotlin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Swift', 'slug' => 'swift', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dart', 'slug' => 'dart', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ruby', 'slug' => 'ruby', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'SQL', 'slug' => 'sql', 'created_at' => now(), 'updated_at' => now()],

            // Frameworks backend
            ['name' => 'Laravel', 'slug' => 'laravel', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Symfony', 'slug' => 'symfony', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Node.js', 'slug' => 'nodejs', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Express.js', 'slug' => 'expressjs', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'NestJS', 'slug' => 'nestjs', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Django', 'slug' => 'django', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Flask', 'slug' => 'flask', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'FastAPI', 'slug' => 'fastapi', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Spring Boot', 'slug' => 'spring-boot', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '.NET', 'slug' => 'dotnet', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ruby on Rails', 'slug' => 'ruby-on-rails', 'created_at' => now(), 'updated_at' => now()],

            // Frontend
            ['name' => 'HTML', 'slug' => 'html', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'CSS', 'slug' => 'css', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Vue.js', 'slug' => 'vuejs', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'React', 'slug' => 'react', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Angular', 'slug' => 'angular', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Svelte', 'slug' => 'svelte', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Inertia.js', 'slug' => 'inertiajs', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tailwind CSS', 'slug' => 'tailwind-css', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bootstrap', 'slug' => 'bootstrap', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Vite', 'slug' => 'vite', 'created_at' => now(), 'updated_at' => now()],

            // Bases de données
            ['name' => 'PostgreSQL', 'slug' => 'postgresql', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'MySQL', 'slug' => 'mysql', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'MariaDB', 'slug' => 'mariadb', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'SQLite', 'slug' => 'sqlite', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'MongoDB', 'slug' => 'mongodb', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Redis', 'slug' => 'redis', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Elasticsearch', 'slug' => 'elasticsearch', 'created_at' => now(), 'updated_at' => now()],

            // Développement mobile
            ['name' => 'Flutter', 'slug' => 'flutter', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'React Native', 'slug' => 'react-native', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Android', 'slug' => 'android', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'iOS', 'slug' => 'ios', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jetpack Compose', 'slug' => 'jetpack-compose', 'created_at' => now(), 'updated_at' => now()],

            // DevOps et infrastructure
            ['name' => 'Docker', 'slug' => 'docker', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kubernetes', 'slug' => 'kubernetes', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Linux', 'slug' => 'linux', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Nginx', 'slug' => 'nginx', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Apache', 'slug' => 'apache', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Git', 'slug' => 'git', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'GitHub Actions', 'slug' => 'github-actions', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'CI/CD', 'slug' => 'ci-cd', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'AWS', 'slug' => 'aws', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Google Cloud', 'slug' => 'google-cloud', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Azure', 'slug' => 'azure', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Terraform', 'slug' => 'terraform', 'created_at' => now(), 'updated_at' => now()],

            // API et architecture
            ['name' => 'REST API', 'slug' => 'rest-api', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'GraphQL', 'slug' => 'graphql', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'WebSocket', 'slug' => 'websocket', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Laravel Reverb', 'slug' => 'laravel-reverb', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Microservices', 'slug' => 'microservices', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Webhooks', 'slug' => 'webhooks', 'created_at' => now(), 'updated_at' => now()],

            // Intelligence artificielle et données
            ['name' => 'Machine Learning', 'slug' => 'machine-learning', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Deep Learning', 'slug' => 'deep-learning', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Artificial Intelligence', 'slug' => 'artificial-intelligence', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Data Science', 'slug' => 'data-science', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pandas', 'slug' => 'pandas', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'NumPy', 'slug' => 'numpy', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'scikit-learn', 'slug' => 'scikit-learn', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PyTorch', 'slug' => 'pytorch', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'TensorFlow', 'slug' => 'tensorflow', 'created_at' => now(), 'updated_at' => now()],

            // Tests, sécurité et outils
            ['name' => 'PHPUnit', 'slug' => 'phpunit', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pest PHP', 'slug' => 'pest-php', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Playwright', 'slug' => 'playwright', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cypress', 'slug' => 'cypress', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cybersecurity', 'slug' => 'cybersecurity', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'OAuth 2.0', 'slug' => 'oauth-2', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'JWT', 'slug' => 'jwt', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
