<x-app-layout>
    <x-slot name="title">{{ $client->name }} · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">{{ $client->name }}</x-slot>
    <x-slot name="actions">
        <a href="{{ route('clients.edit', $client) }}"
           class="text-sm text-matcha-600 border border-matcha-300 hover:bg-matcha-50 px-4 py-2 rounded-lg transition">
            Edit
        </a>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6" x-data="{ logOpen: false, prefillTopic: '' }">

        {{-- Left column (60%) --}}
        <div class="lg:col-span-3 space-y-5">

            {{-- Client header card --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">{{ $client->name }}</h2>
                        @if ($client->phone)
                            <a href="https://wa.me/{{ $client->phone }}" target="_blank"
                               class="text-sm text-green-600 hover:underline mt-0.5 block">
                                {{ $client->phone }}
                            </a>
                        @endif
                    </div>
                    <div class="text-xs text-gray-400 text-right">
                        @if ($client->ic_no) <p>IC: {{ $client->ic_no }}</p> @endif
                        @if ($client->email) <p>{{ $client->email }}</p> @endif
                    </div>
                </div>
                @if ($client->notes)
                    <p class="mt-3 text-sm text-gray-500 border-t border-gray-100 pt-3">{{ $client->notes }}</p>
                @endif
            </div>

            {{-- Policies --}}
            @php
                $planProductsJson = $planProducts->map(fn($p) => [
                    'id'        => $p->id,
                    'name'      => $p->name,
                    'plan_type' => $p->plan_type,
                    'attributes'=> $p->attributes ?? [],
                ])->values()->toJson();
            @endphp

            <div class="bg-white rounded-xl border border-gray-200 p-6"
                 x-data="{
                     addPolicy: false,
                     products: {{ $planProductsJson }},
                     selectedId: '',
                     selectedProduct: null,
                     selectProduct(id) {
                         this.selectedId = id;
                         this.selectedProduct = id ? this.products.find(p => p.id == id) : null;
                     }
                 }">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-700">Policies</h3>
                    <button @click="addPolicy = !addPolicy"
                            class="text-xs bg-matcha-600 hover:bg-matcha-800 text-white px-3 py-1.5 rounded-lg transition">
                        + Add Policy
                    </button>
                </div>

                {{-- Add policy form --}}
                <div x-show="addPolicy" x-transition class="mb-4 p-4 bg-matcha-50 rounded-lg border border-matcha-100">
                    <p class="text-xs font-semibold text-matcha-700 mb-3 uppercase tracking-wide">New Policy</p>
                    <form method="POST" action="{{ route('clients.policies.store', $client) }}">
                        @csrf

                        {{-- Plan Catalog selector --}}
                        @if ($planProducts->count())
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Select from Plan Catalog</label>
                            <select name="plan_product_id"
                                    @change="selectProduct($event.target.value)"
                                    class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400">
                                <option value="">— Select a plan (optional) —</option>
                                @foreach ($planProducts->groupBy('plan_type') as $type => $group)
                                    <optgroup label="{{ ucfirst(str_replace('_', ' ', $type)) }}">
                                        @foreach ($group as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>

                            {{-- Show selected product attributes --}}
                            <div x-show="selectedProduct" x-transition
                                 class="mt-2 p-2.5 bg-white rounded-lg border border-matcha-200">
                                <p class="text-xs font-medium text-matcha-700 mb-1" x-text="selectedProduct?.name"></p>
                                <ul class="space-y-0.5">
                                    <template x-for="(value, key) in (selectedProduct?.attributes ?? {})" :key="key">
                                        <li class="text-xs text-gray-500">
                                            <span class="text-gray-400" x-text="key"></span>
                                            <span class="mx-1 text-gray-300">·</span>
                                            <span class="text-gray-700 font-medium" x-text="value"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mb-3">— or fill in manually below —</p>
                        @endif

                        <div class="grid grid-cols-2 gap-3">
                            <div x-show="!selectedProduct">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Plan Type <span class="text-strawberry-500">*</span></label>
                                <select name="plan_type"
                                        :required="!selectedProduct"
                                        class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400">
                                    @foreach (['medical','critical_illness','personal_accident','group','hibah','income','other'] as $type)
                                        <option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div x-show="!selectedProduct">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Plan Name</label>
                                <input type="text" name="plan_name" placeholder="e.g. A-Plus Med"
                                       class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
                            </div>
                            {{-- Hidden plan_type when product is selected --}}
                            <input type="hidden" name="plan_type" x-show="selectedProduct"
                                   :value="selectedProduct?.plan_type ?? ''" />
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Coverage Amount (RM)</label>
                                <input type="number" step="0.01" name="coverage_amount"
                                       class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Monthly Premium (RM)</label>
                                <input type="number" step="0.01" name="premium_monthly"
                                       class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Start Date</label>
                                <input type="date" name="start_date"
                                       class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Premium Frequency</label>
                                <div class="flex rounded-lg border border-gray-300 overflow-hidden text-sm">
                                    <label class="flex-1 flex items-center justify-center gap-1.5 py-2 cursor-pointer has-[:checked]:bg-matcha-600 has-[:checked]:text-white transition">
                                        <input type="radio" name="frequency" value="monthly" class="sr-only" checked />
                                        Monthly
                                    </label>
                                    <label class="flex-1 flex items-center justify-center gap-1.5 py-2 cursor-pointer border-l border-gray-300 has-[:checked]:bg-matcha-600 has-[:checked]:text-white transition">
                                        <input type="radio" name="frequency" value="yearly" class="sr-only" />
                                        Yearly
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                            <textarea name="notes" rows="2"
                                      class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400"></textarea>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <button type="submit"
                                    class="bg-matcha-600 hover:bg-matcha-800 text-white text-xs px-4 py-2 rounded-lg transition">
                                Save Policy
                            </button>
                            <button type="button" @click="addPolicy = false; selectedProduct = null; selectedId = ''"
                                    class="text-xs text-gray-500 hover:text-gray-700 px-3 py-2">Cancel</button>
                        </div>
                    </form>
                </div>

                {{-- Policy list --}}
                @forelse ($client->policies as $policy)
                    <div class="py-3 border-t border-gray-100 first:border-t-0" x-data="{ del: false }">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="inline-block text-xs bg-matcha-50 text-matcha-700 rounded px-2 py-0.5 font-medium">
                                    {{ ucfirst(str_replace('_', ' ', $policy->plan_type)) }}
                                </span>
                                @if ($policy->plan_name)
                                    <span class="text-sm text-gray-700 ml-1.5">{{ $policy->plan_name }}</span>
                                @endif
                                {{-- Catalog attributes --}}
                                @if ($policy->planProduct && $policy->planProduct->attributes)
                                    <div class="mt-1 flex flex-wrap gap-x-3 gap-y-0.5">
                                        @foreach ($policy->planProduct->attributes as $key => $value)
                                            <span class="text-xs text-gray-400">
                                                {{ $key }}: <span class="text-gray-600 font-medium">{{ $value }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="mt-1 text-xs text-gray-400 space-x-3">
                                    @if ($policy->coverage_amount)
                                        <span>Coverage: RM {{ number_format($policy->coverage_amount, 2) }}</span>
                                    @endif
                                    @if ($policy->premium_monthly)
                                        <span>Premium: RM {{ number_format($policy->premium_monthly, 2) }}/{{ $policy->frequency ?? 'mo' }}</span>
                                    @endif
                                    @php $nextRenewal = $policy->nextRenewalDate(); @endphp
                                    @if ($nextRenewal)
                                        <span class="text-matcha-600 font-medium">
                                            Next renewal: {{ $nextRenewal->format('d M Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <button type="button" @click="del = !del"
                                        class="text-xs text-strawberry-400 hover:text-strawberry-600">Remove</button>
                                <div x-show="del" class="mt-1 flex items-center gap-2">
                                    <span class="text-xs text-gray-500">Sure?</span>
                                    <form method="POST" action="{{ route('clients.policies.destroy', [$client, $policy]) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-strawberry-600 font-medium hover:underline">Yes</button>
                                    </form>
                                    <button @click="del = false" class="text-xs text-gray-400">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 py-2">No policies attached yet.</p>
                @endforelse
            </div>

            {{-- Log Touchpoint --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6" id="log-interaction-panel">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700">Log Touchpoint</h3>
                    <button @click="logOpen = !logOpen"
                            class="text-xs text-matcha-600 border border-matcha-200 hover:bg-matcha-50 px-3 py-1.5 rounded-lg transition">
                        <span x-text="logOpen ? 'Cancel' : '+ Log Interaction'"></span>
                    </button>
                </div>

                <div x-show="logOpen" x-transition class="mt-4">
                    <form method="POST" action="{{ route('clients.touchpoints.store', $client) }}">
                        @csrf
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Date &amp; Time <span class="text-strawberry-500">*</span></label>
                                <input type="datetime-local" name="contacted_at"
                                       value="{{ now()->format('Y-m-d\TH:i') }}"
                                       class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Channel <span class="text-strawberry-500">*</span></label>
                                <select name="channel"
                                        class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400">
                                    @foreach (['whatsapp','phone_call','in_person','dm_instagram','dm_facebook','email','other'] as $ch)
                                        <option value="{{ $ch }}">{{ ucfirst(str_replace('_', ' ', $ch)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Topic <span class="text-strawberry-500">*</span></label>
                                <input type="text" name="topic" required x-model="prefillTopic"
                                       class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Next Action</label>
                                <input type="text" name="next_action"
                                       class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Next Action Date</label>
                                <input type="date" name="next_action_date"
                                       value="{{ now()->addDays(14)->format('Y-m-d') }}"
                                       class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
                                <p class="text-[10px] text-gray-400 mt-1">Defaults to 14 days (a fortnight).</p>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                                <textarea name="notes" rows="2"
                                          class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400"></textarea>
                            </div>
                        </div>
                        <button type="submit"
                                class="mt-3 bg-matcha-600 hover:bg-matcha-800 text-white text-xs px-4 py-2 rounded-lg transition">
                            Save Touchpoint
                        </button>
                    </form>
                </div>
            </div>

        </div>

        {{-- Right column (40%) --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Interaction history --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Interaction History</h3>
                @if ($touchpoints->count())
                    <ul class="space-y-3">
                        @foreach ($touchpoints as $tp)
                            <li class="border-l-2 border-matcha-200 pl-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-400">{{ $tp->contacted_at->format('d M Y') }}</span>
                                    <span class="text-xs text-gray-400 bg-gray-100 rounded px-1.5 py-0.5">
                                        {{ ucfirst(str_replace('_', ' ', $tp->channel)) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-700 mt-0.5">{{ $tp->topic }}</p>
                                @if ($tp->next_action)
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <p class="text-xs text-matcha-600">→ {{ $tp->next_action }}
                                            @if ($tp->next_action_date)
                                                <span class="text-gray-400">({{ $tp->next_action_date->format('d M') }})</span>
                                            @endif
                                        </p>
                                        <button type="button" 
                                                @click="logOpen = true; prefillTopic = '{{ addslashes($tp->next_action) }}'; document.getElementById('log-interaction-panel').scrollIntoView({behavior: 'smooth', block: 'center'})" 
                                                class="text-sm bg-matcha-50 text-matcha-600 hover:bg-matcha-100 hover:text-matcha-800 px-1.5 py-0.5 rounded border border-matcha-200 transition">
                                            Log this
                                        </button>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    @if ($touchpoints->hasPages())
                        <div class="mt-4">{{ $touchpoints->links() }}</div>
                    @endif
                @else
                    <p class="text-sm text-gray-400">No interactions logged yet.</p>
                @endif
            </div>

            {{-- 1st Year Commission Estimate --}}
            @php
                $commissionBreakdown = $client->policies
                    ->map(fn($p) => ['policy' => $p, 'amount' => $p->estimatedCommissionFirstYear()])
                    ->filter(fn($row) => $row['amount'] !== null)
                    ->values();
                $totalCommission = $commissionBreakdown->sum('amount');
            @endphp
            @if ($commissionBreakdown->count())
            <div class="bg-amber-50 rounded-xl border border-amber-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-amber-800">Est. Commission (Yr 1)</h3>
                    <span class="text-sm font-bold text-amber-700">RM {{ number_format($totalCommission, 2) }}</span>
                </div>
                <ul class="divide-y divide-amber-100">
                    @foreach ($commissionBreakdown as $row)
                        <li class="py-2 flex items-start justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-700">{{ $row['policy']->planProduct->name }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ $row['policy']->planProduct->commission_first_year }}% ×
                                    RM {{ number_format($row['policy']->premium_monthly, 2) }}/{{ $row['policy']->frequency ?? 'mo' }}
                                </p>
                            </div>
                            <span class="text-xs font-semibold text-amber-600 ml-2 whitespace-nowrap">
                                RM {{ number_format($row['amount'], 2) }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Reach Angles --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Reach Angles</h3>
                @if ($client->reachAngles->count())
                    <ul class="space-y-1">
                        @foreach ($client->reachAngles as $angle)
                            <li class="text-sm text-gray-700">
                                <span class="text-matcha-600">·</span> {{ $angle->title }}
                                @if ($angle->pivot->reached_at)
                                    <span class="text-xs text-gray-400 ml-1">
                                        ({{ \Carbon\Carbon::parse($angle->pivot->reached_at)->format('d M Y') }})
                                    </span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-400">Not linked to any reach angles.</p>
                @endif
            </div>

        </div>
    </div>

</x-app-layout>
