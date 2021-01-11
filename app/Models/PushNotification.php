<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushNotification extends Model
{
    use HasFactory;

    /**
     * Récupère l'article lié
     */
    public function productItem(): BelongsTo
    {
        return $this->belongsTo(ProductItem::class);
    }
}
