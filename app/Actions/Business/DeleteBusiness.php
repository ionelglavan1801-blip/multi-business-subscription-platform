<?php

namespace App\Actions\Business;

use App\Models\Business;
use App\Models\User;
use App\Notifications\BusinessDeleted;

class DeleteBusiness
{
    /**
     * Delete a business (soft delete).
     */
    public function execute(Business $business, User $deletedBy): bool
    {
        // Get users before detaching to notify them
        $users = $business->users()->get();
        $businessName = $business->name;
        $deletedByName = $deletedBy->name;

        // Remove all user associations
        $business->users()->detach();

        // Soft delete the business (cascades to projects, invitations via DB)
        $deleted = $business->delete();

        // Notify all users that the business was deleted
        if ($deleted) {
            foreach ($users as $user) {
                $user->notify(new BusinessDeleted($businessName, $deletedByName));
            }
        }

        return $deleted;
    }
}
