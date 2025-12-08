<?php

namespace Database\Seeders;

use App\Models\ServiceList;
use App\Models\ServiceListDetail;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceListSeeder extends Seeder
{
    public function run(): void
    {
        $venues = Venue::all();

        if ($venues->isEmpty()) {
            $this->command->warn('Please run VenueSeeder first!');
            return;
        }

        $serviceLists = [
            'Food',
            'Drinks',
            'Equipment Rental',
        ];

        foreach ($venues as $venue) {
            foreach ($serviceLists as $serviceListName) {
                $serviceList = ServiceList::firstOrCreate(
                    [
                        'venue_id' => $venue->id,
                        'name' => $serviceListName,
                    ],
                    [
                        'id' => Str::uuid()->toString(),
                    ]
                );

                // Create service list details
                $details = [];
                
                if ($serviceListName === 'Food') {
                    $details = [
                        ['name' => 'Pizza', 'wholesale' => '150000', 'unit_wholesale' => '1 box (10 pieces)', 'retail' => '20000', 'unit_retail' => '1 piece'],
                        ['name' => 'Burger', 'wholesale' => '120000', 'unit_wholesale' => '1 box (10 pieces)', 'retail' => '15000', 'unit_retail' => '1 piece'],
                        ['name' => 'Sandwich', 'wholesale' => '100000', 'unit_wholesale' => '1 box (10 pieces)', 'retail' => '12000', 'unit_retail' => '1 piece'],
                    ];
                } elseif ($serviceListName === 'Drinks') {
                    $details = [
                        ['name' => 'Water', 'wholesale' => '50000', 'unit_wholesale' => '1 case (24 bottles)', 'retail' => '5000', 'unit_retail' => '1 bottle'],
                        ['name' => 'Soft Drink', 'wholesale' => '80000', 'unit_wholesale' => '1 case (24 cans)', 'retail' => '10000', 'unit_retail' => '1 can'],
                        ['name' => 'Energy Drink', 'wholesale' => '120000', 'unit_wholesale' => '1 case (24 cans)', 'retail' => '15000', 'unit_retail' => '1 can'],
                    ];
                } elseif ($serviceListName === 'Equipment Rental') {
                    $details = [
                        ['name' => 'Racket', 'wholesale' => null, 'unit_wholesale' => 'N/A', 'retail' => '50000', 'unit_retail' => 'per hour'],
                        ['name' => 'Ball', 'wholesale' => null, 'unit_wholesale' => 'N/A', 'retail' => '20000', 'unit_retail' => 'per set'],
                        ['name' => 'Shoes', 'wholesale' => null, 'unit_wholesale' => 'N/A', 'retail' => '30000', 'unit_retail' => 'per hour'],
                    ];
                }

                foreach ($details as $detail) {
                    ServiceListDetail::firstOrCreate(
                        [
                            'service_list_id' => $serviceList->id,
                            'name' => $detail['name'],
                        ],
                        array_merge($detail, [
                            'id' => Str::uuid()->toString(),
                        ])
                    );
                }
            }
        }

        $this->command->info('Service lists seeded successfully!');
    }
}
