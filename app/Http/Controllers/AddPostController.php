<?php

namespace App\Http\Controllers;

use App\Jobs\AddPost;
use App\Jobs\PostAddedNotify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AddPostController extends Controller
{
    public function show(Request $request) {
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

        return redirect()->route('post.index');
    }
}
