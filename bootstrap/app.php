<?php

use App\Exceptions\Api\ApiException;
use App\Http\Middleware\ApiMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocaleMiddleware::class,
        ]);

        $middleware->alias([
            'api.polr' => ApiMiddleware::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/v*/action/*',
            'api/v*/data/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontReport([
            ApiException::class,
            HttpException::class,
        ]);

        $exceptions->render(function (ApiException $e, Request $request) {
            $statusCode = $e->getCode() ?: 400;
            $encoded = $e->getEncodedErrorMessage();
            $contentType = ($e->response_type === 'json') ? 'application/json' : 'text/plain';

            return response($encoded, $statusCode)
                ->header('Content-Type', $contentType)
                ->header('Access-Control-Allow-Origin', '*');
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if (! config('app.debug')) {
                if (env('SETTING_REDIRECT_404') && env('SETTING_INDEX_REDIRECT')) {
                    return redirect()->to(env('SETTING_INDEX_REDIRECT'));
                }

                return response()->view('errors.404', [], 404);
            }
        });

        $exceptions->render(function (HttpException $e, Request $request) {
            if (! config('app.debug')) {
                $statusCode = $e->getStatusCode();
                if ($statusCode === 419) {
                    return null;
                }
                if ($statusCode === 500) {
                    return response()->view('errors.500', [], 500);
                }

                return response()->view('errors.generic', [
                    'status_code' => $statusCode,
                    'status_message' => $e->getMessage(),
                ], $statusCode);
            }
        });
    })->create();
