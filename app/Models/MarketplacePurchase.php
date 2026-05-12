<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplacePurchase extends Model
{
    public $timestamps = false;

    protected $fillable = ['buyer_user_id', 'listing_id', 'credits_paid', 'imported_content_id'];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_user_id');
    }

    public function listing()
    {
        return $this->belongsTo(MarketplaceListing::class);
    }
}
