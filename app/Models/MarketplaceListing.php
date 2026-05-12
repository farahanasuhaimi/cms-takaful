<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceListing extends Model
{
    protected $fillable = ['seller_user_id', 'angle_content_id', 'strategy_id', 'title', 'description', 'price_credits', 'status'];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_user_id');
    }

    public function angleContent()
    {
        return $this->belongsTo(AngleContent::class)->withoutGlobalScopes();
    }

    public function strategy()
    {
        return $this->belongsTo(Strategy::class);
    }

    public function purchases()
    {
        return $this->hasMany(MarketplacePurchase::class, 'listing_id');
    }
}
