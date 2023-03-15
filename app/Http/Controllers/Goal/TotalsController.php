<?php

namespace App\Http\Controllers\Goal;

use App\Exceptions\Goal\GoalNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Goal\Totals\IndexData;
use App\Models\Currency;
use App\Models\Goal;
use App\Services\GoalStep\DifferencesGettingService;
use App\Services\GoalStep\TotalsGettingService;
use Illuminate\Http\JsonResponse;

class TotalsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param IndexData $data
     * @param int $goalId
     * @param TotalsGettingService $totalsGettingService
     * @param DifferencesGettingService $differencesGettingService
     * @return JsonResponse
     */
    public function __invoke(
        IndexData                 $data,
        int                       $goalId,
        TotalsGettingService      $totalsGettingService,
        DifferencesGettingService $differencesGettingService,
    ): JsonResponse {
        /** @var Goal|null $goal */
        $goal = Goal::query()
            ->where('id', $goalId)
            ->where('user_id', auth()->id())
            ->with(['steps', 'steps.estimatedCurrency', 'steps.currency'])
            ->first();
        if (null === $goal) {
            throw new GoalNotFoundException('Goal not found.');
        }

        $currencies = Currency::all();

        $totalsByCurrency = $totalsGettingService->getByCurrency($currencies, $goal);
        $totalsAll = $totalsGettingService->getAll($currencies, $data->courses, $goal);

        $differencesByCurrency = $differencesGettingService->getByCurrency($currencies, $goal);
        $differencesAll = $differencesGettingService->getAll($currencies, $data->courses, $goal);

        $leftByCurrency = $totalsGettingService->getByCurrency($currencies, $goal, left: true);
        $leftAll = $totalsGettingService->getAll($currencies, $data->courses, $goal, left: true);

        return response()->json([
            'data' => [
                'totals' => [
                    'byCurrency' => $totalsByCurrency,
                    'all' => $totalsAll,
                ],
                'differences' => [
                    'byCurrency' => $differencesByCurrency,
                    'all' => $differencesAll,
                ],
                'left' => [
                    'byCurrency' => $leftByCurrency,
                    'all' => $leftAll,
                ],
            ],
        ]);
    }
}
