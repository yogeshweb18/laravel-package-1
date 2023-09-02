<?php

use Illuminate\Support\Facades\Route;
use Laravel\Nova\Http\Requests\NovaRequest;
use Axistrustee\ComplianceOverview\Http\Controllers\ComplianceController;

/*
|--------------------------------------------------------------------------
| Tool Routes
|--------------------------------------------------------------------------
|
| Here is where you may register Inertia routes for your tool. These are
| loaded by the ServiceProvider of the tool. The routes are protected
| by your tool's "Authorize" middleware by default. Now - go build!
|
*/
Route::get('/edit/{id}', 'Axistrustee\ComplianceOverview\Http\Controllers\ComplianceController@edit');
Route::get('/add/{id}', 'Axistrustee\ComplianceOverview\Http\Controllers\ComplianceController@addCovenant');
/*Route::get('/', function (NovaRequest $request) {
    return inertia('ComplianceOverview');
});*/
Route::get('/', function (NovaRequest $request) {
    return inertia('ComplianceOverview');
});

Route::get('/create', function (NovaRequest $request) {
    return inertia('Create');
});
Route::post('/add-tracking', 'Axistrustee\ComplianceOverview\Http\Controllers\ComplianceController@addTracking');
Route::get('/attachment', 'Axistrustee\ComplianceOverview\Http\Controllers\ComplianceController@attachment');
Route::get('/activity-logs', 'Axistrustee\ComplianceOverview\Http\Controllers\ComplianceController@activityLogs');
Route::get('/bulk-import', function (NovaRequest $request) {
    return inertia('BulkImport');
});
