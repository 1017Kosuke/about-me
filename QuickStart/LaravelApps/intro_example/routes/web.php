<?php


use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\WelcomeController;



Route::get('/', function () {
    $faker = fake();
    $chatMessages = $faker->sentences($faker->numberBetween(4, 10));
    $users = User::orderBy('created_at', 'desc')->take(10)->get();

    return view('welcome', [
        'chatMessages' => $chatMessages,
        'users' => $users,
    ]);
});


Route::get('/dashboard', function () {
    $faker = fake();
    return view('dashboard', [
        'welcomeMessages' => $faker->paragraphs(5),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/about-us', function () {
    return view('about-us');
});



require __DIR__.'/auth.php';