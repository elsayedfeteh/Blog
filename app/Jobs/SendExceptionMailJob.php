<?php

namespace App\Jobs;

use App\Mail\ExceptionMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendExceptionMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $exception;
    public $vai;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($exception, $vai = 'email')
    {
        $this->exception = $exception;
        $this->vai = $vai;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->vai == 'slack') {
            Log::channel('slack')->debug("Exception Occoured", $this->exception);
        }
        elseif ($this->vai == 'path') {
            Log::channel('slack')->debug("Exception Occoured", $this->exception);
            Mail::to("elsayedfeteh@gmail.com")->send(new ExceptionMail($this->exception));
        }
        else {
            Mail::to("elsayedfeteh@gmail.com")->send(new ExceptionMail($this->exception));
        }
    }
}
