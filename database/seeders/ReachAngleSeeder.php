<?php

namespace Database\Seeders;

use App\Models\ReachAngle;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReachAngleSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('is_admin', true)->first() ?? User::first();
        if (! $user) {
            $this->command->warn('No user found — skipping ReachAngleSeeder.');
            return;
        }

        $angles = [
            [
                'title'          => 'The "What If" Jab',
                'target_segment' => 'Parents with young children',
                'description'    => 'Open with a concrete "what if" — not abstract fear. "Kalau berlaku sesuatu hari ni, berapa bulan savings boleh tahan keluarga kau?" Let the number do the closing. This is the fastest way to move someone from comfortable to curious.',
            ],
            [
                'title'          => 'Hospital Bill Reality Check',
                'target_segment' => 'Working adults 30–45',
                'description'    => 'Show a real (anonymised) hospital bill in conversation — not a story, actual numbers. ICU semalam RM3,000. Ward 5 hari RM8,500. Biarkan bil tu buat kerja. Most people have no idea what serious illness actually costs.',
            ],
            [
                'title'          => 'EPF Withdrawal Trap',
                'target_segment' => 'Salaried employees relying on EPF',
                'description'    => 'Show how withdrawing EPF to pay medical bills destroys retirement savings. Takaful is the firewall between hospital bills and the retirement fund. Frame it as protecting their future, not selling a product.',
            ],
            [
                'title'          => "Ta'awun — Tolong-Menolong yang Smart",
                'target_segment' => 'Muslim professionals with halal-haram concerns',
                'description'    => "Takaful bukan conventional insurance — ia kontrak ta'awun (mutual aid). Sama konsep dengan zakat dan gotong-royong, tapi untuk perlindungan kewangan. Powerful angle for those with reservations about riba or conventional insurance.",
            ],
            [
                'title'          => 'The Breadwinner Calculator',
                'target_segment' => 'Sole income earners, single breadwinners',
                'description'    => 'Calculate together: monthly salary × 12 months × 10 years. That is the income that disappears if they cannot work. Do the math in front of them — numbers people calculate themselves hit harder than numbers you tell them.',
            ],
            [
                'title'          => 'Freelancer Blind Spot',
                'target_segment' => 'Freelancers, gig workers, self-employed',
                'description'    => 'Unlike employees, freelancers have no meaningful SOCSO, no employer medical benefits. One long hospital admission can wipe 6 months of savings. They need coverage the most and know it the least — high-receptivity, underserved segment.',
            ],
            [
                'title'          => 'The C-Word Conversation',
                'target_segment' => 'Women 35–55',
                'description'    => 'Breast cancer is #1 in Malaysian women. Approach with empathy, not tactics — talk about early detection, treatment timelines, real recovery costs. Critical illness coverage is responsible planning, not worst-case thinking. Never lead with fear; lead with care.',
            ],
            [
                'title'          => 'Keyman Takaful untuk Boss Kecil',
                'target_segment' => 'SME owners, micro-business operators',
                'description'    => 'Can the business survive if the boss is hospitalised for 3 months? Keyman takaful protects business continuity, not just the individual. Frame it as a business expense — the cost of keeping the engine running when the driver is down.',
            ],
            [
                'title'          => 'The First Paycheck Moment',
                'target_segment' => 'Fresh graduates, early career 22–27',
                'description'    => 'First job, lowest premium, best health — the optimal window to enter takaful. Wait one more year = higher premium, or a pre-existing condition locks the door. Do not wait until you need it; enter when you can. This angle closes fast with the right framing.',
            ],
            [
                'title'          => 'Warm Referral dari Klien Sedia Ada',
                'target_segment' => "Existing clients' inner circle",
                'description'    => 'Ask existing clients to name 3 people they care about who are unprotected. "Siapa dalam circle kau yang kau nak pastikan terlindung kalau berlaku sesuatu?" A question of care, not a sales pitch. Highest-conversion prospecting method in takaful.',
            ],
        ];

        foreach ($angles as $data) {
            ReachAngle::withoutGlobalScopes()->firstOrCreate(
                ['user_id' => $user->id, 'title' => $data['title']],
                ['target_segment' => $data['target_segment'], 'description' => $data['description'], 'status' => 'active']
            );
        }

        $this->command->info('ReachAngleSeeder: 10 angles seeded for user ' . $user->email);
    }
}
