<?php

namespace Laravel\Flow\Watchers;

class CustomWatcher
{
    protected $event;

    public function __construct(string $event)
    {
        $this->event = $event;
    }

    public function getEvent()
    {
        return $this->event;
    }
}