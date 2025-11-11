<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\StartController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\EntranceController;
use App\Http\Controllers\OutputController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\LocationController;

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
    return view('Login.index');
});

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth.api')->get('/Start', [StartController::class, 'index'])->name('start.index');
Route::middleware('auth.api')->get('/start/get-data', [StartController::class, 'getData'])->name('start.getData');

Route::get('/test-api', [TestController::class, 'testConnection'])->name('test.api');
Route::get('/test', [TestController::class, 'testView'])->name('test.view');

Route::middleware('auth.api')->resource('products', ProductController::class);

Route::middleware('auth.api')->controller(ProductController::class)->group(function () {
    Route::get('/products/orderbyname', 'orderby')->name('products.orderbyname');
});

Route::middleware('auth.api')->resource('categories', CategoryController::class);
Route::middleware('auth.api')->resource('suppliers', SupplierController::class);
Route::middleware('auth.api')->resource('projects', ProjectController::class);
Route::middleware('auth.api')->resource('locations', LocationController::class); // ← SOLO ESTA LÍNEA
Route::middleware('auth.api')->resource('entrances', EntranceController::class);
Route::middleware('auth.api')->resource('outputs', OutputController::class);
Route::middleware('auth.api')->resource('loans', LoanController::class);
Route::middleware('auth.api')->resource('users', UserController::class);
Route::middleware('auth.api')->get('/databases', [DatabaseController::class, 'index'])->name('databases.index');

Route::middleware('auth.api')->get('/products/generate/pdf', [ProductController::class, 'generatePDF'])->name('products.generate.pdf');
Route::middleware('auth.api')->get('/suppliers/generate/pdf', [SupplierController::class, 'generatePDF'])->name('suppliers.generate.pdf');
Route::middleware('auth.api')->get('/projects/generate/pdf', [ProjectController::class, 'generatePDF'])->name('projects.generate.pdf');
Route::middleware('auth.api')->get('/entrances/generate/pdf', [EntranceController::class, 'generatePDF'])->name('entrances.generate.pdf');
Route::middleware('auth.api')->get('/outputs/generate/pdf', [OutputController::class, 'generatePDF'])->name('outputs.generate.pdf');
Route::middleware('auth.api')->get('/loans/generate/pdf', [LoanController::class, 'generatePDF'])->name('loans.generate.pdf');

// ELIMINA ESTA LÍNEA DUPLICADA ↓
// Route::middleware('auth.api')->resource('locations', LocationController::class);
Route::middleware('auth.api')->get('/locations/generate/pdf', [LocationController::class, 'generatePDF'])->name('locations.generate.pdf');

Route::post('/entrances', [ProductController::class, 'storeEntrance'])->name('products.entrances.store');
Route::post('/products/outputs', [ProductController::class, 'storeOutPuts'])->name('products.outputs.store');
Route::post('/products/loans', [ProductController::class, 'storeLoans'])->name('products.loans.store');

Route::get('/products/{id}/loans', [ProductController::class, 'loansGet'])->name('products.loans.get');
Route::get('/products/{id}/output', [ProductController::class, 'outPutGet'])->name('products.output.get');

Route::get('/products/{id}/entrances', [ProductController::class, 'entrancesGet'])->name('products.entrances.get');
Route::post('/products/{id}/entrances', [ProductController::class, 'entrancesPost'])->name('products.entrances.post');
Route::post('/products/{id}/output', [ProductController::class, 'outPutPost'])->name('products.output.post');

Route::middleware('auth.api')->resource('outputs', OutputController::class);
Route::middleware('auth.api')->resource('loans', LoanController::class);