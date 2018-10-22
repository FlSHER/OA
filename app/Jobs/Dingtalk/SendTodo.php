<?php

namespace App\Jobs\Dingtalk;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Curl;

class SendTodo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sendTodo;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($sendTodo)
    {
        $this->sendTodo = $sendTodo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->sendTodo;
    }

}
