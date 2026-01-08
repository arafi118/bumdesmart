<?php

use App\Http\Controllers\AuthController;
use App\Livewire\Dashboard;
use App\Livewire\Member;
use App\Livewire\Pelanggan;
use App\Livewire\Role;
use App\Livewire\Satuan;
use App\Livewire\Supplier;
use App\Livewire\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [AuthController::class, 'login']);
Route::post('/auth', [AuthController::class, 'auth']);

Route::group([
    'middleware' => 'auth',
], function () {
    Route::get('/dashboard', Dashboard::class);

    Route::get('/master-data/role', Role::class);
    Route::get('/master-data/user', User::class);
    Route::get('/master-data/member', Member::class);
    Route::get('/master-data/pelanggan', Pelanggan::class);
    Route::get('/master-data/supplier', Supplier::class);

    Route::get('/master-produk/satuan', Satuan::class);
});
