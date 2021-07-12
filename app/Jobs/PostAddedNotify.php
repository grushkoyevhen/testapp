<?php

namespace App\Jobs;

use App\Notifications\PostAdded;
use App\Models\User;
use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PostAddedNotify implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $uuid;

    public $tries = 5;
    public $backoff = 5; // если Job выбрасывает исключение, то оно ловится и выполняется $this->release($backoff);
    public $timeout = 5;

    //public $uniqueFor = 3600;
    //public $failOnTimeout = true;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($uuid)
    {
        $this->uuid = $uuid;

        $this->onConnection('database')->onQueue('default');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $post = unserialize(Cache::store('file')->get('addpost_post_' . $this->uuid));
        $user = unserialize(Cache::store('file')->get('addpost_user_' . $this->uuid));

        $user->notify(new PostAdded($post));

        Log::channel('chains')->info(sprintf("%s %s sucess", $this->uuid, get_class($this)));
    }

    public function failed($exception)
    {
        Log::channel('chains')->info(sprintf("%s %s failed: %s", $this->uuid, get_class($this), $exception->getMessage()));
    }

    public function middleware() {
        return [];
    }

/*    public function uniqueId() {
        return $this->post->id . $this->user->id;
    }*/

/*    public function uniqueVia() {
        return Container::getInstance()->make(Cache::class);;
    }*/
}
