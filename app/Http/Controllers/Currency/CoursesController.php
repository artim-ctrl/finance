<?php

namespace App\Http\Controllers\Currency;

use App\Http\Controllers\Controller;
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
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'currencies' => 'required|array',
        ]);

        $currencies = Currency::query()->whereIn('code', $validated['currencies'])->get()->pluck('code');
        if (count($validated['currencies']) !== $currencies->count()) {
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
