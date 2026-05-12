<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationPerson extends Model
{
    public $timestamps = false;

    protected $fillable = ['quotation_id', 'name', 'age', 'sort_order'];

    public function premiums()
    {
        return $this->hasMany(QuotationPremium::class);
    }
}
