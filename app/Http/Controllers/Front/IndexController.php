<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    /**
     * Show default landing page
     *
     */
    public function index()
    {
        return \File::get(public_path() . '/app/index.html');
    }
}
