<?php

<<<<<<< HEAD
=======
use App\Http\Middleware\EnsureAdminRole;
use App\Http\Middleware\EnsureSessionAuthenticated;
>>>>>>> 0026227 (Baru)
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

require_once __DIR__.'/../app/Helpers/helpers.php';

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
<<<<<<< HEAD
        //
=======
        $middleware->alias([
            'session.auth' => EnsureSessionAuthenticated::class,
            'role.admin' => EnsureAdminRole::class,
        ]);
>>>>>>> 0026227 (Baru)
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
<<<<<<< HEAD
    })->create();
=======
    })->create();
>>>>>>> 0026227 (Baru)
