<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Strategy extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'category', 'channel',
        'audience', 'difficulty', 'type', 'source', 'content', 'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function steps()
    {
        return $this->hasMany(StrategyStep::class)->orderBy('step_order');
    }

    public function listing()
    {
        return $this->hasOne(MarketplaceListing::class)->where('status', 'active');
    }

    public function isOwnedBy(int $userId): bool
    {
        return $this->user_id === $userId;
    }

    public static function categoryLabel(string $value): string
    {
        return match($value) {
            'prospecting'       => 'Prospecting',
            'content'           => 'Content',
            'objection_handling'=> 'Objection Handling',
            'follow_up'         => 'Follow Up',
            'referral'          => 'Referral',
            'closing'           => 'Closing',
            default             => ucfirst($value),
        };
    }

    public static function channelLabel(string $value): string
    {
        return match($value) {
            'whatsapp'    => 'WhatsApp',
            'instagram'   => 'Instagram',
            'facebook'    => 'Facebook',
            'face_to_face'=> 'Face to Face',
            'general'     => 'General',
            default       => ucfirst($value),
        };
    }

    public static function audienceLabel(string $value): string
    {
        return match($value) {
            'strangers'     => 'Strangers',
            'warm_leads'    => 'Warm Leads',
            'family_friends'=> 'Family & Friends',
            'corporate'     => 'Corporate',
            'general'       => 'General',
            default         => ucfirst($value),
        };
    }
}
