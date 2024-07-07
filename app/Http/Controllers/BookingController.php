<?php

namespace App\Http\Controllers;

use App\Events\BookingProcess;
use App\Http\Requests\CreateBookingRequest;
use App\Models\Booking;
use App\Models\Event;
use Exception;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function list(Request $request)
    {
        try {
            $user = $request->user();

            $query = Booking::where('user_id', $user->id);

            if (!empty($eventName = $request->get('eventName'))) {
                $query->whereHas('event', function ($query) use ($eventName) {
                    $query->where('name', 'like', sprintf('%%%s%%', $eventName));
                });
            }
            if (!empty($status = $request->get('status'))) {
                $query->where('status', $status);
            }

            if (!empty($minNumberOfTickets = $request->get('minNumberOfTickets'))) {
                $query->where('total_tickets', '>=', intval($minNumberOfTickets));
            }

            if (!empty($maxNumberOfTickets = $request->get('maxNumberOfTickets'))) {
                $query->where('total_tickets', '<=', intval($maxNumberOfTickets));
            }

            if ($this->limit === -1) {
                $this->limit = Booking::count();
            }

            $bookings = $query->orderBy($this->sortBy, $this->sort)
                ->paginate($this->limit, ['*'], 'page', $this->page);

            $result = [
                'data' => Booking::format_data($bookings->items()),
                'currentPage' => $bookings->currentPage(),
                'totalData' => $bookings->total(),
                'perPage' => $bookings->perPage(),
                'nextPage' => $bookings->nextPageUrl() ? $bookings->currentPage() + 1 : null,
            ];

            return $this->sendResponse($result, 'Users booking retrieved successfully');
        } catch (Exception $e) {
            return $this->sendError(null, $e->getMessage());
        }
    }

    public function book(CreateBookingRequest $request)
    {
        try {
            $user = $request->user();

            $event = $this->checkEventId($request->event_id);
            if (empty($event)) {
                return $this->sendError(null, 'Event not found');
            }

            $booking = [
                'user' => $user,
                'event' => $event,
                'numberOfTickets' => $request->number_of_tickets,
            ];

            event(new BookingProcess($booking));

            return $this->sendResponse(null, 'Your booking is being processed.');
        } catch (Exception $e) {
            return $this->sendError(null, $e->getMessage());
        }
    }

    public function query($id)
    {
        try {
            $booking = $this->checkBookingId($id);
            if (empty($booking)) {
                return $this->sendError(null, 'Booking not found');
            }

            return $this->sendResponse($booking->data(), 'Booking found successfully');
        } catch (Exception $e) {
            return $this->sendError(null, $e->getMessage());
        }
    }

    private function checkEventId($id)
    {
        if (!empty(intval($id))) {
            return Event::where('id', $id)->first();
        }

        return null;
    }
    private function checkBookingId($id)
    {
        if (!empty(intval($id))) {
            return Booking::where('id', $id)->first();
        }

        return null;
    }
}
