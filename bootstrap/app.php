<?php

use App\Exceptions\Cart\CartItemNotFoundException;
use App\Exceptions\Cart\CartProductLimitExceededException;
use App\Exceptions\Order\EmptyCartException;
use App\Exceptions\Order\OrderNotFoundException;
use App\Http\Middleware\IsAdminMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => IsAdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (CartProductLimitExceededException $e): JsonResponse {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        });

        $exceptions->render(function (CartItemNotFoundException $e): JsonResponse {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        });

        $exceptions->render(function (EmptyCartException $e): JsonResponse {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        });

        $exceptions->render(function (OrderNotFoundException $e): JsonResponse {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        });
    })->create();
