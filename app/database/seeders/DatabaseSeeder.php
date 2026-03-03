<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@graficaimpacta.local'],
            [
                'name' => 'Administrador',
                'phone' => '(11) 99999-0000',
                'is_admin' => true,
                'password' => Hash::make('password'),
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'cliente@graficaimpacta.local'],
            [
                'name' => 'Cliente Demo',
                'phone' => '(11) 98888-0000',
                'is_admin' => false,
                'password' => Hash::make('password'),
            ],
        );

        $this->call([
            CatalogSeeder::class,
            HomeBannerSeeder::class,
            DemoOrdersSeeder::class,
        ]);
    }
}
