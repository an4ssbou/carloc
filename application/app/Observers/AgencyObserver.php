<?php

namespace App\Observers;

use App\Models\Agency;
use Illuminate\Support\Facades\Storage;

class AgencyObserver
{
    /**
     * Handle the Agency "created" event.
     */
    public function created(Agency $agency): void
    {
        //
    }

    /**
     * Handle the Agency "updated" event.
     */
    public function updated(Agency $agency): void
    {

    }

    /**
     * Handle the Agency "deleted" event.
     */
    public function deleted(Agency $agency): void
    {
        //
    }

    /**
     * Handle the Agency "restored" event.
     */
    public function restored(Agency $agency): void
    {
        //
    }

    /**
     * Handle the Agency "force deleted" event.
     */
    public function forceDeleted(Agency $agency): void
    {
        //
    }

    public function updating(Agency $agency): void
    {
        if ($agency->isDirty('logo')) {
            $oldLogo = $agency->getOriginal('logo');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }
        }

        $reviewedFields = [
            'name',
            'city',
            'address',
            'phone',
            'email',
            'logo',
            'description',
        ];

        $hasBusinessChanges = collect($reviewedFields)->contains(
            fn (string $field) => $agency->isDirty($field)
        );

        if ($hasBusinessChanges && ! $agency->isDirty('status')) {
            $agency->status = 'pending';
            $agency->approved_at = null;
            $agency->rejected_at = null;
            $agency->rejection_reason = null;
        }
    }
}
