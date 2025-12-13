<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'plan_id',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get the owner of the business (as a relationship).
     */
    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps()
            ->wherePivot('role', 'owner');
    }

    /**
     * Get the owner user directly.
     */
    public function getOwnerAttribute(): ?User
    {
        return $this->owners()->first();
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active');
    }

    /**
     * Check if the business can add more projects based on plan limits.
     */
    public function canAddMoreProjects(): bool
    {
        $maxProjects = $this->plan?->max_projects;

        if ($maxProjects === null) {
            return true;
        }

        return $this->projects()->count() < $maxProjects;
    }

    /**
     * Check if the business can add more users based on plan limits.
     */
    public function canAddMoreUsers(): bool
    {
        $maxUsers = $this->plan?->max_users_per_business;

        if ($maxUsers === null) {
            return true;
        }

        return $this->users()->count() < $maxUsers;
    }

    /**
     * Get the projects usage percentage.
     */
    public function projectsUsagePercentage(): int
    {
        $maxProjects = $this->plan?->max_projects;

        if ($maxProjects === null || $maxProjects === 0) {
            return 0;
        }

        return (int) min(100, ($this->projects()->count() / $maxProjects) * 100);
    }

    /**
     * Get the users usage percentage.
     */
    public function usersUsagePercentage(): int
    {
        $maxUsers = $this->plan?->max_users_per_business;

        if ($maxUsers === null || $maxUsers === 0) {
            return 0;
        }

        return (int) min(100, ($this->users()->count() / $maxUsers) * 100);
    }

    /**
     * Get remaining project slots.
     */
    public function remainingProjectSlots(): ?int
    {
        $maxProjects = $this->plan?->max_projects;

        if ($maxProjects === null) {
            return null;
        }

        return max(0, $maxProjects - $this->projects()->count());
    }

    /**
     * Get remaining user slots.
     */
    public function remainingUserSlots(): ?int
    {
        $maxUsers = $this->plan?->max_users_per_business;

        if ($maxUsers === null) {
            return null;
        }

        return max(0, $maxUsers - $this->users()->count());
    }
}
