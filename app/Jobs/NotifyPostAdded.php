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

class NotifyPostAdded implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $chain_id;

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
        $user = unserialize(Cache::store('file')->get('user_' . $this->chain_id));

        $user->notify(new PostAdded($post));
    }

    public function failed($exception)
    {
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
