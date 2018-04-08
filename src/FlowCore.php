<?php

namespace Laravel\Flow;

use Illuminate\Support\Facades\Event;
use Laravel\Flow\Jobs\PerformFlow;
use Laravel\Flow\Watchers\CustomWatcher;
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
        'custom' => [],
    ];

    public function register(BaseFlow $flow)
    {
        $watcher = $flow->watches();

        if ($watcher instanceof CustomWatcher) {
            $this->events['custom'][$watcher->getEvent()][] = get_class($flow);
        } else if ($watcher instanceof EloquentWatcher) {
            $this->events[$watcher->getEvent()][$watcher->getModel()][] = get_class($flow);
        }

        return $this;
    }

    public function listen()
    {
        foreach ($this->events as $event => $models) {
            if ($event == 'custom') {
                $this->listenForCustomEvent($models);
            } else {
                $this->listenForEloquentEvent($event, $models);
            }
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

    public function listenForCustomEvent(array $events = [])
    {
        foreach ($events as $event => $flows) {
            if (class_exists($event)) {
                Event::listen($event, function ($response) use ($flows) {
                    $this->listenToFlows($flows, $response);
                });
            } else {
                Event::listen($event . "*", function ($eventName, $response) use ($flows, $event) {
                    if ($eventName === $event) {
                        $this->listenToFlows($flows, $response);
                    }
                });
            }
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