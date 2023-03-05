<?php

namespace App\Http\Controllers\Calendar;

use App\Http\Controllers\Controller;
use App\Http\Resources\Calendar\CalendarResource;
use App\Models\Calendar;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function show(Request $request): CalendarResource
    {
        /** @var Calendar $calendar */
        $calendar = Calendar::query()
            ->where('user_id', $request->user()->id)
            ->with(['months', 'months.rows'])
            ->first();
        if (null === $calendar) {
            /** @var Calendar $calendar */
            $calendar = Calendar::create([
                'name' => 'Calendar',
                'user_id' => $request->user()->id,
            ]);

            $calendar->load(['months', 'months.rows']);
        }

        return CalendarResource::make($calendar);
    }
}
