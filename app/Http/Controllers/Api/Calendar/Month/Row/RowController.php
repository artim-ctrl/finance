<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Calendar\Month\Row;

use App\Http\Requests\Calendar\Month\Row\StoreData;
use App\Http\Resources\Calendar\Month\Row\MonthRowResource;
use App\Models\Calendar;
use App\Models\CalendarMonth;
use App\Models\MonthRow;
use Exception;
use Illuminate\Http\JsonResponse;

final readonly class RowController
{
    /**
     * @throws Exception
     */
    public function store(StoreData $data, int $monthId): MonthRowResource
    {
        $calendar = Calendar::whereUserId(auth()->id())->firstOrFail();

        $month = CalendarMonth::whereId($monthId)
            ->whereCalendarId($calendar->id)
            ->firstOrFail();

        $row = MonthRow::create([
            'month_id' => $month->id,
            'name' => $data->name,
            'amount' => $data->amount,
            'currency_id' => $data->currencyId,
        ]);

        return MonthRowResource::make($row);
    }

    /**
     * @throws Exception
     */
    public function destroy(int $monthId, int $id): JsonResponse
    {
        $calendar = Calendar::whereUserId(auth()->id())->firstOrFail();

        $month = CalendarMonth::query()
            ->whereId($monthId)
            ->whereCalendarId($calendar->id)
            ->firstOrFail();

        $row = MonthRow::whereId($id)
            ->whereMonthId($month->id)
            ->firstOrFail();

        $row->forceDelete();

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
