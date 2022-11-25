<?php

namespace App\Http\Controllers\Currency;

use App\Http\Controllers\Controller;
use App\Http\Requests\Currency\Course\IndexRequest;
use App\Models\Currency;
use App\Services\Course\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class CoursesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param IndexRequest $request
     * @return JsonResponse
     */
    public function __invoke(IndexRequest $request): JsonResponse
    {
        $currencies = Currency::query()->whereIn('code', $request->input('currencies'))->get()->pluck('code');
        if (count($request->input('currencies')) !== $currencies->count()) {
            throw new RuntimeException('Currencies don\'t exist');
        }

        $courses = [];
        foreach ($currencies as $currencyFrom) {
            $coursesTo = [];
            foreach ($currencies as $currencyTo) {
                if ($currencyFrom === $currencyTo) {
                    $coursesTo[$currencyTo] = 1;
                } else {
                    $coursesTo[$currencyTo] = Course::getCourse($currencyFrom, $currencyTo);
                }
            }

            $courses[$currencyFrom] = $coursesTo;
        }

        return response()->json(['data' => $courses]);
    }
}
