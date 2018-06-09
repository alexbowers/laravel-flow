<?php

namespace Laravel\Flow\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PerformFlow implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $flow;
    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($flow, $data)
    {
        $this->flow = $flow;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $flow = new $this->flow;

        $flow->handle($this->data);

        return $this->data;
    }
}
