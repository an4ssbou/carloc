<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehiclePhoto;
use App\Observers\VehicleObserver;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShowcaseSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            throw new \RuntimeException('Le jeu de démonstration est réservé à local/testing.');
        }
        $assets = json_decode(file_get_contents(resource_path('demo/vehicles.json')), true, 512, JSON_THROW_ON_ERROR);
        if (count($assets) !== 12) {
            throw new \RuntimeException('Douze modèles illustrés sont nécessaires.');
        }
        foreach ($assets as $asset) {
            if ($asset['license'] !== 'CC0' || ! is_file(public_path($asset['asset']))
                || hash_file('sha256', public_path($asset['asset'])) !== $asset['sha256']) {
                throw new \RuntimeException('Photo de démonstration absente ou modifiée : '.$asset['key']);
            }
        }

        DB::transaction(function () use ($assets) {
            DB::table('demo_datasets')->insertOrIgnore(['name' => 'showcase-v1', 'anchor_date' => now()->toDateString(), 'records' => null, 'created_at' => now(), 'updated_at' => now()]);
            $set = DB::table('demo_datasets')->where('name', 'showcase-v1')->lockForUpdate()->first();
            if ($set->records !== null) {
                return;
            } // Never reset fixtures edited after the first import.
            if (Vehicle::withTrashed()->where('registration_number', 'like', 'SHOW-%')->exists()
                || User::withTrashed()->where('email', 'like', '%@showcase.carloc.test')->exists()
                || Agency::withTrashed()->where('email', 'like', '%@showcase.carloc.test')->exists()
                || Reservation::withTrashed()->where('reference', 'like', 'SHOW-RES-%')->exists()) {
                throw new \RuntimeException('Collision du namespace SHOW : aucune donnée existante ne sera adoptée ou écrasée.');
            }
            $this->call(DemoSeeder::class);
            $primary = Agency::where('slug', 'agence-demonstration')->with('founder')->firstOrFail();
            if ($primary->email !== 'agence@carloc.test' || $primary->founder?->email !== 'agence@carloc.test' || ! $primary->isActive()) {
                throw new \RuntimeException('L’agence synthétique de base doit être active et appartenir au compte de démonstration.');
            }
            $anchor = CarbonImmutable::parse($set->anchor_date)->startOfDay();
            $records = ['anchor_date' => $anchor->toDateString(), 'clients' => [], 'owners' => [], 'agencies' => [$primary->id], 'vehicles' => [], 'photos' => [], 'reservations' => []];
            $agencies = [$primary];
            foreach ([['atlas', 'Atlas Mobilité — Démo', 'Rabat'], ['oasis', 'Oasis Location — Démo', 'Marrakech'], ['rivage', 'Rivage Auto — Démo', 'Agadir']] as [$key, $name, $city]) {
                $owner = $this->user($key.'@showcase.carloc.test', 'Agence', 'Demo '.ucfirst($key), 'agency_owner');
                $records['owners'][] = $owner->id;
                $agency = Agency::createQuietly(['name' => $name, 'slug' => 'showcase-'.$key, 'city' => $city,
                    'address' => 'Adresse fictive — démonstration', 'email' => 'agence-'.$key.'@showcase.carloc.test',
                    'founder_id' => $owner->id, 'status' => 'active', 'description' => '[SHOWCASE:v1] Agence fictive pour présentation locale.']);
                $agencies[] = $agency;
                $records['agencies'][] = $agency->id;
            }
            $records['admin'] = $this->user('admin@showcase.carloc.test', 'Admin', 'Demonstration', 'super_admin')->id;
            $clients = [];
            for ($i = 0; $i < 40; $i++) {
                $suffix = chr(65 + intdiv($i, 26)).chr(65 + $i % 26);
                $client = $this->user(sprintf('client-%02d@showcase.carloc.test', $i + 1), 'Client', 'Fictif '.$suffix, 'client');
                $clients[] = $client;
                $records['clients'][] = $client->id;
            }
            foreach ($assets as $asset) {
                $this->copyAsset('showcase/vehicles/'.$asset['key'].'.jpg', file_get_contents(public_path($asset['asset'])));
            }
            $cars = [];
            for ($i = 0; $i < 24; $i++) {
                $asset = $assets[$i % 12];
                $agency = $agencies[$i < 12 ? 0 : 1 + intdiv($i - 12, 4)];
                $rate = $asset['price_per_day'];
                $vehicle = Vehicle::createQuietly(['uuid' => (string) Str::uuid(), 'agency_id' => $agency->id,
                    'brand' => $asset['brand'], 'model' => $asset['model'], 'year' => $asset['year'],
                    'registration_number' => sprintf('SHOW-%03d', $i + 1), 'mileage' => 12000 + $i * 1300,
                    'color' => $asset['color'], 'condition' => 'good', 'seats' => $asset['key'] === '500' ? 4 : 5,
                    'fuel_type' => $asset['fuel_type'], 'transmission' => $asset['transmission'],
                    'price_per_day' => $rate, 'price_week' => $rate * 6, 'price_two_weeks' => $rate * 11, 'price_month' => $rate * 20,
                    'has_gps' => $i % 3 !== 1, 'is_featured' => $i >= 12 && $i % 4 === 0,
                    'description' => '[SHOWCASE:v1] Véhicule fictif. Photo illustrative du modèle, pas une offre de location réelle.',
                    'status' => 'available', 'is_manually_unavailable' => false]);
                $photo = VehiclePhoto::createQuietly(['vehicle_id' => $vehicle->id, 'path' => 'showcase/vehicles/'.$asset['key'].'.jpg', 'is_primary' => true, 'order' => 0]);
                $cars[] = $vehicle;
                $records['vehicles'][] = $vehicle->id;
                $records['photos'][] = $photo->id;
            }
            $baseCar = Vehicle::where('registration_number', 'DEMO-001')->firstOrFail();
            if (! $baseCar->photos()->exists()) {
                // Align only the unchanged legacy synthetic sample when adding its first illustration.
                if ($baseCar->brand === 'Dacia' && $baseCar->model === 'Sandero' && $baseCar->color === 'Blanc'
                    && $baseCar->description === 'Véhicule synthétique pour tester la réservation locale.') {
                    $baseCar->updateQuietly(['color' => 'Rouge']);
                }
                $photo = VehiclePhoto::createQuietly(['vehicle_id' => $baseCar->id, 'path' => 'showcase/vehicles/sandero.jpg', 'is_primary' => true, 'order' => 0]);
                $records['photos'][] = $photo->id;
            }
            // Add a logo only where the synthetic agency has none; preserve all existing custom logos.
            foreach ($agencies as $i => $agency) {
                if ($agency->logo) {
                    continue;
                }
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" viewBox="0 0 128 128"><rect width="128" height="128" rx="24" fill="#0d2137"/><path d="M28 76h72l-8-26H40z" fill="#feb234"/><circle cx="43" cy="85" r="10" fill="#fff"/><circle cx="85" cy="85" r="10" fill="#fff"/></svg>';
                $path = 'showcase/agencies/agency-'.$i.'.svg';
                $this->copyAsset($path, $svg);
                $agency->updateQuietly(['logo' => $path]);
            }

            $number = 0;
            for ($monthOffset = 5; $monthOffset >= 1; $monthOffset--) {
                $month = $anchor->subMonthsNoOverflow($monthOffset)->startOfMonth();
                for ($j = 0; $j < 30; $j++) {
                    $day = $j < 24 ? 2 + ($j % 6) * 3 : 23 + ($j % 3);
                    $start = $month->addDays($day - 1);
                    $status = $j % 7 === 0 ? 'cancelled' : 'completed';
                    $records['reservations'][] = $this->booking(++$number, $cars[$j % 24], $clients[($number - 1) % 40], $start, $start->addDays(2), $status, $anchor);
                }
            }
            $days = [2, 5, 8, 10, 12, 14, 16, 18, 19, 20, 21, 22];
            foreach ($days as $i => $day) {
                $start = $anchor->startOfMonth()->addDays(min($day - 1, max(0, $anchor->day - 4)));
                if ($start->addDays(2)->gte($anchor)) {
                    $start = $anchor->subDays(3);
                }
                $records['reservations'][] = $this->booking(++$number, $cars[$i], $clients[$number % 40], $start, $start->addDays(2), 'completed', $anchor);
            }
            foreach ([0, 3, 6, 9] as $i => $index) {
                $start = $anchor->subDays(1 + $i % 3);
                $records['reservations'][] = $this->booking(++$number, $cars[$index], $clients[$number % 40], $start, $anchor->addDays(2 + $i % 3), 'confirmed', $anchor);
            }
            foreach ([11, 12, 16, 20] as $i => $index) {
                $start = $anchor->addDays(4 + $i)->min($anchor->endOfMonth()->startOfDay());
                $records['reservations'][] = $this->booking(++$number, $cars[$index], $clients[$number % 40], $start, $start->addDays(3), 'confirmed', $anchor);
            }
            foreach ([1, 2, 4, 5, 7, 8] as $i => $index) {
                $start = $anchor->addDays(2 + $i);
                $records['reservations'][] = $this->booking(++$number, $cars[$index], $clients[$number % 40], $start, $start->addDays(2), 'pending', $anchor);
            }
            foreach ([13, 15, 18, 22] as $i => $index) {
                $start = $anchor->subDays(3 + $i);
                $records['reservations'][] = $this->booking(++$number, $cars[$index], $clients[$number % 40], $start, $start->addDays(2), 'cancelled', $anchor);
            }
            foreach ($cars as $car) {
                VehicleObserver::updateVehicleStatus($car);
            }
            foreach ($agencies as $agency) {
                $agency->updateQuietly(['total_vehicles' => $agency->vehicles()->count(), 'total_reservations' => $agency->reservations()->count()]);
            }
            DB::table('demo_datasets')->where('name', 'showcase-v1')->update(['records' => json_encode($records, JSON_THROW_ON_ERROR), 'updated_at' => now()]);
        }, 3);
        Storage::disk('local')->put('showcase/active.json', json_encode(['dataset' => 'showcase-v1', 'synthetic' => true]));
        $this->command?->info('Démonstration : 24 véhicules, 40 clients fictifs et 180 réservations; aucun paiement ni message réel.');
    }

    private function user(string $email, string $first, string $last, string $role): User
    {
        $user = new User;
        $user->forceFill(['email' => $email, 'first_name' => $first, 'last_name' => $last, 'role' => $role,
            'is_active' => true, 'has_store' => $role === 'agency_owner', 'email_verified_at' => now(),
            'password' => Hash::make('Demo-Local-2026!')])->saveQuietly();
        $user->syncRoles([$role]);

        return $user;
    }

    private function copyAsset(string $path, string $bytes): void
    {
        $disk = Storage::disk('public');
        if ($disk->exists($path)) {
            if (hash('sha256', $disk->get($path)) !== hash('sha256', $bytes)) {
                throw new \RuntimeException('Fichier existant différent : '.$path);
            }

            return;
        }
        if (! $disk->put($path, $bytes)) {
            throw new \RuntimeException('Écriture impossible : '.$path);
        }
    }

    private function booking(int $number, Vehicle $car, User $client, CarbonImmutable $start, CarbonImmutable $end, string $status, CarbonImmutable $anchor): int
    {
        $days = (int) $start->diffInDays($end);
        $created = $start->subDays(3 + $number % 5)->min($anchor)->addHours(9);
        $confirmed = in_array($status, ['confirmed', 'completed'], true) ? $created->addHours(1) : null;
        $cancelled = $status === 'cancelled' ? $created->addHours(2) : null;
        $r = Reservation::createQuietly(['reference' => sprintf('SHOW-RES-%04d', $number), 'vehicle_id' => $car->id,
            'agency_id' => $car->agency_id, 'user_id' => $client->id, 'start_date' => $start, 'end_date' => $end,
            'duration_days' => $days, 'price_applied' => $car->getPriceForDuration($days), 'total_price' => $car->calculateRentalTotal($days),
            'status' => $status, 'confirmed_at' => $confirmed, 'completed_at' => $status === 'completed' ? $end->addHours(12) : null,
            'cancelled_at' => $cancelled, 'cancelled_by' => $cancelled ? 'client' : null,
            'cancellation_reason' => $cancelled ? 'Annulation fictive de démonstration.' : null,
            'client_notes' => '[SHOWCASE:v1] Réservation fictive; aucun paiement réel.',
            'payment_status' => $confirmed ? 'simulated' : 'not_simulated',
            'payment_reference' => $confirmed ? sprintf('DEMO-SHOW-%04d', $number) : null,
            'payment_simulated_at' => $confirmed,
            'contract_snapshot' => ['version' => 1, 'synthetic' => true, 'client_name' => $client->full_name,
                'agency_name' => $car->agency->name, 'vehicle_name' => $car->brand.' '.$car->model,
                'registration_number' => $car->registration_number, 'start_date' => $start->toDateString(), 'end_date' => $end->toDateString(),
                'duration_days' => $days, 'price_applied' => number_format($car->getPriceForDuration($days), 2, '.', ''),
                'total_price' => number_format($car->calculateRentalTotal($days), 2, '.', ''), 'currency' => 'MAD'],
            'created_at' => $created, 'updated_at' => $cancelled ?? ($status === 'completed' ? $end->addHours(12) : ($confirmed ?? $created))]);

        return $r->id;
    }
}
