<?php

namespace App\Listeners;

use App\Events\BookingProcess;
use App\Models\Booking;
use App\Models\Event;
use DB;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessBooking implements ShouldQueue
{
    use InteractsWithQueue;

    protected $user;

    protected $event;

    protected $numberOfTickets;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }
    /**
     * Handle the event.
     */
    public function handle(BookingProcess $event): void
    {
        if (!empty($event->booking)) {
            $this->user = $event->booking['user'];

            $this->event = $event->booking['event'];

            $this->numberOfTickets = intval($event->booking['numberOfTickets']);

            DB::transaction(function () {
                $event = Event::lockForUpdate()->find($this->event->id);

                if ($event->available_tickets < $this->numberOfTickets) {
                    //TODO:: send user a email or notification on failure
                    $this->fail(throw new Exception('Not enough tickets available.'));
                }

                $event->decrement('available_tickets', $this->numberOfTickets);

                Booking::create([
                    'user_id' => $this->user->id,
                    'event_id' => $this->event->id,
                    'number_of_tickets' => $this->numberOfTickets,
                ]);

            });
        }
        //}
    }
}
