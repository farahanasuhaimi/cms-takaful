<?php

namespace App\Http\Controllers;

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
        return view('quotations.create');
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

    public function destroy(Quotation $quotation)
    {
        abort_if($quotation->user_id !== auth()->id(), 403);
        $quotation->delete();

        return redirect()->route('quotations.index')->with('success', 'Quotation deleted.');
    }
}
