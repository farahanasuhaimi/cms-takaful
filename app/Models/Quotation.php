<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $fillable = ['user_id', 'lead_id', 'client_id', 'title', 'notes', 'prospect_name', 'prospect_phone', 'prospect_notes'];

    public function lead()
    {
        return $this->belongsTo(Lead::class)->withoutGlobalScopes();
    }

    public function client()
    {
        return $this->belongsTo(Client::class)->withoutGlobalScopes();
    }

    public function people()
    {
        return $this->hasMany(QuotationPerson::class)->orderBy('sort_order');
    }

    public function plans()
    {
        return $this->hasMany(QuotationPlan::class)->orderBy('sort_order');
    }
}
