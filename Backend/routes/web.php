<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

require __DIR__.'/auth.php';

// Add to routes/web.php for testing
Route::get('/test-mail', function () {
    try {
        \Illuminate\Support\Facades\Mail::raw('Test email from CandyCove', function ($message) {
            $message->to('jana.ayoub.004@gmail.com')
                    ->subject('Test Email');
        });
        return 'Mail sent successfully!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});