<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Event::count() == 0) {
            $data = [
                'name' => 'Event 1',
                'status' => Event::STATUS_ACTIVE,
                'total_tickets' => 50,
                'available_tickets' => 50,
            ];

            Event::create($data);
        }
    }
}
