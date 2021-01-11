<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Product
 *
 * @property int $id
 * @property string $title
 * @property int $push
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProductItem[] $items
 * @property-read int|null $items_count
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePush($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
 */
	class Product extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ProductItem
 *
 * @property int $id
 * @property int $product_id
 * @property string $seller
 * @property string $seller_internal_id
 * @property string $url
 * @property string $title
 * @property int $price
 * @property string $state
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PushNotification[] $pushNotifications
 * @property-read int|null $push_notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|ProductItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductItem whereSeller($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductItem whereSellerInternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductItem whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductItem whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductItem whereUrl($value)
 */
	class ProductItem extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PushNotification
 *
 * @property int $id
 * @property int $product_item_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ProductItem $productItem
 * @method static \Illuminate\Database\Eloquent\Builder|PushNotification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PushNotification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PushNotification query()
 * @method static \Illuminate\Database\Eloquent\Builder|PushNotification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PushNotification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PushNotification whereProductItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PushNotification whereUpdatedAt($value)
 */
	class PushNotification extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

