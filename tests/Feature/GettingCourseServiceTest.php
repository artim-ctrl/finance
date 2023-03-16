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
    public function test_loading_courses(): void
    {
        $this->seed();

        $currencies = Currency::query()->get('code')->pluck('code')->all();

        $courses = $this->add_fake_courses($currencies);

        $gettingCourseService = (new GettingCourseService());
        $gettingCourseService->loadCoursesToCache();

        $equals = false;
        foreach ($currencies as $currency) {
            foreach ($currencies as $currencyTo) {
                if ($gettingCourseService->calcAmount($currency, $currencyTo) === round($courses[$currency.$currencyTo], 2)) {
                    $equals = true;
                }
            }
        }

        $this->assertTrue($equals);

        $gettingCourseService->flush();

        $equals = false;
        foreach ($currencies as $currency) {
            foreach ($currencies as $currencyTo) {
                if ($gettingCourseService->calcAmount($currency, $currencyTo) === round($courses[$currency.$currencyTo], 2)) {
                    $equals = true;
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
