<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function ownedBusinesses(): BelongsToMany
    {
        return $this->businesses()->wherePivot('role', 'owner');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class, 'invited_by');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    /**
     * Get the user's current business from session.
     */
    public function currentBusiness(): ?Business
    {
        $businessId = session('current_business_id');

        if (! $businessId) {
            return null;
        }

        return $this->businesses()->find($businessId);
    }

    /**
     * Check if user is owner of current business.
     */
    public function isOwnerOfCurrentBusiness(): bool
    {
        $business = $this->currentBusiness();

        if (! $business) {
            return false;
        }

        $pivot = $this->businesses()->where('business_id', $business->id)->first()?->pivot;

        return $pivot && $pivot->role === 'owner';
    }

    /**
     * Check if user is admin of current business.
     */
    public function isAdminOfCurrentBusiness(): bool
    {
        $business = $this->currentBusiness();

        if (! $business) {
            return false;
        }

        $pivot = $this->businesses()->where('business_id', $business->id)->first()?->pivot;

        return $pivot && in_array($pivot->role, ['owner', 'admin']);
    }
}
