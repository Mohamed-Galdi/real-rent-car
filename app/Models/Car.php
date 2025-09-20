<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Car extends Model
{
    use SoftDeletes;

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
        'available',
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
        'available' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
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
        return '€' . number_format($this->price_per_day, 2);
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
}
