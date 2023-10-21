<?php

namespace App\Notifications;

use App\Models\Question;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class QuestionAnswerPublished extends Notification
{
    use Queueable;

    public function __construct(protected Question $question)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The answer to your question has been published!')
            ->line('Question: '.$this->question->content)
            ->line('Answer: '.$this->question->answers->last()->content);
    }
}
