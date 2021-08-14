<?php

namespace App\Http\Controllers;

use App\Exceptions\PostNotFound;
use App\Jobs\AddComment;
use App\Jobs\NotifyPostCommentators;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Post;
use App\Pagination\Paginator;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function showPost(Request $request, $id)
    {
        if(!($post = Post::find($id))) {
            throw new PostNotFound;
        }

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
        $perPage = (new Post)->getPerPage();

        $posts = Post::withCount('comments as comments_num')
            ->orderByDesc('created_at')
            ->limit($perPage)
            ->offset(($page-1)*$perPage)
            ->get();

        $posts_count = Post::count();

        $paginator = new Paginator($posts, $posts_count, $perPage, $page, [
            'urlResolver' => function($page) {
                return route('post.page', ['id' => $page], false);
            },
        ]);

        return view('posts', ['posts' => $posts, 'paginator' => $paginator]);
    }

    public function addComment(Request $request, $id)
    {
        if(!($post = Post::find($id))) {
            throw new PostNotFound;
        }

        $validator = Validator::make($request->post(), [
            'text' => 'required|min:10|max:1000',
        ]);

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
