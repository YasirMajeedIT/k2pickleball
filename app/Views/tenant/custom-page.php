<?php
/**
 * Tenant Custom Page — renders custom page content from API.
 * Receives $pageSlug from TenantController.
 */
$orgName = htmlspecialchars($org['name'] ?? 'Club', ENT_QUOTES, 'UTF-8');
$slug = htmlspecialchars($pageSlug ?? '', ENT_QUOTES, 'UTF-8');
?>

<div x-data="customPageView()" x-init="load()">
    <!-- Loading -->
    <div x-show="loading" class="py-32 text-center">
        <svg class="animate-spin w-8 h-8 text-gold-500 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
    </div>

    <!-- Not found -->
    <div x-show="!loading && !page" class="py-32 text-center">
        <div class="text-6xl font-extrabold text-slate-700/20">404</div>
        <p class="mt-4 text-lg font-semibold text-white">Page not found</p>
        <a href="/" class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gold-500 text-navy-950 text-sm font-bold hover:bg-gold-400 transition-all">← Back to Home</a>
    </div>

    <!-- Page content -->
    <div x-show="!loading && page" x-cloak>
        <!-- Hero -->
        <section class="relative bg-navy-900 overflow-hidden py-20">
            <div class="absolute inset-0 grid-bg opacity-40"></div>
            <div class="absolute inset-0 hero-glow"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-white" x-text="page.title"></h1>
                <p x-show="page.meta_description" class="mt-4 text-lg text-slate-400 max-w-2xl mx-auto" x-text="page.meta_description"></p>
            </div>
        </section>

        <!-- Content body -->
        <section class="relative py-16 bg-navy-950">
            <div class="absolute inset-0 section-glow"></div>
            <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="prose prose-invert prose-gold max-w-none
                            prose-headings:font-display prose-headings:text-white
                            prose-p:text-slate-300 prose-p:leading-relaxed
                            prose-a:text-gold-400 prose-a:no-underline hover:prose-a:text-gold-300
                            prose-strong:text-white
                            prose-ul:text-slate-300 prose-ol:text-slate-300
                            prose-img:rounded-xl prose-img:shadow-lg"
                     x-html="page.content">
                </div>
            </div>
        </section>
    </div>
</div>

<script>
function customPageView() {
    return {
        page: null, loading: true,
        async load() {
            try {
                const res = await fetch(baseApi + '/api/public/pages/<?= $slug ?>');
                const json = await res.json();
                if (res.ok && json.data) {
                    this.page = json.data;
                    document.title = json.data.title + ' | <?= addslashes($orgName) ?>';
                }
            } catch(e) {}
            this.loading = false;
        }
    };
}
</script>
