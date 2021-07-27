<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Support\Facades\Cache;
use App\Events\ChainJobProcessing;
use App\Events\ChainJobProcessed;
use App\Events\ChainJobFailed;

class AddComment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $chain_id;
    public $post;
    public $author;
    public $text;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($chain_id, Post $post, User $author, $text)
    {
        $this->chain_id = $chain_id;
        $this->post = $post;
        $this->author = $author;
        $this->text = $text;

        $this->onConnection('database')->onQueue('default');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->job->chain_id = $this->chain_id;

        $comment = new Comment;
        $comment->fill([
            'text' => $this->text
        ]);
        $comment->post()->associate($this->post);
        $comment->user()->associate($this->author);
        $comment->save();

        Cache::store('file')->put('post_' . $this->chain_id, serialize($this->post));
        Cache::store('file')->put('author_' . $this->chain_id, serialize($this->author));
        Cache::store('file')->put('comment_' . $this->chain_id, serialize($comment));
    }
}

