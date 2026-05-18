<?php

namespace Database\Seeders;

use App\Models\FocusPoint;
use Illuminate\Database\Seeder;

class FocusPointSeeder extends Seeder
{
    public function run(): void
    {
        $points = [
            // --- Financial ---
            [
                'group'       => 'financial',
                'title'       => 'Simpan untuk Masa Depan',
                'description' => 'Frame takaful as a savings vehicle — not just protection. Saving systematically while building a safety net at the same time.',
            ],
            [
                'group'       => 'financial',
                'title'       => 'Persediaan untuk Masa Depan',
                'description' => 'Broad future-readiness: retirement, children\'s education, unexpected costs. "The best time to prepare was yesterday. The second best is today."',
            ],
            [
                'group'       => 'financial',
                'title'       => 'Inflasi Bil Perubatan',
                'description' => 'Medical costs rising 12–15% yearly in Malaysia. Cash savings alone cannot keep up. Takaful locks in coverage before the next price hike.',
            ],
            [
                'group'       => 'financial',
                'title'       => 'Jangan Sentuh KWSP',
                'description' => 'Using EPF to pay medical bills destroys retirement savings. Takaful is the firewall — protect the KWSP, let it grow undisturbed.',
            ],
            [
                'group'       => 'financial',
                'title'       => 'Lindungi Pendapatan',
                'description' => 'Income stops when you cannot work. Medical card + income replacement = financial continuity during recovery.',
            ],
            [
                'group'       => 'financial',
                'title'       => 'Bayar Hutang jika Tiada',
                'description' => 'Outstanding loans (housing, car, personal) do not disappear when the breadwinner is gone. Takaful ensures debts are settled, not passed on.',
            ],

            // --- Protection ---
            [
                'group'       => 'protection',
                'title'       => 'Perlindungan Perubatan Komprehensif',
                'description' => 'Full hospitalisation and surgical coverage — ward, ICU, specialist, surgery. One serious admission can cost RM20,000+.',
            ],
            [
                'group'       => 'protection',
                'title'       => 'Penyakit Kritikal',
                'description' => 'Lump sum payment on diagnosis of critical illness (cancer, heart attack, stroke). Covers treatment costs AND lost income during recovery.',
            ],
            [
                'group'       => 'protection',
                'title'       => 'Hilang Upaya',
                'description' => 'Disability is more common than death in working years. Covers total and permanent disability — monthly benefit to replace lost income.',
            ],

            // --- Family ---
            [
                'group'       => 'family',
                'title'       => 'Tanggungjawab kepada Keluarga',
                'description' => 'Children, spouse, parents depending on you. Protection is not for you — it is for them. Reframe it as an act of care, not a financial product.',
            ],
            [
                'group'       => 'family',
                'title'       => 'Warisan & Hibah',
                'description' => 'Takaful nomination + hibah ensures assets go directly to intended beneficiaries — faster, simpler, and outside of faraid complications.',
            ],
            [
                'group'       => 'family',
                'title'       => 'Beri Keluarga Masa untuk Bernafas',
                'description' => 'When the breadwinner is gone, family needs time to grieve — not time to scramble for money. A payout buys that breathing room.',
            ],

            // --- Life Stage ---
            [
                'group'       => 'life_stage',
                'title'       => 'Pasangan Baru',
                'description' => 'First major financial decision together. Build the protection layer early — premiums are lowest, health is best. Establish a shared financial foundation.',
            ],
            [
                'group'       => 'life_stage',
                'title'       => 'Ibu Bapa Baru',
                'description' => 'A new child changes the risk equation completely. One income often drops. Medical needs increase. Coverage needs to match the new reality.',
            ],
            [
                'group'       => 'life_stage',
                'title'       => 'Graduan Baru',
                'description' => 'First job, best health, lowest premium — the optimal entry window. Every year of delay increases cost and risk of pre-existing conditions locking the door.',
            ],
            [
                'group'       => 'life_stage',
                'title'       => 'Hampir Bersara',
                'description' => 'Health risks increase. Medical bills can wipe retirement savings fast. Takaful protects what took decades to build.',
            ],

            // --- Emotional ---
            [
                'group'       => 'emotional',
                'title'       => 'Ketenangan Fikiran',
                'description' => 'Know that whatever happens, the family is covered. Not about expecting the worst — about not carrying the weight of the unknown every day.',
            ],
            [
                'group'       => 'emotional',
                'title'       => 'Jangan Tunggu Terlambat',
                'description' => 'Pre-existing conditions lock people out. Most regret comes after a diagnosis, not before. Enter when you can, not when you need to.',
            ],
            [
                'group'       => 'emotional',
                'title'       => 'Bukan tentang Mati — tentang Hidup',
                'description' => 'Takaful is most needed while alive — long illness, disability, recovery. Reframe away from death-focus toward living-well-through-adversity.',
            ],

            // --- Islamic ---
            [
                'group'       => 'islamic',
                'title'       => "Ta'awun — Tolong-Menolong",
                'description' => "Takaful is mutual aid (ta'awun) — participants help each other, not a profit arrangement. Same spirit as zakat and gotong-royong but applied to financial protection.",
            ],
            [
                'group'       => 'islamic',
                'title'       => 'Bebas Riba',
                'description' => 'Unlike conventional insurance, takaful operates on wakalah or mudharabah contracts — shariah-compliant and free from riba. A responsible choice for Muslims.',
            ],
            [
                'group'       => 'islamic',
                'title'       => 'Lebihan Dikembalikan',
                'description' => 'Surplus from the takaful fund is returned to participants or donated to charity — not kept by the company. Your contribution works for you and others.',
            ],
        ];

        foreach ($points as $data) {
            FocusPoint::firstOrCreate(
                ['title' => $data['title']],
                [
                    'description' => $data['description'],
                    'group'       => $data['group'],
                    'status'      => 'active',
                ]
            );
        }

        $this->command->info('FocusPointSeeder: ' . count($points) . ' focus points seeded.');
    }
}
