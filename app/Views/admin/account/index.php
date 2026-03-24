<?php
$title = 'My Account';
$breadcrumbs = [['label' => 'My Account']];
ob_start();
$sqAppId = htmlspecialchars($_ENV['SQUARE_APPLICATION_ID'] ?? '', ENT_QUOTES);
$sqLocId = htmlspecialchars($_ENV['SQUARE_LOCATION_ID'] ?? '', ENT_QUOTES);
$sqEnv   = ($_ENV['SQUARE_ENVIRONMENT'] ?? 'sandbox') === 'production' ? 'production' : 'sandbox';
?>

<div x-data="accountPage()" x-init="init()">

    <!-- ─── Tab Bar ─── -->
    <div class="flex bg-surface-100 dark:bg-surface-800/50 p-1 rounded-2xl mb-6 gap-1 overflow-x-auto">
        <button @click="setTab('profile')"
                class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 text-sm font-medium rounded-xl whitespace-nowrap transition-all"
                :class="activeTab==='profile' ? 'bg-white dark:bg-surface-900 text-surface-900 dark:text-white shadow-sm' : 'text-surface-500 hover:text-surface-700 dark:hover:text-surface-300'">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
            Profile
        </button>
        <button @click="setTab('organization')"
                class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 text-sm font-medium rounded-xl whitespace-nowrap transition-all"
                :class="activeTab==='organization' ? 'bg-white dark:bg-surface-900 text-surface-900 dark:text-white shadow-sm' : 'text-surface-500 hover:text-surface-700 dark:hover:text-surface-300'">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
            Organization
        </button>
        <button @click="setTab('subscription')"
                class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 text-sm font-medium rounded-xl whitespace-nowrap transition-all"
                :class="activeTab==='subscription' ? 'bg-white dark:bg-surface-900 text-surface-900 dark:text-white shadow-sm' : 'text-surface-500 hover:text-surface-700 dark:hover:text-surface-300'">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
            Subscription
        </button>
        <button @click="setTab('invoices')"
                class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 text-sm font-medium rounded-xl whitespace-nowrap transition-all"
                :class="activeTab==='invoices' ? 'bg-white dark:bg-surface-900 text-surface-900 dark:text-white shadow-sm' : 'text-surface-500 hover:text-surface-700 dark:hover:text-surface-300'">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
            Invoices
        </button>
        <button @click="setTab('bank')"
                class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 text-sm font-medium rounded-xl whitespace-nowrap transition-all"
                :class="activeTab==='bank' ? 'bg-white dark:bg-surface-900 text-surface-900 dark:text-white shadow-sm' : 'text-surface-500 hover:text-surface-700 dark:hover:text-surface-300'">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/></svg>
            Payouts
        </button>
    </div>

    <!-- ═══════════ ORGANIZATION TAB ═══════════ -->
    <div x-show="activeTab==='organization'" x-cloak>
        <div x-show="orgMessage" x-transition class="mb-4 p-3.5 rounded-xl text-sm"
             :class="orgMessageType === 'success' ? 'bg-emerald-500/10 border border-emerald-500/20 text-emerald-400' : 'bg-red-500/10 border border-red-500/20 text-red-400'"
             x-text="orgMessage"></div>

        <!-- Organization Info Card -->
        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft mb-4">
            <div class="px-5 py-4 border-b border-surface-100 dark:border-surface-800 flex items-center justify-between">
                <div>
                    <h5 class="text-sm font-bold text-surface-900 dark:text-white">Organization Details</h5>
                    <p class="text-xs text-surface-400 mt-0.5">Update your organization's public information and settings.</p>
                </div>
                <template x-if="org.status">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize"
                          :class="{'bg-emerald-500/10 text-emerald-500': org.status==='active', 'bg-amber-500/10 text-amber-500': org.status==='trial', 'bg-red-500/10 text-red-500': org.status==='suspended' || org.status==='cancelled', 'bg-blue-500/10 text-blue-400': org.status==='inactive'}"
                          x-text="org.status"></span>
                </template>
            </div>
            <div class="p-5">
                <div x-show="orgLoading" class="animate-pulse space-y-3">
                    <div class="h-9 bg-surface-200 dark:bg-surface-800 rounded-xl"></div>
                    <div class="h-9 bg-surface-200 dark:bg-surface-800 rounded-xl"></div>
                    <div class="h-9 bg-surface-200 dark:bg-surface-800 rounded-xl"></div>
                </div>
                <form x-show="!orgLoading" @submit.prevent="saveOrganization" class="space-y-5">

                    <!-- Basic Info -->
                    <div>
                        <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider mb-3">Basic Information</p>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">Organization Name <span class="text-red-400">*</span></label>
                                <input type="text" x-model="org.name" required maxlength="255"
                                       class="w-full px-3 py-2.5 rounded-xl bg-white dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700/60 text-sm text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500/50 focus:ring-2 focus:ring-primary-500/20 transition-colors"
                                       placeholder="My Pickleball Club">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">Contact Email <span class="text-red-400">*</span></label>
                                <input type="email" x-model="org.email" required maxlength="255"
                                       class="w-full px-3 py-2.5 rounded-xl bg-white dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700/60 text-sm text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500/50 focus:ring-2 focus:ring-primary-500/20 transition-colors"
                                       placeholder="contact@yourclub.com">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">Phone</label>
                                <input type="tel" x-model="org.phone"
                                       class="w-full px-3 py-2.5 rounded-xl bg-white dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700/60 text-sm text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500/50 focus:ring-2 focus:ring-primary-500/20 transition-colors"
                                       placeholder="(555) 123-4567">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">Website</label>
                                <input type="url" x-model="org.website"
                                       class="w-full px-3 py-2.5 rounded-xl bg-white dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700/60 text-sm text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500/50 focus:ring-2 focus:ring-primary-500/20 transition-colors"
                                       placeholder="https://yoursite.com">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">Timezone</label>
                                <select x-model="org.timezone"
                                        class="w-full px-3 py-2.5 rounded-xl bg-white dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700/60 text-sm text-surface-900 dark:text-white focus:border-primary-500/50 focus:ring-2 focus:ring-primary-500/20 transition-colors">
                                    <option value="">Select timezone...</option>
                                    <option value="America/New_York">Eastern Time (ET)</option>
                                    <option value="America/Chicago">Central Time (CT)</option>
                                    <option value="America/Denver">Mountain Time (MT)</option>
                                    <option value="America/Phoenix">Arizona (no DST)</option>
                                    <option value="America/Los_Angeles">Pacific Time (PT)</option>
                                    <option value="America/Anchorage">Alaska Time (AKT)</option>
                                    <option value="Pacific/Honolulu">Hawaii Time (HT)</option>
                                    <option value="UTC">UTC</option>
                                    <option value="Europe/London">London (GMT/BST)</option>
                                    <option value="Europe/Berlin">Central European (CET)</option>
                                    <option value="Asia/Dubai">Dubai (GST)</option>
                                    <option value="Asia/Kolkata">India (IST)</option>
                                    <option value="Asia/Singapore">Singapore (SGT)</option>
                                    <option value="Australia/Sydney">Sydney (AEST)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Address -->
                    <div>
                        <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider mb-3">Address</p>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">Address Line 1</label>
                                <input type="text" x-model="org.address_line1" maxlength="255"
                                       class="w-full px-3 py-2.5 rounded-xl bg-white dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700/60 text-sm text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500/50 focus:ring-2 focus:ring-primary-500/20 transition-colors"
                                       placeholder="123 Main Street">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">Address Line 2</label>
                                <input type="text" x-model="org.address_line2" maxlength="255"
                                       class="w-full px-3 py-2.5 rounded-xl bg-white dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700/60 text-sm text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500/50 focus:ring-2 focus:ring-primary-500/20 transition-colors"
                                       placeholder="Suite 100">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">City</label>
                                <input type="text" x-model="org.city" maxlength="100"
                                       class="w-full px-3 py-2.5 rounded-xl bg-white dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700/60 text-sm text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500/50 focus:ring-2 focus:ring-primary-500/20 transition-colors"
                                       placeholder="Tampa">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">State / Province</label>
                                <input type="text" x-model="org.state" maxlength="100"
                                       class="w-full px-3 py-2.5 rounded-xl bg-white dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700/60 text-sm text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500/50 focus:ring-2 focus:ring-primary-500/20 transition-colors"
                                       placeholder="FL">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">ZIP / Postal Code</label>
                                <input type="text" x-model="org.zip_code" maxlength="20"
                                       class="w-full px-3 py-2.5 rounded-xl bg-white dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700/60 text-sm text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500/50 focus:ring-2 focus:ring-primary-500/20 transition-colors"
                                       placeholder="33601">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">Country</label>
                                <select x-model="org.country"
                                        class="w-full px-3 py-2.5 rounded-xl bg-white dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700/60 text-sm text-surface-900 dark:text-white focus:border-primary-500/50 focus:ring-2 focus:ring-primary-500/20 transition-colors">
                                    <option value="">Select country...</option>
                                    <option value="US">United States</option>
                                    <option value="CA">Canada</option>
                                    <option value="GB">United Kingdom</option>
                                    <option value="AU">Australia</option>
                                    <option value="NZ">New Zealand</option>
                                    <option value="DE">Germany</option>
                                    <option value="FR">France</option>
                                    <option value="ES">Spain</option>
                                    <option value="IN">India</option>
                                    <option value="SG">Singapore</option>
                                    <option value="AE">United Arab Emirates</option>
                                    <option value="PK">Pakistan</option>
                                    <option value="PH">Philippines</option>
                                    <option value="MX">Mexico</option>
                                    <option value="BR">Brazil</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Read-only info -->
                    <div class="rounded-xl bg-surface-50 dark:bg-surface-800/30 border border-surface-200 dark:border-surface-700/40 p-4">
                        <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider mb-3">Account Info</p>
                        <div class="grid sm:grid-cols-3 gap-4">
                            <div>
                                <p class="text-xs text-surface-400 mb-0.5">URL Slug</p>
                                <p class="text-sm font-mono font-medium text-surface-700 dark:text-surface-300" x-text="org.slug || '—'"></p>
                                <p class="text-xs text-surface-400 mt-0.5">Contact support to change</p>
                            </div>
                            <div>
                                <p class="text-xs text-surface-400 mb-0.5">Member Since</p>
                                <p class="text-sm font-medium text-surface-700 dark:text-surface-300" x-text="org.created_at ? new Date(org.created_at).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'}) : '—'"></p>
                            </div>
                            <div x-show="org.trial_ends_at">
                                <p class="text-xs text-surface-400 mb-0.5">Trial Ends</p>
                                <p class="text-sm font-medium"
                                   :class="new Date(org.trial_ends_at) > new Date() ? 'text-amber-500' : 'text-red-400'"
                                   x-text="org.trial_ends_at ? new Date(org.trial_ends_at).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'}) : ''"></p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-1">
                        <button type="submit" :disabled="savingOrg"
                                class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 disabled:opacity-50 rounded-xl transition-colors">
                            <span x-show="!savingOrg">Save Changes</span>
                            <span x-show="savingOrg" class="flex items-center gap-1.5"><svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ═══════════ PROFILE TAB ═══════════ -->
    <div x-show="activeTab==='profile'" x-cloak>
        <div x-show="message" x-transition class="mb-4 p-3.5 rounded-xl text-sm"
             :class="messageType === 'success' ? 'bg-emerald-500/10 border border-emerald-500/20 text-emerald-400' : 'bg-red-500/10 border border-red-500/20 text-red-400'"
             x-text="message"></div>

        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft mb-4">
            <div class="px-5 py-4 border-b border-surface-100 dark:border-surface-800">
                <h5 class="text-sm font-bold text-surface-900 dark:text-white">Profile Information</h5>
            </div>
            <div class="p-5">
                <div x-show="loadingProfile" class="animate-pulse space-y-3">
                    <div class="h-9 bg-surface-200 dark:bg-surface-800 rounded-xl"></div>
                    <div class="h-9 bg-surface-200 dark:bg-surface-800 rounded-xl"></div>
                </div>
                <form x-show="!loadingProfile" @submit.prevent="saveProfile" class="space-y-4">
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">First Name</label>
                            <input type="text" x-model="profile.first_name" required class="w-full px-3 py-2.5 rounded-xl bg-white dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700/60 text-sm text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500/50 focus:ring-2 focus:ring-primary-500/20 transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">Last Name</label>
                            <input type="text" x-model="profile.last_name" required class="w-full px-3 py-2.5 rounded-xl bg-white dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700/60 text-sm text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500/50 focus:ring-2 focus:ring-primary-500/20 transition-colors">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">Email</label>
                        <input type="email" x-model="profile.email" disabled class="w-full px-3 py-2.5 rounded-xl bg-surface-100 dark:bg-surface-800/30 border border-surface-200 dark:border-surface-700/40 text-sm text-surface-400 cursor-not-allowed">
                        <p class="mt-1 text-xs text-surface-400">Contact support to change your email address.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">Phone</label>
                        <input type="tel" x-model="profile.phone" class="w-full px-3 py-2.5 rounded-xl bg-white dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700/60 text-sm text-surface-900 dark:text-white placeholder-surface-400 focus:border-primary-500/50 focus:ring-2 focus:ring-primary-500/20 transition-colors" placeholder="(555) 123-4567">
                    </div>
                    <div class="flex justify-end pt-1">
                        <button type="submit" :disabled="savingProfile" class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 disabled:opacity-50 rounded-xl transition-colors">
                            <span x-show="!savingProfile">Save Changes</span>
                            <span x-show="savingProfile" class="flex items-center gap-1.5"><svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft mb-4">
            <div class="px-5 py-4 border-b border-surface-100 dark:border-surface-800">
                <h5 class="text-sm font-bold text-surface-900 dark:text-white">Change Password</h5>
            </div>
            <div class="p-5">
                <form @submit.prevent="changePassword" class="space-y-4">
                    <div class="grid sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">Current Password</label>
                            <input type="password" x-model="passwords.current" required class="w-full px-3 py-2.5 rounded-xl bg-white dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700/60 text-sm text-surface-900 dark:text-white focus:border-primary-500/50 focus:ring-2 focus:ring-primary-500/20 transition-colors" placeholder="••••••••">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">New Password</label>
                            <input type="password" x-model="passwords.new_password" required minlength="8" class="w-full px-3 py-2.5 rounded-xl bg-white dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700/60 text-sm text-surface-900 dark:text-white focus:border-primary-500/50 focus:ring-2 focus:ring-primary-500/20 transition-colors" placeholder="Min. 8 chars">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1.5">Confirm Password</label>
                            <input type="password" x-model="passwords.confirm" required class="w-full px-3 py-2.5 rounded-xl bg-white dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700/60 text-sm text-surface-900 dark:text-white focus:border-primary-500/50 focus:ring-2 focus:ring-primary-500/20 transition-colors" placeholder="Repeat new password">
                        </div>
                    </div>
                    <div class="flex justify-end pt-1">
                        <button type="submit" :disabled="savingPassword" class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 disabled:opacity-50 rounded-xl transition-colors">
                            <span x-show="!savingPassword">Update Password</span>
                            <span x-show="savingPassword" class="flex items-center gap-1.5"><svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>Updating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="rounded-2xl border border-red-500/20 bg-red-500/5 px-5 py-4 flex items-center justify-between gap-4">
            <div>
                <h5 class="text-sm font-bold text-surface-900 dark:text-white">Danger Zone</h5>
                <p class="text-xs text-surface-500 mt-0.5">Permanently delete your account. This cannot be undone.</p>
            </div>
            <button @click="if(confirm('Are you sure you want to delete your account? This cannot be undone.')) deleteAccount()"
                    class="flex-shrink-0 inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-500 border border-red-500/30 hover:bg-red-500/10 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                Delete Account
            </button>
        </div>
    </div>

    <!-- ═══════════ SUBSCRIPTION TAB ═══════════ -->
    <div x-show="activeTab==='subscription'" x-cloak>
        <div x-show="subMessage" x-transition class="mb-4 p-3.5 rounded-xl text-sm"
             :class="subMessageType === 'success' ? 'bg-emerald-500/10 border border-emerald-500/20 text-emerald-500' : 'bg-red-500/10 border border-red-500/20 text-red-400'"
             x-text="subMessage"></div>

        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft mb-5">
            <div class="px-5 py-4 border-b border-surface-100 dark:border-surface-800 flex flex-wrap items-center justify-between gap-3">
                <h5 class="text-sm font-bold text-surface-900 dark:text-white">Current Plan</h5>
                <div class="flex items-center gap-2.5 text-sm">
                    <span :class="!annual ? 'text-surface-800 dark:text-white font-medium' : 'text-surface-400'">Monthly</span>
                    <button @click="annual = !annual" class="relative h-5 w-9 rounded-full transition-colors flex-shrink-0"
                            :class="annual ? 'bg-primary-600' : 'bg-surface-300 dark:bg-surface-700'">
                        <span class="absolute top-0.5 left-0.5 h-4 w-4 rounded-full bg-white shadow transition-transform"
                              :class="annual ? 'translate-x-4' : ''"></span>
                    </button>
                    <span :class="annual ? 'text-surface-800 dark:text-white font-medium' : 'text-surface-400'">
                        Annual <span class="text-xs font-semibold text-emerald-500">–17%</span>
                    </span>
                </div>
            </div>
            <div class="p-5">
                <div x-show="subLoading" class="animate-pulse flex gap-4 items-center">
                    <div class="h-7 w-32 bg-surface-200 dark:bg-surface-800 rounded"></div>
                    <div class="h-5 w-16 bg-surface-200 dark:bg-surface-800 rounded-full"></div>
                </div>
                <template x-if="!subLoading && currentSub">
                    <div class="flex flex-wrap items-center gap-4">
                        <div>
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="text-xl font-bold text-surface-900 dark:text-white" x-text="currentSub.plan_name"></span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold"
                                      :class="{'bg-emerald-500/10 text-emerald-500': currentSub.status === 'active', 'bg-amber-500/10 text-amber-500': currentSub.status === 'pending', 'bg-red-500/10 text-red-500': currentSub.status === 'cancelled'}"
                                      x-text="currentSub.status"></span>
                            </div>
                            <div class="flex items-baseline gap-1">
                                <span class="text-2xl font-extrabold text-surface-900 dark:text-white" x-text="'$' + parseFloat(currentSub.price || 0).toFixed(2)"></span>
                                <span class="text-sm text-surface-400" x-text="'/' + (currentSub.billing_cycle || 'month')"></span>
                            </div>
                        </div>
                        <div x-show="currentSub.current_period_end" class="ml-auto text-right">
                            <p class="text-xs text-surface-400">Next billing</p>
                            <p class="text-sm font-medium text-surface-700 dark:text-surface-300" x-text="new Date(currentSub.current_period_end).toLocaleDateString()"></p>
                        </div>
                    </div>
                </template>
                <template x-if="!subLoading && !currentSub">
                    <p class="text-sm text-surface-400">No active subscription. Choose a plan below to get started.</p>
                </template>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-4 mb-4">
            <template x-for="plan in plans" :key="plan.id">
                <div class="rounded-2xl border p-5 flex flex-col transition-all"
                     :class="isCurrent(plan) ? 'border-primary-500/40 bg-primary-500/5 ring-1 ring-primary-500/30' : 'border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900'">
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="text-base font-bold text-surface-900 dark:text-white" x-text="plan.name"></h3>
                        <span x-show="isCurrent(plan)" class="px-1.5 py-0.5 rounded-full bg-primary-500/10 text-primary-500 text-xs font-semibold">Current</span>
                    </div>
                    <p class="text-xs text-surface-400 mb-3" x-text="plan.description"></p>
                    <div class="flex items-baseline gap-1 mb-4">
                        <span class="text-2xl font-extrabold text-surface-900 dark:text-white"
                              x-text="'$' + (annual ? Math.round(plan.price_yearly || 0) : (plan.price_monthly > 0 ? parseFloat(plan.price_monthly).toFixed(2).replace(/\.00$/, '') : '0'))"></span>
                        <template x-if="plan.price_monthly > 0">
                            <span class="text-xs text-surface-400" x-text="annual ? '/year' : '/month'"></span>
                        </template>
                        <template x-if="plan.price_monthly == 0">
                            <span class="text-xs text-surface-400">forever</span>
                        </template>
                    </div>
                    <div class="flex flex-wrap gap-1.5 mb-3">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-surface-100 dark:bg-surface-800 rounded-md text-xs text-surface-600 dark:text-surface-400">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span x-text="plan.max_facilities ? plan.max_facilities + ' facilit' + (plan.max_facilities == 1 ? 'y' : 'ies') : 'Unlimited'"></span>
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-surface-100 dark:bg-surface-800 rounded-md text-xs text-surface-600 dark:text-surface-400">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span x-text="plan.max_users ? plan.max_users + ' users' : 'Unlimited'"></span>
                        </span>
                    </div>
                    <ul class="space-y-1.5 mb-4 flex-1">
                        <template x-for="feat in getPlanFeatureLabels(plan)" :key="feat">
                            <li class="flex items-start gap-1.5 text-xs text-surface-600 dark:text-surface-400">
                                <svg class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span x-text="feat"></span>
                            </li>
                        </template>
                    </ul>
                    <template x-if="isCurrent(plan)">
                        <button disabled class="w-full py-2 rounded-xl text-xs font-semibold bg-surface-100 dark:bg-surface-800 text-surface-400 cursor-not-allowed">Current Plan</button>
                    </template>
                    <template x-if="!isCurrent(plan)">
                        <button @click="changePlan(plan)" :disabled="changingPlan || subLoading"
                                class="w-full py-2 rounded-xl text-xs font-semibold transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                                :class="getPlanAction(plan).cls"
                                x-text="getPlanAction(plan).label">
                        </button>
                    </template>
                </div>
            </template>
        </div>

        <!-- Downgrade Modal -->
        <div x-show="showDowngradeModal" x-transition.opacity
             class="fixed inset-0 z-[9999] bg-surface-950/60 backdrop-blur-sm flex items-center justify-center p-4"
             x-cloak @click.self="showDowngradeModal=false">
            <div x-show="showDowngradeModal" x-transition
                 class="w-full max-w-md bg-white dark:bg-surface-900 rounded-2xl border border-surface-200 dark:border-surface-800 p-6 shadow-2xl">
                <div class="text-center mb-4">
                    <div class="w-12 h-12 mx-auto rounded-full bg-amber-100 dark:bg-amber-500/10 flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-surface-900 dark:text-white">Downgrade to <span class="text-amber-500" x-text="selectedPlan?.name"></span>?</h3>
                    <p class="text-sm text-surface-400 mt-1">This change takes effect immediately.</p>
                </div>
                <template x-if="getLostFeatures(selectedPlan).length > 0">
                    <div class="mb-4 p-3 bg-amber-50 dark:bg-amber-500/10 rounded-xl border border-amber-200 dark:border-amber-500/30">
                        <p class="text-xs font-semibold text-amber-700 dark:text-amber-400 uppercase tracking-wide mb-2">Features you'll lose</p>
                        <ul class="space-y-1">
                            <template x-for="feat in getLostFeatures(selectedPlan)" :key="feat">
                                <li class="flex items-center gap-1.5 text-xs text-amber-700 dark:text-amber-400">
                                    <svg class="w-3 h-3 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span x-text="feat"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </template>
                <p class="text-xs text-surface-500 mb-5">Data associated with higher-tier features may become inaccessible and cannot be automatically restored.</p>
                <div class="flex gap-3">
                    <button @click="showDowngradeModal=false" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-surface-600 dark:text-surface-300 border border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors">Cancel</button>
                    <button @click="confirmDowngrade()" :disabled="changingPlan"
                            class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-white bg-amber-500 hover:bg-amber-400 disabled:opacity-50 transition-colors">
                        <span x-show="!changingPlan">Confirm Downgrade</span>
                        <span x-show="changingPlan" class="flex items-center justify-center gap-2"><svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>Downgrading...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Payment Modal -->
        <div x-show="showPayment" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" @click.self="showPayment=false">
            <div class="w-full max-w-md bg-white dark:bg-surface-900 rounded-2xl border border-surface-200 dark:border-surface-800 p-6 shadow-2xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-surface-900 dark:text-white">
                        <span x-text="currentSub ? 'Upgrade to' : 'Subscribe to'"></span>
                        <span class="text-primary-500" x-text="' ' + (selectedPlan?.name ?? '')"></span>
                    </h3>
                    <button @click="showPayment=false;paying=false;payError=''" class="text-surface-400 hover:text-surface-600 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="mb-4 p-3 bg-surface-50 dark:bg-surface-800/50 rounded-xl space-y-1 text-sm">
                    <div class="flex justify-between"><span class="text-surface-400">Plan</span><span class="font-medium text-surface-900 dark:text-white" x-text="selectedPlan?.name"></span></div>
                    <div class="flex justify-between"><span class="text-surface-400">Billing</span><span class="text-surface-900 dark:text-white" x-text="annual ? 'Annual' : 'Monthly'"></span></div>
                    <div class="flex justify-between border-t border-surface-200 dark:border-surface-700 pt-2 mt-2 font-bold">
                        <span class="text-surface-900 dark:text-white">Total due today</span>
                        <span class="text-primary-500" x-text="'$' + (annual ? (selectedPlan?.price_yearly||0).toFixed(2) : (selectedPlan?.price_monthly||0).toFixed(2))"></span>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-medium text-surface-400">Card Details</label>
                        <span class="text-xs text-surface-500">Powered by Square</span>
                    </div>
                    <div id="sq-upgrade-card" class="min-h-[52px] px-3 py-2 rounded-xl bg-surface-50 dark:bg-surface-800/50 border border-surface-200 dark:border-surface-700/60"></div>
                </div>
                <p x-show="payError" class="text-red-500 text-xs mb-3" x-text="payError"></p>
                <button @click="submitUpgrade()" :disabled="paying || !sqCardReady"
                        class="w-full py-3 font-semibold text-white bg-primary-600 hover:bg-primary-500 disabled:opacity-50 rounded-xl transition-all">
                    <span x-show="!paying">Pay &amp; Upgrade</span>
                    <span x-show="paying" class="flex items-center justify-center gap-2"><svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>Processing...</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ═══════════ INVOICES TAB ═══════════ -->
    <div x-show="activeTab==='invoices'" x-cloak>
        <div class="grid sm:grid-cols-3 gap-4 mb-5">
            <div class="p-4 rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft">
                <p class="text-xs text-surface-400 mb-1">Total Paid</p>
                <p class="text-xl font-bold text-surface-900 dark:text-white" x-text="'$' + totalPaid.toFixed(2)">$0.00</p>
            </div>
            <div class="p-4 rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft">
                <p class="text-xs text-surface-400 mb-1">Outstanding</p>
                <p class="text-xl font-bold text-amber-500" x-text="'$' + totalOutstanding.toFixed(2)">$0.00</p>
            </div>
            <div class="p-4 rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft">
                <p class="text-xs text-surface-400 mb-1">Total Invoices</p>
                <p class="text-xl font-bold text-surface-900 dark:text-white" x-text="invoices.length">0</p>
            </div>
        </div>
        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
            <div x-show="invoicesLoading" class="p-6 space-y-3 animate-pulse">
                <div class="h-4 bg-surface-200 dark:bg-surface-800 rounded w-full"></div>
                <div class="h-4 bg-surface-200 dark:bg-surface-800 rounded w-5/6"></div>
                <div class="h-4 bg-surface-200 dark:bg-surface-800 rounded w-4/6"></div>
            </div>
            <div x-show="!invoicesLoading && invoices.length > 0" class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface-50 dark:bg-surface-800/50">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-500">Invoice #</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-500">Date</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-surface-500">Description</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-surface-500">Amount</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-surface-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100 dark:divide-surface-800">
                        <template x-for="inv in invoices" :key="inv.id">
                            <tr class="hover:bg-surface-50/50 dark:hover:bg-surface-800/20 transition-colors">
                                <td class="py-3 px-5 font-medium text-surface-900 dark:text-white" x-text="'#' + (inv.invoice_number || inv.id)"></td>
                                <td class="py-3 px-5 text-surface-500" x-text="new Date(inv.created_at || inv.issue_date).toLocaleDateString()"></td>
                                <td class="py-3 px-5 text-surface-600 dark:text-surface-300" x-text="inv.plan_name ? ('Subscription — ' + inv.plan_name) : (inv.notes || 'Subscription payment')"></td>
                                <td class="py-3 px-5 text-right font-medium text-surface-900 dark:text-white" x-text="'$' + parseFloat(inv.total || 0).toFixed(2)"></td>
                                <td class="py-3 px-5 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold"
                                          :class="{'bg-emerald-500/10 text-emerald-500': inv.status === 'paid', 'bg-amber-500/10 text-amber-500': inv.status === 'pending' || inv.status === 'sent', 'bg-red-500/10 text-red-500': inv.status === 'overdue', 'bg-surface-500/10 text-surface-400': inv.status === 'draft'}"
                                          x-text="inv.status"></span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div x-show="!invoicesLoading && invoices.length === 0" class="text-center py-10">
                <svg class="w-10 h-10 mx-auto mb-3 text-surface-300 dark:text-surface-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                <p class="text-sm text-surface-500">No invoices yet.</p>
                <p class="text-xs text-surface-400 mt-1">Invoices will appear here after your first billing cycle.</p>
            </div>
        </div>
    </div>

    <!-- ═══════════ PAYOUTS TAB ═══════════ -->
    <div x-show="activeTab==='bank'" x-cloak>
        <div class="grid sm:grid-cols-3 gap-4 mb-5">
            <div class="rounded-xl bg-gradient-to-br from-emerald-500/10 to-emerald-600/5 border border-emerald-500/20 p-4">
                <p class="text-xs font-medium text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Available Balance</p>
                <p class="text-xl font-bold text-surface-900 dark:text-white mt-1" x-text="'$' + bankData.availableBalance"></p>
            </div>
            <div class="rounded-xl bg-gradient-to-br from-amber-500/10 to-amber-600/5 border border-amber-500/20 p-4">
                <p class="text-xs font-medium text-amber-600 dark:text-amber-400 uppercase tracking-wider">Pending</p>
                <p class="text-xl font-bold text-surface-900 dark:text-white mt-1" x-text="'$' + bankData.pendingBalance"></p>
            </div>
            <div class="rounded-xl bg-gradient-to-br from-primary-500/10 to-primary-600/5 border border-primary-500/20 p-4">
                <p class="text-xs font-medium text-primary-600 dark:text-primary-400 uppercase tracking-wider">Total Withdrawn</p>
                <p class="text-xl font-bold text-surface-900 dark:text-white mt-1" x-text="'$' + bankData.totalWithdrawn"></p>
            </div>
        </div>

        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft mb-4">
            <div class="px-5 py-4 border-b border-surface-100 dark:border-surface-800">
                <h5 class="text-sm font-bold text-surface-900 dark:text-white">Connected Bank Account</h5>
            </div>
            <div class="p-5">
                <template x-if="bankData.bankAccount">
                    <div class="flex items-center justify-between rounded-xl border border-surface-200 dark:border-surface-700 px-4 py-3 bg-surface-50/50 dark:bg-surface-800/30">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-lg bg-primary-500/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-surface-800 dark:text-white" x-text="bankData.bankAccount.brand || 'Bank Account'"></p>
                                <p class="text-xs text-surface-500" x-text="'•••• ' + (bankData.bankAccount.last_four || '****')"></p>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-600 dark:text-emerald-400" x-show="bankData.bankAccount.status === 'active'">Active</span>
                        </div>
                        <button @click="removeBankAccount()" class="text-red-400 hover:text-red-500 text-xs font-medium transition-colors">Remove</button>
                    </div>
                </template>
                <template x-if="!bankData.bankAccount">
                    <div>
                        <div class="rounded-xl border-2 border-dashed border-surface-200 dark:border-surface-700 px-5 py-6 text-center mb-3">
                            <p class="text-sm text-surface-500 mb-3">No bank account connected.</p>
                            <button @click="bankData.showAddForm = !bankData.showAddForm"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 rounded-xl transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Add Bank Account
                            </button>
                        </div>
                        <div x-show="bankData.showAddForm" x-transition class="rounded-xl border border-primary-200 dark:border-primary-500/30 bg-primary-50/50 dark:bg-primary-500/5 p-4">
                            <h6 class="text-sm font-semibold text-surface-800 dark:text-white mb-3">Add Bank Account</h6>
                            <div class="grid sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1">Account Holder Name</label>
                                    <input type="text" x-model="bankData.newAccount.cardholder_name" class="w-full px-3 py-2 rounded-lg bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 text-sm text-surface-900 dark:text-white focus:border-primary-400 focus:outline-none" placeholder="John Smith">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1">Bank Name</label>
                                    <input type="text" x-model="bankData.newAccount.brand" class="w-full px-3 py-2 rounded-lg bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 text-sm text-surface-900 dark:text-white focus:border-primary-400 focus:outline-none" placeholder="Chase, Bank of America, etc.">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1">Routing Number</label>
                                    <input type="text" x-model="bankData.newAccount.routing_number" maxlength="9" class="w-full px-3 py-2 rounded-lg bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 text-sm text-surface-900 dark:text-white focus:border-primary-400 focus:outline-none" placeholder="9 digits">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1">Account Number (Last 4)</label>
                                    <input type="text" x-model="bankData.newAccount.last_four" maxlength="4" class="w-full px-3 py-2 rounded-lg bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 text-sm text-surface-900 dark:text-white focus:border-primary-400 focus:outline-none" placeholder="Last 4 digits">
                                </div>
                            </div>
                            <div class="flex items-center gap-3 mt-3">
                                <button @click="addBankAccount()" :disabled="bankData.savingAccount"
                                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 disabled:opacity-50 rounded-lg transition-colors">
                                    <span x-show="!bankData.savingAccount">Save</span>
                                    <span x-show="bankData.savingAccount">Saving...</span>
                                </button>
                                <button @click="bankData.showAddForm = false" class="px-4 py-2 text-sm text-surface-500 hover:text-surface-700 dark:hover:text-surface-300 transition-colors">Cancel</button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="rounded-2xl border border-surface-200 bg-white dark:border-surface-800 dark:bg-surface-900 shadow-soft overflow-hidden">
            <div class="px-5 py-4 border-b border-surface-100 dark:border-surface-800">
                <h5 class="text-sm font-bold text-surface-900 dark:text-white">Transaction History</h5>
            </div>
            <div x-show="bankData.loadingHistory" class="p-5 space-y-3 animate-pulse">
                <div class="h-10 bg-surface-100 dark:bg-surface-800 rounded-xl"></div>
                <div class="h-10 bg-surface-100 dark:bg-surface-800 rounded-xl"></div>
            </div>
            <template x-if="!bankData.loadingHistory && bankData.transactions.length === 0">
                <div class="text-center py-10">
                    <svg class="w-10 h-10 mx-auto text-surface-300 dark:text-surface-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm text-surface-500">No transactions yet</p>
                </div>
            </template>
            <div x-show="!bankData.loadingHistory && bankData.transactions.length > 0" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-surface-200 dark:divide-surface-700">
                    <thead class="bg-surface-50 dark:bg-surface-800/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-surface-500 uppercase tracking-wider">Description</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-surface-500 uppercase tracking-wider">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100 dark:divide-surface-800">
                        <template x-for="tx in bankData.transactions" :key="tx.id">
                            <tr class="hover:bg-surface-50 dark:hover:bg-surface-800/30 transition-colors">
                                <td class="px-4 py-3 text-sm text-surface-600 dark:text-surface-400" x-text="new Date(tx.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })"></td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium"
                                          :class="{'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400': tx.type === 'charge', 'bg-red-500/10 text-red-600 dark:text-red-400': tx.type === 'refund', 'bg-primary-500/10 text-primary-600 dark:text-primary-400': tx.type === 'payout', 'bg-amber-500/10 text-amber-600 dark:text-amber-400': tx.type === 'adjustment' || tx.type === 'credit'}"
                                          x-text="tx.type.charAt(0).toUpperCase() + tx.type.slice(1)"></span>
                                </td>
                                <td class="px-4 py-3 text-sm text-surface-700 dark:text-surface-300" x-text="tx.description || '-'"></td>
                                <td class="px-4 py-3 text-sm font-semibold text-right"
                                    :class="tx.type === 'refund' || tx.type === 'payout' ? 'text-red-500' : 'text-emerald-600 dark:text-emerald-400'"
                                    x-text="(tx.type === 'refund' || tx.type === 'payout' ? '-' : '+') + '$' + (parseFloat(tx.amount) / 100).toFixed(2)"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
const SQ_APP_ID = '<?= $sqAppId ?>';
const SQ_LOC_ID = '<?= $sqLocId ?>';
const SQ_ENV    = '<?= $sqEnv ?>';

function accountPage() {
    return {
        activeTab: 'profile',

        // ── Organization ──
        org: { id: null, name: '', slug: '', email: '', phone: '', website: '', address_line1: '', address_line2: '', city: '', state: '', zip_code: '', country: '', timezone: '', status: '', created_at: '', trial_ends_at: '' },
        orgLoading: true,
        savingOrg: false,
        orgMessage: '',
        orgMessageType: 'success',

        // ── Profile ──
        profile: { first_name: '', last_name: '', email: '', phone: '' },
        loadingProfile: true,
        savingProfile: false,
        passwords: { current: '', new_password: '', confirm: '' },
        savingPassword: false,
        message: '',
        messageType: 'success',

        // ── Subscription ──
        currentSub: null,
        plans: [],
        annual: false,
        subLoading: true,
        changingPlan: false,
        subMessage: '',
        subMessageType: 'success',
        showPayment: false,
        selectedPlan: null,
        sqPayments: null,
        sqCard: null,
        sqCardReady: false,
        paying: false,
        payError: '',
        showDowngradeModal: false,
        featureLabels: {
            'basic_scheduling':          'Basic Scheduling',
            'court_management':          'Court Management',
            'player_profiles':           'Player Profiles',
            'online_payments':           'Online Payments',
            'tournament_management':     'Tournament Management',
            'email_notifications':       'Email Notifications',
            'reports_analytics':         'Reports & Analytics',
            'api_access':                'API Access',
            'custom_branding':           'Custom Branding',
            'priority_support':          'Priority Support',
            'sla_guarantee':             'SLA Guarantee',
            'dedicated_account_manager': 'Dedicated Account Manager',
            'custom_integrations':       'Custom Integrations',
        },

        // ── Invoices ──
        invoices: [],
        invoicesLoading: true,
        get totalPaid() {
            return this.invoices.filter(i => i.status === 'paid').reduce((s, i) => s + parseFloat(i.total || 0), 0);
        },
        get totalOutstanding() {
            return this.invoices.filter(i => i.status !== 'paid' && i.status !== 'draft').reduce((s, i) => s + parseFloat(i.total || 0), 0);
        },

        // ── Bank ──
        bankData: {
            availableBalance: '0.00',
            pendingBalance: '0.00',
            totalWithdrawn: '0.00',
            bankAccount: null,
            transactions: [],
            loadingHistory: true,
            showAddForm: false,
            savingAccount: false,
            newAccount: { cardholder_name: '', brand: '', routing_number: '', last_four: '' },
        },

        setTab(tab) {
            this.activeTab = tab;
            history.replaceState(null, '', '#' + tab);
        },

        init() {
            const hash = window.location.hash.slice(1);
            if (['profile', 'organization', 'subscription', 'invoices', 'bank'].includes(hash)) this.activeTab = hash;

            if (SQ_APP_ID && !window.Square) {
                const s = document.createElement('script');
                s.src = `https://${SQ_ENV === 'production' ? 'web' : 'sandbox.web'}.squarecdn.com/v1/square.js`;
                document.head.appendChild(s);
            }

            // Single /api/auth/me call shared across all tabs (reuses layout-level cache)
            const mePromise = (typeof getMe === 'function') ? getMe() : authFetch(APP_BASE + '/api/auth/me').then(r => r.json()).catch(() => ({}));
            this.loadProfile(mePromise);
            this.loadOrganization(mePromise);
            this.loadSubscription();
            this.loadInvoices();
            this.loadBank();
        },

        async loadOrganization(mePromise) {
            try {
                // Resolve org_id from shared /api/auth/me promise (no extra request)
                let orgId = null;
                try {
                    const user = JSON.parse(localStorage.getItem('user') || '{}');
                    orgId = user.organization_id || null;
                } catch (_) {}
                if (!orgId && mePromise) {
                    const meJson = await mePromise;
                    const meUser = meJson.data || meJson;
                    orgId = meUser.organization_id || null;
                    if (orgId) {
                        try {
                            const stored = JSON.parse(localStorage.getItem('user') || '{}');
                            localStorage.setItem('user', JSON.stringify({ ...stored, organization_id: orgId }));
                        } catch (_) {}
                    }
                }
                if (!orgId) { this.orgLoading = false; return; }
                const res = await authFetch(APP_BASE + '/api/organizations/' + orgId);
                const json = await res.json();
                const data = json.data || json;
                if (data && data.id) {
                    this.org = {
                        id:            data.id,
                        name:          data.name || '',
                        slug:          data.slug || '',
                        email:         data.email || '',
                        phone:         data.phone || '',
                        website:       data.website || '',
                        address_line1: data.address_line1 || '',
                        address_line2: data.address_line2 || '',
                        city:          data.city || '',
                        state:         data.state || '',
                        zip_code:      data.zip || data.zip_code || '',
                        country:       data.country || '',
                        timezone:      data.timezone || '',
                        status:        data.status || '',
                        created_at:    data.created_at || '',
                        trial_ends_at: data.trial_ends_at || '',
                    };
                }
            } catch (e) { console.error('loadOrganization:', e); }
            this.orgLoading = false;
        },

        async saveOrganization() {
            if (!this.org.id) { this.orgMessage = 'No organization found.'; this.orgMessageType = 'error'; return; }
            this.orgMessage = '';
            this.savingOrg = true;
            try {
                const payload = {
                    name:          this.org.name.trim(),
                    slug:          this.org.slug.trim(),
                    email:         this.org.email.trim(),
                    phone:         this.org.phone || null,
                    website:       this.org.website || null,
                    address_line1: this.org.address_line1 || null,
                    address_line2: this.org.address_line2 || null,
                    city:          this.org.city || null,
                    state:         this.org.state || null,
                    zip_code:      this.org.zip_code || null,
                    country:       this.org.country || null,
                    timezone:      this.org.timezone || null,
                };
                const res = await authFetch(APP_BASE + '/api/organizations/' + this.org.id, {
                    method: 'PUT',
                    body: JSON.stringify(payload)
                });
                const json = await res.json();
                if (res.ok) {
                    this.orgMessage = 'Organization details updated successfully.';
                    this.orgMessageType = 'success';
                    // Refresh local org data
                    const data = json.data || json;
                    if (data && data.name) this.org.name = data.name;
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Organization updated.' } }));
                } else {
                    const firstErr = json.errors ? Object.values(json.errors).flat()[0] : null;
                    this.orgMessage = firstErr || json.message || 'Failed to update organization.';
                    this.orgMessageType = 'error';
                }
            } catch (e) { this.orgMessage = 'Network error. Please try again.'; this.orgMessageType = 'error'; }
            this.savingOrg = false;
        },

        async loadProfile(mePromise) {
            try {
                const stored = JSON.parse(localStorage.getItem('user') || '{}');
                if (stored && (stored.first_name || stored.email)) {
                    this.profile = { first_name: stored.first_name || '', last_name: stored.last_name || '', email: stored.email || '', phone: stored.phone || '' };
                    this.loadingProfile = false;
                }
            } catch (_) {}
            (mePromise || authFetch(APP_BASE + '/api/auth/me').then(r => r.json())).then(data => {
                const user = data.data || data;
                if (user && (user.first_name || user.email)) {
                    this.profile = { first_name: user.first_name || '', last_name: user.last_name || '', email: user.email || '', phone: user.phone || '' };
                    try {
                        const stored = JSON.parse(localStorage.getItem('user') || '{}');
                        localStorage.setItem('user', JSON.stringify({ ...stored, ...this.profile, organization_id: user.organization_id || stored.organization_id }));
                    } catch (_) {}
                }
                this.loadingProfile = false;
            }).catch(() => { this.loadingProfile = false; });
        },

        async loadSubscription() {
            try {
                const [subData, planData] = await Promise.all([
                    authFetch(APP_BASE + '/api/subscriptions/current').then(r => r.json()),
                    authFetch(APP_BASE + '/api/plans?per_page=50').then(r => r.json()),
                ]);
                const sub = subData.data || subData;
                if (sub && sub.id) {
                    this.currentSub = sub;
                    if (sub.plan) {
                        this.currentSub.plan_name = sub.plan.name || '';
                        this.currentSub.price = sub.billing_cycle === 'yearly'
                            ? parseFloat(sub.plan.price_yearly || 0)
                            : parseFloat(sub.plan.price_monthly || 0);
                    }
                }
                const p = planData.data || planData;
                this.plans = (Array.isArray(p) ? p : []).map(pl => ({
                    ...pl,
                    price_monthly: parseFloat(pl.price_monthly) || 0,
                    price_yearly:  parseFloat(pl.price_yearly)  || 0,
                    sort_order:    parseInt(pl.sort_order)       || 0,
                }));
            } catch (e) { console.error(e); }
            this.subLoading = false;
        },

        async loadInvoices() {
            authFetch(APP_BASE + '/api/invoices').then(r => r.json()).then(data => {
                this.invoices = data.data || data || [];
                if (!Array.isArray(this.invoices)) this.invoices = [];
                this.invoicesLoading = false;
            }).catch(() => { this.invoices = []; this.invoicesLoading = false; });
        },

        async loadBank() {
            authFetch(APP_BASE + '/api/payment-methods').then(r => r.json()).then(data => {
                const methods = data.data || [];
                const bank = methods.find(m => m.type === 'bank_account' && m.status === 'active');
                if (bank) this.bankData.bankAccount = bank;
            }).catch(() => {});
            authFetch(APP_BASE + '/api/transactions').then(r => r.json()).then(data => {
                const txs = data.data || [];
                this.bankData.transactions = txs;
                let charges = 0, refunds = 0, payouts = 0;
                txs.forEach(tx => {
                    const amt = (parseFloat(tx.amount) || 0) / 100;
                    if (tx.type === 'charge') charges += amt;
                    else if (tx.type === 'refund') refunds += amt;
                    else if (tx.type === 'payout') payouts += amt;
                });
                this.bankData.availableBalance = (charges - refunds - payouts).toFixed(2);
                this.bankData.totalWithdrawn = payouts.toFixed(2);
                this.bankData.loadingHistory = false;
            }).catch(() => { this.bankData.loadingHistory = false; });
        },

        // ── Profile actions ──
        async saveProfile() {
            this.savingProfile = true; this.message = '';
            try {
                const res = await authFetch(APP_BASE + '/api/auth/profile', {
                    method: 'PUT',
                    body: JSON.stringify({ first_name: this.profile.first_name, last_name: this.profile.last_name, phone: this.profile.phone })
                });
                if (res.ok) {
                    this.message = 'Profile updated successfully.'; this.messageType = 'success';
                    const user = JSON.parse(localStorage.getItem('user') || '{}');
                    user.first_name = this.profile.first_name; user.last_name = this.profile.last_name;
                    localStorage.setItem('user', JSON.stringify(user));
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Profile updated.' } }));
                } else {
                    const err = await res.json(); this.message = err.error || err.message || 'Failed to update profile.'; this.messageType = 'error';
                }
            } catch (e) { this.message = 'Network error.'; this.messageType = 'error'; }
            this.savingProfile = false;
        },

        async changePassword() {
            this.message = '';
            if (this.passwords.new_password !== this.passwords.confirm) { this.message = 'New passwords do not match.'; this.messageType = 'error'; return; }
            if (this.passwords.new_password.length < 8) { this.message = 'Password must be at least 8 characters.'; this.messageType = 'error'; return; }
            this.savingPassword = true;
            try {
                const res = await authFetch(APP_BASE + '/api/auth/change-password', {
                    method: 'POST',
                    body: JSON.stringify({ current_password: this.passwords.current, password: this.passwords.new_password, password_confirmation: this.passwords.confirm })
                });
                if (res.ok) {
                    this.message = 'Password updated successfully.'; this.messageType = 'success';
                    this.passwords = { current: '', new_password: '', confirm: '' };
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Password updated.' } }));
                } else {
                    const err = await res.json(); this.message = err.error || err.message || 'Failed to update password.'; this.messageType = 'error';
                }
            } catch (e) { this.message = 'Network error.'; this.messageType = 'error'; }
            this.savingPassword = false;
        },

        async deleteAccount() {
            try { await authFetch(APP_BASE + '/api/auth/account', { method: 'DELETE' }); } catch (e) {}
            localStorage.clear();
            window.location.href = APP_BASE + '/';
        },

        // ── Bank actions ──
        async addBankAccount() {
            const acc = this.bankData.newAccount;
            if (!acc.cardholder_name || !acc.last_four) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Please fill in all required fields.' } })); return;
            }
            this.bankData.savingAccount = true;
            try {
                const res = await authFetch(APP_BASE + '/api/payment-methods', {
                    method: 'POST',
                    body: JSON.stringify({ type: 'bank_account', brand: acc.brand, last_four: acc.last_four, cardholder_name: acc.cardholder_name, is_default: true })
                });
                const json = await res.json();
                if (res.ok) {
                    this.bankData.bankAccount = json.data;
                    this.bankData.showAddForm = false;
                    this.bankData.newAccount = { cardholder_name: '', brand: '', routing_number: '', last_four: '' };
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Bank account added successfully.' } }));
                } else {
                    const errMsg = json.errors ? Object.values(json.errors).flat()[0] : (json.message || 'Failed to add bank account.');
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: errMsg } }));
                }
            } catch (e) { window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Network error.' } })); }
            this.bankData.savingAccount = false;
        },

        async removeBankAccount() {
            if (!confirm('Remove this bank account?')) return;
            if (!this.bankData.bankAccount) return;
            try {
                const res = await authFetch(APP_BASE + '/api/payment-methods/' + this.bankData.bankAccount.id, { method: 'DELETE' });
                if (res.ok) { this.bankData.bankAccount = null; window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Bank account removed.' } })); }
            } catch (e) { window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Failed to remove account.' } })); }
        },

        // ── Subscription helpers ──
        isCurrent(plan) { return this.currentSub && this.currentSub.plan_id == plan.id; },
        getPlanTier(plan) { return plan ? (plan.sort_order || 0) : 0; },
        parseRawFeatures(plan) {
            if (!plan || !plan.features) return [];
            try { return typeof plan.features === 'string' ? JSON.parse(plan.features) : (plan.features || []); } catch { return []; }
        },
        getPlanFeatureLabels(plan) {
            return this.parseRawFeatures(plan).map(k => this.featureLabels[k] || k.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()));
        },
        getLostFeatures(newPlan) {
            if (!newPlan || !this.currentSub) return [];
            const currentPlan = this.plans.find(p => p.id == this.currentSub.plan_id) || this.currentSub.plan;
            if (!currentPlan) return [];
            const currentFeats = new Set(this.parseRawFeatures(currentPlan));
            const newFeats = new Set(this.parseRawFeatures(newPlan));
            return [...currentFeats].filter(f => !newFeats.has(f))
                .map(k => this.featureLabels[k] || k.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()));
        },
        getPlanAction(plan) {
            if (this.isCurrent(plan)) return { label: 'Current Plan', cls: 'bg-surface-100 dark:bg-surface-800 text-surface-400 cursor-not-allowed' };
            const currentPlan = this.currentSub ? (this.plans.find(p => p.id == this.currentSub.plan_id) || this.currentSub.plan) : null;
            const currentTier = this.getPlanTier(currentPlan);
            const newTier = this.getPlanTier(plan);
            if (!this.currentSub || newTier > currentTier) return { label: plan.price_monthly > 0 ? 'Upgrade' : 'Get Started', cls: 'bg-primary-600 hover:bg-primary-500 text-white shadow-soft' };
            if (newTier < currentTier) return { label: 'Downgrade', cls: 'bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-300 dark:border-amber-500/40' };
            return { label: 'Switch', cls: 'bg-primary-600 hover:bg-primary-500 text-white shadow-soft' };
        },

        // ── Subscription actions ──
        async changePlan(plan) {
            if (this.isCurrent(plan) || this.changingPlan) return;
            this.subMessage = '';
            const action = this.getPlanAction(plan);
            if (action.label === 'Downgrade') { this.selectedPlan = plan; this.showDowngradeModal = true; return; }
            if (plan.price_monthly > 0) {
                this.selectedPlan = plan; this.showPayment = true; this.paying = false; this.payError = '';
                this.$nextTick(() => this.initSquareCard()); return;
            }
            if (!confirm(`Subscribe to the ${plan.name} plan?`)) return;
            this.changingPlan = true;
            try {
                const res = await this._doChangePlan(plan.id, 'monthly');
                if (res.ok) { window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: `Subscribed to ${plan.name}.` } })); await this.loadSubscription(); }
                else { const e = await res.json(); this.subMessage = e.message || 'Failed.'; this.subMessageType = 'error'; }
            } catch { this.subMessage = 'Network error.'; this.subMessageType = 'error'; }
            this.changingPlan = false;
        },

        async confirmDowngrade() {
            this.changingPlan = true;
            try {
                const cycle = this.selectedPlan.price_monthly > 0 ? (this.annual ? 'yearly' : 'monthly') : 'monthly';
                const res = await this._doChangePlan(this.selectedPlan.id, cycle);
                this.showDowngradeModal = false;
                if (res.ok) { window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: `Downgraded to ${this.selectedPlan.name}.` } })); await this.loadSubscription(); }
                else { const e = await res.json(); this.subMessage = e.message || 'Failed to downgrade.'; this.subMessageType = 'error'; }
            } catch { this.subMessage = 'Network error.'; this.subMessageType = 'error'; }
            this.changingPlan = false;
        },

        async _doChangePlan(planId, cycle) {
            if (this.currentSub) return authFetch(`${APP_BASE}/api/subscriptions/${this.currentSub.id}/change-plan`, { method: 'PUT', body: JSON.stringify({ plan_id: planId, billing_cycle: cycle }) });
            const user = JSON.parse(localStorage.getItem('user') || '{}');
            return authFetch(`${APP_BASE}/api/subscriptions`, { method: 'POST', body: JSON.stringify({ organization_id: user.organization_id, plan_id: planId, billing_cycle: cycle }) });
        },

        async initSquareCard() {
            if (!SQ_APP_ID) return;
            let attempts = 0;
            while (!window.Square && attempts < 20) { await new Promise(r => setTimeout(r, 200)); attempts++; }
            if (!window.Square) { console.warn('Square SDK not loaded'); return; }
            if (this.sqCard) { await this.sqCard.attach('#sq-upgrade-card'); this.sqCardReady = true; return; }
            try {
                this.sqPayments = Square.payments(SQ_APP_ID, SQ_LOC_ID);
                this.sqCard = await this.sqPayments.card({
                    style: {
                        '.input-container': { borderColor: '#334155', borderRadius: '12px' },
                        '.input-container.is-focus': { borderColor: '#6366f1' },
                        '.message-text': { color: '#f87171' },
                        '.message-icon': { color: '#f87171' },
                        input: { backgroundColor: '#0f172a', color: '#e2e8f0', fontSize: '15px' },
                        'input.is-focus': { backgroundColor: '#0f172a' },
                        'input::placeholder': { color: '#475569' },
                    }
                });
                await this.sqCard.attach('#sq-upgrade-card');
                this.sqCardReady = true;
            } catch(e) { console.warn('Square init:', e); }
        },

        async submitUpgrade() {
            if (!this.sqCard || !this.sqCardReady) { this.payError = 'Payment form not loaded. Please try again.'; return; }
            this.payError = ''; this.paying = true;
            try {
                const tokenResult = await this.sqCard.tokenize();
                if (tokenResult.status !== 'OK') { this.payError = (tokenResult.errors || []).map(e => e.message).join(', ') || 'Card error.'; this.paying = false; return; }
                const price = this.annual ? (this.selectedPlan.price_yearly || 0) : (this.selectedPlan.price_monthly || 0);
                const payRes = await authFetch(APP_BASE + '/api/payments', { method: 'POST', body: JSON.stringify({ source_id: tokenResult.token, amount: Math.round(price * 100), currency: 'USD', description: `${this.selectedPlan.name} subscription` }) });
                const payData = await payRes.json();
                if (!payRes.ok) { this.payError = payData.message || 'Payment failed.'; this.paying = false; return; }
                const cycle = this.annual ? 'yearly' : 'monthly';
                const subRes = await this._doChangePlan(this.selectedPlan.id, cycle);
                this.showPayment = false; this.sqCard = null; this.sqCardReady = false;
                if (subRes.ok) { window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: `Upgraded to ${this.selectedPlan.name}!` } })); }
                else { this.subMessage = 'Payment received but subscription update failed. Contact support.'; this.subMessageType = 'error'; }
                await this.loadSubscription();
            } catch { this.payError = 'Network error. Please try again.'; }
            this.paying = false;
        }
    }
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/admin.php';
?>
