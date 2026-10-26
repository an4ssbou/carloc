<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use App\Notifications\AppNotification;
use App\Support\SafeNotifier;

class SendReturnReminders extends Command
{
    protected $signature = 'reservations:send-reminders';
    protected $description = 'Envoie des rappels de retour de véhicule pour aujourd’hui et demain';

    public function handle()
    {
        $dates = [
            now()->format('Y-m-d'),
            now()->addDay()->format('Y-m-d')
        ];

        $this->info("Vérification des retours pour : " . implode(', ', $dates));

        $reservations = Reservation::with(['client', 'vehicle'])
            ->where('status', 'confirmed')
            ->whereIn('end_date', $dates)
            ->get();

        if ($reservations->isEmpty()) {
            $this->info("Aucune réservation trouvée.");
            return;
        }

        foreach ($reservations as $reservation) {
            if (!$reservation->client || !$reservation->vehicle) {
                $this->warn("Données incomplètes pour la résa #{$reservation->id}");
                continue;
            }

            try {
                SafeNotifier::send($reservation->client, new AppNotification([
                    'subject' => 'Rappel : Retour de véhicule',
                    'body'    => "Rappel : le retour de votre véhicule ({$reservation->vehicle->brand}) est prévu pour le {$reservation->end_date->format('d/m/Y')}.",
                    'url'     => '/my-reservations'
                ]), ['event' => 'return_reminder']);

                $this->info("✅ Rappel envoyé pour la réservation #{$reservation->id}");
            } catch (\Exception $e) {
                $this->error("❌ Erreur résa #{$reservation->id} : " . $e->getMessage());
            }
        }

        $this->info("Traitement terminé.");
    }
}
