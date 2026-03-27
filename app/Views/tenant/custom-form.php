<?php
/**
 * Tenant Custom Form — renders a public form from API.
 * Receives $formSlug from TenantController.
 */
$orgName = htmlspecialchars($org['name'] ?? 'Club', ENT_QUOTES, 'UTF-8');
$slug = htmlspecialchars($formSlug ?? '', ENT_QUOTES, 'UTF-8');
?>

<div x-data="customFormView()" x-init="load()">
    <!-- Loading -->
    <div x-show="loading" class="py-32 text-center">
        <svg class="animate-spin w-8 h-8 text-gold-500 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
    </div>

    <!-- Not found / closed -->
    <div x-show="!loading && !formData" class="py-32 text-center">
        <div class="text-6xl font-extrabold text-slate-700/20">404</div>
        <p class="mt-4 text-lg font-semibold text-white">Form not found</p>
        <a href="/" class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gold-500 text-navy-950 text-sm font-bold hover:bg-gold-400 transition-all">← Back to Home</a>
    </div>

    <!-- Form -->
    <div x-show="!loading && formData" x-cloak>
        <!-- Hero -->
        <section class="relative bg-navy-900 overflow-hidden py-20">
            <div class="absolute inset-0 grid-bg opacity-40"></div>
            <div class="absolute inset-0 hero-glow"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-white" x-text="formData.title"></h1>
                <p x-show="formData.description" class="mt-4 text-lg text-slate-400 max-w-2xl mx-auto" x-text="formData.description"></p>
            </div>
        </section>

        <!-- Form body -->
        <section class="relative py-16 bg-navy-950">
            <div class="absolute inset-0 section-glow"></div>
            <div class="relative max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Success message -->
                <div x-show="submitted" x-transition class="rounded-2xl glass-card p-8 text-center">
                    <div class="w-16 h-16 rounded-full bg-emerald-500/20 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-white mb-2">Submitted!</h2>
                    <p class="text-slate-400" x-text="formData.success_message || 'Thank you for your submission!'"></p>
                </div>

                <!-- Form fields -->
                <form x-show="!submitted" @submit.prevent="submit()" class="rounded-2xl glass-card p-6 sm:p-8 space-y-6">
                    <div class="flex flex-wrap gap-x-4 gap-y-5">
                        <template x-for="(field, idx) in fields" :key="idx">
                            <div :class="field.width === 'half' ? 'w-full sm:w-[calc(50%-0.5rem)]' : 'w-full'">
                                <!-- Heading -->
                                <template x-if="field.type === 'heading'">
                                    <h3 class="text-lg font-bold text-white border-b border-slate-700 pb-2" x-text="field.label"></h3>
                                </template>
                                <!-- Paragraph -->
                                <template x-if="field.type === 'paragraph'">
                                    <p class="text-sm text-slate-400" x-text="field.help_text || field.label"></p>
                                </template>
                                <!-- Inputs -->
                                <template x-if="!['heading','paragraph','hidden'].includes(field.type)">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-200 mb-1.5">
                                            <span x-text="field.label"></span>
                                            <span x-show="field.is_required == 1" class="text-red-400">*</span>
                                        </label>

                                        <!-- Text / Email / Phone / Number / Date -->
                                        <template x-if="['text','email','phone','number','date'].includes(field.type)">
                                            <input :type="field.type === 'phone' ? 'tel' : field.type"
                                                   x-model="formValues[field.name]"
                                                   :placeholder="field.placeholder"
                                                   :required="field.is_required == 1"
                                                   class="w-full rounded-xl border border-slate-700 bg-navy-900/50 px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:ring-2 focus:ring-gold-500 focus:border-gold-500 transition-all">
                                        </template>

                                        <!-- Textarea -->
                                        <template x-if="field.type === 'textarea'">
                                            <textarea x-model="formValues[field.name]"
                                                      :placeholder="field.placeholder"
                                                      :required="field.is_required == 1"
                                                      rows="4"
                                                      class="w-full rounded-xl border border-slate-700 bg-navy-900/50 px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:ring-2 focus:ring-gold-500 focus:border-gold-500 transition-all"></textarea>
                                        </template>

                                        <!-- Select -->
                                        <template x-if="field.type === 'select'">
                                            <select x-model="formValues[field.name]"
                                                    :required="field.is_required == 1"
                                                    class="w-full rounded-xl border border-slate-700 bg-navy-900/50 px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-gold-500 focus:border-gold-500 transition-all">
                                                <option value="">Select...</option>
                                                <template x-for="opt in (field.options || [])" :key="opt">
                                                    <option :value="opt" x-text="opt"></option>
                                                </template>
                                            </select>
                                        </template>

                                        <!-- Radio -->
                                        <template x-if="field.type === 'radio'">
                                            <div class="space-y-2 mt-1">
                                                <template x-for="opt in (field.options || [])" :key="opt">
                                                    <label class="flex items-center gap-2 cursor-pointer">
                                                        <input type="radio" :name="field.name" :value="opt" x-model="formValues[field.name]"
                                                               class="border-slate-600 text-gold-500 focus:ring-gold-500 bg-navy-900">
                                                        <span class="text-sm text-slate-300" x-text="opt"></span>
                                                    </label>
                                                </template>
                                            </div>
                                        </template>

                                        <!-- Checkbox -->
                                        <template x-if="field.type === 'checkbox'">
                                            <div class="space-y-2 mt-1">
                                                <template x-for="opt in (field.options || [])" :key="opt">
                                                    <label class="flex items-center gap-2 cursor-pointer">
                                                        <input type="checkbox" :value="opt"
                                                               @change="toggleCheckbox(field.name, opt, $event)"
                                                               class="rounded border-slate-600 text-gold-500 focus:ring-gold-500 bg-navy-900">
                                                        <span class="text-sm text-slate-300" x-text="opt"></span>
                                                    </label>
                                                </template>
                                            </div>
                                        </template>

                                        <!-- File -->
                                        <template x-if="field.type === 'file'">
                                            <input type="file" @change="formValues[field.name] = $event.target.files[0]?.name || ''"
                                                   class="w-full rounded-xl border border-slate-700 bg-navy-900/50 px-4 py-2 text-sm text-slate-400 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-gold-500/10 file:text-gold-400 hover:file:bg-gold-500/20 transition-all">
                                        </template>

                                        <!-- Help text -->
                                        <p x-show="field.help_text" class="mt-1 text-xs text-slate-500" x-text="field.help_text"></p>
                                    </div>
                                </template>
                                <!-- Hidden -->
                                <template x-if="field.type === 'hidden'">
                                    <input type="hidden" x-model="formValues[field.name]" :value="field.placeholder">
                                </template>
                            </div>
                        </template>
                    </div>

                    <!-- Error -->
                    <div x-show="error" x-transition class="rounded-xl bg-red-500/10 border border-red-500/20 px-4 py-3 text-sm text-red-400" x-text="error"></div>

                    <!-- Submit -->
                    <button type="submit" :disabled="submitting"
                            class="w-full rounded-xl bg-gold-500 text-navy-950 px-6 py-3 text-sm font-bold hover:bg-gold-400 disabled:opacity-50 transition-all flex items-center justify-center gap-2">
                        <svg x-show="submitting" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span x-text="submitting ? 'Submitting...' : 'Submit'"></span>
                    </button>
                </form>
            </div>
        </section>
    </div>
</div>

<script>
function customFormView() {
    return {
        formData: null, fields: [], loading: true,
        formValues: {}, submitted: false, submitting: false, error: '',
        async load() {
            try {
                const res = await fetch(baseApi + '/api/public/forms/<?= $slug ?>');
                const json = await res.json();
                if (res.ok && json.data) {
                    this.formData = json.data;
                    this.fields = json.data.fields || [];
                    // Init form values
                    this.fields.forEach(f => {
                        if (f.type === 'checkbox') { this.formValues[f.name] = []; }
                        else if (f.type === 'hidden') { this.formValues[f.name] = f.placeholder || ''; }
                        else { this.formValues[f.name] = ''; }
                    });
                    document.title = json.data.title + ' | <?= addslashes($orgName) ?>';
                }
            } catch(e) {}
            this.loading = false;
        },
        toggleCheckbox(fieldName, value, event) {
            if (!Array.isArray(this.formValues[fieldName])) this.formValues[fieldName] = [];
            if (event.target.checked) { this.formValues[fieldName].push(value); }
            else { this.formValues[fieldName] = this.formValues[fieldName].filter(v => v !== value); }
        },
        async submit() {
            this.error = '';
            this.submitting = true;
            // Serialize checkbox arrays to comma string
            const data = {};
            for (const [k, v] of Object.entries(this.formValues)) {
                data[k] = Array.isArray(v) ? v.join(', ') : v;
            }
            try {
                const headers = { 'Content-Type': 'application/json' };
                const token = localStorage.getItem('player_token');
                if (token) headers['Authorization'] = 'Bearer ' + token;
                const res = await fetch(baseApi + '/api/public/forms/<?= $slug ?>/submit', {
                    method: 'POST', headers, body: JSON.stringify(data)
                });
                const json = await res.json();
                if (!res.ok) throw new Error(json.error || 'Submission failed');
                this.submitted = true;
            } catch(e) {
                this.error = e.message;
            }
            this.submitting = false;
        }
    };
}
</script>
