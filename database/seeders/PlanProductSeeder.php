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
                    'Type'        => 'Medical Card (Hospital & Surgical)',
                    'Plan'        => 'Plan 180 | Tiada MediBoost',
                    'Room & Board'=> 'RM180/malam | Gold: +50% → RM270 | Platinum: +100% → RM360',
                    'Coverage'    => 'RM180,000/tahun | Tiada had seumur hidup',
                    'Kenaikan'    => 'Annual renewal',
                    'Privilege'   => 'ICU as charged | Emergency Evacuation USD1M | Health screening dwi-tahunan (Gold RM500 / Platinum RM600) | Deductible: RM500 / 20% co-takaful / RM20K',
                    'Waiver'      => 'no',
                ],
                'attribute_options' => [
                    'Plan'        => ['Plan 180', 'Plan 180 (Tiada MediBoost)'],
                    'Room & Board'=> ['RM180/malam', 'RM270/malam (Gold)', 'RM360/malam (Platinum)'],
                    'Coverage'    => ['RM180,000/tahun'],
                    'Kenaikan'    => ['Annual renewal'],
                ],
                'notes' => 'Kad perubatan standalone. Tiada had seumur hidup. Plan 180 — tiada MediBoost. Sesuai sebagai permulaan atau top-up kepada employer benefit (guna RM20K deductible).',
            ],
            [
                'plan_type' => 'medical',
                'name'      => 'A-Life MediFlex-i 250',
                'attributes' => [
                    'Type'        => 'Medical Card (Hospital & Surgical)',
                    'Plan'        => 'Plan 250 | MediBoost opsyen: RM1,000,000/tahun',
                    'Room & Board'=> 'RM250/malam | Gold: +50% → RM375 | Platinum: +100% → RM500',
                    'Coverage'    => 'RM250,000/tahun | MediBoost → RM1,000,000 | Tiada had seumur hidup',
                    'Kenaikan'    => 'Annual renewal',
                    'Privilege'   => 'ICU as charged | PMCM (MediBoost) | Emergency Evacuation USD1M | MediRecover-i opsyen (lump sum ≥15 hari ward / ≥5 hari ICU) | Deductible: RM500 / 20% / RM20K',
                    'Waiver'      => 'no',
                ],
                'attribute_options' => [
                    'Plan'        => ['Plan 250', 'Plan 250 + MediBoost (RM1M)'],
                    'Room & Board'=> ['RM250/malam', 'RM375/malam (Gold)', 'RM500/malam (Platinum)'],
                    'Coverage'    => ['RM250,000/tahun', 'RM1,000,000/tahun (MediBoost)'],
                    'Kenaikan'    => ['Annual renewal'],
                ],
                'notes' => 'Plan paling popular. Dengan MediBoost limit jadi RM1M. Vitality Platinum naikkan R&B ke RM500/malam tanpa tambah kos. ~RM133/bulan (30 thn, bukan perokok, dengan MediBoost).',
            ],
            [
                'plan_type' => 'medical',
                'name'      => 'A-Life MediFlex-i 350',
                'attributes' => [
                    'Type'        => 'Medical Card (Hospital & Surgical)',
                    'Plan'        => 'Plan 350 | MediBoost opsyen: RM1,500,000/tahun',
                    'Room & Board'=> 'RM350/malam | Gold: +50% → RM525 | Platinum: +100% → RM700',
                    'Coverage'    => 'RM350,000/tahun | MediBoost → RM1,500,000 | Tiada had seumur hidup',
                    'Kenaikan'    => 'Annual renewal',
                    'Privilege'   => 'ICU as charged | PMCM (MediBoost) | Emergency Evacuation USD1M | MediRecover-i opsyen | Deductible: RM500 / 20% / RM20K',
                    'Waiver'      => 'no',
                ],
                'attribute_options' => [
                    'Plan'        => ['Plan 350', 'Plan 350 + MediBoost (RM1.5M)'],
                    'Room & Board'=> ['RM350/malam', 'RM525/malam (Gold)', 'RM700/malam (Platinum)'],
                    'Coverage'    => ['RM350,000/tahun', 'RM1,500,000/tahun (MediBoost)'],
                    'Kenaikan'    => ['Annual renewal'],
                ],
                'notes' => 'Pelan premium. MediBoost limit RM1.5M. Sesuai untuk klien yang nak perlindungan perubatan tertinggi.',
            ],
            // ── Hibah / Legacy ────────────────────────────────────────────────────
            [
                'plan_type' => 'hibah',
                'name'      => 'A-Life Legasi Beyond',
                'attributes' => [
                    'Type'            => 'ILP Hibah (Investment-Linked)',
                    'Plan'            => 'Bayar 6 / 10 / 20 tahun / Full Pay | Matang: umur 70 atau 80',
                    'Coverage'        => 'Wafat/TPD: 100% SC atau nilai akaun | Kemalangan: 2× SC | Bencana alam: 6× SC',
                    'Kenaikan'        => 'Tiada (nilai akaun tumbuh melalui pelaburan)',
                    'Privilege'       => 'Legasi Rewards (thn 10/20/30/40) | Legasi Booster (matang) | Vitality Booster sehingga 20% SC | Estate Mgmt RM15K–RM50K | Hajj/Umrah: 2× SC',
                    'Umur Matang'     => 'Umur 70 atau 80 (auto-lanjut ke umur 100)',
                    'Pampasan Matang' => 'Nilai akaun + Legasi Booster',
                    'Waiver'          => 'yes (A-Plus Waiver-i: 44 CI)',
                ],
                'attribute_options' => [
                    'Plan'            => ['Bayar 6 tahun', 'Bayar 10 tahun', 'Bayar 20 tahun', 'Full Pay'],
                    'Umur Matang'     => ['Umur 70', 'Umur 80'],
                    'Kenaikan'        => ['Tiada'],
                ],
                'notes' => 'ILP dengan hibah — pelaburan + perlindungan + legasi. Legasi Rewards dibayar setiap 10 tahun (syarat: tiada pengeluaran + caruman lancar). Coverage boleh sampai 6× SC (bencana alam). Untuk klien yang nak tinggalkan warisan sambil pelaburan tumbuh.',
            ],
            [
                'plan_type' => 'hibah',
                'name'      => 'A-Life Idaman',
                'attributes' => [
                    'Type'            => 'Family Takaful ILP (Hibah + Simpanan)',
                    'Coverage'        => 'Wafat: 100% SC sebagai Hibah | Wafat Kemalangan: 2× SC | TPD: 100% SC',
                    'Kenaikan'        => 'Tiada (nilai akaun tumbuh melalui pelaburan)',
                    'Privilege'       => 'Badal Haji / Funeral: RM5,000 | Accidental Death 2× | BabyCare Xtra-i (dari kandungan 13 minggu) | ParentCare + ParentWaiver opsyen',
                    'Umur Matang'     => 'Umur 70 atau 80 (auto-lanjut ke umur 100)',
                    'Pampasan Matang' => '100% nilai akaun (termasuk A-Plus Saver-i)',
                    'Waiver'          => 'yes (A-Plus WaiverExtra-i: 44 CI atau TPD)',
                ],
                'attribute_options' => [
                    'Umur Matang' => ['Umur 70', 'Umur 80'],
                    'Kenaikan'    => ['Tiada'],
                ],
                'notes' => 'Gabungan perlindungan nyawa + simpanan dengan mekanisme hibah. Perlindungan bermula dari kandungan 13 minggu. Sesuai untuk keluarga muda atau yang merancang anak. ~RM323/bulan (30 thn, RM150K, dengan WaiverExtra-i + Saver-i RM200/bln).',
            ],
            [
                'plan_type' => 'hibah',
                'name'      => 'A-Life Sejuta Makna',
                'attributes' => [
                    'Type'            => 'Hibah Term (Family Takaful — Term Life)',
                    'Coverage'        => 'Min RM350,000 | Wafat/TPD: 100% SC atau nilai akaun (mana lebih tinggi)',
                    'Plan'            => '10 tahun / 20 tahun / sehingga umur 60',
                    'Kenaikan'        => 'Tiada',
                    'Privilege'       => 'Wafat semasa Haji/Umrah: 2× SC | Estate Mgmt: RM15K–RM75K (ikut SC) | VYCB tahunan (Gold/Platinum) | Conversion Privilege (tanpa underwriting semula)',
                    'Umur Matang'     => 'Umur 60 atau tamat tempoh 10/20 tahun',
                    'Pampasan Matang' => 'Nilai akaun dikembalikan',
                    'Waiver'          => 'no',
                ],
                'attribute_options' => [
                    'Plan'        => ['10 tahun', '20 tahun', 'Sehingga umur 60'],
                    'Umur Matang' => ['Umur 60', 'Tamat tempoh (10 tahun)', 'Tamat tempoh (20 tahun)'],
                    'Kenaikan'    => ['Tiada'],
                ],
                'notes' => 'Perlindungan nyawa tinggi bermula RM350K. Sesuai untuk breadwinner yang nak coverage maksimum dengan caruman berpatutan. Tiada komponen pelaburan — murni perlindungan + hibah. ~RM258/bulan untuk RM1M (35 thn, bukan perokok, 25 tahun).',
            ],
            // ── Critical Illness ──────────────────────────────────────────────────
            [
                'plan_type' => 'critical_illness',
                'name'      => 'A-Life Kritikal Protector',
                'attributes' => [
                    'Type'            => 'CI Standalone (Penyakit Kritikal)',
                    'Plan'            => '45 penyakit kritikal | Bayar 20 tahun atau Full Pay',
                    'Coverage'        => 'Diagnosis CI: 100% SC + nilai akaun | Kematian: 10% SC (Compassionate) | Matang: nilai akaun',
                    'Kenaikan'        => 'Tiada',
                    'Privilege'       => 'Caregiver Benefit RM3,000 (SC ≥RM100K) | Vitality Booster sehingga 20% SC | Recover-i opsyen (≥15 hari ward / ≥5 hari ICU) | CI Booster-i opsyen → 75 CI + mental illness',
                    'Umur Matang'     => 'Umur 60 atau 70',
                    'Pampasan Matang' => 'Nilai akaun dalam PAF',
                    'Waiver'          => 'no',
                ],
                'attribute_options' => [
                    'Plan'        => ['45 CI — Bayar 20 tahun', '45 CI — Full Pay', '75 CI + CI Booster-i — Bayar 20 tahun', '75 CI + CI Booster-i — Full Pay'],
                    'Umur Matang' => ['Umur 60', 'Umur 70'],
                    'Kenaikan'    => ['Tiada'],
                ],
                'notes' => 'CI standalone — lump sum bebas guna: rawatan, gaji hilang, penjaga. 45 penyakit termasuk kanser, strok, serangan jantung. Boleh expand ke 75 CI dengan CI Booster-i. Rule of thumb: SC = 3–5× pendapatan tahunan. ~RM798/tahun (25 thn, RM100K, Full Pay).',
            ],
            [
                'plan_type' => 'critical_illness',
                'name'      => 'A-Life Kritikal Flex',
                'attributes' => [
                    'Type'            => 'CI Multi-Peringkat (Penyakit Kritikal)',
                    'Plan'            => '75 CI (base) | +Kritikal Early: 180 CI (awal + pertengahan + lanjut)',
                    'Coverage'        => 'Diagnosis CI: 100% SC + nilai akaun | Kematian: 10% SC | Matang: nilai akaun',
                    'Kenaikan'        => 'Tiada | Pilihan: Yearly Increasing atau Level Contribution',
                    'Privilege'       => 'VYCB 15% (Platinum) / 7.5% (Gold) | Kritikal Early: bayar awal peringkat awal | Recover-i + PMCM opsyen | Caregiver RM3,000 | Infaq rider opsyen | Auto-extend ke umur 80',
                    'Umur Matang'     => 'Umur 60, 70, atau 80 (auto-lanjut ke 80 jika nilai akaun cukup)',
                    'Pampasan Matang' => 'Nilai akaun dalam PAF',
                    'Waiver'          => 'no',
                ],
                'attribute_options' => [
                    'Plan'        => ['75 CI (Base)', '75 CI + Kritikal Early (180 CI)'],
                    'Umur Matang' => ['Umur 60', 'Umur 70', 'Umur 80'],
                    'Kenaikan'    => ['Level Contribution', 'Yearly Increasing'],
                ],
                'notes' => 'CI paling komprehensif. Dengan Kritikal Early: 180 penyakit dari peringkat awal. VYCB antara tertinggi dalam lineup (15% Platinum). Untuk klien yang nak perlindungan CI dari deteksi awal hingga peringkat lanjut. ~RM4,405/tahun (30 thn, RM200K, dengan Kritikal Early + Recover-i).',
            ],
            // ── Personal Accident ─────────────────────────────────────────────────
            [
                'plan_type' => 'personal_accident',
                'name'      => 'A-Life Pelindung',
                'attributes' => [
                    'Type'      => 'Personal Accident (Takaful Kemalangan)',
                    'Plan'      => 'Plan 1: RM100K–RM200K | Plan 2: RM300K–RM600K | Tempoh: 20 tahun atau umur 85',
                    'Coverage'  => 'Kemalangan/TPD: RM100K (P1) / RM300K (P2) → naik 20% setiap 2 tahun hingga 2× | Koma: RM100K / RM300K',
                    'Kenaikan'  => 'Tiada tambahan caruman — perlindungan naik automatik setiap 2 tahun',
                    'Privilege' => 'GIO (tanpa soalan kesihatan) | Haji/Umrah & Cuti Umum Bonus: RM20K (P1) / RM60K (P2) | ICU: RM500/hari max 120 hari | Fisioterapi: RM1,000 | VYCB: Platinum 10% / Gold 5% caruman tahunan',
                    'Waiver'    => 'no',
                ],
                'attribute_options' => [
                    'Plan'     => ['Plan 1 (RM100K–RM200K)', 'Plan 2 (RM300K–RM600K)'],
                    'Coverage' => ['RM100,000 → RM200,000 (Plan 1)', 'RM300,000 → RM600,000 (Plan 2)'],
                    'Kenaikan' => ['Naik automatik setiap 2 tahun (tiada tambahan caruman)'],
                ],
                'notes' => 'PA tanpa soalan kesihatan. Dari RM30/bulan (umur 10–40, Plan 1). Perlindungan naik setiap 2 tahun tanpa tambah caruman. Sesuai sebagai pelengkap kepada pelan hayat + perubatan.',
            ],
        ];

        foreach ($products as $data) {
            $attrs   = $data['attributes'];
            $options = $data['attribute_options'] ?? null;
            unset($data['attributes'], $data['attribute_options']);

            PlanProduct::withoutGlobalScopes()->updateOrCreate(
                ['user_id' => $user->id, 'name' => $data['name']],
                array_merge($data, [
                    'user_id'           => $user->id,
                    'attributes'        => $attrs,
                    'attribute_options' => $options,
                    'notes'             => $data['notes'] ?? null,
                ])
            );
        }

        $this->command->info("Seeded {$user->name}'s plan catalog with " . count($products) . ' Alife products.');
    }
}
