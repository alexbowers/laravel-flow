<?php

namespace Laravel\Flow\Tests\Feature\Eloquent;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Laravel\Flow\BaseFlow;
use Laravel\Flow\Jobs\PerformFlow;
use Laravel\Flow\Jobs\ScheduleFlow;
use Laravel\Flow\Tests\Feature\TestCase;
use Laravel\Flow\Tests\Helpers\Mail\StubbedEmail;
use Laravel\Flow\Tests\Helpers\Model\Customer;
use Laravel\Flow\Watchers\EloquentWatcher;
use Facades\ {
    Laravel\Flow\FlowCore
};

class ImmediateDelayTest extends TestCase
{
    /**
     * @test
     */
    function delay_immediately_carbon()
    {
        Mail::fake();
        Queue::fake();

        $flow = new class extends BaseFlow
        {
            public function handle($record)
            {
                Mail::send(new StubbedEmail);
            }

            public function delay()
            {
                return Carbon::now()->addWeek();
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

        Queue::assertNotPushed(PerformFlow::class);

        Mail::assertNotSent(StubbedEmail::class);

        Queue::assertPushed(ScheduleFlow::class, function($job) use ($flow) {
            $record = $job->handle();

            $this->assertTrue($record->available_at->isFuture());

            return $record;
        });

        $this->assertEmpty(
            FlowCore::schedule()
        );

        Carbon::setTestNow(Carbon::now()->addMonth());

        $this->assertNotEmpty(
            FlowCore::schedule()
        );

        Queue::assertPushed(PerformFlow::class);

        Mail::assertSent(StubbedEmail::class);

        dd(
            FlowCore::schedule()
        );
    }
}