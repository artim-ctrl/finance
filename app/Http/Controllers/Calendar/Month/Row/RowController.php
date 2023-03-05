<?php

namespace App\Http\Controllers\Calendar\Month\Row;

use App\Http\Controllers\Controller;
use App\Http\Requests\Calendar\Month\Row\StoreRequest;
use App\Http\Resources\Calendar\Month\Row\MonthRowResource;
use App\Models\Calendar;
use App\Models\CalendarMonth;
use App\Models\MonthRow;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RowController extends Controller
{
    /**
     * @throws Exception
     */
    public function store(StoreRequest $request, int $monthId): MonthRowResource
    {
        /** @var Calendar $calendar */
        $calendar = Calendar::query()
            ->where('user_id', $request->user()->id)
            ->first();

        /** @var CalendarMonth $month */
        $month = CalendarMonth::query()
            ->where('id', $monthId)
            ->where('calendar_id', $calendar->id)
            ->first();
        if (null === $month) {
            throw new Exception('Month does not exist');
        }

        $row = MonthRow::create([
            'month_id' => $month->id,
            'name' => $request->input('name'),
            'amount' => (float)$request->input('amount'),
            'currency_id' => $request->input('currency_id'),
        ]);

        return MonthRowResource::make($row);
    }

    /**
     * @throws Exception
     */
    public function destroy(Request $request, int $monthId, int $id): JsonResponse
    {
        /** @var Calendar $calendar */
        $calendar = Calendar::query()
            ->where('user_id', $request->user()->id)
            ->first();

        /** @var CalendarMonth $month */
        $month = CalendarMonth::query()
            ->where('id', $monthId)
            ->where('calendar_id', $calendar->id)
            ->first();
        if (null === $month) {
            throw new Exception('Month does not exist');
        }

        $row = MonthRow::query()
            ->where('id', $id)
            ->where('month_id', $month->id)
            ->first();
        if (null === $row) {
            throw new Exception('Row does not exist');
        }

        $row->forceDelete();

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
