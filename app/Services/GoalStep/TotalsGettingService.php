<?php

namespace App\Services\GoalStep;

use App\Models\Currency;
use App\Models\Goal;
use App\Models\GoalStep;
use App\Services\Currency\GettingCourseService;
use Illuminate\Support\Collection;

class TotalsGettingService
{
    public function __construct(protected GettingCourseService $gettingCourseService)
    {
    }

    /**
     * @param Collection<Currency> $currencies
     * @param Goal $goal
     * @param bool $left
     * @return array<string, float>
     */
    public function getByCurrency(Collection $currencies, Goal $goal, bool $left = false): array
    {
        $totals = [];
        foreach ($currencies as $currency) {
            $sum = $goal->steps
                ->filter(fn(GoalStep $step) => $step->estimatedCurrency->code === $currency->code)
                ->map(function (GoalStep $step) use ($left) {
                    if ($left && null !== $step->amount) {
                        return 0;
                    }

                    return $step->estimated_amount;
                })
                ->sum();

            $totals[$currency->code] = round($sum, 2);
        }

        return $totals;
    }

    /**
     * @param Collection<Currency> $currencies
     * @param array<string, array<string, float>> $courses
     * @param Goal $goal
     * @param bool $left
     * @return array<string, float>
     */
    public function getAll(Collection $currencies, array $courses, Goal $goal, bool $left = false): array
    {
        $totals = [];
        foreach ($currencies as $currency) {
            $sum = $goal->steps->map(function (GoalStep $goalStep) use ($currency, $courses, $left) {
                if ($left && null !== $goalStep->amount) {
                    return 0;
                }

                return $this->getCourse($goalStep, $currency, $courses);
            })->sum();

            $totals[$currency->code] = round($sum, 2);
        }

        return $totals;
    }

    protected function getCourse(GoalStep $goalStep, Currency $currency, array $courses): float
    {
        $customCourse = $courses[$goalStep->estimatedCurrency->code][$currency->code] ?? null;
        if ($customCourse !== null) {
            return $customCourse * $goalStep->estimated_amount;
        }

        return $this->gettingCourseService->calcAmount($goalStep->estimatedCurrency->code, $currency->code, $goalStep->estimated_amount);
    }
}
