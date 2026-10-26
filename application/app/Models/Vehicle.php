<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $dateFormat = 'Y-m-d H:i:s.u';

    protected $fillable = [
        'uuid', 'agency_id', 'brand', 'model', 'year',
        'registration_number', 'mileage', 'color', 'condition',
        'seats', 'fuel_type', 'transmission', 'description',
        'has_gps',
        'price_per_day', 'price_week', 'price_two_weeks', 'price_month',
        'status', 'is_manually_unavailable', 'is_featured',
    ];

    protected $hidden = ['gps_latitude', 'gps_longitude', 'gps_token_hash', 'gps_recorded_at', 'gps_received_at', 'gps_source'];

    protected $casts = [
        'gps_recorded_at' => 'immutable_datetime',
        'gps_received_at' => 'immutable_datetime',
        'has_gps' => 'boolean',
        'is_manually_unavailable' => 'boolean',
        'is_featured' => 'boolean',
        'price_per_day' => 'decimal:2',
        'price_week' => 'decimal:2',
        'price_two_weeks' => 'decimal:2',
        'price_month' => 'decimal:2',
        'gps_latitude' => 'decimal:7',
        'gps_longitude' => 'decimal:7',
    ];

    protected static function boot()
    {
        parent::boot();
        static::updating(function ($vehicle) {
            if ($vehicle->isDirty('has_gps') && ! $vehicle->has_gps) {
                $vehicle->forceFill([
                    'gps_latitude' => null, 'gps_longitude' => null, 'gps_token_hash' => null,
                    'gps_recorded_at' => null, 'gps_received_at' => null, 'gps_source' => null,
                ]);
            }
        });
        static::creating(function ($vehicle) {
            $vehicle->uuid = Str::uuid();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function photos()
    {
        return $this->hasMany(VehiclePhoto::class)->orderBy('order');
    }

    public function primaryPhoto()
    {
        return $this->hasOne(VehiclePhoto::class)->where('is_primary', true);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function activeReservations()
    {
        return $this->hasMany(Reservation::class)
            ->whereIn('status', ['pending', 'confirmed']);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
            ->where('is_manually_unavailable', false);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByBrand($query, string $brand)
    {
        return $query->where('brand', $brand);
    }

    public function scopeByCity($query, string $city)
    {
        return $query->whereHas('agency', fn ($q) => $q->where('city', $city)->where('status', 'active')
        );
    }

    public function scopeByPriceRange($query, float $min, float $max)
    {
        return $query->whereBetween('price_per_day', [$min, $max]);
    }

    public function getPrimaryPhotoUrlAttribute(): string
    {
        $primary = $this->primaryPhoto;
        if ($primary) {
            return asset('storage/'.$primary->path);
        }

        return asset('images/default-vehicle.jpg');
    }

    public function getConditionLabelAttribute(): string
    {
        return match ($this->condition) {
            'new' => 'Neuf',
            'good' => 'Bon état',
            'used' => 'Usagé',
            default => 'Inconnu',
        };
    }

    public function getFuelTypeLabelAttribute(): string
    {
        return match ($this->fuel_type) {
            'gasoline' => 'Essence',
            'diesel' => 'Diesel',
            'electric' => 'Électrique',
            'hybrid' => 'Hybride',
            default => 'Inconnu',
        };
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->status === 'available'
            && ! $this->is_manually_unavailable;
    }

    public function getPriceForDuration(int $days): float
    {
        if ($days >= 30 && $this->price_month) {
            return (float) $this->price_month;
        }
        if ($days >= 15 && $this->price_two_weeks) {
            return (float) $this->price_two_weeks;
        }
        if ($days >= 7 && $this->price_week) {
            return (float) $this->price_week;
        }

        return (float) $this->price_per_day;
    }

    public function calculateRentalTotal(int $days): float
    {
        $price = $this->getPriceForDuration($days);

        if (
            ($days >= 30 && $this->price_month) ||
            ($days >= 15 && $this->price_two_weeks) ||
            ($days >= 7 && $this->price_week)
        ) {
            return $price;
        }

        return $price * $days;
    }

    public function getBlockedDates(): array
    {
        return $this->activeReservations()
            ->get(['start_date', 'end_date'])
            ->map(fn ($r) => [
                'from' => $r->start_date->format('Y-m-d'),
                'to' => $r->end_date->format('Y-m-d'),
            ])
            ->toArray();
    }
}
