<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'name', 'status', 'total_tickets', 'available_tickets',
    ];

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INACTIVE = 'INACTIVE';

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'bail|required',
        'status' => 'bail|required',
        'total_tickets' => 'bail|required|numeric|min:1',
        // 'available_tickets' => 'bail|numeric|min:1',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!isset($model->status)) {
                $model->status = self::STATUS_ACTIVE;
            }

            if (empty($model->available_tickets)) {
                $model->available_tickets = $model->total_tickets;
            }
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
            'name' => $this->name,
            'status' => $this->status,
            'totalTickets' => number_format($this->total_tickets),
            'availableTickets' => number_format($this->available_tickets),
            'createdAt' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updatedAt' => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
