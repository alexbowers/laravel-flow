<?php

namespace Laravel\Flow\Watchers;

class EloquentWatcher
{
    protected $model;
    protected $event;

    public function __construct(string $model, string $event)
    {
        $this->model = $model;
        $this->event = $event;
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function getModel()
    {
        return $this->model;
    }
}