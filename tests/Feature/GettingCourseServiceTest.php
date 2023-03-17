<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Services\Currency\GettingCourseService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GettingCourseServiceTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;
    use WithFaker;

    /**
     * A basic feature test example.
     */
    public function test_loading_all_courses(): void
    {
        $this->seed();

        $currencies = Currency::query()->get('code')->pluck('code')->all();

        $courses = $this->add_fake_courses($currencies);

        $gettingCourseService = (new GettingCourseService());
        $gettingCourseService->loadCoursesToCache();

        $equals = true;
        foreach ($currencies as $currency) {
            foreach ($currencies as $currencyTo) {
                $course1 = $gettingCourseService->calcAmount($currency, $currencyTo);
                $course2 = round($courses[$currency.$currencyTo], 5);
                if ($course1 !== $course2) {
                    $equals = false;

                    break 2;
                }
            }
        }

        $this->assertTrue($equals);
    }

    public function test_loading_courses(): void
    {
        $this->seed();

        $currencies = Currency::query()->get('code')->pluck('code')->all();

        $gettingCourseService = (new GettingCourseService());

        $gettingCourseService->flush();

        $courses = $this->add_fake_courses($currencies);

        $equals = true;
        foreach ($currencies as $currency) {
            foreach ($currencies as $currencyTo) {
                $course1 = $gettingCourseService->calcAmount($currency, $currencyTo);
                $course2 = round($courses[$currency.$currencyTo], 5);
                if ($course1 !== $course2) {
                    $equals = false;

                    break 2;
                }
            }
        }

        $this->assertTrue($equals);
    }

    /**
     * @param array<int, string> $currencies
     *
     * @return array<string, float>
     */
    public function add_fake_courses(array $currencies): array
    {
        $requestsData = [];
        $courses = [];

        foreach ($currencies as $currency) {
            foreach ($currencies as $currencyTo) {
                if ($currency === $currencyTo) {
                    $course = 1;
                } elseif (! array_key_exists($currencyTo.$currency, $courses)) {
                    $course = $this->faker->numberBetween(3, 80);
                } else {
                    $course = 1 / $courses[$currencyTo.$currency];
                }

                $courses[$currency.$currencyTo] = $course;
            }
        }

        foreach ($currencies as $currency) {
            $quotes = [];
            foreach ($currencies as $currencyTo) {
                $quotes[$currency.$currencyTo] = $courses[$currency.$currencyTo];
            }

            $requestsData[] = [
                'request' => [
                    'source' => $currency,
                    'currencies' => implode(',', $currencies),
                ],
                'response' => [
                    'quotes' => $quotes,
                ],
            ];

            foreach ($currencies as $currencyTo) {
                $requestsData[] = [
                    'request' => [
                        'source' => $currency,
                        'currencies' => $currencyTo,
                    ],
                    'response' => [
                        'quotes' => [
                            $currency.$currencyTo => $courses[$currency.$currencyTo],
                        ],
                    ],
                ];
            }
        }

        $requests = [];
        foreach ($requestsData as $requestData) {
            $requests['https://api.apilayer.com/currency_data/live?'.http_build_query($requestData['request'])] = Http::response($requestData['response']);
        }

        Http::fake($requests);

        return $courses;
    }
}
