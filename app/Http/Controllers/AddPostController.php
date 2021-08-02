<?php

namespace App\Http\Controllers;

use App\Jobs\AddPost;
use App\Jobs\NotifyPostAdded;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AddPostController extends Controller
{
    public function showAdd() {
        return view('addpost');
    }

    public function doAdd(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:100',
            'text' => 'required|max:1000',
        ])->stopOnFirstFailure();

        $user = $request->user();

        if($validator->fails()) {
            return redirect()
                ->route('post.add')
                ->withErrors($validator->errors(), 'kappa');
        }

        $chain_id = (string)Str::uuid();

        Bus::chain([
            new AddPost($chain_id, $user, $request->title, $request->text),
            new NotifyPostAdded($chain_id),
        ])->dispatch();

        return redirect()->route('post.index');
    }
}
