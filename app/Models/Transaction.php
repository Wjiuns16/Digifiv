<?php

namespace App\Models;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'booking_id', 'status',
    ];

    const STATUS_PENDING = 'PENDING';
    const STATUS_SUCCESS = 'SUCCESS';
    const STATUS_FAILED = 'FAILED';

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'status' => 'bail|required',
    ];

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
            'booking' => !empty($this->booking_id) ? $this->booking->data() : null,
            'status' => $this->status,
            'createdAt' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updatedAt' => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),
        ];
    }

    public function markAsSuccess()
    {
        DB::transaction(function () {
            $this->forceFill(['status' => self::STATUS_SUCCESS])->save();

            $this->booking->forceFill(['status' => Booking::STATUS_CONFIRMED])->save();
        });
    }

    public function markAsFailed($officer = '', $enhanced_kyc = '', $remark = '', $follow_ups = '')
    {
        DB::transaction(function () {
            $this->forceFill(['status' => self::STATUS_FAILED])->save();

            $this->booking->forceFill(['status' => Booking::STATUS_CANCELLED])->save();

            $this->booking->event->increment('available_tickets', $this->booking->number_of_tickets);
        });
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', self::STATUS_SUCCESS);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
