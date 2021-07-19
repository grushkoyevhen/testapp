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

    public $uuid;
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
    public function __construct($uuid, User $user, $title, $text)
    {
        $this->uuid = $uuid;
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
        $post = new Post;
        $post->fill([
            'title' => $this->title,
            'body' => $this->text
        ]);
        $post->user()->associate($this->user);
        $post->save();

        Cache::store('file')->put('addpost_post_' . $this->uuid, serialize($post));
        Cache::store('file')->put('addpost_user_' . $this->uuid, serialize($this->user));

        Log::channel('chains')->info(sprintf("%s %s sucess", $this->uuid, get_class($this)));
    }

    public function failed($exception)
    {
        Log::channel('chains')->info(sprintf("%s %s failed: %s", $this->uuid, get_class($this), $exception->getMessage()));
    }
}
