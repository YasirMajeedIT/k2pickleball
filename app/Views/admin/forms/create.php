<?php
$title = 'Create Form';
$breadcrumbs = [['label' => 'Content', 'url' => '#'], ['label' => 'Forms', 'url' => '/admin/forms'], ['label' => 'Create']];
ob_start();
?>
<style>
    .form-quill-editor .ql-container { border-radius: 0 0 0.75rem 0.75rem; min-height: 120px; font-size: 0.875rem; }
    .form-quill-editor .ql-toolbar { border-radius: 0.75rem 0.75rem 0 0; }
    .form-quill-editor .ql-editor { min-height: 120px; }
    .dark .form-quill-editor .ql-toolbar { background: #1e293b; border-color: #334155; }
    .dark .form-quill-editor .ql-container { background: #1e293b; border-color: #334155; color: #e2e8f0; }
    .dark .form-quill-editor .ql-toolbar button .ql-stroke { stroke: #94a3b8; }
    .dark .form-quill-editor .ql-toolbar button .ql-fill { fill: #94a3b8; }
    .dark .form-quill-editor .ql-toolbar button:hover .ql-stroke { stroke: #e2e8f0; }
    .dark .form-quill-editor .ql-toolbar .ql-picker-label { color: #94a3b8; }
    .dark .form-quill-editor .ql-toolbar .ql-picker-options { background: #1e293b; border-color: #334155; }
</style>
<div x-data="formBuilder()" x-init="init()">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-white">Create Form</h1>
            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">Build a custom form with drag-and-drop fields.</p>
        </div>
        <a href="/admin/forms" class="inline-flex items-center gap-2 rounded-xl border border-surface-200 dark:border-surface-700 px-4 py-2 text-sm font-medium text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            Back
        </a>
    </div>

    <form @submit.prevent="save()" class="space-y-6">
        <!-- Form Settings -->
        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="px-6 py-4 border-b border-surface-100 dark:border-surface-800">
                <h3 class="text-sm font-semibold text-surface-900 dark:text-white">Form Details</h3>
            </div>
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">Title <span class="text-red-500">*</span></label>
                        <input type="text" x-model="form.title" @input="autoSlug()" required
                               class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm text-surface-900 dark:border-surface-700 dark:bg-surface-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                               placeholder="e.g. Job Application, Feedback Form">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">Slug</label>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-surface-400">/forms/</span>
                            <input type="text" x-model="form.slug"
                                   class="flex-1 rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm text-surface-900 dark:border-surface-700 dark:bg-surface-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">Description</label>
                    <div class="form-quill-editor rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                        <div id="create-description-editor"></div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">Status</label>
                        <select x-model="form.status"
                                class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm text-surface-900 dark:border-surface-700 dark:bg-surface-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                            <option value="draft">Draft</option>
                            <option value="active">Active</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">Max Submissions</label>
                        <input type="number" x-model="form.max_submissions" min="0" placeholder="0 = unlimited"
                               class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm text-surface-900 dark:border-surface-700 dark:bg-surface-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">Closes At</label>
                        <input type="text" x-ref="closesAtCreate" x-model="form.closes_at" placeholder="Select date & time"
                               class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm text-surface-900 dark:border-surface-700 dark:bg-surface-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-surface-700 dark:text-surface-300 mb-1.5">Success Message</label>
                    <input type="text" x-model="form.success_message"
                           class="w-full rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm text-surface-900 dark:border-surface-700 dark:bg-surface-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                           placeholder="Thank you for your submission!">
                </div>
                <div class="flex flex-wrap gap-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" :checked="form.requires_auth == 1" @change="form.requires_auth = $event.target.checked ? 1 : 0"
                               class="rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                        <div>
                            <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Require Login</span>
                            <p class="text-xs text-surface-400">Only logged-in users can submit</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" :checked="form.show_in_nav == 1" @change="form.show_in_nav = $event.target.checked ? 1 : 0"
                               class="rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                        <div>
                            <span class="text-sm font-medium text-surface-700 dark:text-surface-300">Show in Navigation</span>
                            <p class="text-xs text-surface-400">Form link appears in nav bar</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Fields Builder -->
        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="px-6 py-4 border-b border-surface-100 dark:border-surface-800 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-surface-900 dark:text-white">Fields</h3>
                <span class="text-xs text-surface-400" x-text="fields.length + ' field(s)'"></span>
            </div>
            <div class="p-6 space-y-3">
                <!-- Empty state -->
                <div x-show="fields.length === 0" class="py-8 text-center text-surface-400">
                    <p class="text-sm">No fields yet. Add your first field below.</p>
                </div>

                <!-- Fields list -->
                <template x-for="(field, idx) in fields" :key="field._key">
                    <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800/50 overflow-hidden">
                        <div class="flex items-center gap-3 px-4 py-3 border-b border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800">
                            <span class="text-xs text-surface-400 font-mono w-6" x-text="'#' + (idx+1)"></span>
                            <span class="flex-1 text-sm font-medium text-surface-700 dark:text-surface-300" x-text="field.label || '(untitled)'"></span>
                            <span class="rounded-md bg-surface-100 dark:bg-surface-700 px-2 py-0.5 text-xs text-surface-500" x-text="field.type"></span>
                            <button type="button" @click="moveField(idx, -1)" :disabled="idx===0" class="p-1 text-surface-400 hover:text-surface-600 disabled:opacity-30"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 15.75l7.5-7.5 7.5 7.5"/></svg></button>
                            <button type="button" @click="moveField(idx, 1)" :disabled="idx===fields.length-1" class="p-1 text-surface-400 hover:text-surface-600 disabled:opacity-30"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg></button>
                            <button type="button" @click="removeField(idx)" class="p-1 text-red-400 hover:text-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg></button>
                        </div>
                        <div class="p-4 space-y-3">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-surface-500 mb-1">Label *</label>
                                    <input type="text" x-model="field.label" @input="autoFieldName(field)" required
                                           class="w-full rounded-lg border border-surface-200 bg-white px-3 py-2 text-sm dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:ring-2 focus:ring-primary-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-surface-500 mb-1">Field Name</label>
                                    <input type="text" x-model="field.name"
                                           class="w-full rounded-lg border border-surface-200 bg-white px-3 py-2 text-sm dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:ring-2 focus:ring-primary-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-surface-500 mb-1">Type</label>
                                    <select x-model="field.type"
                                            class="w-full rounded-lg border border-surface-200 bg-white px-3 py-2 text-sm dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:ring-2 focus:ring-primary-500">
                                        <option value="text">Text</option>
                                        <option value="textarea">Textarea</option>
                                        <option value="email">Email</option>
                                        <option value="phone">Phone</option>
                                        <option value="number">Number</option>
                                        <option value="date">Date</option>
                                        <option value="select">Select / Dropdown</option>
                                        <option value="radio">Radio Buttons</option>
                                        <option value="checkbox">Checkbox</option>
                                        <option value="file">File Upload</option>
                                        <option value="heading">Section Heading</option>
                                        <option value="paragraph">Paragraph Text</option>
                                        <option value="hidden">Hidden Field</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-surface-500 mb-1">Placeholder</label>
                                    <input type="text" x-model="field.placeholder"
                                           class="w-full rounded-lg border border-surface-200 bg-white px-3 py-2 text-sm dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:ring-2 focus:ring-primary-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-surface-500 mb-1">Help Text</label>
                                    <input type="text" x-model="field.help_text"
                                           class="w-full rounded-lg border border-surface-200 bg-white px-3 py-2 text-sm dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:ring-2 focus:ring-primary-500">
                                </div>
                            </div>
                            <!-- Options for select/radio -->
                            <div x-show="['select','radio','checkbox'].includes(field.type)">
                                <label class="block text-xs font-medium text-surface-500 mb-1">Options (one per line)</label>
                                <textarea x-model="field._optionsText" rows="3" placeholder="Option 1&#10;Option 2&#10;Option 3"
                                          class="w-full rounded-lg border border-surface-200 bg-white px-3 py-2 text-sm font-mono dark:border-surface-600 dark:bg-surface-700 dark:text-white focus:ring-2 focus:ring-primary-500"></textarea>
                            </div>
                            <div class="flex flex-wrap items-center gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" :checked="field.is_required == 1" @change="field.is_required = $event.target.checked ? 1 : 0"
                                           class="rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                                    <span class="text-xs font-medium text-surface-600 dark:text-surface-400">Required</span>
                                </label>
                                <div class="flex items-center gap-2">
                                    <label class="text-xs font-medium text-surface-500">Width:</label>
                                    <select x-model="field.width"
                                            class="rounded-lg border border-surface-200 bg-white px-2 py-1 text-xs dark:border-surface-600 dark:bg-surface-700 dark:text-white">
                                        <option value="full">Full</option>
                                        <option value="half">Half</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Add field -->
                <div class="pt-3">
                    <button type="button" @click="addField()"
                            class="inline-flex items-center gap-2 rounded-xl border-2 border-dashed border-surface-200 dark:border-surface-700 px-4 py-2.5 text-sm font-medium text-surface-500 hover:text-primary-600 hover:border-primary-300 dark:hover:border-primary-600 transition-all w-full justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Add Field
                    </button>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-3">
            <button type="submit" :disabled="saving"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 disabled:opacity-50 transition-all">
                <svg x-show="saving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Create Form
            </button>
            <a href="/admin/forms" class="rounded-xl border border-surface-200 dark:border-surface-700 px-6 py-2.5 text-sm font-medium text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800 transition-all">Cancel</a>
        </div>
    </form>

    <div x-show="toast.show" x-cloak x-transition class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-xl border px-4 py-3 shadow-lg text-sm font-medium"
         :class="toast.type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800 dark:bg-emerald-500/10 dark:border-emerald-500/30 dark:text-emerald-400' : 'bg-red-50 border-red-200 text-red-800 dark:bg-red-500/10 dark:border-red-500/30 dark:text-red-400'">
        <span x-text="toast.message"></span>
    </div>
</div>

<script>
function formBuilder() {
    let _keyCounter = 0;
    return {
        form: { title:'', slug:'', description:'', status:'draft', success_message:'Thank you for your submission!', redirect_url:'', requires_auth:0, max_submissions:null, closes_at:'', show_in_nav:0 },
        fields: [],
        saving: false,
        toast: { show:false, type:'success', message:'' },
        quillEditor: null,
        flatpickrInstance: null,
        init() {
            this.$nextTick(() => {
                this.initQuill();
                this.initFlatpickr();
            });
        },
        initQuill() {
            const editorEl = document.getElementById('create-description-editor');
            if (!editorEl) return;
            this.quillEditor = new Quill(editorEl, {
                theme: 'snow',
                placeholder: 'Brief description shown at the top of the form...',
                modules: {
                    toolbar: [
                        [{ header: [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['link'],
                        ['clean'],
                    ],
                },
            });
        },
        initFlatpickr() {
            if (this.$refs.closesAtCreate && typeof flatpickr !== 'undefined') {
                this.flatpickrInstance = flatpickr(this.$refs.closesAtCreate, {
                    enableTime: true,
                    dateFormat: 'Y-m-d H:i',
                    onChange: (selectedDates, dateStr) => { this.form.closes_at = dateStr; },
                });
            }
        },
        autoSlug() {
            this.form.slug = this.form.title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        },
        autoFieldName(field) {
            if (!field._nameEdited) {
                field.name = field.label.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '');
            }
        },
        addField() {
            this.fields.push({
                _key: ++_keyCounter, label:'', name:'', type:'text', placeholder:'', help_text:'',
                is_required:0, options:[], _optionsText:'', width:'full', _nameEdited:false
            });
        },
        removeField(idx) { this.fields.splice(idx, 1); },
        moveField(idx, dir) {
            const t = idx + dir;
            if (t < 0 || t >= this.fields.length) return;
            [this.fields[idx], this.fields[t]] = [this.fields[t], this.fields[idx]];
        },
        async save() {
            if (this.saving) return;
            if (this.fields.length === 0) { this.showToast('Add at least one field', 'error'); return; }
            this.saving = true;
            // Sync Quill content
            if (this.quillEditor) {
                this.form.description = this.quillEditor.root.innerHTML;
                if (this.form.description === '<p><br></p>') this.form.description = '';
            }
            const payload = {
                ...this.form,
                requires_auth: this.form.requires_auth ? 1 : 0,
                show_in_nav:   this.form.show_in_nav   ? 1 : 0,
                closes_at: this.form.closes_at ? this.form.closes_at.replace('T', ' ').substring(0, 19) : null,
                max_submissions: (this.form.max_submissions !== '' && this.form.max_submissions !== null) ? (parseInt(this.form.max_submissions) || null) : null,
            };
            payload.fields = this.fields.map((f, i) => ({
                label: f.label, name: f.name, type: f.type, placeholder: f.placeholder,
                help_text: f.help_text, is_required: f.is_required ? 1 : 0,
                options: ['select','radio','checkbox'].includes(f.type) ? f._optionsText.split('\n').map(o=>o.trim()).filter(Boolean) : [],
                width: f.width, sort_order: i
            }));
            try {
                const res = await authFetch('/api/custom-forms', {
                    method: 'POST',
                    headers: { 'Content-Type':'application/json' },
                    body: JSON.stringify(payload)
                });
                const json = await res.json();
                if (!res.ok) throw new Error(json.message || 'Save failed');
                window.location.href = '/admin/forms';
            } catch(e) { this.showToast(e.message, 'error'); }
            this.saving = false;
        },
        showToast(msg, type='success') { this.toast = {show:true,type,message:msg}; setTimeout(()=>this.toast.show=false, 4000); }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
