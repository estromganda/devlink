<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;

use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (Throwable $exception, Request $request) {
            // Laisser Laravel gérer normalement les réponses JSON.
            if ($request->expectsJson()) {
                return null;
            }

            // En développement, conserver les détails utiles au débogage.
            if (app()->environment('local') && config('app.debug')) {
                // return null;
            }

            // Ne pas remplacer les redirections d'authentification.
            if ($exception instanceof AuthenticationException) {
                return null;
            }

            // Laisser Laravel gérer normalement les erreurs de validation.
            if ($exception instanceof ValidationException) {
                return null;
            }

            // Une session ou un jeton CSRF expiré.
            if ($exception instanceof TokenMismatchException) {
                $status = 419;
            } elseif ($exception instanceof AuthorizationException) {
                $status = 403;
            } elseif ($exception instanceof HttpExceptionInterface) {
                $status = $exception->getStatusCode();
            } else {
                $status = 500;
            }

            return Inertia::render('Errors/Http', [
                'status' => $status,
            ])->toResponse($request)
                ->setStatusCode($status);
        });
    })->create();
