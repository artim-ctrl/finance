<?php

declare(strict_types = 1);

namespace App\Services\GoalStep;

use App\Models\Currency;
use App\Models\Goal;
use App\Models\GoalStep;
use App\Services\Currency\GettingCourseService;
use Illuminate\Support\Collection;

final readonly class DifferencesGettingService
{
    public function __construct(
        private GettingCourseService $gettingCourseService,
    ) {
    }

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
                ->filter(fn (GoalStep $goalStep) => null !== $goalStep->amount && null !== $goalStep->currency)
                ->map(function (GoalStep $goalStep) use ($currency) {
                    if ($goalStep->currency?->code === $currency->code && $goalStep->estimatedCurrency->code === $currency->code) {
                        return $goalStep->estimated_amount - $goalStep->amount;
                    } elseif ($goalStep->currency?->code === $currency->code) {
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
                ->filter(fn (GoalStep $goalStep) => null !== $goalStep->amount && null !== $goalStep->currency)
                ->map(fn (GoalStep $goalStep) => $this->getCourse($goalStep, $currency, $courses))
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
        if (null !== $estimatedCourse) {
            $estimatedAmount = $estimatedCourse * $goalStep->estimated_amount;
        } else {
            $estimatedAmount = $this->gettingCourseService->calcAmount($goalStep->estimatedCurrency->code, $currency->code, $goalStep->estimated_amount);
        }

        $course = $courses[$goalStep->currency?->code][$currency->code] ?? null;
        if (null !== $course) {
            $amount = $course * $goalStep->estimated_amount;
        } else {
            $amount = $this->gettingCourseService->calcAmount((string) $goalStep->currency?->code, $currency->code, (float) $goalStep->amount);
        }

        return $estimatedAmount - $amount;
    }
}
