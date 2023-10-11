<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;    
use App\Models\Post;


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
        'topics.*' => 'distinct',
        'attach_file' => 'nullable|mimes:txt,doc,docx,pdf,xls|max:4096',
        'attach_image' => 'nullable|mimes:jpg,png|max:4096',
        ],
        [
            'title.required' => 'Milyen címet? A gecimet',
            'required' => 'teso ezt csinald itt meg azonnal 💪',
        ]
    );
    [
        'title' => $title, 
        'desc' => $desc,
        'author' => $author,
        'topics' => $topics,
    ] = $request;
   
    $attachHashName = '';
    $attachFileName = '';
    $imageHashName = '';
    
    if($request->hasFile('attach_file')) {
        $attachFile = $request->file('attach_file');
        $attachFileName = $attachFile->getOriginalClientName();
        $attachHashName = $attachFile->hashName();
        Storage::disk('public')->put($attachHashName, $attachFile->get());
    }

    if($request->hasFile('attach_image')) {
        $imageFile = $request->file('attach_image');
        $imageHashName = $imageFile->hashName();
        Storage::disk('public')->put($attachHashName, $imageFile->get());
    }

    Post::create([
        'title' => $title,
        'desc' => $desc,
        'author' => $author,
        'topics' => json_encode($topics),
        'attachment_hash_name' => $attachHashName,
        'attachment_file_name' => $attachFileName,
        'image_hash_name' => $imageHashName,
    ]);
})->name('posts.store');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
