<?php

declare(strict_types = 1);

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

final class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e): Response
    {
        if (str_contains($request->getUri(), '/api')) {
            return $this->renderJson($e);
        }

        return parent::render($request, $e);
    }

    protected function renderJson(Throwable $e): JsonResponse
    {
        if ($e instanceof ModelNotFoundException) {
            return response()->json([
                'error' => sprintf(
                    'Models [%s] with ids %s not found.',
                    $e->getModel(),
                    implode(', ', $e->getIds()),
                ),
            ], 404);
        }

        if ($e instanceof ValidationException) {
            return response()->json([
                'error' => 'Wrong data',
                'data' => [
                    'errors' => $e->errors(),
                ],
            ], 400);
        }

        if ($e instanceof NotFoundHttpException) {
            return response()->json([
                'error' => 'Not found',
            ], 404);
        }

        if ($e instanceof AuthenticationException) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 401);
        }

        return response()->json([
            'error' => $e->getMessage(),
        ], 500);
    }
}
