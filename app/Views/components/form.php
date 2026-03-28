<?php
/**
 * Reusable Form Component — Premium SaaS Design
 * 
 * Variables expected:
 *   $formId     - string: Alpine component name
 *   $apiUrl     - string: API endpoint for POST (create) or PUT (edit)
 *   $method     - string: 'POST' or 'PUT'
 *   $fields     - array: Field definitions
 *   $backUrl    - string: URL to go back on cancel
 *   $submitLabel - string: Submit button text
 */
$formId = $formId ?? 'formData';
$method = $method ?? 'POST';
$submitLabel = $submitLabel ?? ($method === 'POST' ? 'Create' : 'Update');
$backUrl = $backUrl ?? (($baseUrl ?? '') . '/admin');
?>
<div x-data="<?= htmlspecialchars($formId, ENT_QUOTES) ?>()" class="mx-auto max-w-3xl">
    <form @submit.prevent="submitForm()" class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
        <!-- Form Header -->
        <div class="border-b border-surface-100 dark:border-surface-800 px-6 py-5 bg-surface-50/50 dark:bg-surface-800/30">
            <h3 class="text-lg font-bold text-surface-900 dark:text-white tracking-tight"><?= htmlspecialchars($title ?? 'Form', ENT_QUOTES) ?></h3>
            <p class="text-sm text-surface-400 mt-0.5"><?= $method === 'POST' ? 'Fill in the details below to create a new record' : 'Update the details below' ?></p>
        </div>

        <!-- Form Fields -->
        <div class="space-y-5 px-6 py-6">
            <?php foreach ($fields as $field): ?>
                <?php
                    $name = $field['name'];
                    $label = $field['label'] ?? ucfirst(str_replace('_', ' ', $name));
                    $type = $field['type'] ?? 'text';
                    $required = $field['required'] ?? false;
                    $placeholder = $field['placeholder'] ?? '';
                    $helpText = $field['help'] ?? '';
                    $cols = $field['cols'] ?? 'full';
                ?>
                <?php if ($type === 'section'): ?>
                <div class="border-t border-surface-100 dark:border-surface-800 pt-1 first:border-t-0 first:pt-0">
                    <div class="mb-1">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-surface-900 dark:text-white">
                            <?= htmlspecialchars($label, ENT_QUOTES) ?>
                        </h4>
                        <?php if ($helpText): ?>
                            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400"><?= htmlspecialchars($helpText, ENT_QUOTES) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php continue; ?>
                <?php endif; ?>
                <div class="<?= $cols === 'half' ? 'inline-block w-[calc(50%-0.625rem)] align-top first:mr-5' : '' ?>">
                    <?php if ($type !== 'checkbox'): ?>
                    <label class="mb-2 block text-sm font-semibold text-surface-700 dark:text-surface-300">
                        <?= htmlspecialchars($label, ENT_QUOTES) ?>
                        <?php if ($required): ?><span class="text-red-400 ml-0.5">*</span><?php endif; ?>
                    </label>
                    <?php endif; ?>

                    <?php if ($type === 'textarea'): ?>
                        <textarea x-model="form.<?= $name ?>"
                            rows="<?= $field['rows'] ?? 3 ?>"
                            placeholder="<?= htmlspecialchars($placeholder, ENT_QUOTES) ?>"
                            class="w-full rounded-xl border border-surface-200 bg-white px-4 py-3 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 focus:outline-none dark:border-surface-700 dark:bg-surface-800 dark:text-white placeholder:text-surface-400 transition-all"
                            <?= $required ? 'required' : '' ?>></textarea>

                    <?php elseif ($type === 'select'): ?>
                        <div class="relative">
                            <select data-field="<?= htmlspecialchars($name, ENT_QUOTES) ?>" x-model="form.<?= $name ?>"
                                class="w-full rounded-xl border border-surface-200 bg-white px-4 py-3 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 focus:outline-none dark:border-surface-700 dark:bg-surface-800 dark:text-white appearance-none transition-all"
                                <?= $required ? 'required' : '' ?>>
                                <option value="">Select...</option>
                                <?php foreach (($field['options'] ?? []) as $val => $optLabel): ?>
                                    <?php
                                        // Support both ['key' => 'Label'] and [['value' => 'key', 'label' => 'Label']] formats
                                        if (is_array($optLabel)) {
                                            $optValue = $optLabel['value'] ?? $val;
                                            $optText = $optLabel['label'] ?? $optLabel['name'] ?? $optValue;
                                        } else {
                                            $optValue = $val;
                                            $optText = $optLabel;
                                        }
                                    ?>
                                    <option value="<?= htmlspecialchars((string)$optValue, ENT_QUOTES) ?>"><?= htmlspecialchars((string)$optText, ENT_QUOTES) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <svg class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>

                    <?php elseif ($type === 'checkbox'): ?>
                        <label class="inline-flex items-center gap-3 cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox" x-model="form.<?= $name ?>" class="peer sr-only">
                                <div class="h-5 w-9 rounded-full bg-surface-200 peer-checked:bg-primary-500 dark:bg-surface-700 transition-colors"></div>
                                <div class="absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow-sm transform peer-checked:translate-x-4 transition-transform"></div>
                            </div>
                            <span class="text-sm font-medium text-surface-600 dark:text-surface-400 group-hover:text-surface-800 dark:group-hover:text-surface-200 transition-colors"><?= htmlspecialchars($helpText ?: $label, ENT_QUOTES) ?></span>
                        </label>

                    <?php elseif ($type === 'feature-list'): ?>
                        <div class="space-y-2">
                            <template x-for="(item, idx) in (form.<?= $name ?> || [])" :key="idx">
                                <div class="flex items-center gap-2 group">
                                    <svg class="w-4 h-4 text-primary-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                                    <span class="flex-1 text-sm text-surface-700 dark:text-surface-300" x-text="item"></span>
                                    <button type="button" @click="form.<?= $name ?>.splice(idx, 1)"
                                        class="opacity-0 group-hover:opacity-100 text-red-400 hover:text-red-600 transition-all" title="Remove">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </template>
                            <div x-show="!form.<?= $name ?> || form.<?= $name ?>.length === 0" class="text-sm text-surface-400 italic py-1">No features added yet.</div>
                            <div class="flex items-center gap-2 mt-2" x-data="{ newFeature: '' }">
                                <input type="text" x-model="newFeature" placeholder="<?= htmlspecialchars($placeholder ?: 'Type a feature and press Enter', ENT_QUOTES) ?>"
                                    @keydown.enter.prevent="if (newFeature.trim()) { if (!form.<?= $name ?>) form.<?= $name ?> = []; form.<?= $name ?>.push(newFeature.trim()); newFeature = ''; }"
                                    class="flex-1 rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 focus:outline-none dark:border-surface-700 dark:bg-surface-800 dark:text-white placeholder:text-surface-400 transition-all">
                                <button type="button" @click="if (newFeature.trim()) { if (!form.<?= $name ?>) form.<?= $name ?> = []; form.<?= $name ?>.push(newFeature.trim()); newFeature = ''; }"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Add
                                </button>
                            </div>
                        </div>

                    <?php elseif ($type === 'json'): ?>
                        <textarea x-model="form.<?= $name ?>"
                            rows="<?= $field['rows'] ?? 5 ?>"
                            placeholder="<?= htmlspecialchars($placeholder ?: '{}', ENT_QUOTES) ?>"
                            class="w-full rounded-xl border border-surface-200 bg-white px-4 py-3 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 focus:outline-none dark:border-surface-700 dark:bg-surface-800 dark:text-white font-mono placeholder:text-surface-400 transition-all"
                            <?= $required ? 'required' : '' ?>></textarea>

                    <?php elseif ($type === 'color'): ?>
                        <div class="flex items-center gap-3">
                            <input type="color" x-model="form.<?= $name ?>"
                                class="h-10 w-14 cursor-pointer rounded-lg border border-surface-200 bg-white p-1 dark:border-surface-700 dark:bg-surface-800 transition-all">
                            <input type="text" x-model="form.<?= $name ?>" maxlength="7" pattern="#[0-9a-fA-F]{6}"
                                placeholder="#6366f1"
                                class="w-28 rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm font-mono shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 focus:outline-none dark:border-surface-700 dark:bg-surface-800 dark:text-white transition-all">
                        </div>

                    <?php elseif ($type === 'checkbox-group'): ?>
                        <?php $defaultItems = $field['default_items'] ?? []; ?>
                        <div class="space-y-2">
                            <?php foreach ($defaultItems as $itemValue => $itemLabel): ?>
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="checkbox"
                                    :checked="(form.<?= $name ?> || []).includes('<?= htmlspecialchars($itemValue, ENT_QUOTES) ?>')"
                                    @change="
                                        if (!form.<?= $name ?>) form.<?= $name ?> = [];
                                        if ($event.target.checked) {
                                            if (!form.<?= $name ?>.includes('<?= htmlspecialchars($itemValue, ENT_QUOTES) ?>')) form.<?= $name ?>.push('<?= htmlspecialchars($itemValue, ENT_QUOTES) ?>');
                                        } else {
                                            form.<?= $name ?> = form.<?= $name ?>.filter(v => v !== '<?= htmlspecialchars($itemValue, ENT_QUOTES) ?>');
                                        }
                                    "
                                    class="h-4 w-4 rounded border-surface-300 text-primary-600 focus:ring-primary-500/20 dark:border-surface-600 dark:bg-surface-700">
                                <span class="text-sm text-surface-700 dark:text-surface-300"><?= htmlspecialchars($itemLabel, ENT_QUOTES) ?></span>
                            </label>
                            <?php endforeach; ?>
                            <!-- Custom items -->
                            <template x-for="(item, idx) in (form._custom_<?= $name ?> || [])" :key="'custom_' + idx">
                                <label class="flex items-center gap-2.5 cursor-pointer group">
                                    <input type="checkbox"
                                        :checked="(form.<?= $name ?> || []).includes(item)"
                                        @change="
                                            if (!form.<?= $name ?>) form.<?= $name ?> = [];
                                            if ($event.target.checked) {
                                                if (!form.<?= $name ?>.includes(item)) form.<?= $name ?>.push(item);
                                            } else {
                                                form.<?= $name ?> = form.<?= $name ?>.filter(v => v !== item);
                                                form._custom_<?= $name ?>.splice(idx, 1);
                                            }
                                        "
                                        class="h-4 w-4 rounded border-surface-300 text-primary-600 focus:ring-primary-500/20 dark:border-surface-600 dark:bg-surface-700" checked>
                                    <span class="text-sm text-surface-700 dark:text-surface-300" x-text="item"></span>
                                    <button type="button" @click="form.<?= $name ?> = (form.<?= $name ?>||[]).filter(v => v !== item); form._custom_<?= $name ?>.splice(idx, 1)"
                                        class="ml-auto text-red-400 hover:text-red-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                                </label>
                            </template>
                            <!-- Add custom button -->
                            <div class="flex items-center gap-2 mt-2" x-data="{ adding: false, newItem: '' }">
                                <template x-if="!adding">
                                    <button type="button" @click="adding = true" class="inline-flex items-center gap-1.5 text-xs font-medium text-primary-500 hover:text-primary-400 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Add Custom
                                    </button>
                                </template>
                                <template x-if="adding">
                                    <div class="flex items-center gap-2 w-full">
                                        <input type="text" x-model="newItem" placeholder="Custom item name" @keydown.enter.prevent="
                                            if (newItem.trim()) {
                                                if (!$data.form._custom_<?= $name ?>) $data.form._custom_<?= $name ?> = [];
                                                $data.form._custom_<?= $name ?>.push(newItem.trim());
                                                if (!$data.form.<?= $name ?>) $data.form.<?= $name ?> = [];
                                                $data.form.<?= $name ?>.push(newItem.trim());
                                                newItem = ''; adding = false;
                                            }
                                        " class="flex-1 rounded-lg border border-surface-200 bg-white px-3 py-1.5 text-sm dark:border-surface-700 dark:bg-surface-800 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 focus:outline-none">
                                        <button type="button" @click="
                                            if (newItem.trim()) {
                                                if (!$data.form._custom_<?= $name ?>) $data.form._custom_<?= $name ?> = [];
                                                $data.form._custom_<?= $name ?>.push(newItem.trim());
                                                if (!$data.form.<?= $name ?>) $data.form.<?= $name ?> = [];
                                                $data.form.<?= $name ?>.push(newItem.trim());
                                                newItem = ''; adding = false;
                                            }
                                        " class="rounded-lg bg-primary-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-primary-700 transition-colors">Add</button>
                                        <button type="button" @click="adding = false; newItem = ''" class="text-surface-400 hover:text-surface-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                    <?php elseif ($type === 'multiselect'): ?>
                        <div class="relative" x-data="{ msOpen_<?= $name ?>: false }" @click.away="msOpen_<?= $name ?> = false">
                            <div @click="msOpen_<?= $name ?> = !msOpen_<?= $name ?>"
                                 class="min-h-[46px] w-full cursor-pointer rounded-xl border border-surface-200 bg-white px-4 py-2.5 text-sm shadow-soft focus-within:border-primary-400 dark:border-surface-700 dark:bg-surface-800 transition-all flex flex-wrap gap-1.5 items-center">
                                <template x-if="(form.<?= $name ?> || []).length === 0">
                                    <span class="text-surface-400">Select facilities...</span>
                                </template>
                                <template x-for="val in (form.<?= $name ?> || [])" :key="val">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-primary-100 px-2.5 py-0.5 text-xs font-semibold text-primary-700 dark:bg-primary-500/10 dark:text-primary-400">
                                        <span x-text="(ms_<?= $name ?> || []).find(o => String(o.value) === String(val))?.label || val"></span>
                                        <button type="button" @click.stop="form.<?= $name ?> = (form.<?= $name ?> || []).filter(v => String(v) !== String(val))"
                                                class="ml-0.5 text-primary-500 hover:text-primary-700 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </span>
                                </template>
                                <svg class="ml-auto w-4 h-4 text-surface-400 flex-shrink-0" :class="{ 'rotate-180': msOpen_<?= $name ?> }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                            <div x-show="msOpen_<?= $name ?>" x-transition
                                 class="absolute z-20 mt-1.5 w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 shadow-medium overflow-hidden">
                                <div class="max-h-52 overflow-y-auto divide-y divide-surface-100 dark:divide-surface-700">
                                    <template x-if="!(ms_<?= $name ?> || []).length">
                                        <div class="px-4 py-3 text-sm text-surface-400 text-center">No facilities available</div>
                                    </template>
                                    <template x-for="opt in (ms_<?= $name ?> || [])" :key="opt.value">
                                        <label class="flex items-center gap-3 px-4 py-2.5 hover:bg-surface-50 dark:hover:bg-surface-700 cursor-pointer transition-colors">
                                            <input type="checkbox"
                                                   :checked="(form.<?= $name ?> || []).some(v => String(v) === String(opt.value))"
                                                   @change="
                                                       if (!form.<?= $name ?>) form.<?= $name ?> = [];
                                                       if ($event.target.checked) {
                                                           if (!form.<?= $name ?>.some(v => String(v) === String(opt.value))) form.<?= $name ?>.push(opt.value);
                                                       } else {
                                                           form.<?= $name ?> = form.<?= $name ?>.filter(v => String(v) !== String(opt.value));
                                                       }
                                                   "
                                                   class="h-4 w-4 rounded border-surface-300 text-primary-600 focus:ring-primary-500/20 dark:border-surface-600 dark:bg-surface-700">
                                            <span class="text-sm text-surface-700 dark:text-surface-300" x-text="opt.label"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </div>

                    <?php elseif ($type === 'file'): ?>
                        <?php $accept = $field['accept'] ?? 'image/*'; ?>
                        <div class="space-y-3">
                            <template x-if="form._preview_<?= $name ?>">
                                <div class="relative inline-block">
                                    <img :src="form._preview_<?= $name ?>" class="h-24 w-24 rounded-xl object-cover border-2 border-surface-200 dark:border-surface-700 shadow-soft">
                                    <button type="button" @click="form._preview_<?= $name ?> = ''; form.<?= $name ?> = ''; form._file_<?= $name ?> = null"
                                        class="absolute -top-2 -right-2 rounded-full bg-red-500 p-1 text-white shadow hover:bg-red-600 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </template>
                            <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border-2 border-dashed border-surface-200 bg-surface-50 px-4 py-6 text-sm text-surface-500 hover:border-primary-400 hover:bg-primary-50/30 dark:border-surface-700 dark:bg-surface-800/50 dark:hover:border-primary-500/50 dark:hover:bg-surface-800 transition-all"
                                x-show="!form._preview_<?= $name ?>">
                                <svg class="w-6 h-6 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                <span>Click to upload or drag & drop</span>
                                <input type="file" accept="<?= htmlspecialchars($accept, ENT_QUOTES) ?>" class="hidden" @change="
                                    const file = $event.target.files[0];
                                    if (file) {
                                        form._file_<?= $name ?> = file;
                                        const reader = new FileReader();
                                        reader.onload = e => form._preview_<?= $name ?> = e.target.result;
                                        reader.readAsDataURL(file);
                                    }
                                ">
                            </label>
                        </div>

                    <?php elseif ($type === 'custom' && $name === 'operating_hours'): ?>
                        <!-- Operating Hours: Day-by-day time picker -->
                        <div class="space-y-3">
                            <template x-for="(day, key) in form.hours" :key="key">
                                <div class="flex items-center gap-3 rounded-xl border border-surface-200 dark:border-surface-700 px-4 py-3 bg-white dark:bg-surface-800">
                                    <span class="w-24 text-sm font-medium text-surface-700 dark:text-surface-300" x-text="form.dayLabels[key] || key"></span>
                                    <template x-if="!day.closed">
                                        <div class="flex items-center gap-2 flex-1">
                                            <select x-model="day.open" class="rounded-lg border border-surface-200 bg-white px-3 py-2 text-sm dark:border-surface-700 dark:bg-surface-800 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 focus:outline-none">
                                                <template x-for="opt in timeOptions()" :key="opt.value">
                                                    <option :value="opt.value" x-text="opt.label"></option>
                                                </template>
                                            </select>
                                            <span class="text-surface-400 text-sm">to</span>
                                            <select x-model="day.close" class="rounded-lg border border-surface-200 bg-white px-3 py-2 text-sm dark:border-surface-700 dark:bg-surface-800 dark:text-white focus:border-primary-400 focus:ring-1 focus:ring-primary-500/20 focus:outline-none">
                                                <template x-for="opt in timeOptions()" :key="opt.value">
                                                    <option :value="opt.value" x-text="opt.label"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </template>
                                    <template x-if="day.closed">
                                        <span class="flex-1 text-sm text-surface-400 italic">Closed</span>
                                    </template>
                                    <label class="flex items-center gap-2 cursor-pointer ml-auto">
                                        <input type="checkbox" x-model="day.closed" class="h-4 w-4 rounded border-surface-300 text-red-500 focus:ring-red-500/20 dark:border-surface-600 dark:bg-surface-700">
                                        <span class="text-xs text-surface-500">Closed</span>
                                    </label>
                                </div>
                            </template>
                        </div>

                    <?php elseif ($type === 'custom' && $name === 'social_links'): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-surface-600 dark:text-surface-400 mb-1.5">Instagram URL</label>
                                <input type="url" x-model="form.instagram_url" placeholder="https://instagram.com/yourfacility"
                                       class="w-full rounded-xl border border-surface-200 bg-white dark:bg-surface-800 dark:border-surface-700 dark:text-white px-3.5 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-all">
                                <p class="text-xs text-surface-400 mt-1">Leave blank if not applicable</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-surface-600 dark:text-surface-400 mb-1.5">Facebook URL</label>
                                <input type="url" x-model="form.facebook_url" placeholder="https://facebook.com/yourfacility"
                                       class="w-full rounded-xl border border-surface-200 bg-white dark:bg-surface-800 dark:border-surface-700 dark:text-white px-3.5 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-all">
                                <p class="text-xs text-surface-400 mt-1">Leave blank if not applicable</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-surface-600 dark:text-surface-400 mb-1.5">YouTube URL</label>
                                <input type="url" x-model="form.youtube_url" placeholder="https://youtube.com/@yourfacility"
                                       class="w-full rounded-xl border border-surface-200 bg-white dark:bg-surface-800 dark:border-surface-700 dark:text-white px-3.5 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-all">
                                <p class="text-xs text-surface-400 mt-1">Leave blank if not applicable</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-xs font-semibold text-surface-600 dark:text-surface-400 mb-1.5">Hero Background Video URL</label>
                            <input type="url" x-model="form.hero_video_url" placeholder="https://cdn.example.com/video.mp4"
                                   class="w-full rounded-xl border border-surface-200 bg-white dark:bg-surface-800 dark:border-surface-700 dark:text-white px-3.5 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-all">
                            <p class="text-xs text-surface-400 mt-1">Direct link to an MP4 video displayed in the hero section of the public site. Keep under 10 seconds and ~5 MB for best performance.</p>
                        </div>

                    <?php elseif ($type === 'custom' && $name === 'twilio_settings'): ?>
                        <p class="text-xs text-surface-400 -mt-1 mb-3">Configure Twilio to send SMS notifications to players. Obtain credentials from <a href="https://console.twilio.com" target="_blank" class="text-primary-500 hover:underline">console.twilio.com</a>.</p>
                        <label class="inline-flex items-center gap-3 cursor-pointer mb-4">
                            <div class="relative">
                                <input type="checkbox" x-model="form.twilio_enabled" class="peer sr-only">
                                <div class="h-5 w-9 rounded-full bg-surface-200 peer-checked:bg-primary-500 dark:bg-surface-700 transition-colors"></div>
                                <div class="absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow-sm transform peer-checked:translate-x-4 transition-transform"></div>
                            </div>
                            <span class="text-sm font-medium text-surface-600 dark:text-surface-400">Enable Twilio SMS for this facility</span>
                        </label>
                        <div x-show="form.twilio_enabled" x-transition class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-surface-600 dark:text-surface-400 mb-1.5">Account SID <span class="text-red-500">*</span></label>
                                <input type="text" x-model="form.twilio_sid" placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
                                       class="w-full rounded-xl border border-surface-200 bg-white dark:bg-surface-800 dark:border-surface-700 dark:text-white px-3.5 py-2.5 text-sm font-mono outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-all">
                                <p class="text-xs text-surface-400 mt-1">Found in your Twilio Console under &#34;Account Info&#34;</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-surface-600 dark:text-surface-400 mb-1.5">Auth Token <span class="text-red-500">*</span></label>
                                <input type="password" x-model="form.twilio_auth_token" placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;"
                                       class="w-full rounded-xl border border-surface-200 bg-white dark:bg-surface-800 dark:border-surface-700 dark:text-white px-3.5 py-2.5 text-sm font-mono outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-all">
                                <p class="text-xs text-surface-400 mt-1">Your Twilio auth token — keep this secret</p>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-surface-600 dark:text-surface-400 mb-1.5">From Phone Number <span class="text-red-500">*</span></label>
                                <input type="tel" x-model="form.twilio_from_number" placeholder="+15551234567"
                                       class="w-full sm:w-72 rounded-xl border border-surface-200 bg-white dark:bg-surface-800 dark:border-surface-700 dark:text-white px-3.5 py-2.5 text-sm font-mono outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition-all">
                                <p class="text-xs text-surface-400 mt-1">Twilio-provisioned number in E.164 format (e.g. +15551234567)</p>
                            </div>
                        </div>

                    <?php else: ?>
                        <input type="<?= htmlspecialchars($type, ENT_QUOTES) ?>"
                            x-model="form.<?= $name ?>"
                            placeholder="<?= htmlspecialchars($placeholder, ENT_QUOTES) ?>"
                            <?= isset($field['step']) ? 'step="' . $field['step'] . '"' : '' ?>
                            <?= isset($field['min']) ? 'min="' . $field['min'] . '"' : '' ?>
                            <?= isset($field['max']) ? 'max="' . $field['max'] . '"' : '' ?>
                            class="w-full rounded-xl border border-surface-200 bg-white px-4 py-3 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 focus:outline-none dark:border-surface-700 dark:bg-surface-800 dark:text-white placeholder:text-surface-400 transition-all"
                            <?= $required ? 'required' : '' ?>>
                    <?php endif; ?>

                    <?php if ($helpText && $type !== 'checkbox'): ?>
                        <p class="mt-1.5 text-xs text-surface-400 dark:text-surface-500"><?= htmlspecialchars($helpText, ENT_QUOTES) ?></p>
                    <?php endif; ?>

                    <template x-if="errors?.<?= $name ?>">
                        <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-text="errors.<?= $name ?>[0] || errors.<?= $name ?>"></span>
                        </p>
                    </template>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-3 border-t border-surface-100 dark:border-surface-800 px-6 py-4 bg-surface-50/50 dark:bg-surface-800/30">
            <a href="<?= htmlspecialchars($backUrl, ENT_QUOTES) ?>"
                class="rounded-xl border border-surface-200 dark:border-surface-700 px-5 py-2.5 text-sm font-semibold text-surface-600 dark:text-surface-300 hover:bg-surface-50 dark:hover:bg-surface-800 transition-all">Cancel</a>
            <button type="submit" :disabled="submitting"
                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-2.5 text-sm font-semibold text-white hover:from-primary-700 hover:to-primary-800 disabled:opacity-50 shadow-soft hover:shadow-medium transition-all">
                <template x-if="submitting">
                    <div class="relative h-4 w-4">
                        <div class="absolute inset-0 rounded-full border-2 border-white/30"></div>
                        <div class="absolute inset-0 rounded-full border-2 border-transparent border-t-white animate-spin"></div>
                    </div>
                </template>
                <?= htmlspecialchars($submitLabel, ENT_QUOTES) ?>
            </button>
        </div>
    </form>
</div>
