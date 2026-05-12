<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationPremium extends Model
{
    public $timestamps = false;

    protected $fillable = ['quotation_plan_id', 'quotation_person_id', 'amount'];
}
