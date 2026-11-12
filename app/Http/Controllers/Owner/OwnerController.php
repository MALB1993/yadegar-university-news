<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class OwnerController extends Controller
{
    public function index(){
        return view('owner.index', [
            'latestPosts' => Post::with(['user', 'category'])->latest()->take(5)->get(),
        ]);
    }
}
