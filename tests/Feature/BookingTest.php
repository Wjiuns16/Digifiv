<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Database\Seeders\EventSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected static $setUpHasRunOnce = false;

    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        if (!static::$setUpHasRunOnce) {
            $this->seed([
                EventSeeder::class,
            ]);

            static::$setUpHasRunOnce = true;
        }

        $user = User::factory()->create([
            'password' => bcrypt('1234'),
        ]);

        $this->callLoginEndpoint($user);

        $this->token = Auth::user()->createToken('test')->plainTextToken;
    }

    private function callLoginEndpoint($user)
    {
        $data = [
            'email' => $user->email,
            'password' => '1234',
        ];

        $this->post('/api/login', $data);
    }

    public function test_user_booking()
    {
        $data = [
            'event_id' => Event::active()->first()->id,
            'number_of_tickets' => 2,
        ];

        $headers = [
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $responses = Http::pool(fn(Pool $pool) => [
            $pool->withHeaders($headers)->post('http://localhost:8000/api/bookings', $data),
            $pool->withHeaders($headers)->post('http://localhost:8000/api/bookings', $data),
            $pool->withHeaders($headers)->post('http://localhost:8000/api/bookings', $data),
        ]);

        $assertFalse = false;

        foreach ($responses as $response) {
            if (!$response->ok()) {
                $assertFalse = true;
            }
        }

        $this->assertTrue($assertFalse);
    }

}
