<?php

namespace Laravel\Flow\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laravel\Flow\Flow;

class ScheduleFlow implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $flow;
    protected $record;
    protected $flow_delay;
    protected $flow_interval;
    protected $flow_times;

    public function __construct($flow, $record, $flow_delay = null)
    {
        $this->flow = $flow;
        $this->record = $record;
        $this->flow_delay = $flow_delay;
    }

    public function handle()
    {
        return Flow::create([
            'flow' => $this->flow,
            'record' => get_class($this->record),
            'record_id' => $this->record->id,
            'interval' => $this->flow_interval,
            'times' => $this->flow_times,
            'available_at' => $this->flow_delay,
        ]);
    }
}
