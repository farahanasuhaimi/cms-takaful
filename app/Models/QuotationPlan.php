<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationPlan extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'quotation_id', 'category', 'plan_name', 'type', 'coverage', 'room_board',
        'umur_matang', 'pampasan_matang', 'kenaikan', 'plan_type',
        'privilege', 'waiver', 'notes', 'sort_order',
    ];

    public function premiums()
    {
        return $this->hasMany(QuotationPremium::class);
    }
}
