<?php

namespace App\Http\Controllers;

use App\Jobs\AddComment;
use App\Jobs\NotifyPostCommentators;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Post;
use App\Models\Comment;
use App\Pagination\Paginator;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function showPost(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $pageName = 'p';
        $perPage = $post->getPerPage();
        $page = $request->query($pageName, 1);

        $post->load([
            'comments' => function($relation) use($page, $perPage) {
                $relation->orderByDesc('created_at')->limit($perPage)->offset(($page-1)*$perPage);
              },
            'comments.user:name,id'
        ])->loadCount('comments as comments_num');

        $paginator = new Paginator($post->comments, $post->comments_num, $perPage, $page, [
            'urlResolver' => function($page) use ($id, $pageName) {
                return route('post.single', ['id' => $id, $pageName => $page], false);
            },
        ]);

        return view('post', ['post' => $post, 'paginator' => $paginator]);
    }

    public function showList($page = 1)
    {
        $posts = null;
        $posts_count = null;

        $perPage = (new Post)->getPerPage();

        DB::transaction(function() use (&$posts, &$posts_count, $page, $perPage) {
            $posts = Post::withCount('comments')
                ->orderByDesc('created_at')
                ->limit($perPage)
                ->offset(($page-1)*$perPage)
                ->get();
            $posts_count = Post::count();
        }, 3);

        $paginator = new Paginator($posts, $posts_count, $perPage, $page, [
            'urlResolver' => function($page) {
                return route('post.page', ['id' => $page], false);
            },
        ]);

        return view('posts', ['posts' => $posts, 'paginator' => $paginator]);
    }

    public function addComment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'text' => 'required|min:10|max:1000',
        ]);

        $post = Post::findOrFail($id);

        if($validator->fails())
            return redirect()
                ->route('post.single', ['id' => $id])
                ->withErrors($validator->errors(), 'addComment');


        $author = $request->user();
        $chain_id = (string)Str::uuid();

        Bus::chain([
            new AddComment($chain_id, $post, $author, $request->text),
            new NotifyPostCommentators($chain_id),
        ])->dispatch();

        return redirect()->route('post.single', ['id' => $id]);
    }
}
