<?php

namespace App\Mail;

//use App\Models\Post;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewPost extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The order instance.
     *
     * @var Array
     */
    public $post;

    /**
     * Create a new message instance.
     *
     * @param  Array  $post
     * @return void
     */
    public function __construct(Array $post)
    {
        $this->post = $post;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.posts.newpost');
    }
}
