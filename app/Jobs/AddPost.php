<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AddPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $chain_id;
    public $user;
    public $title;
    public $text;

    public $tries = 5;
    public $backoff = 5;
    public $timeout = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($chain_id, User $user, $title, $text)
    {
        $this->chain_id = $chain_id;
        $this->user = $user;
        $this->title = $title;
        $this->text = $text;

        $this->onConnection('database')->onQueue('default')->delay(5);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->job->chain_id = $this->chain_id;

        $post = new Post;
        $post->fill([
            'title' => $this->title,
            'body' => $this->text
        ]);
        $post->user()->associate($this->user);
        $post->save();

        Cache::store('file')->put('post_' . $this->chain_id, serialize($post));
        Cache::store('file')->put('user_' . $this->chain_id, serialize($this->user));
    }
}
