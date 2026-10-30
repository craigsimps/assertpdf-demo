<?php

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;

Route::get('/invoices/1001/pdf', function () {
    return Pdf::loadView('invoice')
        ->setPaper('a4')
        ->stream('invoice-1001.pdf');
});
