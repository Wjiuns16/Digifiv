<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEventRequest;
use App\Http\Requests\EditEventRequest;
use App\Models\Event;
use Exception;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function list(Request $request)
    {
        try {
            $query = Event::query();

            if (!empty($name = $request->get('name'))) {
                $query->where('name', 'like', sprintf('%%%s%%', $name));
            }

            if (!empty($status = $request->get('status'))) {
                $query->where('status', $status);
            }

            if (!empty($minTotalTickets = $request->get('minTotalTickets'))) {
                $query->where('total_tickets', '>=', intval($minTotalTickets));
            }

            if (!empty($maxTotalTickets = $request->get('maxTotalTickets'))) {
                $query->where('total_tickets', '<=', intval($maxTotalTickets));
            }

            if (!empty($minAvailableTickets = $request->get('minAvailableTickets'))) {
                $query->where('available_tickets', '>=', intval($minAvailableTickets));
            }

            if (!empty($maxAvailableTickets = $request->get('maxAvailableTickets'))) {
                $query->where('available_tickets', '<=', intval($maxAvailableTickets));
            }

            if ($this->limit === -1) {
                $this->limit = Event::count();
            }

            $events = $query->orderBy($this->sortBy, $this->sort)
                ->paginate($this->limit, ['*'], 'page', $this->page);

            $result = [
                'data' => Event::format_data($events->items()),
                'currentPage' => $events->currentPage(),
                'totalData' => $events->total(),
                'perPage' => $events->perPage(),
                'nextPage' => $events->nextPageUrl() ? $events->currentPage() + 1 : null,
            ];

            return $this->sendResponse($result, 'Events retrieved successfully');
        } catch (Exception $e) {
            return $this->sendError(null, $e->getMessage());
        }
    }

    public function create(CreateEventRequest $request)
    {
        try {
            $event = Event::create([
                "name" => $request->name,
                "status" => $request->status,
                "total_tickets" => $request->total_tickets,
            ]);

            return $this->sendResponse($event->data(), 'Event added successfully');
        } catch (Exception $e) {
            return $this->sendError(null, $e->getMessage());
        }
    }

    public function edit(EditEventRequest $request, $id)
    {
        try {
            $event = $this->checkEventId($id);
            if (empty($event)) {
                return $this->sendError(null, 'Event not found');
            }

            $event->update([
                "name" => $request->name,
                "status" => $request->status,
                "total_tickets" => $request->total_tickets,
                "available_tickets" => $request->available_tickets,
            ]);

            return $this->sendResponse(null, 'Event updated successfully');
        } catch (Exception $e) {
            return $this->sendError(null, $e->getMessage());
        }
    }

    public function query($id)
    {
        try {
            $event = $this->checkEventId($id);
            if (empty($event)) {
                return $this->sendError(null, 'Event not found');
            }

            return $this->sendResponse($event->data(), 'Event found successfully');
        } catch (Exception $e) {
            return $this->sendError(null, $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $event = $this->checkEventId($id);
            if (empty($event)) {
                return $this->sendError(null, 'Event not found');
            }

            $event->delete();

            return $this->sendResponse(null, 'Event deleted successfully');
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
}
