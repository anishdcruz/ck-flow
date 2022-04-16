<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\RecurringExport;
use App\mail\SendExportEmails as EX;
use Mail;

class SendExportEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $export;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($export)
    {
        $this->export = $export;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::send(new EX($this->export));
    }
}
