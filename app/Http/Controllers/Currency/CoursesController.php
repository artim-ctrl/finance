<?php

namespace App\Http\Controllers\Currency;

use App\Http\Controllers\Controller;
use App\Http\Requests\Currency\Course\IndexData;
use App\Models\Currency;
use App\Services\Currency\GettingCourseService;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class CoursesController extends Controller
{
    public function __construct(
        protected GettingCourseService $gettingCourseService,
    ) {
    }

    /**
     * Handle the incoming request.
     *
     * @param IndexData $request
     * @return JsonResponse
     */
    public function __invoke(IndexData $request): JsonResponse
    {
        $currencies = Currency::query()->whereIn('code', $request->currencies)->get()->pluck('code');
        if (count($request->currencies) !== $currencies->count()) {
            throw new RuntimeException('Currencies don\'t exist');
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
