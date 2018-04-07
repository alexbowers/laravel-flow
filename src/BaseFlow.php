<?php

namespace Laravel\Flow;

abstract class BaseFlow
{
    abstract public function watches();

    abstract public function handle($record);
}