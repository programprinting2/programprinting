<?php

namespace App\Http\Controllers;

use App\Services\TagService;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function search(Request $request)
    {
        $query = trim($request->term ?? $request->q ?? '');
        $tags = TagService::searchTags($query);
        return response()->json($tags);
    }
    
    public function index()
    {
        $tags = TagService::getAllTags();
        return response()->json(['tags' => $tags]);
    }
}