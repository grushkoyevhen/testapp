<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PostCommentAdded;
use App\Models\User;

class NotifyPostCommentators implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $chain_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($chain_id)
    {
        $this->chain_id = $chain_id;

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

        $post = unserialize(Cache::store('file')->get('post_' . $this->chain_id));
        $author = unserialize(Cache::store('file')->get('author_' . $this->chain_id));
        $comment = unserialize(Cache::store('file')->get('comment_' . $this->chain_id));

        $users = User::whereHas('comments', function($query) use ($post, $author) {
            $query
                ->where($post->comments()->getForeignKeyName(), $post->getKey())
                ->where($author->comments()->getForeignKeyName(), '!=', $author->getKey());
        })->get();

        Notification::send($users, new PostCommentAdded($comment, $author, $post));
    }
}
