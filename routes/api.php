<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Balance\BalanceController;
use App\Http\Controllers\Calendar\CalendarController;
use App\Http\Controllers\Calendar\Month\MonthController;
use App\Http\Controllers\Calendar\Month\Row\RowController;
use App\Http\Controllers\Currency\CoursesController;
use App\Http\Controllers\Currency\CurrencyController;
use App\Http\Controllers\Exchange\ExchangeController;
use App\Http\Controllers\Expense\ExpenseController;
use App\Http\Controllers\ExpenseType\ExpenseTypeController;
use App\Http\Controllers\Goal\GoalController;
use App\Http\Controllers\Goal\TotalsController;
use App\Http\Controllers\GoalStep\GoalStepController;
use App\Http\Controllers\Income\IncomeController;
use App\Http\Controllers\Loan\LoanController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\TokenController;
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

    Route::get('balances', [BalanceController::class, 'index']);
    Route::post('balances', [BalanceController::class, 'store']);
    Route::put('balances/{id}', [BalanceController::class, 'update'])->whereNumber('id');
    Route::delete('balances/{id}', [BalanceController::class, 'destroy'])->whereNumber('id');

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
