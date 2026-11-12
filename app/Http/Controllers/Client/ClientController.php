<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(){
        return view('client.index', [
            'latestPosts' => Post::with(['user', 'category'])->latest()->take(5)->get(),
        ]);
    }
}
