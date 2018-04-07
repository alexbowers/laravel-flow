<?php

namespace Laravel\Flow;

use Illuminate\Support\Facades\Event;
use Laravel\Flow\Jobs\PerformFlow;
use Laravel\Flow\Watchers\EloquentWatcher;

class FlowCore
{
    protected $events = [
        'retrieved' => [],
        'created' => [],
        'updated' => [],
        'saved' => [],
        'deleted' => [],
        'restored' => [],
    ];

    public function register(BaseFlow $flow)
    {
        $watcher = $flow->watches();

        if ($watcher instanceof EloquentWatcher) {
            $this->events[$watcher->getEvent()][$watcher->getModel()][] = get_class($flow);
        }

        return $this;
    }

    public function listen()
    {
        foreach ($this->events as $event => $models) {
            $this->listenForEloquentEvent($event, $models);
        }
    }

    public function listenForEloquentEvent($event, array $models = [])
    {
        foreach ($models as $model => $flows) {
            Event::listen("eloquent.{$event}: {$model}", function ($response) use ($flows) {
                $this->listenToFlows($flows, $response);
            });
        }
    }

    private function listenToFlows($flows, $response)
    {
        foreach ($flows as $flow) {
            dispatch(
                new PerformFlow($flow, $response)
            );
        }
    }
}