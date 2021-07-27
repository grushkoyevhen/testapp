<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\Comment;
use App\Models\User;
use App\Models\Post;
use Illuminate\Queue\SerializesModels;

class PostCommentAdded extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $comment;
    public $author;
    public $post;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Comment $comment, User $author, Post $post)
    {
        $this->comment = $comment;
        $this->author = $author;
        $this->post = $post;

        $this->onConnection('database')->onQueue('default');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'comment_text' => $this->comment->text,
            'comment_author' => $this->author->name,
            'comment_post' => $this->post->title
        ];
    }
}
