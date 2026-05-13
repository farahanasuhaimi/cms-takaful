<?php

namespace App\Http\Controllers;

use App\Models\PlanProduct;
use App\Models\Quotation;
use App\Models\QuotationPerson;
use App\Models\QuotationPlan;
use App\Models\QuotationPremium;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    public function index()
    {
        $quotations = Quotation::where('user_id', auth()->id())
            ->withCount(['people', 'plans'])
            ->latest()
            ->get();

        return view('quotations.index', compact('quotations'));
    }

    public function create()
    {
        $planCatalog = $this->catalogForJs();
        return view('quotations.create', compact('planCatalog'));
    }

    public function store(Request $request)
    {
        $request->validate(['data' => 'required|string']);

        $data = json_decode($request->data, true);

        abort_if(empty(trim($data['title'] ?? '')), 422, 'Title is required.');

        $quotation = Quotation::create([
            'user_id' => auth()->id(),
            'title'   => trim($data['title']),
            'notes'   => trim($data['notes'] ?? '') ?: null,
        ]);

        $personIds = [];
        foreach (($data['people'] ?? []) as $i => $row) {
            if (empty(trim($row['name'] ?? ''))) continue;
            $person = QuotationPerson::create([
                'quotation_id' => $quotation->id,
                'name'         => trim($row['name']),
                'age'          => is_numeric($row['age'] ?? '') ? (int) $row['age'] : null,
                'sort_order'   => $i,
            ]);
            $personIds[$i] = $person->id;
        }

        foreach (($data['plans'] ?? []) as $j => $row) {
            if (empty(trim($row['plan_name'] ?? ''))) continue;
            $plan = QuotationPlan::create([
                'quotation_id'    => $quotation->id,
                'category'        => trim($row['category'] ?? '') ?: null,
                'plan_name'       => trim($row['plan_name']),
                'type'            => trim($row['type'] ?? '') ?: null,
                'coverage'        => trim($row['coverage'] ?? '') ?: null,
                'room_board'      => trim($row['room_board'] ?? '') ?: null,
                'umur_matang'     => trim($row['umur_matang'] ?? '') ?: null,
                'pampasan_matang' => trim($row['pampasan_matang'] ?? '') ?: null,
                'kenaikan'        => trim($row['kenaikan'] ?? '') ?: null,
                'plan_type'       => $row['plan_type'] ?? null,
                'privilege'       => trim($row['privilege'] ?? '') ?: null,
                'waiver'          => $row['waiver'] ?? null,
                'notes'           => trim($row['notes'] ?? '') ?: null,
                'sort_order'      => $j,
            ]);

            foreach (($row['premiums'] ?? []) as $i => $amount) {
                if (! isset($personIds[$i])) continue;
                QuotationPremium::create([
                    'quotation_plan_id'   => $plan->id,
                    'quotation_person_id' => $personIds[$i],
                    'amount'              => is_numeric($amount) ? $amount : null,
                ]);
            }
        }

        return redirect()->route('quotations.show', $quotation)
            ->with('success', 'Quotation created.');
    }

    public function show(Quotation $quotation)
    {
        abort_if($quotation->user_id !== auth()->id(), 403);

        $people = $quotation->people;
        $plans  = $quotation->plans->load('premiums');

        // Build a lookup: plan_id → person_id → amount
        $premiumMap = [];
        foreach ($plans as $plan) {
            foreach ($plan->premiums as $premium) {
                $premiumMap[$plan->id][$premium->quotation_person_id] = $premium->amount;
            }
        }

        // Group plans by category for the column header
        $grouped = $plans->groupBy(fn($p) => $p->category ?: '');

        return view('quotations.show', compact('quotation', 'people', 'plans', 'grouped', 'premiumMap'));
    }

    public function edit(Quotation $quotation)
    {
        abort_if($quotation->user_id !== auth()->id(), 403);

        $people = $quotation->people;
        $plans  = $quotation->plans->load('premiums');

        $personIndex = $people->pluck('id')->flip();

        $initial = [
            'title'  => $quotation->title,
            'notes'  => $quotation->notes ?? '',
            'people' => $people->map(fn($p) => ['name' => $p->name, 'age' => $p->age])->values()->toArray(),
            'plans'  => $plans->map(function ($plan) use ($people, $personIndex) {
                $premiums = array_fill(0, $people->count(), '');
                foreach ($plan->premiums as $premium) {
                    $idx = $personIndex[$premium->quotation_person_id] ?? null;
                    if ($idx !== null) {
                        $premiums[$idx] = $premium->amount;
                    }
                }
                return [
                    'category'        => $plan->category ?? '',
                    'plan_name'       => $plan->plan_name,
                    'type'            => $plan->type ?? '',
                    'coverage'        => $plan->coverage ?? '',
                    'room_board'      => $plan->room_board ?? '',
                    'umur_matang'     => $plan->umur_matang ?? '',
                    'pampasan_matang' => $plan->pampasan_matang ?? '',
                    'kenaikan'        => $plan->kenaikan ?? '',
                    'plan_type'       => $plan->plan_type ?? 'no_investment',
                    'privilege'       => $plan->privilege ?? '',
                    'waiver'          => $plan->waiver ?? 'yes',
                    'notes'           => $plan->notes ?? '',
                    'premiums'        => array_values($premiums),
                ];
            })->values()->toArray(),
        ];

        $planCatalog = $this->catalogForJs();
        return view('quotations.edit', compact('quotation', 'initial', 'planCatalog'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        abort_if($quotation->user_id !== auth()->id(), 403);
        $request->validate(['data' => 'required|string']);

        $data = json_decode($request->data, true);
        abort_if(empty(trim($data['title'] ?? '')), 422, 'Title is required.');

        $quotation->update([
            'title' => trim($data['title']),
            'notes' => trim($data['notes'] ?? '') ?: null,
        ]);

        // Wipe and rebuild — simpler than diffing
        $quotation->people()->delete();
        $quotation->plans()->delete();

        $personIds = [];
        foreach (($data['people'] ?? []) as $i => $row) {
            if (empty(trim($row['name'] ?? ''))) continue;
            $person = QuotationPerson::create([
                'quotation_id' => $quotation->id,
                'name'         => trim($row['name']),
                'age'          => is_numeric($row['age'] ?? '') ? (int) $row['age'] : null,
                'sort_order'   => $i,
            ]);
            $personIds[$i] = $person->id;
        }

        foreach (($data['plans'] ?? []) as $j => $row) {
            if (empty(trim($row['plan_name'] ?? ''))) continue;
            $plan = QuotationPlan::create([
                'quotation_id'    => $quotation->id,
                'category'        => trim($row['category'] ?? '') ?: null,
                'plan_name'       => trim($row['plan_name']),
                'type'            => trim($row['type'] ?? '') ?: null,
                'coverage'        => trim($row['coverage'] ?? '') ?: null,
                'room_board'      => trim($row['room_board'] ?? '') ?: null,
                'umur_matang'     => trim($row['umur_matang'] ?? '') ?: null,
                'pampasan_matang' => trim($row['pampasan_matang'] ?? '') ?: null,
                'kenaikan'        => trim($row['kenaikan'] ?? '') ?: null,
                'plan_type'       => $row['plan_type'] ?? null,
                'privilege'       => trim($row['privilege'] ?? '') ?: null,
                'waiver'          => $row['waiver'] ?? null,
                'notes'           => trim($row['notes'] ?? '') ?: null,
                'sort_order'      => $j,
            ]);

            foreach (($row['premiums'] ?? []) as $i => $amount) {
                if (! isset($personIds[$i])) continue;
                QuotationPremium::create([
                    'quotation_plan_id'   => $plan->id,
                    'quotation_person_id' => $personIds[$i],
                    'amount'              => is_numeric($amount) ? $amount : null,
                ]);
            }
        }

        return redirect()->route('quotations.show', $quotation)->with('success', 'Quotation updated.');
    }

    public function duplicate(Quotation $quotation)
    {
        abort_if($quotation->user_id !== auth()->id(), 403);

        $copy = Quotation::create([
            'user_id' => auth()->id(),
            'title'   => 'Copy of ' . $quotation->title,
            'notes'   => $quotation->notes,
        ]);

        $personMap = [];
        foreach ($quotation->people as $person) {
            $new = QuotationPerson::create([
                'quotation_id' => $copy->id,
                'name'         => $person->name,
                'age'          => $person->age,
                'sort_order'   => $person->sort_order,
            ]);
            $personMap[$person->id] = $new->id;
        }

        foreach ($quotation->plans->load('premiums') as $plan) {
            $newPlan = QuotationPlan::create([
                'quotation_id'    => $copy->id,
                'category'        => $plan->category,
                'plan_name'       => $plan->plan_name,
                'type'            => $plan->type,
                'coverage'        => $plan->coverage,
                'room_board'      => $plan->room_board,
                'umur_matang'     => $plan->umur_matang,
                'pampasan_matang' => $plan->pampasan_matang,
                'kenaikan'        => $plan->kenaikan,
                'plan_type'       => $plan->plan_type,
                'privilege'       => $plan->privilege,
                'waiver'          => $plan->waiver,
                'notes'           => $plan->notes,
                'sort_order'      => $plan->sort_order,
            ]);

            foreach ($plan->premiums as $premium) {
                if (! isset($personMap[$premium->quotation_person_id])) continue;
                QuotationPremium::create([
                    'quotation_plan_id'   => $newPlan->id,
                    'quotation_person_id' => $personMap[$premium->quotation_person_id],
                    'amount'              => $premium->amount,
                ]);
            }
        }

        return redirect()->route('quotations.edit', $copy)->with('success', 'Quotation duplicated. Edit and save.');
    }

    private function catalogForJs(): array
    {
        return PlanProduct::get()->map(fn($p) => [
            'id'         => $p->id,
            'name'       => $p->name,
            'category'   => ucfirst(str_replace('_', ' ', $p->plan_type)),
            'attributes' => $p->attributes ?? [],
        ])->values()->toArray();
    }

    public function destroy(Quotation $quotation)
    {
        abort_if($quotation->user_id !== auth()->id(), 403);
        $quotation->delete();

        return redirect()->route('quotations.index')->with('success', 'Quotation deleted.');
    }
}
