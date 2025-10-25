<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController; // ✅ PENTING!

// Ganti 'abstract class Controller' menjadi 'class Controller extends BaseController'
class Controller extends BaseController 
{
    // Gunakan trait agar method 'authorize' dan 'middleware' berfungsi
    use AuthorizesRequests, ValidatesRequests; 
}