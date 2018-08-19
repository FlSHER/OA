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

    protected $sendAddTodo;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($sendAddTodo)
    {
        $this->sendAddTodo = $sendAddTodo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $result = $this->sendAddTodo;
        if(!empty($result->record_id)){
            $this->addTodoCallback($result);
        }
    }

    protected function addTodoCallback($todoData)
    {
        $data = $todoData->only(['step_run_id','record_id']);
        $result = Curl::setUrl($todoData->callback)->sendMessageByPost($data);
    }
}
