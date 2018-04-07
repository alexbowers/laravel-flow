<?php

namespace Laravel\Flow\Tests\Feature;

use Laravel\Flow\FlowCore;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function registerFlow($flow)
    {
        $flow_initialiser = app()->make(FlowCore::class);

        $flow_initialiser->register($flow);

        $flow_initialiser->listen();
    }

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/../Helpers/Migrations');

        $this->artisan('migrate', ['--database' => 'testing']);
    }
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}