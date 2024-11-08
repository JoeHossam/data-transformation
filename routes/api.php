<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataTransformationController;

Route::post('/general/external-integration', [DataTransformationController::class, 'transform']);
