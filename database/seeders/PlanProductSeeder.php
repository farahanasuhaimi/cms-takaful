<?php

namespace Database\Seeders;

use App\Models\PlanProduct;
use App\Models\User;
use Illuminate\Database\Seeder;

class PlanProductSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('is_admin', true)->first() ?? User::first();
        if (! $user) {
            $this->command->warn('No user found — skipping PlanProductSeeder.');
            return;
        }

        $products = [
            // ── Medical ───────────────────────────────────────────────────────────
            [
                'plan_type' => 'medical',
                'name'      => 'A-Life MediFlex-i 180',
                'attributes' => [
                    'Type'        => 'Stand-alone',
                    'Room & Board'=> 'RM180/malam',
                    'Coverage'    => 'RM180,000/tahun',
                    'Kenaikan'    => 'Setiap tahun',
                    'Privilege'   => 'No lifetime limit, ICU as charged, Emergency Evacuation USD1M',
                    'Waiver'      => 'no',
                ],
            ],
            [
                'plan_type' => 'medical',
                'name'      => 'A-Life MediFlex-i 250',
                'attributes' => [
                    'Type'        => 'Stand-alone',
                    'Room & Board'=> 'RM250/malam',
                    'Coverage'    => 'RM250,000/tahun (MediBoost: RM1,000,000)',
                    'Kenaikan'    => 'Setiap tahun',
                    'Privilege'   => 'No lifetime limit, PMCM (MediBoost), Emergency Evacuation USD1M',
                    'Waiver'      => 'no',
                ],
            ],
            [
                'plan_type' => 'medical',
                'name'      => 'A-Life MediFlex-i 350',
                'attributes' => [
                    'Type'        => 'Stand-alone',
                    'Room & Board'=> 'RM350/malam',
                    'Coverage'    => 'RM350,000/tahun (MediBoost: RM1,500,000)',
                    'Kenaikan'    => 'Setiap tahun',
                    'Privilege'   => 'No lifetime limit, PMCM (MediBoost), Emergency Evacuation USD1M',
                    'Waiver'      => 'no',
                ],
            ],
            // ── Hibah / Legacy ────────────────────────────────────────────────────
            [
                'plan_type' => 'hibah',
                'name'      => 'A-Life Legasi Beyond',
                'attributes' => [
                    'Type'            => 'ILP Hibah',
                    'Umur Matang'     => '70 atau 80',
                    'Pampasan Matang' => 'Nilai akaun',
                    'Kenaikan'        => 'Tiada',
                    'Privilege'       => 'Legasi Rewards, Vitality Booster, 6x (bencana alam), Hajj/Umrah 2x',
                    'Waiver'          => 'yes',
                ],
            ],
            [
                'plan_type' => 'hibah',
                'name'      => 'A-Life Idaman',
                'attributes' => [
                    'Type'            => 'Hibah + Simpanan',
                    'Umur Matang'     => '70 atau 80',
                    'Pampasan Matang' => '100% nilai akaun',
                    'Kenaikan'        => 'Tiada',
                    'Privilege'       => 'Badal Haji RM5,000, Accidental 2x, BabyCare Xtra-i',
                    'Waiver'          => 'yes',
                ],
            ],
            [
                'plan_type' => 'hibah',
                'name'      => 'A-Life Sejuta Makna',
                'attributes' => [
                    'Type'            => 'Hibah Term',
                    'Coverage'        => 'Dari RM350,000',
                    'Umur Matang'     => '60, atau tempoh 10/20 tahun',
                    'Pampasan Matang' => 'Nilai akaun',
                    'Kenaikan'        => 'Tiada',
                    'Privilege'       => 'Hajj/Umrah 2x, Estate Management hingga RM75K, VYCB, Conversion Privilege',
                    'Waiver'          => 'no',
                ],
            ],
            // ── Critical Illness ──────────────────────────────────────────────────
            [
                'plan_type' => 'critical_illness',
                'name'      => 'A-Life Kritikal Protector',
                'attributes' => [
                    'Type'        => 'CI Standalone (45 CI)',
                    'Umur Matang' => '60 atau 70',
                    'Kenaikan'    => 'Tiada',
                    'Privilege'   => 'Caregiver Benefit RM3,000, Vitality Booster 20%, Recover-i (add-on)',
                    'Waiver'      => 'no',
                ],
            ],
            [
                'plan_type' => 'critical_illness',
                'name'      => 'A-Life Kritikal Flex',
                'attributes' => [
                    'Type'        => 'CI Multi-Peringkat (75 CI)',
                    'Umur Matang' => '60, 70, atau 80',
                    'Kenaikan'    => 'Tiada',
                    'Privilege'   => '+ Kritikal Early: 180 CI, VYCB 15%, PMCM, auto-extend ke 80',
                    'Waiver'      => 'no',
                ],
            ],
            // ── Personal Accident ─────────────────────────────────────────────────
            [
                'plan_type' => 'personal_accident',
                'name'      => 'A-Life Pelindung',
                'attributes' => [
                    'Type'      => 'Personal Accident',
                    'Coverage'  => 'RM100,000 → RM200,000 (naik setiap 2 tahun)',
                    'Kenaikan'  => 'Tiada (perlindungan naik automatik)',
                    'Privilege' => 'GIO, Haji/Umrah Bonus RM20K, ICU RM500/hari, Coma Benefit, Physio RM1K',
                    'Waiver'    => 'no',
                ],
            ],
        ];

        foreach ($products as $data) {
            $attrs = $data['attributes'];
            unset($data['attributes']);

            PlanProduct::withoutGlobalScopes()->firstOrCreate(
                ['user_id' => $user->id, 'name' => $data['name']],
                array_merge($data, [
                    'user_id'    => $user->id,
                    'attributes' => $attrs,
                    'notes'      => null,
                ])
            );
        }

        $this->command->info("Seeded {$user->name}'s plan catalog with " . count($products) . ' Alife products.');
    }
}
