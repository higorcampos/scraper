<?php
// filepath: /app/Mail/ScrapingCompleted.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ScrapingCompleted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function build()
    {
        return $this->subject('Scraping Job Completed')
            ->view('emails.scraping_completed');
    }
}