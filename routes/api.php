<?php

use App\Http\Controllers\API\SingerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\SongController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\PlaylistController;
use App\Http\Controllers\API\RegisterController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);
Route::get('categories', [SongController::class, 'getCategories']);
Route::post('logout', [LoginController::class, 'logout']);
Route::group(['middleware' => ['jwt.verify']], function () {
    Route::prefix('users')->group(function () {
        Route::get('{id}', [UserController::class, 'profile']);
        Route::put('{id}/update', [UserController::class, 'update']);
        Route::post('user', [LoginController::class, 'getAuthenticatedUser']);
        Route::post('me', [LoginController::class, 'me']);
        Route::put('change-password', [UserController::class, 'changePassword']);
    });
    Route::prefix('songs')->group(function () {
        Route::get('my-songs/{id}', [SongController::class, 'getMySongs']);
        Route::post('create-song', [SongController::class, 'store']);
        Route::get('{id}/detailSong', [SongController::class, 'getSongById']);
        Route::put('{id}/update', [SongController::class, 'update']);
        Route::get('{id}/delete', [SongController::class, 'delete']);
        Route::post('add-liked', [SongController::class, 'addLiked']);
    });
    Route::prefix('playlists')->group(function () {
        Route::post('create-playlist', [PlaylistController::class, 'store']);
        Route::post('{id}/update', [PlaylistController::class, 'update']);
        Route::get('my-playlist/{id}', [PlaylistController::class, 'myPlaylist']);
        Route::get('{id}/get-playlist', [PlaylistController::class, 'getById']);
        Route::get('{id}/get-songs', [PlaylistController::class, 'getSong']);
        Route::post('add-song', [PlaylistController::class, 'addSong']);
        Route::get('{id}/delete', [PlaylistController::class, 'delete']);
        Route::get('{id}/song-id', [PlaylistController::class, 'getSongId']);
        Route::get('{id}/delete_playlist', [PlaylistController::class, 'delete_playlist']);
        Route::get('{id}/play-playlist', [PlaylistController::class, 'playPlaylist']);
    });
    Route::prefix('singers')->group(function (){
        Route::post('create',[SingerController::class,'create']);
        Route::get('list',[SingerController::class,'getAll']);
    });


});

Route::prefix('songs')->group(function () {
    Route::get('list', [SongController::class, 'getAll']);
    Route::get('{id}/play', [SongController::class, 'detailSong']);
    Route::get('search/{name}', [SongController::class, 'search']);
    Route::get('{user_id}/new-songs', [SongController::class, 'getNewSongs']);
    Route::get('many-listens', [SongController::class, 'getSongManyListens']);
    Route::get('many-liked', [SongController::class, 'getSongManyLiked']);
});

Route::prefix('playlists')->group(function () {
    Route::get('search/{name}', [PlaylistController::class, 'search']);
});



