<?php

use Illuminate\Database\Seeder;

// models
use App\Models\Name;

class NamesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create an array of names from the CSV
        $fullNames = file(storage_path('names.csv'));

        // empty array ready to store names
        $names = [];

        // loop through each of the names
        foreach ($fullNames as $fullName) {
            // split the full name into first/last names and assign to vars
            list($first_name, $last_name) = preg_split('/\s+/', $fullName);

            // add to the names array using keys that match fields in the db
            $names[] = compact('first_name', 'last_name');
        }

        // add all of the names to the db
        Name::insert($names);
    }
}
