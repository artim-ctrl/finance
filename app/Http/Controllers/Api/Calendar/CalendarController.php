<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Calendar;

use App\Http\Resources\Calendar\CalendarResource;
use App\Models\Calendar;

final readonly class CalendarController
{
    public function show(): CalendarResource
    {
        $userId = auth()->id();

        /** @var Calendar|null $calendar */
        $calendar = Calendar::whereUserId($userId)
            ->with(['months', 'months.rows'])
            ->first();
        if (null === $calendar) {
            /** @var Calendar $calendar */
            $calendar = Calendar::create([
                'name' => 'Calendar',
                'user_id' => $userId,
            ]);

            $calendar->load(['months', 'months.rows']);
        }

        return CalendarResource::make($calendar);
    }
}
