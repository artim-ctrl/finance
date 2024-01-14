<?php

declare(strict_types = 1);

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Balance\BalanceController;
use App\Http\Controllers\Api\Calendar\CalendarController;
use App\Http\Controllers\Api\Calendar\Month\MonthController;
use App\Http\Controllers\Api\Calendar\Month\Row\RowController;
use App\Http\Controllers\Api\Currency\CoursesController;
use App\Http\Controllers\Api\Currency\CurrencyController;
use App\Http\Controllers\Api\Exchange\ExchangeController;
use App\Http\Controllers\Api\Expense\ExpenseController;
use App\Http\Controllers\Api\ExpenseType\ExpenseTypeController;
use App\Http\Controllers\Api\Goal\GoalController;
use App\Http\Controllers\Api\Goal\TotalsController;
use App\Http\Controllers\Api\GoalStep\GoalStepController;
use App\Http\Controllers\Api\Income\IncomeController;
use App\Http\Controllers\Api\Loan\LoanController;
use App\Http\Controllers\Api\User\ProfileController;
use App\Http\Controllers\Api\User\TokenController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', LoginController::class);
Route::post('register', RegisterController::class);

Route::middleware('auth:sanctum')->group(function () { // TODO: split into files
    Route::get('profile', [ProfileController::class, 'show']);
    Route::put('profile', [ProfileController::class, 'update']);

    Route::get('tokens', [TokenController::class, 'index']);

    Route::post('logout', LogoutController::class);

    Route::delete('tokens/{id}', [TokenController::class, 'delete'])->whereNumber('id');

    Route::get('loans', [LoanController::class, 'index']);
    Route::post('loans', [LoanController::class, 'store']);
    Route::delete('loans/{id}', [LoanController::class, 'destroy'])->whereNumber('id');

    Route::get('currencies', [CurrencyController::class, 'index']);

    Route::get('incomes', [IncomeController::class, 'index']);
    Route::post('incomes', [IncomeController::class, 'store']);
    Route::delete('incomes/{id}', [IncomeController::class, 'destroy'])->whereNumber('id');

    Route::get('goals', [GoalController::class, 'index']);
    Route::get('goals/{id}', [GoalController::class, 'show'])->whereNumber('id');
    Route::post('goals', [GoalController::class, 'store']);
    Route::delete('goals/{id}', [GoalController::class, 'destroy'])->whereNumber('id');

    Route::get('goals/{goalId}/steps', [GoalStepController::class, 'index'])->whereNumber('goalId');
    Route::post('goals/{goalId}/steps', [GoalStepController::class, 'store'])->whereNumber('goalId');
    Route::put('goals/{goalId}/steps/{id}', [GoalStepController::class, 'update'])->whereNumber(['goalId', 'id']);
    Route::delete('goals/{goalId}/steps/{id}', [GoalStepController::class, 'destroy'])->whereNumber(['goalId', 'id']);

    Route::post('goals/{goalId}/totals', TotalsController::class)->whereNumber('goalId');

    Route::prefix('balances')->as('balances.')->group(static function () {
        Route::get('/', [BalanceController::class, 'index'])->name('index');
        Route::post('/', [BalanceController::class, 'store'])->name('store');
        Route::get('/{balance}', [BalanceController::class, 'show'])
            ->can('own-balance', 'balance')
            ->name('show');
        Route::put('/{balance}', [BalanceController::class, 'update'])
            ->can('own-balance', 'balance')
            ->name('update');
        Route::delete('/{balance}', [BalanceController::class, 'destroy'])
            ->can('own-balance', 'balance')
            ->name('destroy');
    });

    Route::get('exchanges', [ExchangeController::class, 'index']);
    Route::post('exchanges', [ExchangeController::class, 'store']);

    Route::get('expense-types', [ExpenseTypeController::class, 'index']);
    Route::post('expense-types', [ExpenseTypeController::class, 'store']);

    Route::get('expenses', [ExpenseController::class, 'index']);
    Route::post('expenses', [ExpenseController::class, 'store']);

    Route::get('courses', CoursesController::class);

    Route::prefix('calendars')->group(function () {
        Route::get('/', [CalendarController::class, 'show']);

        Route::prefix('/months')->group(function () {
            Route::post('/', [MonthController::class, 'store']);
            Route::delete('/{to}', [MonthController::class, 'destroy']);

            Route::prefix('{monthId}/rows')->group(function () {
                Route::post('/', [RowController::class, 'store']);
                Route::delete('/{id}', [RowController::class, 'destroy']);
            });
        });
    });
});
