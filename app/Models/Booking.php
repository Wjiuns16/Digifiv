<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id', 'event_id', 'number_of_tickets', 'status',
    ];

    const STATUS_PENDING = 'PENDING';
    const STATUS_CONFIRMED = 'CONFIRMED';
    const STATUS_CANCELLED = 'CANCELLED';

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'event_id' => 'bail|required',
        'number_of_tickets' => 'bail|required|numeric|min:1',
        // 'status' => 'bail|required',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!isset($model->status)) {
                $model->status = self::STATUS_PENDING;
            }
        });

        static::created(function ($model) {
            Transaction::create([
                'booking_id' => $model->id,
                'status' => Transaction::STATUS_PENDING,
            ]);
        });

    }

    public static function format_data($result)
    {
        $formattedData = [];

        foreach ($result as $item) {
            $formattedData[] = $item->data();
        }

        return $formattedData;
    }

    public function data()
    {
        return [
            'id' => $this->id,
            'user' => !empty($this->user_id) ? $this->user->data() : null,
            'event' => !empty($this->event_id) ? $this->event->data() : null,
            'numberOfTickets' => number_format($this->number_of_tickets),
            'status' => $this->status,
            'createdAt' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updatedAt' => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),
        ];
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}
