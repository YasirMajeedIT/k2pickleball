<?php
$title = 'Form Details';
$breadcrumbs = [['label' => 'Content', 'url' => '#'], ['label' => 'Forms', 'url' => '/admin/forms'], ['label' => 'Details']];
ob_start();
?>
<div x-data="formShow()" x-init="load()">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-surface-900 dark:text-white" x-text="formData.title || 'Loading...'"></h1>
            <p class="mt-1 text-sm text-surface-400" x-show="formData.slug">/forms/<span x-text="formData.slug"></span></p>
        </div>
        <div class="flex items-center gap-2">
            <a :href="'/admin/forms/' + formId + '/edit'"
               class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                Edit
            </a>
            <a href="/admin/forms" class="inline-flex items-center gap-2 rounded-xl border border-surface-200 dark:border-surface-700 px-4 py-2 text-sm font-medium text-surface-700 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800 transition-all">
                Back
            </a>
        </div>
    </div>

    <div x-show="loading" class="py-20 text-center">
        <svg class="animate-spin w-6 h-6 text-primary-500 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
    </div>

    <div x-show="!loading" class="space-y-6">
        <!-- Info bar -->
        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="grid grid-cols-2 md:grid-cols-5 divide-x divide-surface-100 dark:divide-surface-800">
                <div class="px-5 py-4">
                    <p class="text-xs text-surface-400 font-medium uppercase tracking-wider">Status</p>
                    <span class="mt-1 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                          :class="formData.status === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-400' : formData.status === 'draft' ? 'bg-amber-100 text-amber-800 dark:bg-amber-500/10 dark:text-amber-400' : 'bg-surface-100 text-surface-600 dark:bg-surface-800 dark:text-surface-400'"
                          x-text="formData.status"></span>
                </div>
                <div class="px-5 py-4">
                    <p class="text-xs text-surface-400 font-medium uppercase tracking-wider">Fields</p>
                    <p class="mt-1 text-sm font-medium text-surface-900 dark:text-white" x-text="(formData.fields || []).length"></p>
                </div>
                <div class="px-5 py-4">
                    <p class="text-xs text-surface-400 font-medium uppercase tracking-wider">Submissions</p>
                    <p class="mt-1 text-sm font-medium text-surface-900 dark:text-white" x-text="submissions.length"></p>
                </div>
                <div class="px-5 py-4">
                    <p class="text-xs text-surface-400 font-medium uppercase tracking-wider">Requires Auth</p>
                    <p class="mt-1 text-sm font-medium text-surface-900 dark:text-white" x-text="formData.requires_auth == 1 ? 'Yes' : 'No'"></p>
                </div>
                <div class="px-5 py-4">
                    <p class="text-xs text-surface-400 font-medium uppercase tracking-wider">Created</p>
                    <p class="mt-1 text-sm font-medium text-surface-900 dark:text-white" x-text="formData.created_at ? new Date(formData.created_at).toLocaleDateString() : '—'"></p>
                </div>
            </div>
        </div>

        <!-- Fields Preview -->
        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="px-6 py-4 border-b border-surface-100 dark:border-surface-800">
                <h3 class="text-sm font-semibold text-surface-900 dark:text-white">Form Fields</h3>
            </div>
            <div class="divide-y divide-surface-100 dark:divide-surface-800">
                <template x-for="(field, idx) in (formData.fields || [])" :key="idx">
                    <div class="px-6 py-3 flex items-center gap-4">
                        <span class="text-xs text-surface-400 font-mono w-6" x-text="'#' + (idx+1)"></span>
                        <span class="flex-1 text-sm font-medium text-surface-900 dark:text-white" x-text="field.label"></span>
                        <span class="rounded-md bg-surface-100 dark:bg-surface-700 px-2 py-0.5 text-xs text-surface-500" x-text="field.type"></span>
                        <span x-show="field.is_required == 1" class="text-xs text-red-500 font-medium">Required</span>
                        <span class="text-xs text-surface-400" x-text="field.width"></span>
                    </div>
                </template>
            </div>
        </div>

        <!-- Submissions -->
        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="px-6 py-4 border-b border-surface-100 dark:border-surface-800 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-surface-900 dark:text-white">Submissions</h3>
                <div class="flex items-center gap-2">
                    <select x-model="statusFilter" @change="loadSubmissions()"
                            class="rounded-lg border border-surface-200 bg-white px-3 py-1.5 text-xs dark:border-surface-700 dark:bg-surface-800 dark:text-white">
                        <option value="">All</option>
                        <option value="new">New</option>
                        <option value="reviewed">Reviewed</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
            </div>
            <div x-show="submissions.length === 0" class="px-6 py-8 text-center text-surface-400 text-sm">
                No submissions yet.
            </div>
            <div x-show="submissions.length > 0" class="divide-y divide-surface-100 dark:divide-surface-800">
                <template x-for="sub in submissions" :key="sub.id">
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-mono text-surface-400" x-text="'#' + sub.id"></span>
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                      :class="sub.status === 'new' ? 'bg-primary-100 text-primary-800 dark:bg-primary-500/10 dark:text-primary-400' : sub.status === 'reviewed' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-surface-100 text-surface-600 dark:bg-surface-800 dark:text-surface-400'"
                                      x-text="sub.status"></span>
                                <span class="text-xs text-surface-400" x-text="sub.player_name || sub.ip_address"></span>
                                <span class="text-xs text-surface-400" x-text="new Date(sub.created_at).toLocaleString()"></span>
                            </div>
                            <div class="flex items-center gap-1">
                                <button @click="toggleDetail(sub)" class="p-1.5 rounded-lg text-surface-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-500/10 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </button>
                                <button x-show="sub.status === 'new'" @click="markReviewed(sub)" title="Mark Reviewed" class="p-1.5 rounded-lg text-surface-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                </button>
                                <button @click="deleteSubmission(sub)" title="Delete" class="p-1.5 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                </button>
                            </div>
                        </div>
                        <!-- Detail -->
                        <div x-show="sub._showDetail" x-transition class="mt-3 rounded-lg bg-surface-50 dark:bg-surface-800/50 p-4 space-y-2">
                            <template x-for="d in (sub._detail || [])" :key="d.field_name">
                                <div class="flex gap-3">
                                    <span class="text-xs font-semibold text-surface-500 min-w-[120px]" x-text="d.field_name + ':'"></span>
                                    <span class="text-sm text-surface-900 dark:text-white break-all" x-text="d.value"></span>
                                </div>
                            </template>
                            <div x-show="!sub._detail || sub._detail.length === 0" class="text-xs text-surface-400">No data</div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <div x-show="toast.show" x-cloak x-transition class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-xl border px-4 py-3 shadow-lg text-sm font-medium"
         :class="toast.type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800 dark:bg-emerald-500/10 dark:border-emerald-500/30 dark:text-emerald-400' : 'bg-red-50 border-red-200 text-red-800 dark:bg-red-500/10 dark:border-red-500/30 dark:text-red-400'">
        <span x-text="toast.message"></span>
    </div>
</div>

<script>
function formShow() {
    const parts = window.location.pathname.split('/').filter(Boolean);
    const id = parts[parts.indexOf('forms') + 1];
    return {
        formData: {}, formId: id, submissions: [], loading: true, statusFilter: '',
        toast: { show:false, type:'success', message:'' },
        async load() {
            try {
                const [fRes, sRes] = await Promise.all([
                    fetch('/api/custom-forms/' + this.formId, { headers: { 'Authorization':'Bearer '+(localStorage.getItem('admin_token')||'') }}),
                    fetch('/api/custom-forms/' + this.formId + '/submissions', { headers: { 'Authorization':'Bearer '+(localStorage.getItem('admin_token')||'') }})
                ]);
                const fJson = await fRes.json();
                const sJson = await sRes.json();
                this.formData = fJson.data || {};
                this.submissions = (sJson.data || []).map(s=>({...s, _showDetail:false, _detail:null}));
            } catch(e) {}
            this.loading = false;
        },
        async loadSubmissions() {
            try {
                const url = '/api/custom-forms/' + this.formId + '/submissions' + (this.statusFilter ? '?status=' + this.statusFilter : '');
                const res = await fetch(url, { headers: { 'Authorization':'Bearer '+(localStorage.getItem('admin_token')||'') }});
                const json = await res.json();
                this.submissions = (json.data || []).map(s=>({...s, _showDetail:false, _detail:null}));
            } catch(e) {}
        },
        async toggleDetail(sub) {
            if (sub._showDetail) { sub._showDetail = false; return; }
            if (!sub._detail) {
                try {
                    const res = await fetch('/api/custom-forms/' + this.formId + '/submissions/' + sub.id, { headers: { 'Authorization':'Bearer '+(localStorage.getItem('admin_token')||'') }});
                    const json = await res.json();
                    sub._detail = json.data?.submission_data || json.data?.data || [];
                } catch(e) { sub._detail = []; }
            }
            sub._showDetail = true;
        },
        async markReviewed(sub) {
            try {
                await fetch('/api/custom-forms/' + this.formId + '/submissions/' + sub.id + '/status', {
                    method: 'PATCH',
                    headers: { 'Content-Type':'application/json', 'Authorization':'Bearer '+(localStorage.getItem('admin_token')||'') },
                    body: JSON.stringify({status:'reviewed'})
                });
                sub.status = 'reviewed';
                this.showToast('Marked as reviewed');
            } catch(e) { this.showToast('Failed', 'error'); }
        },
        async deleteSubmission(sub) {
            if (!confirm('Delete this submission?')) return;
            try {
                await fetch('/api/custom-forms/' + this.formId + '/submissions/' + sub.id, {
                    method: 'DELETE',
                    headers: { 'Authorization':'Bearer '+(localStorage.getItem('admin_token')||'') }
                });
                this.submissions = this.submissions.filter(s => s.id !== sub.id);
                this.showToast('Submission deleted');
            } catch(e) { this.showToast('Delete failed', 'error'); }
        },
        showToast(msg, type='success') { this.toast = {show:true,type,message:msg}; setTimeout(()=>this.toast.show=false, 3000); }
    };
}
</script>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
