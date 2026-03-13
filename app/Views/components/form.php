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
                            <select x-model="form.<?= $name ?>"
                                class="w-full rounded-xl border border-surface-200 bg-white px-4 py-3 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 focus:outline-none dark:border-surface-700 dark:bg-surface-800 dark:text-white appearance-none transition-all"
                                <?= $required ? 'required' : '' ?>>
                                <option value="">Select...</option>
                                <?php foreach (($field['options'] ?? []) as $val => $optLabel): ?>
                                    <option value="<?= htmlspecialchars($val, ENT_QUOTES) ?>"><?= htmlspecialchars($optLabel, ENT_QUOTES) ?></option>
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

                    <?php elseif ($type === 'json'): ?>
                        <textarea x-model="form.<?= $name ?>"
                            rows="<?= $field['rows'] ?? 5 ?>"
                            placeholder="<?= htmlspecialchars($placeholder ?: '{}', ENT_QUOTES) ?>"
                            class="w-full rounded-xl border border-surface-200 bg-white px-4 py-3 text-sm shadow-soft focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 focus:outline-none dark:border-surface-700 dark:bg-surface-800 dark:text-white font-mono placeholder:text-surface-400 transition-all"
                            <?= $required ? 'required' : '' ?>></textarea>

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
