<?php

use App\Http\Controllers\BankController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\MonthController;
use App\Http\Controllers\PayrollRulesController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\YearController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::group(['middleware' => 'auth', 'prefix' => 'dashboard'], function () {
    Route::resource('users', UsersController::class);

    Route::resource('company', CompanyController::class);
    Route::resource('bank', BankController::class);
    Route::resource('year', YearController::class);
    Route::resource('month', MonthController::class);
    Route::resource('faq', FaqController::class);
    Route::resource('transaction', TransactionController::class);
    Route::post('userFinance',[UsersController::class,'userFinance'])->name('userFinance');
    Route::resource('payroll-rules', PayrollRulesController::class);
//dynamicTransaction.fetch
Route::get('dynamicTransaction/fetch', [TransactionController::class,'yearData'])->name('dynamicTransaction.fetch');
//updateDetailsValues
Route::get('updateDetailsValues', [TransactionController::class,'updateDetails'])->name('updateDetailsValues');
//sendNotification
Route::get('sendNotification', [TransactionController::class,'sendNotification'])->name('sendNotification');

});

require __DIR__ . '/auth.php';