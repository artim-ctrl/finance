<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Calendar\Month;

use App\Http\Controllers\Controller;
use App\Http\Requests\Calendar\Month\StoreData;
use App\Http\Resources\Calendar\Month\CalendarMonthResource;
use App\Models\Calendar;
use App\Models\CalendarMonth;
use Exception;
use Illuminate\Http\JsonResponse;

final class MonthController extends Controller
{
    /**
     * @throws Exception
     */
    public function store(StoreData $data): CalendarMonthResource
    {
        $calendar = Calendar::whereUserId(auth()->id())->firstOrFail();

        // TODO: change to enum ?
        if ('left' === $data->to) {
            $firstMonth = CalendarMonth::whereCalendarId($calendar->id)
                ->orderBy('year')
                ->orderBy('month')
                ->firstOrFail();

            if (1 === $firstMonth->month) {
                $year = $firstMonth->year - 1;
                $month = 12;
            } else {
                $year = $firstMonth->year;
                $month = $firstMonth->month - 1;
            }
        } elseif ('right' === $data->to) {
            /** @var CalendarMonth $lastMonth */
            $lastMonth = CalendarMonth::whereCalendarId($calendar->id)
                ->orderByDesc('year')
                ->orderByDesc('month')
                ->firstOrFail();

            if (12 === $lastMonth->month) {
                $year = $lastMonth->year + 1;
                $month = 1;
            } else {
                $year = $lastMonth->year;
                $month = $lastMonth->month + 1;
            }
        } else {
            if (CalendarMonth::whereCalendarId($calendar->id)->exists()) {
                // TODO: change this to validation error or replace to validation rule
                throw new Exception('Months already exist');
            }

            $now = now();

            $year = $now->year;
            $month = $now->month;
        }

        $month = CalendarMonth::create([
            'calendar_id' => $calendar->id,
            'year' => $year,
            'month' => $month,
        ]);

        $month->load(relations: 'rows');

        return CalendarMonthResource::make($month);
    }

    public function destroy(string $to): JsonResponse
    {
        $calendar = Calendar::whereUserId(auth()->id())->firstOrFail();

        if ('left' === $to) {
            $month = CalendarMonth::whereCalendarId($calendar->id)
                ->orderBy('year')
                ->orderBy('month')
                ->firstOrFail();
        } else {
            $month = CalendarMonth::whereCalendarId($calendar->id)
                ->orderByDesc('year')
                ->orderByDesc('month')
                ->firstOrFail();
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
