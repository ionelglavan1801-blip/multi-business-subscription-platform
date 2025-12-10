<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_monthly',
        'max_businesses',
        'max_users_per_business',
        'max_projects',
        'stripe_price_id',
    ];

    protected function casts(): array
    {
        return [
            'price_monthly' => 'integer',
            'max_businesses' => 'integer',
            'max_users_per_business' => 'integer',
            'max_projects' => 'integer',
        ];
    }

    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
