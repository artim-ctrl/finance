<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Currency\CurrencyController;
use App\Http\Controllers\Goal\GoalController;
use App\Http\Controllers\GoalStep\GoalStepController;
use App\Http\Controllers\Income\IncomeController;
use App\Http\Controllers\Loan\LoanController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\TokenController;
use App\Http\Controllers\UserBalance\UserBalanceController;
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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('profile', [ProfileController::class, 'show']);
    Route::post('profile', [ProfileController::class, 'update']);

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
    Route::post('goals', [GoalController::class, 'store']);
    Route::delete('goals/{id}', [GoalController::class, 'destroy'])->whereNumber('id');

    Route::get('goals/{goalId}/steps', [GoalStepController::class, 'index'])->whereNumber('goalId');
    Route::post('goals/{goalId}/steps', [GoalStepController::class, 'store'])->whereNumber('goalId');
    Route::delete('goals/{goalId}/steps/{id}', [GoalStepController::class, 'destroy'])->whereNumber(['goalId', 'id']);

    Route::get('user-balances', [UserBalanceController::class, 'index']);
    Route::post('user-balances', [UserBalanceController::class, 'store']);
    Route::put('user-balances/{id}', [UserBalanceController::class, 'update'])->whereNumber('id');
    Route::delete('user-balances/{id}', [UserBalanceController::class, 'destroy'])->whereNumber('id');
});

// Example for auth
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
