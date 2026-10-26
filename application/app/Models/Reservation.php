<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Str;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference', 'vehicle_id', 'user_id', 'agency_id',
        'start_date', 'end_date', 'duration_days',
        'price_applied', 'total_price', 'status',
        'client_notes', 'cancellation_reason', 'cancelled_by',
        'confirmed_at', 'cancelled_at', 'completed_at',
        'payment_status', 'payment_reference', 'payment_simulated_at', 'contract_snapshot',
    ];

    protected $casts = [
        'contract_snapshot' => 'array',
        'payment_simulated_at' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
        'price_applied' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($reservation) {
            if ($reservation->isDirty('status') && $reservation->status === 'cancelled' && $reservation->payment_status === 'simulated') {
                $reservation->payment_status = 'simulation_cancelled';
            }
        });

        static::creating(function ($reservation) {
            $reservation->reference = self::generateReference();

            $reservation->duration_days = Carbon::parse($reservation->start_date)
                ->diffInDays(Carbon::parse($reservation->end_date));
        });
    }

    public static function generateReference(): string
    {
        return 'RES-'.now()->year.'-'.Str::uuid();
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverlapping($query, string $start, string $end)
    {
        return $query->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($q2) use ($start, $end) {
                        $q2->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                    });
            });
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'En attente',
            'confirmed' => 'Confirmée',
            'cancelled' => 'Annulée',
            'completed' => 'Terminée',
            'rejected' => 'Rejetée',
            default => 'Inconnu',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'confirmed' => 'green',
            'cancelled' => 'red',
            'completed' => 'blue',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    public function canBeCancelledByClient(): bool
    {
        if (! in_array($this->status, ['pending', 'confirmed'], true)) {
            return false;
        }

        if (! $this->created_at) {
            return false;
        }

        return now()->lessThanOrEqualTo($this->getClientCancellationDeadline());
    }

    public function getClientCancellationDeadline(): SupportCarbon
    {
        return $this->created_at->copy()->addHours(24);
    }

    public function canBeConfirmedByAgency(): bool
    {
        return $this->status === 'pending';
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }
}
