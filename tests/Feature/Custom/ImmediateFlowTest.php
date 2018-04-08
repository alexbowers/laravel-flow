<?php

namespace Laravel\Flow\Tests\Feature\Custom;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Laravel\Flow\BaseFlow;
use Laravel\Flow\Jobs\PerformFlow;
use Laravel\Flow\Tests\Feature\TestCase;
use Laravel\Flow\Tests\Helpers\Events\CustomerLoggedIn;
use Laravel\Flow\Tests\Helpers\Mail\StubbedEmail;
use Laravel\Flow\Tests\Helpers\Model\Customer;
use Laravel\Flow\Watchers\CustomWatcher;

class ImmediateFlowTest extends TestCase
{
    /**
     * @test
     */
     function initialised_custom_event_immediately()
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
                 return new CustomWatcher(CustomerLoggedIn::class);
             }
         };

         $this->registerFlow($flow);

         $customer = Customer::create([
             'name' => 'Alex Bowers',
             'email' => 'test@example.com',
         ]);

         Event::fire(new CustomerLoggedIn($customer));

         Queue::assertPushed(PerformFlow::class, function($job) use ($customer) {
             $record = $job->handle();

             $this->assertInstanceOf(CustomerLoggedIn::class, $record);

             $this->assertEquals($customer->name, $record->customer->name);

             return $record;
         });

         Mail::assertSent(StubbedEmail::class);
     }

    /**
     * @test
     */
    function string_without_data_custom_event_immediately()
    {
        Mail::fake();
        Queue::fake();

        $flow = new class extends BaseFlow
        {
            public function handle($data)
            {
                Mail::send(new StubbedEmail);
            }

            public function watches()
            {
                return new CustomWatcher('Special Event');
            }
        };

        $this->registerFlow($flow);

        Event::fire('Special Event');

        Queue::assertPushed(PerformFlow::class, function($job) {
            $data = $job->handle();

            return $data === null;
        });

        Mail::assertSent(StubbedEmail::class);
    }
}