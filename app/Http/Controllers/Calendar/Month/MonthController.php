<?php

namespace App\Http\Controllers\Calendar\Month;

use App\Http\Controllers\Controller;
use App\Http\Requests\Calendar\Month\StoreData;
use App\Http\Resources\Calendar\Month\CalendarMonthResource;
use App\Models\Calendar;
use App\Models\CalendarMonth;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MonthController extends Controller
{
    /**
     * @throws Exception
     */
    public function store(StoreData $data): CalendarMonthResource
    {
        /** @var Calendar $calendar */
        $calendar = Calendar::query()
            ->where('user_id', auth()->id())
            ->first();

        // TODO: change to enum ?
        if ('left' === $data->to) {
            /** @var CalendarMonth $firstMonth */
            $firstMonth = CalendarMonth::query()
                ->where('calendar_id', $calendar->id)
                ->orderBy('year')
                ->orderBy('month')
                ->first();

            if (1 === $firstMonth->month) {
                $year = $firstMonth->year - 1;
                $month = 12;
            } else {
                $year = $firstMonth->year;
                $month = $firstMonth->month - 1;
            }
        } elseif ('right' === $data->to) {
            /** @var CalendarMonth $lastMonth */
            $lastMonth = CalendarMonth::query()
                ->where('calendar_id', $calendar->id)
                ->orderByDesc('year')
                ->orderByDesc('month')
                ->first();

            if (12 === $lastMonth->month) {
                $year = $lastMonth->year + 1;
                $month = 1;
            } else {
                $year = $lastMonth->year;
                $month = $lastMonth->month + 1;
            }
        } else {
            if (CalendarMonth::query()->where('calendar_id', $calendar->id)->exists()) {
                throw new Exception('Months already exist');
            }

            $now = now();

            $year = $now->year;
            $month = $now->month;
        }

        /** @var CalendarMonth $month */
        $month = CalendarMonth::create([
            'calendar_id' => $calendar->id,
            'year' => $year,
            'month' => $month,
        ]);

        $month->load(['rows']);

        return CalendarMonthResource::make($month);
    }

    public function destroy(Request $request, string $to): JsonResponse
    {
        /** @var Calendar $calendar */
        $calendar = Calendar::query()
            ->where('user_id', $request->user()->id)
            ->first();

        /** @var CalendarMonth $month */
        if ('left' === $to) {
            $month = CalendarMonth::query()
                ->where('calendar_id', $calendar->id)
                ->orderBy('year')
                ->orderBy('month')
                ->first();
        } else {
            $month = CalendarMonth::query()
                ->where('calendar_id', $calendar->id)
                ->orderByDesc('year')
                ->orderByDesc('month')
                ->first();
        }

        $month->forceDelete();

        return response()->json([
            'status' => 'ok',
            'data' => [
                'id' => $month->id,
            ],
        ]);
    }
}
