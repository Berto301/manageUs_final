<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('index_page');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::post('/logout','AuthController@logout')->name('logout');
    Route::match(['get', 'delete'],'/dashboard', 'PageController@dashboard')->name('dashboard');
    Route::get('/teams', 'TeamsController@teams')->name('teams');
    Route::get('/tasks', 'TaskController@tasks')->name('tasks');
    Route::patch('/group/{data}', 'GroupController@update')->name('group_update');
    Route::get('/events', 'EventController@lists')->name('events');
    Route::get('/profile', 'AuthController@profile')->name('myProfile');
    Route::post('/teams/{team}/tasks', 'TaskController@createTask')->name('task_create');
    Route::post('/teams/create', 'TeamsController@create')->name('teams_create');
    Route::patch('/teams/{task}', 'TeamsController@update')->name('teams_update');
    Route::delete('/teams/{id}', 'TeamsController@deleteTeam')->name('teams_delete');
    Route::post('/teams/createMember', 'TeamsController@createMember')->name('create_member');
    Route::post('/teams/settings', 'TeamsController@settingsTeams')->name('team_settings');
    Route::patch('/tasks/{task}', 'TaskController@patchTask')->name('task_patch');
    Route::delete('/tasks/{task}', 'TaskController@deleteTask')->name('task_delete');
    Route::post('/image/upload', 'ImageController@upload')->name('image.upload');
});
