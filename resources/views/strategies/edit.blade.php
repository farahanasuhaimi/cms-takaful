<x-app-layout>
    <x-slot name="title">Edit Strategy · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">Edit Strategy</x-slot>

    <x-slot name="actions">
        <a href="{{ route('strategies.show', $strategy) }}"
           class="text-xs bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition">
            ← Back
        </a>
    </x-slot>

    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-5">
        <form method="POST" action="{{ route('strategies.update', $strategy) }}">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">

                <div class="sm:col-span-2">
                    <label class="block text-xs text-gray-600 mb-1">Title</label>
                    <input type="text" name="title" value="{{ old('title', $strategy->title) }}" required
                           class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                    <x-input-error :messages="$errors->get('title')" class="mt-1"/>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs text-gray-600 mb-1">Description</label>
                    <textarea name="description" rows="2"
                              class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">{{ old('description', $strategy->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs text-gray-600 mb-1">Category</label>
                    <select name="category" required
                            class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                        @foreach(['prospecting'=>'Prospecting','content'=>'Content','objection_handling'=>'Objection Handling','follow_up'=>'Follow Up','referral'=>'Referral','closing'=>'Closing'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('category', $strategy->category) === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs text-gray-600 mb-1">Channel</label>
                    <select name="channel" required
                            class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                        @foreach(['whatsapp'=>'WhatsApp','instagram'=>'Instagram','facebook'=>'Facebook','face_to_face'=>'Face to Face','general'=>'General'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('channel', $strategy->channel) === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs text-gray-600 mb-1">Audience</label>
                    <select name="audience" required
                            class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                        @foreach(['strangers'=>'Strangers','warm_leads'=>'Warm Leads','family_friends'=>'Family & Friends','corporate'=>'Corporate','general'=>'General'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('audience', $strategy->audience) === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs text-gray-600 mb-1">Difficulty</label>
                    <select name="difficulty" required
                            class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                        <option value="beginner"     @selected(old('difficulty', $strategy->difficulty) === 'beginner')>Beginner</option>
                        <option value="intermediate" @selected(old('difficulty', $strategy->difficulty) === 'intermediate')>Intermediate</option>
                        <option value="advanced"     @selected(old('difficulty', $strategy->difficulty) === 'advanced')>Advanced</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs text-gray-600 mb-1">Type</label>
                    <select name="type" required
                            class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                        <option value="script" @selected(old('type', $strategy->type) === 'script')>Script</option>
                        <option value="flow"   @selected(old('type', $strategy->type) === 'flow')>Flow (multi-step)</option>
                    </select>
                </div>
            </div>

            {{-- Product + approach by prospect type --}}
            <div class="mb-4 border-t border-gray-100 pt-4">
                <div class="flex flex-wrap items-end justify-between gap-3 mb-3">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Approach by prospect</p>
                        <p class="text-xs text-gray-400 mt-1">Leave a box empty if this strategy isn't for that type. It drives the Cold / Warm / Hot filter.
                            End a box with a line starting <code class="text-gray-500">Mesej:</code> and the Copy button copies just that message.</p>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Product</label>
                        <select name="product_line"
                                class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                            <option value="">Not set</option>
                            @foreach (\App\Models\Strategy::PRODUCT_LINES as $val => $label)
                                <option value="{{ $val }}" @selected(old('product_line', $strategy->product_line) === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-3">
                    @foreach (\App\Models\Strategy::TEMPERATURES as $t => $label)
                        <div>
                            <label class="block text-xs font-medium mb-1 {{ ['cold' => 'text-sky-700', 'warm' => 'text-amber-700', 'hot' => 'text-red-700'][$t] }}">
                                {{ $label }} <span class="font-normal text-gray-400">· {{ \App\Models\Strategy::TEMPERATURE_HINTS[$t] }}</span>
                            </label>
                            <textarea name="angle_{{ $t }}" rows="4"
                                      class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">{{ old("angle_{$t}", $strategy->{"angle_{$t}"}) }}</textarea>
                            <x-input-error :messages="$errors->get('angle_' . $t)" class="mt-1"/>
                        </div>
                    @endforeach

                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Facts to use <span class="text-gray-400">· numbers and proof points, with the drtakaful page they came from</span></label>
                        <textarea name="key_facts" rows="5"
                                  class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">{{ old('key_facts', $strategy->key_facts) }}</textarea>
                    </div>
                </div>
            </div>

            @if ($strategy->type === 'script')
                <div class="mb-4">
                    <label class="block text-xs text-gray-600 mb-1">Script Content</label>
                    <textarea name="content" rows="8"
                              class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">{{ old('content', $strategy->content) }}</textarea>
                </div>
            @endif

            {{-- Focus Points --}}
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Focus Points</label>
                <div class="space-y-4">
                    @foreach ($focusPoints as $group => $points)
                        <div>
                            <p class="text-xs font-semibold text-gray-600 mb-2">
                                {{ match($group) {
                                    'financial'  => 'Financial',
                                    'protection' => 'Protection',
                                    'family'     => 'Family',
                                    'life_stage' => 'Life Stage',
                                    'emotional'  => 'Emotional',
                                    'islamic'    => 'Islamic',
                                    default      => ucwords(str_replace('_', ' ', $group)),
                                } }}
                            </p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                @foreach ($points as $fp)
                                    <label class="flex items-start gap-2 text-xs text-gray-600 cursor-pointer group">
                                        <input type="checkbox" name="focus_point_ids[]" value="{{ $fp->id }}"
                                               {{ in_array($fp->id, $selectedFocusPointIds) ? 'checked' : '' }}
                                               class="mt-0.5 flex-shrink-0 rounded border-gray-300 text-matcha-600 focus:ring-matcha-400">
                                        <span class="leading-snug group-hover:text-gray-800 transition">{{ $fp->title }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                        class="text-sm bg-matcha-600 hover:bg-matcha-700 text-white px-5 py-2 rounded-lg transition">
                    Save Changes
                </button>

                <form method="POST" action="{{ route('strategies.destroy', $strategy) }}"
                      onsubmit="return confirm('Delete this strategy?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="text-sm text-red-400 hover:text-red-600 transition">
                        Delete
                    </button>
                </form>
            </div>
        </form>
    </div>

</x-app-layout>
