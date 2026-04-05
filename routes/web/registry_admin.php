<?php

use App\Http\Controllers\RegistryTenantAdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RegistryTenantAdminController::class, 'index'])->name('index');
Route::get('/create', [RegistryTenantAdminController::class, 'create'])->name('create');
Route::post('/', [RegistryTenantAdminController::class, 'store'])->name('store');
Route::get('/{registry_tenant}/edit', [RegistryTenantAdminController::class, 'edit'])->name('edit');
Route::put('/{registry_tenant}', [RegistryTenantAdminController::class, 'update'])->name('update');
Route::delete('/{registry_tenant}', [RegistryTenantAdminController::class, 'destroy'])->name('destroy');
