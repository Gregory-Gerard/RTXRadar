<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    /**
     * Récupère les articles
     */
    public function items(): HasMany
    {
        return $this->hasMany(ProductItem::class)->orderBy('state', 'desc')->orderBy('price', 'asc');
    }
}
