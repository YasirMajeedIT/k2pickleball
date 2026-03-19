<?php
/**
 * Tenant Book a Court — K2 Navy/Gold Theme
 * Interactive court reservation: select facility → date → court → time slots → submit.
 * Uses /api/public/courts/availability and POST /api/public/courts/book
 */
?>

<div x-data="bookCourtPage()" x-init="load()">
    <!-- Page Header -->
    <section class="relative bg-navy-900 overflow-hidden py-20">
        <div class="absolute inset-0 grid-bg opacity-40"></div>
        <div class="absolute inset-0 hero-glow"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full glass-card text-gold-400 text-xs font-semibold uppercase tracking-wider mb-4">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zM7 7h2v2H7zm4 0h2v2h-2zm4 0h2v2h-2zM7 11h2v2H7zm4 0h2v2h-2z"/></svg>
                Reserve
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-white" x-text="category.name || 'Book a Court'"></h1>
            <p class="mt-4 text-lg text-slate-400 max-w-2xl" x-text="category.description || 'Reserve your court in just a few clicks. Choose your preferred time and you\u0027re set.'"></p>
        </div>
    </section>

    <!-- Inactive Notice (shown when admin deactivates the category) -->
    <template x-if="categoryLoaded && !categoryActive">
        <section class="relative py-20 bg-navy-950">
            <div class="max-w-lg mx-auto px-4 text-center">
                <div class="glass-card rounded-2xl p-10 gold-border">
                    <svg class="w-12 h-12 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    <h2 class="text-xl font-display font-bold text-white mb-2">Court Booking Unavailable</h2>
                    <p class="text-slate-400 text-sm">Court reservations are currently not available. Please check back later or browse our scheduled sessions.</p>
                    <a href="/schedule" class="inline-flex items-center gap-2 mt-6 px-6 py-3 rounded-xl gradient-gold-bg text-navy-950 font-bold text-sm hover:shadow-gold transition-all">View Schedule</a>
                </div>
            </div>
        </section>
    </template>

    <!-- Step Indicator -->
    <section x-show="categoryActive" class="glass border-b border-gold-500/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-center gap-1 sm:gap-3">
                <template x-for="(s, i) in steps" :key="i">
                    <div class="flex items-center gap-1 sm:gap-3">
                        <div class="flex items-center gap-2 cursor-pointer" @click="goToStep(i)" :class="i <= step ? 'opacity-100' : 'opacity-40'">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all"
                                 :class="i < step ? 'gradient-gold-bg text-navy-950' : (i === step ? 'border-2 border-gold-500 text-gold-400' : 'border border-navy-600 text-slate-500')">
                                <template x-if="i < step">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </template>
                                <template x-if="i >= step">
                                    <span x-text="i + 1"></span>
                                </template>
                            </div>
                            <span class="hidden sm:inline text-xs font-semibold" :class="i === step ? 'text-gold-400' : (i < step ? 'text-white' : 'text-slate-500')" x-text="s"></span>
                        </div>
                        <template x-if="i < steps.length - 1">
                            <div class="w-6 sm:w-12 h-px" :class="i < step ? 'bg-gold-500/50' : 'bg-navy-700'"></div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section x-show="categoryActive" class="relative py-10 bg-navy-950 min-h-[60vh]">
        <div class="absolute inset-0 section-glow"></div>
        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Step 0: Select Date -->
            <div x-show="step === 0" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-display font-bold text-white">When would you like to play?</h2>
                    <p class="text-slate-400 mt-2">Select a date to see available courts</p>
                </div>

                <!-- No facilities at all: admin must create them first -->
                <template x-if="facilities.length === 0">
                    <div class="max-w-md mx-auto mb-4 glass-card rounded-2xl p-8 gold-border text-center">
                        <svg class="w-12 h-12 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <h3 class="text-lg font-bold text-white mb-2">No Locations Set Up Yet</h3>
                        <p class="text-slate-400 text-sm">Court booking isn't available yet — the administrator hasn't added any facilities. Please check back soon or contact us.</p>
                        <a href="/contact" class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 rounded-xl gradient-gold-bg text-navy-950 font-bold text-sm hover:shadow-gold transition-all">Contact Us</a>
                    </div>
                </template>

            <!-- Facility Picker — only shown when org has multiple facilities -->
                <template x-if="facilities.length > 1">
                    <div class="max-w-md mx-auto mb-6">
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Select Location</p>
                        <div class="grid gap-2">
                            <template x-for="f in facilities" :key="f.id">
                                <button @click="selectFacilityForBooking(f)"
                                        class="flex items-center justify-between px-4 py-3 rounded-xl border transition-all text-left"
                                        :class="selectedFacilityId === f.id ? 'gradient-gold-bg text-navy-950 border-gold-400 font-bold' : 'bg-navy-800/60 border-navy-700 text-slate-300 hover:border-gold-500/40 hover:text-white'">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span class="font-semibold" x-text="f.name"></span>
                                    </div>
                                    <span class="text-xs opacity-70" x-text="f.city + (f.state ? ', ' + f.state : '')"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>

                <div class="max-w-md mx-auto glass-card rounded-2xl p-8 gold-border">
                    <label class="block text-sm font-semibold text-slate-300 mb-3">Select Date</label>
                    <input type="date" x-model="selectedDate" :min="todayStr" @change="onDateChange()"
                           class="w-full px-4 py-3 rounded-xl bg-navy-800 border border-navy-700 text-white focus:outline-none focus:ring-2 focus:ring-gold-500/50 focus:border-gold-500/50 transition-all text-lg">

                    <div class="mt-6 flex flex-wrap gap-2 justify-center">
                        <button @click="setQuickDate(0)" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all"
                                :class="selectedDate === offsetDate(0) ? 'gradient-gold-bg text-navy-950' : 'bg-navy-800 border border-navy-700 text-slate-400 hover:text-white hover:border-gold-500/30'">Today</button>
                        <button @click="setQuickDate(1)" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all"
                                :class="selectedDate === offsetDate(1) ? 'gradient-gold-bg text-navy-950' : 'bg-navy-800 border border-navy-700 text-slate-400 hover:text-white hover:border-gold-500/30'">Tomorrow</button>
                        <template x-for="d in [2,3,4,5,6]" :key="d">
                            <button @click="setQuickDate(d)" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all"
                                    :class="selectedDate === offsetDate(d) ? 'gradient-gold-bg text-navy-950' : 'bg-navy-800 border border-navy-700 text-slate-400 hover:text-white hover:border-gold-500/30'"
                                    x-text="shortDayName(d)"></button>
                        </template>
                    </div>

                    <button @click="step = 1; fetchAvailability()" :disabled="!selectedDate || !selectedFacilityId"
                            class="w-full mt-8 py-3 rounded-xl font-bold text-sm gradient-gold-bg text-navy-950 hover:shadow-gold transition-all disabled:opacity-40 disabled:cursor-not-allowed">
                        <span x-show="!selectedFacilityId">Select a location above to continue</span>
                        <span x-show="selectedFacilityId">See Available Courts</span>
                    </button>
                </div>
            </div>

            <!-- Step 1: Select Court & Time -->
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-display font-bold text-white">Choose Your Court & Time</h2>
                        <p class="text-slate-400 mt-1">
                            <template x-if="selectedFacilityName">
                                <span class="text-gold-400 font-semibold" x-text="selectedFacilityName + ' &middot; '"></span>
                            </template>
                            <span x-text="formatDisplayDate(selectedDate)"></span>
                            <template x-if="operatingHours">
                                <span class="text-gold-500"> &middot; Open <span x-text="operatingHours"></span></span>
                            </template>
                        </p>
                    </div>
                    <button @click="step = 0" class="flex items-center gap-1 text-sm text-slate-400 hover:text-gold-400 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Change Date
                    </button>
                </div>

                <!-- Duration Selector -->
                <div class="mb-6 glass-card rounded-xl p-4 gold-border">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Duration</label>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="dur in durations" :key="dur.value">
                            <button @click="selectedDuration = dur.value; clearSelection()" class="px-4 py-2 rounded-lg text-sm font-semibold transition-all"
                                    :class="selectedDuration === dur.value ? 'gradient-gold-bg text-navy-950' : 'bg-navy-800 border border-navy-700 text-slate-400 hover:text-white hover:border-gold-500/30'"
                                    x-text="dur.label"></button>
                        </template>
                    </div>
                </div>

                <!-- Loading -->
                <div x-show="loadingSlots" class="py-16 text-center">
                    <div class="inline-flex items-center gap-3 text-slate-500">
                        <svg class="animate-spin w-5 h-5 text-gold-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Loading availability...
                    </div>
                </div>

                <!-- Closed Notice -->
                <div x-show="!loadingSlots && facilityClosed" class="py-16 text-center">
                    <div class="glass-card rounded-2xl p-10 inline-block max-w-md">
                        <svg class="w-12 h-12 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        <p class="text-white font-bold">Facility Closed</p>
                        <p class="text-slate-400 text-sm mt-1">This facility is closed on the selected date. Please choose another day.</p>
                    </div>
                </div>

                <!-- No Courts -->
                <div x-show="!loadingSlots && !facilityClosed && courts.length === 0" class="py-16 text-center">
                    <div class="glass-card rounded-2xl p-10 inline-block max-w-md">
                        <svg class="w-12 h-12 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2z"/></svg>
                        <p class="text-white font-bold">No Courts Available</p>
                        <p class="text-slate-400 text-sm mt-1">No active courts were found for this location. Try a different date or location.</p>
                    </div>
                </div>

                <!-- Court Grid -->
                <div x-show="!loadingSlots && !facilityClosed && courts.length > 0" class="space-y-4">
                    <template x-for="court in courts" :key="court.id">
                        <div class="glass-card rounded-xl p-5 gold-border transition-all" :class="selectedCourt?.id === court.id ? 'ring-1 ring-gold-500/50 shadow-gold' : ''">
                            <!-- Court Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg gradient-gold-bg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-navy-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="2"/><line x1="3" y1="12" x2="21" y2="12" stroke-width="2"/><line x1="12" y1="3" x2="12" y2="21" stroke-width="1.5"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-white" x-text="court.name"></h3>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-xs text-slate-400 capitalize" x-text="court.sport_type"></span>
                                            <span class="text-navy-600">&middot;</span>
                                            <span class="text-xs text-slate-400" x-text="court.is_indoor ? 'Indoor' : 'Outdoor'"></span>
                                            <template x-if="court.is_lighted">
                                                <span class="text-xs text-gold-500" title="Lighted">&#9728;</span>
                                            </template>
                                            <template x-if="court.surface_type">
                                                <span>
                                                    <span class="text-navy-600">&middot;</span>
                                                    <span class="text-xs text-slate-400 capitalize" x-text="court.surface_type"></span>
                                                </span>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-gold-400">$<span x-text="parseFloat(court.hourly_rate).toFixed(2)"></span><span class="text-xs font-normal text-slate-500">/hr</span></div>
                                    <div class="text-[10px] text-slate-500">Up to <span x-text="court.max_players"></span> players</div>
                                </div>
                            </div>

                            <!-- No slots for current duration notice -->
                            <div x-show="!courtHasSelectableSlots(court)" class="mb-3 px-3 py-2 rounded-lg bg-navy-800/60 border border-navy-700/50 flex items-center justify-between gap-3">
                                <p class="text-xs text-slate-500">No <span x-text="selectedDuration"></span>-min slots available. Available durations:</p>
                                <div class="flex gap-1 flex-wrap justify-end">
                                    <template x-for="d in getAvailableDurations(court)" :key="d.value">
                                        <button @click="selectedDuration = d.value; clearSelection()"
                                                class="px-2 py-0.5 rounded text-xs font-bold gradient-gold-bg text-navy-950"
                                                x-text="d.label"></button>
                                    </template>
                                    <template x-if="getAvailableDurations(court).length === 0">
                                        <span class="text-xs text-slate-600">Fully booked</span>
                                    </template>
                                </div>
                            </div>

                            <!-- Time Slots -->
                            <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 gap-1.5">
                                <template x-for="slot in court.slots" :key="slot.start">
                                    <button @click="selectSlot(court, slot)"
                                            :disabled="!canSelectSlot(court, slot)"
                                            class="relative px-1 py-2 rounded-lg text-xs font-medium text-center transition-all"
                                            :class="isSlotSelected(court, slot) ? 'gradient-gold-bg text-navy-950 font-bold shadow-gold ring-1 ring-gold-400' :
                                                    isSlotInRange(court, slot) ? 'bg-gold-500/20 text-gold-300 border border-gold-500/30' :
                                                    slot.available ? 'bg-navy-800/80 text-slate-300 border border-navy-700/50 hover:border-gold-500/30 hover:text-white hover:bg-navy-800' :
                                                    'bg-navy-900/50 text-navy-700 border border-navy-800/30 cursor-not-allowed line-through'"
                                            x-text="slot.start">
                                    </button>
                                </template>
                            </div>

                            <!-- Selection Summary -->
                            <div x-show="selectedCourt?.id === court.id && selectedSlot" class="mt-4 pt-4 border-t border-navy-700/50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="text-sm">
                                            <span class="text-slate-400">Time:</span>
                                            <span class="text-white font-bold" x-text="selectedSlot?.start + ' – ' + calcEndTime()"></span>
                                        </div>
                                        <div class="text-sm">
                                            <span class="text-slate-400">Duration:</span>
                                            <span class="text-white font-bold" x-text="selectedDuration + ' min'"></span>
                                        </div>
                                    </div>
                                    <div class="text-lg font-bold text-gold-400">$<span x-text="calcPrice(court).toFixed(2)"></span></div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Continue Button -->
                    <div class="pt-4" x-show="selectedCourt && selectedSlot">
                        <button @click="step = 2"
                                class="w-full py-3 rounded-xl font-bold text-sm gradient-gold-bg text-navy-950 hover:shadow-gold transition-all">
                            Continue to Details
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2: Player Details -->
            <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-display font-bold text-white">Your Details</h2>
                        <p class="text-slate-400 mt-1">Almost there — just a few details to confirm your reservation.</p>
                    </div>
                    <button @click="step = 1" class="flex items-center gap-1 text-sm text-slate-400 hover:text-gold-400 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Change Court
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Form -->
                    <div class="lg:col-span-2 glass-card rounded-2xl p-8 gold-border">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">First Name *</label>
                                <input type="text" x-model="form.first_name" required
                                       class="w-full px-4 py-3 rounded-xl bg-navy-800 border border-navy-700 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-gold-500/50 focus:border-gold-500/50 transition-all"
                                       placeholder="John">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Last Name *</label>
                                <input type="text" x-model="form.last_name" required
                                       class="w-full px-4 py-3 rounded-xl bg-navy-800 border border-navy-700 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-gold-500/50 focus:border-gold-500/50 transition-all"
                                       placeholder="Doe">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Email *</label>
                                <input type="email" x-model="form.email" required
                                       class="w-full px-4 py-3 rounded-xl bg-navy-800 border border-navy-700 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-gold-500/50 focus:border-gold-500/50 transition-all"
                                       placeholder="john@example.com">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Phone</label>
                                <input type="tel" x-model="form.phone"
                                       class="w-full px-4 py-3 rounded-xl bg-navy-800 border border-navy-700 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-gold-500/50 focus:border-gold-500/50 transition-all"
                                       placeholder="(555) 123-4567">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Number of Players</label>
                                <select x-model="form.num_players"
                                        class="w-full px-4 py-3 rounded-xl bg-navy-800 border border-navy-700 text-white focus:outline-none focus:ring-2 focus:ring-gold-500/50 focus:border-gold-500/50 transition-all">
                                    <template x-for="n in maxPlayers" :key="n">
                                        <option :value="n" x-text="n + (n === 1 ? ' player' : ' players')"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <!-- Error -->
                        <div x-show="bookingError" class="mt-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20">
                            <p class="text-sm text-red-400" x-text="bookingError"></p>
                        </div>

                        <button @click="submitBooking()" :disabled="submitting || !isFormValid()" class="w-full mt-8 py-3.5 rounded-xl font-bold text-sm gradient-gold-bg text-navy-950 hover:shadow-gold transition-all disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <svg x-show="submitting" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span x-text="submitting ? 'Booking...' : 'Confirm Reservation'"></span>
                        </button>
                    </div>

                    <!-- Booking Summary Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="glass-card rounded-2xl p-6 gold-border sticky top-28">
                            <h3 class="text-sm font-semibold text-gold-400 uppercase tracking-wider mb-4">Booking Summary</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-start">
                                    <span class="text-xs text-slate-400">Court</span>
                                    <span class="text-sm font-bold text-white text-right" x-text="selectedCourt?.name || '—'"></span>
                                </div>
                                <div class="flex justify-between items-start">
                                    <span class="text-xs text-slate-400">Type</span>
                                    <div class="text-right">
                                        <span class="text-sm text-white capitalize" x-text="selectedCourt?.sport_type || '—'"></span>
                                        <span class="text-xs text-slate-500 block" x-text="selectedCourt?.is_indoor ? 'Indoor' : 'Outdoor'"></span>
                                    </div>
                                </div>
                                <div class="border-t border-navy-700/50 pt-3 flex justify-between items-start">
                                    <span class="text-xs text-slate-400">Date</span>
                                    <span class="text-sm font-bold text-white" x-text="formatDisplayDate(selectedDate)"></span>
                                </div>
                                <div class="flex justify-between items-start">
                                    <span class="text-xs text-slate-400">Time</span>
                                    <span class="text-sm font-bold text-white" x-text="selectedSlot ? selectedSlot.start + ' – ' + calcEndTime() : '—'"></span>
                                </div>
                                <div class="flex justify-between items-start">
                                    <span class="text-xs text-slate-400">Duration</span>
                                    <span class="text-sm text-white" x-text="selectedDuration + ' min'"></span>
                                </div>
                                <div class="flex justify-between items-start">
                                    <span class="text-xs text-slate-400">Rate</span>
                                    <span class="text-sm text-white">$<span x-text="selectedCourt ? parseFloat(selectedCourt.hourly_rate).toFixed(2) : '0.00'"></span>/hr</span>
                                </div>
                                <div class="border-t border-gold-500/20 pt-3 flex justify-between items-center">
                                    <span class="text-sm font-semibold text-gold-400">Total</span>
                                    <span class="text-xl font-extrabold text-gold-400">$<span x-text="selectedCourt ? calcPrice(selectedCourt).toFixed(2) : '0.00'"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Confirmation -->
            <div x-show="step === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                <div class="max-w-lg mx-auto text-center">
                    <div class="glass-card rounded-2xl p-10 gold-border">
                        <div class="w-16 h-16 rounded-full gradient-gold-bg flex items-center justify-center mx-auto mb-5">
                            <svg class="w-8 h-8 text-navy-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <h2 class="text-2xl font-display font-bold text-white mb-2">Court Reserved!</h2>
                        <p class="text-slate-400 mb-6">Your court has been booked successfully. A confirmation has been sent to your email.</p>

                        <div class="glass rounded-xl p-5 text-left space-y-2 mb-6">
                            <div class="flex justify-between">
                                <span class="text-xs text-slate-400">Booking ID</span>
                                <span class="text-sm font-mono text-gold-400" x-text="confirmation?.booking_id?.substring(0, 8) + '...'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs text-slate-400">Court</span>
                                <span class="text-sm font-bold text-white" x-text="confirmation?.court_name"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs text-slate-400">Date</span>
                                <span class="text-sm text-white" x-text="formatDisplayDate(confirmation?.date)"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs text-slate-400">Time</span>
                                <span class="text-sm text-white" x-text="confirmation?.start_time + ' – ' + confirmation?.end_time"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs text-slate-400">Players</span>
                                <span class="text-sm text-white" x-text="confirmation?.num_players"></span>
                            </div>
                            <div class="flex justify-between border-t border-navy-700/50 pt-2">
                                <span class="text-sm font-semibold text-gold-400">Total</span>
                                <span class="text-sm font-bold text-gold-400">$<span x-text="parseFloat(confirmation?.total_price || 0).toFixed(2)"></span></span>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="/schedule" class="flex-1 py-3 rounded-xl font-bold text-sm bg-navy-800 border border-navy-700 text-white hover:border-gold-500/30 transition-all text-center">View Schedule</a>
                            <button @click="resetBooking()" class="flex-1 py-3 rounded-xl font-bold text-sm gradient-gold-bg text-navy-950 hover:shadow-gold transition-all">Book Another Court</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<script>
function bookCourtPage() {
    return {
        step: 0,
        steps: ['Date', 'Court & Time', 'Details', 'Confirmation'],

        // Category
        category: { name: 'Book a Court', description: '', color: '#d4af37', image_url: null },
        categoryActive: true,
        categoryLoaded: false,

        // Facilities
        facilities: [],
        selectedFacilityId: null,
        selectedFacilityName: '',

        // Data
        selectedDate: '',
        todayStr: new Date().toISOString().split('T')[0],
        courts: [],
        operatingHours: null,
        facilityClosed: false,

        // Selection
        selectedCourt: null,
        selectedSlot: null,
        selectedDuration: 60,
        durations: [
            { value: 30, label: '30 min' },
            { value: 60, label: '1 hour' },
            { value: 90, label: '1.5 hours' },
            { value: 120, label: '2 hours' },
        ],

        // Form
        form: { first_name: '', last_name: '', email: '', phone: '', num_players: 2 },
        submitting: false,
        bookingError: '',
        confirmation: null,

        // State
        loading: false,
        loadingSlots: false,

        get maxPlayers() {
            return parseInt(this.selectedCourt?.max_players || 4);
        },

        async load() {
            this.selectedDate = this.todayStr;

            // --- Facility auto-selection ---
            this.facilities = window.ORG?.facilities || [];
            let savedFacId = null;
            try {
                const saved = localStorage.getItem('selected_facility');
                if (saved) {
                    const parsed = JSON.parse(saved);
                    savedFacId = parsed?.id ? parseInt(parsed.id) : parseInt(saved);
                }
            } catch(e) {}
            const matchedFac = savedFacId && this.facilities.find(f => f.id === savedFacId);
            if (matchedFac) {
                this.selectedFacilityId = matchedFac.id;
                this.selectedFacilityName = matchedFac.name;
            } else if (this.facilities.length === 1) {
                // Only one facility — auto-select it
                this.selectedFacilityId = this.facilities[0].id;
                this.selectedFacilityName = this.facilities[0].name;
            }
            // If multiple facilities and none saved, user must pick in Step 0

            // Fetch category data (admin-customized name/description/active status)
            try {
                const resp = await fetch(`${window.baseApi}/api/public/court-category`);
                const json = await resp.json();
                if (json.status === 'success' && json.data) {
                    this.category = json.data;
                    this.categoryActive = json.data.is_active !== false && json.data.is_active !== 0;
                }
            } catch(e) { /* fallback to defaults */ }
            this.categoryLoaded = true;

            // Pre-fill form from logged-in player
            const token = localStorage.getItem('player_token');
            if (token) {
                try {
                    const payload = JSON.parse(atob(token.split('.')[1]));
                    if (payload.first_name) this.form.first_name = payload.first_name;
                    if (payload.last_name) this.form.last_name = payload.last_name;
                    if (payload.email) this.form.email = payload.email;
                } catch(e) {}
            }
        },

        selectFacilityForBooking(f) {
            this.selectedFacilityId = f.id;
            this.selectedFacilityName = f.name;
            // Persist to localStorage so the header location switcher stays in sync
            localStorage.setItem('selected_facility', JSON.stringify({
                id: f.id, name: f.name, slug: f.slug || '', city: f.city || '', state: f.state || ''
            }));
            this.courts = [];
            this.clearSelection();
        },

        onDateChange() {
            if (this.step >= 1) this.fetchAvailability();
        },

        offsetDate(days) {
            const d = new Date();
            d.setDate(d.getDate() + days);
            return d.toISOString().split('T')[0];
        },

        setQuickDate(days) {
            this.selectedDate = this.offsetDate(days);
        },

        shortDayName(days) {
            const d = new Date();
            d.setDate(d.getDate() + days);
            return d.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
        },

        formatDisplayDate(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr + 'T12:00:00');
            return d.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
        },

        async fetchAvailability() {
            const facilityId = this.selectedFacilityId;
            if (!facilityId || !this.selectedDate) return;

            this.loadingSlots = true;
            this.facilityClosed = false;
            this.courts = [];
            this.clearSelection();

            try {
                const resp = await fetch(`${window.baseApi}/api/public/courts/availability?facility_id=${facilityId}&date=${encodeURIComponent(this.selectedDate)}`);
                const json = await resp.json();

                if (json.status === 'success') {
                    if (json.data.closed) {
                        this.facilityClosed = true;
                    } else {
                        this.courts = json.data.courts || [];
                        this.operatingHours = json.data.operating_hours || null;
                    }
                }
            } catch(e) {
                console.error('Failed to load court availability', e);
            } finally {
                this.loadingSlots = false;
            }
        },

        clearSelection() {
            this.selectedCourt = null;
            this.selectedSlot = null;
        },

        selectSlot(court, slot) {
            if (!slot.available) return;
            if (!this.canSelectSlot(court, slot)) return;
            this.selectedCourt = court;
            this.selectedSlot = slot;
            // Clamp num_players to court max
            if (this.form.num_players > parseInt(court.max_players)) {
                this.form.num_players = parseInt(court.max_players);
            }
        },

        canSelectSlot(court, slot) {
            if (!slot.available) return false;
            // Check that all consecutive slots for the selected duration are available
            const slotsNeeded = this.selectedDuration / 30;
            const courtSlots = court.slots;
            const slotIdx = courtSlots.findIndex(s => s.start === slot.start);
            if (slotIdx === -1) return false;
            for (let i = 0; i < slotsNeeded; i++) {
                const s = courtSlots[slotIdx + i];
                if (!s || !s.available) return false;
            }
            return true;
        },

        isSlotSelected(court, slot) {
            return this.selectedCourt?.id === court.id && this.selectedSlot?.start === slot.start;
        },

        isSlotInRange(court, slot) {
            if (!this.selectedCourt || this.selectedCourt.id !== court.id || !this.selectedSlot) return false;
            const courtSlots = court.slots;
            const startIdx = courtSlots.findIndex(s => s.start === this.selectedSlot.start);
            const thisIdx = courtSlots.findIndex(s => s.start === slot.start);
            const slotsNeeded = this.selectedDuration / 30;
            return thisIdx > startIdx && thisIdx < startIdx + slotsNeeded;
        },

        calcEndTime() {
            if (!this.selectedSlot) return '';
            const [h, m] = this.selectedSlot.start.split(':').map(Number);
            const total = h * 60 + m + this.selectedDuration;
            const eh = Math.floor(total / 60).toString().padStart(2, '0');
            const em = (total % 60).toString().padStart(2, '0');
            return `${eh}:${em}`;
        },

        calcPrice(court) {
            const rate = parseFloat(court.hourly_rate || 0);
            return rate * (this.selectedDuration / 60);
        },

        // Returns true if the current selectedDuration has at least 1 bookable slot sequence on this court
        courtHasSelectableSlots(court) {
            const needed = this.selectedDuration / 30;
            const slots = court.slots || [];
            for (let i = 0; i <= slots.length - needed; i++) {
                let ok = true;
                for (let j = 0; j < needed; j++) {
                    if (!slots[i + j]?.available) { ok = false; break; }
                }
                if (ok) return true;
            }
            return false;
        },

        // Returns array of duration options that have at least 1 bookable slot sequence on this court
        getAvailableDurations(court) {
            return this.durations.filter(d => {
                const needed = d.value / 30;
                const slots = court.slots || [];
                for (let i = 0; i <= slots.length - needed; i++) {
                    let ok = true;
                    for (let j = 0; j < needed; j++) {
                        if (!slots[i + j]?.available) { ok = false; break; }
                    }
                    if (ok) return true;
                }
                return false;
            });
        },

        goToStep(i) {
            if (i > this.step) return; // Can only go back
            if (i === 3) return; // Can't go back to confirmation
            this.step = i;
            if (i === 1) this.fetchAvailability();
        },

        isFormValid() {
            return this.form.first_name.trim() !== '' &&
                   this.form.last_name.trim() !== '' &&
                   this.form.email.trim() !== '' &&
                   this.form.email.includes('@');
        },

        async submitBooking() {
            this.bookingError = '';
            if (!this.isFormValid()) return;
            if (!this.selectedCourt || !this.selectedSlot) return;

            this.submitting = true;

            try {
                const resp = await fetch(`${window.baseApi}/api/public/courts/book`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        facility_id: this.selectedFacilityId,
                        court_id: this.selectedCourt.id,
                        date: this.selectedDate,
                        start_time: this.selectedSlot.start,
                        end_time: this.calcEndTime(),
                        first_name: this.form.first_name.trim(),
                        last_name: this.form.last_name.trim(),
                        email: this.form.email.trim(),
                        phone: this.form.phone.trim(),
                        num_players: parseInt(this.form.num_players),
                    })
                });
                const json = await resp.json();

                if (json.status === 'success') {
                    this.confirmation = json.data;
                    this.step = 3;
                } else {
                    this.bookingError = json.message || 'Booking failed. Please try again.';
                }
            } catch (e) {
                this.bookingError = 'Network error. Please check your connection and try again.';
            } finally {
                this.submitting = false;
            }
        },

        resetBooking() {
            this.step = 0;
            this.selectedDate = this.todayStr;
            this.courts = [];
            this.clearSelection();
            this.bookingError = '';
            this.confirmation = null;
        }
    };
}
</script>
