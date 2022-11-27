<?php

namespace App\Services\GoalStep;

use App\Models\Currency;
use App\Models\Goal;
use App\Models\GoalStep;
use App\Services\Course\Course;
use Illuminate\Support\Collection;

class DifferencesGettingService
{
    /**
     * @param Collection<Currency> $currencies
     * @param Goal $goal
     * @return array<string, float>
     */
    public function getByCurrency(Collection $currencies, Goal $goal): array
    {
        $differences = [];
        foreach ($currencies as $currency) {
            $sum = $goal->steps
                ->filter(fn(GoalStep $goalStep) => $goalStep->amount !== null && $goalStep->currency !== null)
                ->map(function (GoalStep $goalStep) use ($currency) {
                    if ($goalStep->currency->code === $currency->code && $goalStep->estimatedCurrency->code === $currency->code) {
                        return $goalStep->estimated_amount - $goalStep->amount;
                    } elseif ($goalStep->currency->code === $currency->code) {
                        return -$goalStep->amount;
                    } elseif ($goalStep->estimatedCurrency->code === $currency->code) {
                        return $goalStep->estimated_amount;
                    }

                    return 0;
                })
                ->sum();

            $differences[$currency->code] = round($sum, 2);
        }

        return $differences;
    }

    /**
     * @param Collection<Currency> $currencies
     * @param array<string, array<string, float>> $courses
     * @param Goal $goal
     * @return array<string, float>
     */
    public function getAll(Collection $currencies, array $courses, Goal $goal): array
    {
        $differences = [];
        foreach ($currencies as $currency) {
            $sum = $goal->steps
                ->filter(fn(GoalStep $goalStep) => $goalStep->amount !== null && $goalStep->currency !== null)
                ->map(fn(GoalStep $goalStep) => $this->getCourse($goalStep, $currency, $courses))
                ->sum();

            $differences[$currency->code] = round($sum, 2);
        }

        return $differences;
    }

    /**
     * @param GoalStep $goalStep
     * @param Currency $currency
     * @param array<string, array<string, float>> $courses
     * @return float
     */
    protected function getCourse(GoalStep $goalStep, Currency $currency, array $courses): float
    {
        $estimatedCourse = $courses[$goalStep->estimatedCurrency->code][$currency->code] ?? null;
        if ($estimatedCourse !== null) {
            $estimatedAmount = $estimatedCourse * $goalStep->estimated_amount;
        } else {
            $estimatedAmount = Course::getCourse($goalStep->estimatedCurrency->code, $currency->code, $goalStep->estimated_amount);
        }

        $course = $courses[$goalStep->currency->code][$currency->code] ?? null;
        if ($course !== null) {
            $amount = $course * $goalStep->estimated_amount;
        } else {
            $amount = Course::getCourse($goalStep->currency->code, $currency->code, $goalStep->amount);
        }

        return $estimatedAmount - $amount;
    }
}
