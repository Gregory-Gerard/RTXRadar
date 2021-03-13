<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Product
 */
class Product extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'url' => route('page.product', $this->id),
            'title' => $this->title,
            'items' => [
                'data' => ProductItem::collection($this->whenLoaded('items')),
                'count' => $this->items()->count(),
                'avg_price' => $this->items()->avg('price'),
                'state' => array_intersect(['yes'], $this->items->pluck('state')->toArray()) ? 'yes' : (array_intersect(['soon'], $this->items->pluck('state')->toArray()) ? 'soon' : 'no'),
                'updated_at' => $this->items()->reorder()->orderByDesc('updated_at')->first()->updated_at,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
