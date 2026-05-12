<x-app-layout>
    <x-slot name="title">New Strategy · Dr Takaful CMS</x-slot>
    <x-slot name="pageTitle">New Strategy</x-slot>

    <x-slot name="actions">
        <a href="{{ route('strategies.index') }}"
           class="text-xs bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-lg transition">
            ← Back
        </a>
    </x-slot>

    {{-- Mode tabs --}}
    <div class="flex gap-2 mb-5" id="mode-tabs">
        <button type="button" onclick="switchMode('self')"
                id="tab-self"
                class="tab-btn text-sm font-medium px-4 py-2 rounded-lg border border-matcha-400 bg-matcha-50 text-matcha-700 transition">
            Self Made
        </button>
        <button type="button" onclick="switchMode('ai')"
                id="tab-ai"
                class="tab-btn text-sm font-medium px-4 py-2 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition">
            AI Guided
        </button>
    </div>

    {{-- Shared fields --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-4">Strategy Details</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" id="shared-fields">

            <div class="sm:col-span-2">
                <label class="block text-xs text-gray-600 mb-1">Title <span class="text-red-400">*</span></label>
                <input type="text" id="f-title" name="title"
                       class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400"
                       placeholder="e.g. Cold WhatsApp Opener for Strangers">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs text-gray-600 mb-1">Description</label>
                <textarea id="f-description" name="description" rows="2"
                          class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400"
                          placeholder="When and how to use this strategy"></textarea>
            </div>

            <div>
                <label class="block text-xs text-gray-600 mb-1">Category <span class="text-red-400">*</span></label>
                <select id="f-category" name="category"
                        class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                    <option value="">Select category</option>
                    <option value="prospecting">Prospecting</option>
                    <option value="content">Content</option>
                    <option value="objection_handling">Objection Handling</option>
                    <option value="follow_up">Follow Up</option>
                    <option value="referral">Referral</option>
                    <option value="closing">Closing</option>
                </select>
            </div>

            <div>
                <label class="block text-xs text-gray-600 mb-1">Channel <span class="text-red-400">*</span></label>
                <select id="f-channel" name="channel"
                        class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                    <option value="">Select channel</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="instagram">Instagram</option>
                    <option value="facebook">Facebook</option>
                    <option value="face_to_face">Face to Face</option>
                    <option value="general">General</option>
                </select>
            </div>

            <div>
                <label class="block text-xs text-gray-600 mb-1">Audience <span class="text-red-400">*</span></label>
                <select id="f-audience" name="audience"
                        class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                    <option value="">Select audience</option>
                    <option value="strangers">Strangers</option>
                    <option value="warm_leads">Warm Leads</option>
                    <option value="family_friends">Family & Friends</option>
                    <option value="corporate">Corporate</option>
                    <option value="general">General</option>
                </select>
            </div>

            <div>
                <label class="block text-xs text-gray-600 mb-1">Difficulty <span class="text-red-400">*</span></label>
                <select id="f-difficulty" name="difficulty"
                        class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                </select>
            </div>

            <div>
                <label class="block text-xs text-gray-600 mb-1">Type <span class="text-red-400">*</span></label>
                <select id="f-type" name="type" onchange="toggleType(this.value)"
                        class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400">
                    <option value="script">Script</option>
                    <option value="flow">Flow (multi-step)</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Self Made panel --}}
    <div id="panel-self">

        {{-- Script content (shown when type=script) --}}
        <div id="self-script-section" class="bg-white rounded-xl border border-gray-200 p-5 mb-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Script Content</p>
            <textarea id="f-content" name="content" rows="8"
                      class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400"
                      placeholder="Write your script here — what to say, how to say it..."></textarea>
        </div>

        {{-- Flow steps (shown when type=flow) --}}
        <div id="self-flow-section" class="hidden bg-white rounded-xl border border-gray-200 p-5 mb-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Flow Steps</p>
            <p class="text-xs text-gray-400 mb-4">You can add steps after saving the strategy.</p>
        </div>

        <form method="POST" action="{{ route('strategies.store') }}" id="self-form">
            @csrf
            <input type="hidden" name="title" id="hf-title">
            <input type="hidden" name="description" id="hf-description">
            <input type="hidden" name="category" id="hf-category">
            <input type="hidden" name="channel" id="hf-channel">
            <input type="hidden" name="audience" id="hf-audience">
            <input type="hidden" name="difficulty" id="hf-difficulty">
            <input type="hidden" name="type" id="hf-type">
            <input type="hidden" name="content" id="hf-content">
            <button type="button" onclick="submitSelf()"
                    class="text-sm bg-matcha-600 hover:bg-matcha-700 text-white px-5 py-2 rounded-lg transition">
                Save Strategy
            </button>
        </form>
    </div>

    {{-- AI Guided panel --}}
    <div id="panel-ai" class="hidden">

        <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Brief (Optional)</p>
            <textarea id="ai-brief" rows="3"
                      class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-matcha-400"
                      placeholder="Any specific context? e.g. 'target mak cik 40an yang dah ada takaful lama' or 'WhatsApp opener jangan terlalu formal'"></textarea>
        </div>

        <button type="button" onclick="runAi()"
                id="ai-generate-btn"
                class="text-sm bg-amber-500 hover:bg-amber-600 text-white px-5 py-2 rounded-lg transition mb-5">
            Generate with AI
        </button>

        {{-- AI result --}}
        <div id="ai-result" class="hidden">

            <div class="bg-white rounded-xl border border-matcha-200 p-5 mb-4">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Generated Strategy</p>

                <div id="ai-script-preview" class="hidden">
                    <div id="ai-content-text" class="text-sm text-gray-700 bg-gray-50 rounded-lg p-4 whitespace-pre-wrap leading-relaxed mb-3"></div>
                </div>

                <div id="ai-flow-preview" class="hidden">
                    <div id="ai-steps-preview" class="space-y-3"></div>
                </div>
            </div>

            <form method="POST" action="{{ route('strategies.store-generated') }}" id="ai-form">
                @csrf
                <input type="hidden" name="title" id="ai-hf-title">
                <input type="hidden" name="description" id="ai-hf-description">
                <input type="hidden" name="category" id="ai-hf-category">
                <input type="hidden" name="channel" id="ai-hf-channel">
                <input type="hidden" name="audience" id="ai-hf-audience">
                <input type="hidden" name="difficulty" id="ai-hf-difficulty">
                <input type="hidden" name="type" id="ai-hf-type">
                <input type="hidden" name="content" id="ai-hf-content">
                <div id="ai-steps-inputs"></div>
                <div class="flex gap-3">
                    <button type="submit"
                            class="text-sm bg-matcha-600 hover:bg-matcha-700 text-white px-5 py-2 rounded-lg transition">
                        Save This Strategy
                    </button>
                    <button type="button" onclick="runAi()"
                            class="text-sm bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-5 py-2 rounded-lg transition">
                        Regenerate
                    </button>
                </div>
            </form>
        </div>

        <div id="ai-error" class="hidden text-sm text-red-500 mt-3"></div>
    </div>

<script>
function switchMode(mode) {
    document.getElementById('panel-self').classList.toggle('hidden', mode !== 'self');
    document.getElementById('panel-ai').classList.toggle('hidden', mode !== 'ai');
    document.getElementById('tab-self').className = mode === 'self'
        ? 'tab-btn text-sm font-medium px-4 py-2 rounded-lg border border-matcha-400 bg-matcha-50 text-matcha-700 transition'
        : 'tab-btn text-sm font-medium px-4 py-2 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition';
    document.getElementById('tab-ai').className = mode === 'ai'
        ? 'tab-btn text-sm font-medium px-4 py-2 rounded-lg border border-matcha-400 bg-matcha-50 text-matcha-700 transition'
        : 'tab-btn text-sm font-medium px-4 py-2 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition';
}

function toggleType(type) {
    document.getElementById('self-script-section').classList.toggle('hidden', type !== 'script');
    document.getElementById('self-flow-section').classList.toggle('hidden', type !== 'flow');
}

function getShared() {
    return {
        title:       document.getElementById('f-title').value,
        description: document.getElementById('f-description').value,
        category:    document.getElementById('f-category').value,
        channel:     document.getElementById('f-channel').value,
        audience:    document.getElementById('f-audience').value,
        difficulty:  document.getElementById('f-difficulty').value,
        type:        document.getElementById('f-type').value,
    };
}

function submitSelf() {
    const d = getShared();
    document.getElementById('hf-title').value       = d.title;
    document.getElementById('hf-description').value = d.description;
    document.getElementById('hf-category').value    = d.category;
    document.getElementById('hf-channel').value     = d.channel;
    document.getElementById('hf-audience').value    = d.audience;
    document.getElementById('hf-difficulty').value  = d.difficulty;
    document.getElementById('hf-type').value        = d.type;
    document.getElementById('hf-content').value     = document.getElementById('f-content').value;
    document.getElementById('self-form').submit();
}

async function runAi() {
    const d = getShared();
    const brief = document.getElementById('ai-brief').value;

    const btn = document.getElementById('ai-generate-btn');
    btn.textContent = 'Generating…';
    btn.disabled = true;
    document.getElementById('ai-result').classList.add('hidden');
    document.getElementById('ai-error').classList.add('hidden');

    try {
        const res = await fetch('{{ route('strategies.generate') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ ...d, brief }),
        });

        const json = await res.json();

        if (!json.success) {
            document.getElementById('ai-error').textContent = json.message;
            document.getElementById('ai-error').classList.remove('hidden');
            return;
        }

        const data = json.data;

        // Populate hidden form fields
        document.getElementById('ai-hf-title').value       = data.title || d.title;
        document.getElementById('ai-hf-description').value = data.description || d.description;
        document.getElementById('ai-hf-category').value    = d.category;
        document.getElementById('ai-hf-channel').value     = d.channel;
        document.getElementById('ai-hf-audience').value    = d.audience;
        document.getElementById('ai-hf-difficulty').value  = d.difficulty;
        document.getElementById('ai-hf-type').value        = d.type;

        if (d.type === 'script') {
            document.getElementById('ai-hf-content').value = data.content || '';
            document.getElementById('ai-content-text').textContent = data.content || '';
            document.getElementById('ai-script-preview').classList.remove('hidden');
            document.getElementById('ai-flow-preview').classList.add('hidden');
        } else {
            const steps = data.steps || [];
            document.getElementById('ai-steps-inputs').innerHTML = steps.map((s, i) => `
                <input type="hidden" name="steps[${i}][title]"       value="${escHtml(s.title)}">
                <input type="hidden" name="steps[${i}][script]"      value="${escHtml(s.script)}">
                <input type="hidden" name="steps[${i}][timing_note]" value="${escHtml(s.timing_note || '')}">
                <input type="hidden" name="steps[${i}][branch_yes]"  value="${escHtml(s.branch_yes || '')}">
                <input type="hidden" name="steps[${i}][branch_no]"   value="${escHtml(s.branch_no || '')}">
            `).join('');

            document.getElementById('ai-steps-preview').innerHTML = steps.map((s, i) => `
                <div class="border border-gray-100 rounded-lg p-3">
                    <p class="text-xs font-semibold text-gray-700 mb-1">Step ${i+1}: ${escHtml(s.title)}</p>
                    <p class="text-xs text-gray-600 whitespace-pre-wrap mb-1">${escHtml(s.script)}</p>
                    ${s.timing_note ? `<p class="text-xs text-gray-400 italic">${escHtml(s.timing_note)}</p>` : ''}
                    ${s.branch_yes ? `<p class="text-xs text-green-600 mt-1">✓ ${escHtml(s.branch_yes)}</p>` : ''}
                    ${s.branch_no  ? `<p class="text-xs text-red-400 mt-1">✗ ${escHtml(s.branch_no)}</p>` : ''}
                </div>
            `).join('');

            document.getElementById('ai-flow-preview').classList.remove('hidden');
            document.getElementById('ai-script-preview').classList.add('hidden');
        }

        // Update title field if AI suggested one
        if (data.title && !document.getElementById('f-title').value) {
            document.getElementById('f-title').value = data.title;
        }

        document.getElementById('ai-result').classList.remove('hidden');
    } catch (e) {
        document.getElementById('ai-error').textContent = 'Unexpected error. Please try again.';
        document.getElementById('ai-error').classList.remove('hidden');
    } finally {
        btn.textContent = 'Generate with AI';
        btn.disabled = false;
    }
}

function escHtml(str) {
    return String(str)
        .replace(/&/g,'&amp;')
        .replace(/</g,'&lt;')
        .replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;');
}
</script>

</x-app-layout>
