<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Strategy extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'category', 'channel',
        'audience', 'difficulty', 'type', 'source', 'content', 'status',
        'product_line', 'angle_cold', 'angle_warm', 'angle_hot', 'key_facts',
    ];

    // Prospect types, coldest first. A strategy suits a type when it has an angle for it.
    public const TEMPERATURES = [
        'cold' => 'Cold',
        'warm' => 'Warm',
        'hot'  => 'Hot',
    ];

    public const TEMPERATURE_HINTS = [
        'cold' => "Doesn't know you yet, or hasn't replied: strangers, followers, old contacts.",
        'warm' => 'Has replied, asked something, or is a lead you are nurturing.',
        'hot'  => 'Asked for a quote, or is deciding now.',
    ];

    public const PRODUCT_LINES = [
        'medical'           => 'Medical Card',
        'critical_illness'  => 'Critical Illness',
        'hibah'             => 'Hibah',
        'personal_accident' => 'Personal Accident',
        'general'           => 'General',
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

    public function angles()
    {
        return $this->belongsToMany(ReachAngle::class, 'angle_strategy')->withPivot('linked_at');
    }

    public function focusPoints()
    {
        return $this->belongsToMany(FocusPoint::class, 'strategy_focus_point');
    }

    public function touchpoints()
    {
        return $this->hasMany(Touchpoint::class);
    }

    /** @return array<string, string> the prospect types this strategy has an angle for, e.g. ['cold' => '...'] */
    public function prospectAngles(): array
    {
        return collect(array_keys(self::TEMPERATURES))
            ->mapWithKeys(fn ($t) => [$t => trim((string) $this->{"angle_{$t}"})])
            ->filter()
            ->all();
    }

    /**
     * The copy-ready message inside an angle: whatever follows a line starting
     * "Mesej:" (outer quotes stripped). Falls back to the whole angle.
     */
    public function openerFor(string $temperature): ?string
    {
        $angle = trim((string) $this->{"angle_{$temperature}"});

        if ($angle === '') {
            return null;
        }

        if (preg_match('/^Mesej:\s*(.+)\z/ims', $angle, $m)) {
            return preg_replace('/^["“”]+|["“”]+$/u', '', trim($m[1]));
        }

        return $angle;
    }

    public function suits(string $temperature): bool
    {
        return trim((string) $this->{"angle_{$temperature}"}) !== '';
    }

    public function scopeForTemperature($query, string $temperature)
    {
        return $query->whereNotNull("angle_{$temperature}")->where("angle_{$temperature}", '!=', '');
    }

    public static function productLabel(?string $value): ?string
    {
        return $value ? (self::PRODUCT_LINES[$value] ?? ucfirst($value)) : null;
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
