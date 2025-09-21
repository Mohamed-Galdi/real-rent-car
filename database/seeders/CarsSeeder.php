<?php

namespace Database\Seeders;

use App\Models\Car;
use Illuminate\Database\Seeder;

class CarsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cars = [
            [
                'make' => 'Toyota', 'model' => 'Corolla', 'year' => 2019,
                'license_plate' => 'AA-1001', 'color' => 'White',
                'price_per_day' => 45.00, 'mileage' => 45000,
                'transmission' => 'automatic', 'seats' => 5,
                'fuel_type' => 'Petrol', 'description' => 'Reliable and efficient sedan.', 'available' => true,
            ],
            [
                'make' => 'Honda', 'model' => 'Civic', 'year' => 2020,
                'license_plate' => 'AA-1002', 'color' => 'Black',
                'price_per_day' => 50.00, 'mileage' => 38000,
                'transmission' => 'manual', 'seats' => 5,
                'fuel_type' => 'Diesel', 'description' => 'Sporty and comfortable.', 'available' => true,
            ],
            [
                'make' => 'Ford', 'model' => 'Focus', 'year' => 2018,
                'license_plate' => 'AA-1003', 'color' => 'Blue',
                'price_per_day' => 42.00, 'mileage' => 60000,
                'transmission' => 'automatic', 'seats' => 5,
                'fuel_type' => 'Petrol', 'description' => 'Great handling, compact.', 'available' => true,
            ],
            [
                'make' => 'Volkswagen', 'model' => 'Golf', 'year' => 2021,
                'license_plate' => 'AA-1004', 'color' => 'Grey',
                'price_per_day' => 55.00, 'mileage' => 22000,
                'transmission' => 'automatic', 'seats' => 5,
                'fuel_type' => 'Petrol', 'description' => 'Premium compact hatch.', 'available' => true,
            ],
            [
                'make' => 'Hyundai', 'model' => 'Elantra', 'year' => 2019,
                'license_plate' => 'AA-1005', 'color' => 'Red',
                'price_per_day' => 44.00, 'mileage' => 50000,
                'transmission' => 'manual', 'seats' => 5,
                'fuel_type' => 'Petrol', 'description' => 'Comfortable daily driver.', 'available' => true,
            ],
            [
                'make' => 'Kia', 'model' => 'Sportage', 'year' => 2020,
                'license_plate' => 'AA-1006', 'color' => 'White',
                'price_per_day' => 60.00, 'mileage' => 35000,
                'transmission' => 'automatic', 'seats' => 5,
                'fuel_type' => 'Diesel', 'description' => 'Compact SUV with space.', 'available' => true,
            ],
            [
                'make' => 'Nissan', 'model' => 'Qashqai', 'year' => 2018,
                'license_plate' => 'AA-1007', 'color' => 'Silver',
                'price_per_day' => 52.00, 'mileage' => 70000,
                'transmission' => 'manual', 'seats' => 5,
                'fuel_type' => 'Petrol', 'description' => 'Crossover comfort.', 'available' => true,
            ],
            [
                'make' => 'BMW', 'model' => '3 Series', 'year' => 2021,
                'license_plate' => 'AA-1008', 'color' => 'Black',
                'price_per_day' => 95.00, 'mileage' => 25000,
                'transmission' => 'automatic', 'seats' => 5,
                'fuel_type' => 'Petrol', 'description' => 'Luxury sedan experience.', 'available' => true,
            ],
            [
                'make' => 'Mercedes', 'model' => 'C-Class', 'year' => 2020,
                'license_plate' => 'AA-1009', 'color' => 'White',
                'price_per_day' => 100.00, 'mileage' => 30000,
                'transmission' => 'automatic', 'seats' => 5,
                'fuel_type' => 'Petrol', 'description' => 'Comfort and style.', 'available' => true,
            ],
            [
                'make' => 'Audi', 'model' => 'A4', 'year' => 2019,
                'license_plate' => 'AA-1010', 'color' => 'Grey',
                'price_per_day' => 90.00, 'mileage' => 40000,
                'transmission' => 'automatic', 'seats' => 5,
                'fuel_type' => 'Diesel', 'description' => 'Premium driving.', 'available' => true,
            ],
            [
                'make' => 'Renault', 'model' => 'Clio', 'year' => 2018,
                'license_plate' => 'AA-1011', 'color' => 'Blue',
                'price_per_day' => 38.00, 'mileage' => 65000,
                'transmission' => 'manual', 'seats' => 5,
                'fuel_type' => 'Petrol', 'description' => 'City-friendly hatch.', 'available' => true,
            ],
            [
                'make' => 'Peugeot', 'model' => '208', 'year' => 2021,
                'license_plate' => 'AA-1012', 'color' => 'Yellow',
                'price_per_day' => 48.00, 'mileage' => 20000,
                'transmission' => 'automatic', 'seats' => 5,
                'fuel_type' => 'Petrol', 'description' => 'Modern design and tech.', 'available' => true,
            ],
        ];

        foreach ($cars as $data) {
            Car::query()->updateOrCreate(
                ['license_plate' => $data['license_plate']],
                $data
            );
        }
    }
}
