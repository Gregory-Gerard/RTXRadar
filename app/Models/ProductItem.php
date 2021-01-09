<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductItem extends Model
{
    use HasFactory;

    /**
     * Récupère les notifications push envoyées
     */
    public function pushNotifications(): HasMany
    {
        return $this->hasMany(PushNotification::class);
    }
}
