<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class AppController extends Controller
{
    public function index(): \Inertia\Response
    {
        $communities = \App\Models\Community::query()
            ->where('is_active', true)
            ->where('is_private', false)
            ->inRandomOrder()
            ->limit(10)
            ->get([
                'id',
                'name',
                'slug',
                'description',
                'avatar_path',
            ]);
        return Inertia::render('app/Home', [
            'communities' => $communities,
        ]);
    }
}
