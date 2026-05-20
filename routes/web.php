<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\ReportController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::post('/analyze', [AnalysisController::class, 'analyze'])->name('analyze');

Route::get('/report/{report}', [ReportController::class, 'show'])->name('report.show');