<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Axistrustee\ComplianceOverview\Http\Controllers\ComplianceController;
/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

// Route::get('/', function (Request $request) {
//     //
// });
Route::post('/list', 'Axistrustee\ComplianceOverview\Http\Controllers\ComplianceController@listOverview');
Route::post('/view', 'Axistrustee\ComplianceOverview\Http\Controllers\ComplianceController@view');
Route::post('/close', 'Axistrustee\ComplianceOverview\Http\Controllers\ComplianceController@close');
Route::post('/store','Axistrustee\ComplianceOverview\Http\Controllers\ComplianceController@save');
Route::post('/getComplianceCovenant','Axistrustee\ComplianceOverview\Http\Controllers\ComplianceController@getComplianceCovenant');
Route::post('/update','Axistrustee\ComplianceOverview\Http\Controllers\ComplianceController@update');
Route::get('/fetchClients','Axistrustee\ComplianceOverview\Http\Controllers\ComplianceController@fetchClients');
Route::get('/fetchClcode','Axistrustee\ComplianceOverview\Http\Controllers\ComplianceController@fetchClcode');
Route::post('/fetchIsins','Axistrustee\ComplianceOverview\Http\Controllers\ComplianceController@fetchIsins');
Route::post('/clcode-import','Axistrustee\ComplianceOverview\Http\Controllers\ComplianceController@clcodeImport');
Route::post('/fetchCovenant','Axistrustee\ComplianceOverview\Http\Controllers\ComplianceController@fetchCovenant');
Route::post('/fetchSubtypes','Axistrustee\ComplianceOverview\Http\Controllers\ComplianceController@fetchSubtypes');
Route::post('/saveCovenantData','Axistrustee\ComplianceOverview\Http\Controllers\ComplianceController@saveCovenantData');
Route::get('/attachment', 'Axistrustee\ComplianceOverview\Http\Controllers\ComplianceController@attachment');
Route::post('/getInstances','Axistrustee\ComplianceOverview\Http\Controllers\ComplianceController@getInstances');
Route::post('/saveTimelines','Axistrustee\ComplianceOverview\Http\Controllers\ComplianceController@saveTimelines');
Route::post('/viewLog','Axistrustee\ComplianceOverview\Http\Controllers\ComplianceController@viewLog');
Route::post('/bulkUpload','Axistrustee\ComplianceOverview\Http\Controllers\ComplianceController@bulkUpload');