<x-app-layout>
    <x-slot name="title">Edit Plan · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Edit Plan Product</x-slot>

    <div class="max-w-lg">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <form method="POST" action="{{ route('plan-products.update', $planProduct) }}">
                @csrf
                @method('PUT')

                <div class="space-y-4">

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="plan_type" class="block text-sm font-medium text-gray-700 mb-1">Plan Type <span class="text-strawberry-500">*</span></label>
                            <select id="plan_type" name="plan_type" required
                                    class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400">
                                @foreach (['medical','critical_illness','personal_accident','group','hibah','income','other'] as $type)
                                    <option value="{{ $type }}"
                                        {{ old('plan_type', $planProduct->plan_type) === $type ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Plan Name <span class="text-strawberry-500">*</span></label>
                            <input type="text" id="name" name="name"
                                   value="{{ old('name', $planProduct->name) }}" required
                                   class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
                        </div>
                    </div>

                    <div>
                        <label for="commission_first_year" class="block text-sm font-medium text-gray-700 mb-1">1st Year Commission (%)</label>
                        <input type="number" id="commission_first_year" name="commission_first_year" step="0.01" min="0" max="100"
                               value="{{ old('commission_first_year', $planProduct->commission_first_year) }}"
                               placeholder="e.g. 12.50"
                               class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400 @error('commission_first_year') border-red-400 @enderror" />
                        @error('commission_first_year') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Dynamic Attributes --}}
                    @php
                        $initialRows = [];
                        if (old('attr_keys')) {
                            foreach (old('attr_keys', []) as $i => $k) {
                                $initialRows[] = ['key' => $k, 'value' => old('attr_values')[$i] ?? ''];
                            }
                        } elseif ($planProduct->attributes) {
                            foreach ($planProduct->attributes as $k => $v) {
                                $initialRows[] = ['key' => $k, 'value' => $v];
                            }
                        }
                        if (empty($initialRows)) {
                            $initialRows = [['key' => '', 'value' => '']];
                        }
                    @endphp

                    <div x-data='{ rows: {{ json_encode($initialRows) }} }'>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-700">Attributes</label>
                            <button type="button"
                                    @click="rows.push({ key: '', value: '' })"
                                    class="text-xs text-matcha-600 hover:text-matcha-800 font-medium">
                                + Add attribute
                            </button>
                        </div>

                        <div class="space-y-2">
                            <template x-for="(row, i) in rows" :key="i">
                                <div class="flex items-center gap-2">
                                    <input type="text"
                                           :name="'attr_keys[' + i + ']'"
                                           x-model="row.key"
                                           placeholder="e.g. Room & Board"
                                           class="flex-1 text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
                                    <input type="text"
                                           :name="'attr_values[' + i + ']'"
                                           x-model="row.value"
                                           placeholder="e.g. 350"
                                           class="flex-1 text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400" />
                                    <button type="button"
                                            @click="rows.splice(i, 1)"
                                            x-show="rows.length > 1"
                                            class="text-gray-300 hover:text-strawberry-400 transition flex-shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <p class="mt-1.5 text-xs text-gray-400">Key = attribute name &nbsp;·&nbsp; Value = what it is.</p>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea id="notes" name="notes" rows="2"
                                  class="w-full text-sm rounded-lg border-gray-300 focus:ring-matcha-400 focus:border-matcha-400">{{ old('notes', $planProduct->notes) }}</textarea>
                    </div>

                </div>

                <div class="flex items-center justify-between mt-6">
                    <div class="flex items-center gap-3">
                        <button type="submit"
                                class="bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                            Update Plan
                        </button>
                        <a href="{{ route('plan-products.index') }}"
                           class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                    </div>

                    <div x-data="{ confirm: false }">
                        <button type="button" @click="confirm = true" x-show="!confirm"
                                class="text-xs text-strawberry-500 hover:text-strawberry-700">Delete</button>
                        <div x-show="confirm" class="flex items-center gap-2">
                            <span class="text-xs text-gray-600">Sure?</span>
                            <form method="POST" action="{{ route('plan-products.destroy', $planProduct) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-strawberry-600 font-medium hover:underline">Yes</button>
                            </form>
                            <button @click="confirm = false" class="text-xs text-gray-400">No</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
