<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Adminfront extends Controller
{
    public function adminindex() {
        return 'index page';
    }

    public function search($query) {
        return "$query search page";
    }
}
