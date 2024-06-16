<?php

namespace App\Http\Controllers\Animal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DogController extends Controller
{
    public function index(){
        return view('dog.index');
    }
}
