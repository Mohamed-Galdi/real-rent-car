<?php

namespace App\Models;

use App\Enums\CarColor;
use App\Enums\CarStatus;
use App\Enums\FuelType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use MohamedGaldi\ViltFilepond\Traits\HasFiles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\ReservationStatus;

class Car extends Model
{
    use SoftDeletes;
    use HasFiles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'make',
        'model',
        'year',
        'license_plate',
        'color',
        'price_per_day',
        'mileage',
        'transmission',
        'seats',
        'fuel_type',
        'description',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'year' => 'integer',
        'price_per_day' => 'decimal:2',
        'mileage' => 'integer',
        'seats' => 'integer',
        'status' => CarStatus::class,
        'fuel_type' => FuelType::class,
        'color' => CarColor::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'image_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Get the formatted price attribute.
     *
     * @return string
     */
    public function getFormattedPriceAttribute()
    {
        return config('app.currency_symbol') . number_format($this->price_per_day, 2);
    }

    /**
     * Get the full car name (make + model + year).
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->year} {$this->make} {$this->model}";
    }

    /**
     * Get the car image URL. Uses the first file in the 'image' collection if available,
     * otherwise falls back to the default public image.
     */
    public function getImageUrlAttribute(): string
    {
        // If the relation is already loaded, use it to avoid N+1
        $file = null;
        if ($this->relationLoaded('files')) {
            $file = $this->files->firstWhere('collection', 'image');
        }

        // Otherwise query for the first image file
        if (!$file) {
            $file = $this->files()->where('collection', 'image')->first();
        }

        if ($file && $file->path) {
            return Storage::url($file->path);
        }

        // Fallback to the public default image
        return asset('images/car-default.jpg');
    }

    /**
     * Get the reservations for the car.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Check if car is available for given date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @param int|null $excludeReservationId
     * @return bool
     */
    public function isAvailable(string $startDate, string $endDate, ?int $excludeReservationId = null): bool
    {
        $query = $this->reservations()
            ->whereIn('status', [
                ReservationStatus::CONFIRMED,
                ReservationStatus::ACTIVE
            ])
            ->betweenDates($startDate, $endDate);

        if ($excludeReservationId) {
            $query->where('id', '!=', $excludeReservationId);
        }

        return $query->count() === 0;
    }
}
