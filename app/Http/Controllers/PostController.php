<?php

namespace App\Http\Controllers;

use App\Jobs\AddPost;
use App\Jobs\PostAddedNotify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;

class PostController extends Controller
{
    public function showAdd(Request $request) {
        $user = $request->user();
        return view('addpost', ['name' => $user->name]);
    }

    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:100',
            'text' => 'required|max:1000',
        ])->stopOnFirstFailure();

        $user = $request->user();

        if($validator->fails()) {
            return view('addpost', ['name' => $user->name])->withErrors($validator->errors(), 'kappa');
        }

        $chain_id = (string)Str::uuid();

        Bus::chain([
            new AddPost($chain_id, $user, $request->title, $request->text),
            new PostAddedNotify($chain_id),
        ])->catch(function($e) use ($chain_id) {
            Log::channel('chains')->info(sprintf("%s chain failed: %s", $chain_id, $e->getMessage()));
        })->dispatch();

        return redirect()->route('post.list');
    }

    public function showPost(Request $request, $id) {
        $post = Post::findOrFail($id);
        return view('post', ['post' => $post]);
    }

    public function showList($page = 1) {
        $posts = null;
        $posts_count = null;

        DB::transaction(function() use (&$posts, &$posts_count, $page) {
            $posts = Post::orderByDesc('created_at')->limit(15)->offset(($page-1)*15)->get();
            $posts_count = Post::orderByDesc('created_at')->count();
        }, 3);

        $paginator = new LengthAwarePaginator($posts, $posts_count, 15, $page);

        return view('posts', ['posts' => $posts, 'paginator' => $paginator]);
    }
}
