<?php

namespace Laravel\Flow\Tests\Feature\Eloquent;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Laravel\Flow\BaseFlow;
use Laravel\Flow\Jobs\PerformFlow;
use Laravel\Flow\Tests\Feature\TestCase;
use Laravel\Flow\Tests\Helpers\Mail\StubbedEmail;
use Laravel\Flow\Tests\Helpers\Model\Customer;
use Laravel\Flow\Watchers\EloquentWatcher;

class ImmediateFlowTest extends TestCase
{
    /**
     * @test
     */
    function a_flow_can_run_immediately()
    {
        Mail::fake();
        Queue::fake();

        $flow = new class extends BaseFlow
        {
            public function handle($record)
            {
                Mail::send(new StubbedEmail);
            }

            public function watches()
            {
                return new EloquentWatcher(Customer::class, 'created');
            }
        };

        $this->registerFlow($flow);

        $customer = Customer::create([
            'name' => 'Alex Bowers',
            'email' => 'test@example.com',
        ]);

        Queue::assertPushed(PerformFlow::class, function($job) use ($customer) {
            $record = $job->handle();

            $this->assertEquals($customer->name, $record->name);
            $this->assertEquals($customer->email, $record->email);
            $this->assertEquals($customer->id, $record->id);

            return $record;
        });

        Mail::assertSent(StubbedEmail::class);
    }
}