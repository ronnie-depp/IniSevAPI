<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FirstEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->afterCommit();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('valentin@sellcodes.com', 'IniSev')
            /*->to('mr.salman.ahmad@hotmail.com', 'Salman')*/
            ->subject('Valhala')
            ->markdown('emails.plaintext_firstemail')
            ->onConnection('database')
            ->onQueue('emails');
            //->send('emails.plaintext_firstemail');
            /*
            Mail::queueOn('queue-name', 'Html.view', $data, function ($message) {
                $message->from('john@johndoe.com', 'John Doe');
                $message->sender('john@johndoe.com', 'John Doe');
                $message->to('john@johndoe.com', 'John Doe');
                $message->cc('john@johndoe.com', 'John Doe');
                $message->bcc('john@johndoe.com', 'John Doe');
                $message->replyTo('john@johndoe.com', 'John Doe');
                $message->subject('Subject');
                $message->priority(3);
                $message->attach('pathToFile');
            });
            */
    }
}
