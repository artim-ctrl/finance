<?php

namespace App\Services\GoalStep;

use App\Models\Currency;
use App\Models\Goal;
use App\Models\GoalStep;
use App\Services\Course\Course;
use Illuminate\Support\Collection;

class TotalsGettingService
{
    /**
     * @param Collection<Currency> $currencies
     * @param Goal $goal
     * @return array<string, float>
     */
    public function getByCurrency(Collection $currencies, Goal $goal): array
    {
        $totals = [];
        foreach ($currencies as $currency) {
            $sum = $goal->steps
                ->filter(fn(GoalStep $step) => $step->estimatedCurrency->code === $currency->code)
                ->pluck('estimated_amount')
                ->sum();

            $totals[$currency->code] = round($sum, 2);
        }

        return $totals;
    }

    /**
     * @param Collection<Currency> $currencies
     * @param array<string, array<string, float>> $courses
     * @param Goal $goal
     * @return array<string, float>
     */
    public function getAll(Collection $currencies, array $courses, Goal $goal): array
    {
        $totals = [];
        foreach ($currencies as $currency) {
            $sum = $goal->steps->map(
                fn(GoalStep $goalStep) => $this->getCourse($goalStep, $currency, $courses)
            )->sum();

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

        return Course::getCourse($goalStep->estimatedCurrency->code, $currency->code, $goalStep->estimated_amount);
    }
}
