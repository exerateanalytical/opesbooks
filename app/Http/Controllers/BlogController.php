<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::published()->latest('published_at')->paginate(9);
        return view('blog.index', compact('posts'));
    }

    public function show(BlogPost $post)
    {
        abort_unless($post->is_published, 404);
        return view('blog.show', compact('post'));
    }
}
