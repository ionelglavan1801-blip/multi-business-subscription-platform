<?php

namespace App\Actions\Business;

use App\Models\Business;

class DeleteBusiness
{
    /**
     * Delete a business (soft delete).
     */
    public function execute(Business $business): bool
    {
        // Remove all user associations
        $business->users()->detach();

        // Soft delete the business (cascades to projects, invitations via DB)
        return $business->delete();
    }
}
