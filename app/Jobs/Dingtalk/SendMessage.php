<?php

namespace App\Jobs\Dingtalk;

use App\Services\Dingtalk\Notification\JobNotificationMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Artisan;

class SendMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $notificationMessage;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data,$notificationMessage)
    {
        $this->data = $data;
        $this->notificationMessage = $notificationMessage;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $result = $this->notificationMessage->sendMessage($this->data);
//        dump($result);
    }
}
