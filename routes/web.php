<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;    

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/posts/create', function () {
    return view('posts.create');
})->name('posts.create');

Route::post('/posts/store', function (Request $request) {
    $request->validate(
        [
        'title' => 'required|min:5|max:50',
        'desc' => 'required|min:5|max:500',
        'author' => 'required|min:3|max:50',
        'topics' => 'required|array|min:1',
        'topics.*' => 'distinct'
        ],
        [
            'title.required' => 'Milyen címet? A gecimet',
            'required' => 'teso ezt csinald itt meg azonnal 💪',
        ]
);
})->name('posts.store');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
