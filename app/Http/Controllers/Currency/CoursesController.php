<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Currency;

use App\Http\Controllers\Controller;
use App\Http\Requests\Currency\Course\IndexData;
use App\Models\Currency;
use App\Services\Currency\GettingCourseService;
use Illuminate\Http\JsonResponse;
use RuntimeException;

final class CoursesController extends Controller
{
    public function __construct(
        protected GettingCourseService $gettingCourseService,
    ) {
    }

    public function __invoke(IndexData $data): JsonResponse
    {
        $currencies = Currency::query()->whereIn(column: 'code', values: $data->currencies)->get()->pluck('code');
        if (count($data->currencies) !== $currencies->count()) {
            throw new RuntimeException(message: 'Currencies don\'t exist');
        }

        $courses = [];
        foreach ($currencies as $currencyFrom) {
            $coursesTo = [];
            foreach ($currencies as $currencyTo) {
                if ($currencyFrom === $currencyTo) {
                    $coursesTo[$currencyTo] = 1;
                } else {
                    $coursesTo[$currencyTo] = $this->gettingCourseService->calcAmount($currencyFrom, $currencyTo);
                }
            }

            $courses[$currencyFrom] = $coursesTo;
        }

        return response()->json(['data' => $courses]);
    }
}
