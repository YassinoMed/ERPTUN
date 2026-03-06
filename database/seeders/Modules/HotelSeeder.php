<?php

namespace Database\Seeders\Modules;

/**
 * Hotel Service Seeder — Hospitality Management
 * Seeds: room types, amenities, booking statuses, rate seasons
 */
class HotelSeeder extends BaseModuleSeeder
{
    protected string $moduleName = 'Hotel';

    protected function seed(): void
    {
        if ($this->tableExists('room_types')) {
            $types = [
                ['name' => 'Standard Single', 'slug' => 'standard_single', 'base_price' => 80.00, 'max_occupancy' => 1, 'sort_order' => 1],
                ['name' => 'Standard Double', 'slug' => 'standard_double', 'base_price' => 120.00, 'max_occupancy' => 2, 'sort_order' => 2],
                ['name' => 'Superior', 'slug' => 'superior', 'base_price' => 180.00, 'max_occupancy' => 2, 'sort_order' => 3],
                ['name' => 'Deluxe', 'slug' => 'deluxe', 'base_price' => 250.00, 'max_occupancy' => 3, 'sort_order' => 4],
                ['name' => 'Suite', 'slug' => 'suite', 'base_price' => 400.00, 'max_occupancy' => 4, 'sort_order' => 5],
                ['name' => 'Presidential Suite', 'slug' => 'presidential', 'base_price' => 800.00, 'max_occupancy' => 6, 'sort_order' => 6],
                ['name' => 'Family Room', 'slug' => 'family', 'base_price' => 200.00, 'max_occupancy' => 5, 'sort_order' => 7],
            ];
            foreach ($types as $t) {
                $this->upsert('room_types', ['slug' => $t['slug']], $t);
            }
        }

        if ($this->tableExists('booking_statuses')) {
            $statuses = [
                ['name' => 'Pending', 'slug' => 'pending', 'color' => '#FFA500'],
                ['name' => 'Confirmed', 'slug' => 'confirmed', 'color' => '#3498DB'],
                ['name' => 'Checked In', 'slug' => 'checked_in', 'color' => '#27AE60'],
                ['name' => 'Checked Out', 'slug' => 'checked_out', 'color' => '#95A5A6'],
                ['name' => 'Cancelled', 'slug' => 'cancelled', 'color' => '#E74C3C'],
                ['name' => 'No Show', 'slug' => 'no_show', 'color' => '#8E44AD'],
            ];
            foreach ($statuses as $s) {
                $this->upsert('booking_statuses', ['slug' => $s['slug']], $s);
            }
        }

        if ($this->tableExists('amenities')) {
            $amenities = [
                ['name' => 'WiFi', 'slug' => 'wifi', 'icon' => 'wifi', 'category' => 'technology'],
                ['name' => 'Air Conditioning', 'slug' => 'ac', 'icon' => 'snowflake', 'category' => 'comfort'],
                ['name' => 'Mini Bar', 'slug' => 'minibar', 'icon' => 'glass', 'category' => 'food'],
                ['name' => 'Room Service', 'slug' => 'room_service', 'icon' => 'bell', 'category' => 'service'],
                ['name' => 'Swimming Pool', 'slug' => 'pool', 'icon' => 'water', 'category' => 'recreation'],
                ['name' => 'Spa', 'slug' => 'spa', 'icon' => 'spa', 'category' => 'wellness'],
                ['name' => 'Parking', 'slug' => 'parking', 'icon' => 'car', 'category' => 'transport'],
                ['name' => 'Restaurant', 'slug' => 'restaurant', 'icon' => 'utensils', 'category' => 'food'],
                ['name' => 'Gym', 'slug' => 'gym', 'icon' => 'dumbbell', 'category' => 'wellness'],
                ['name' => 'Laundry', 'slug' => 'laundry', 'icon' => 'tshirt', 'category' => 'service'],
            ];
            foreach ($amenities as $a) {
                $this->upsert('amenities', ['slug' => $a['slug']], $a);
            }
        }
    }
}
