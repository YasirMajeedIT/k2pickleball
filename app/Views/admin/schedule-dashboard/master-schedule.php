<!-- Master Schedule Calendar View -->
<div x-data="masterSchedule()" x-init="init()" x-effect="handleFacilityChange(facilityId)" class="space-y-4">

    <!-- Header: Category Filters + View Controls + Date Picker + Settings -->
    <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft p-4">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <!-- Category Filter Pills -->
            <div class="flex flex-wrap gap-2 items-center">
                <span class="text-xs font-medium text-surface-500 dark:text-surface-400 mr-1">Filter:</span>
                <button @click="categoryFilter = ''"
                        :class="categoryFilter === '' ? 'bg-primary-500 text-white shadow-sm' : 'bg-surface-100 dark:bg-surface-800 text-surface-600 dark:text-surface-300 hover:bg-surface-200 dark:hover:bg-surface-700'"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all">
                    All
                </button>
                <template x-for="cat in categories" :key="cat.id">
                    <button @click="categoryFilter = String(cat.id)"
                            :class="categoryFilter === String(cat.id)
                                ? 'text-white shadow-sm'
                                : 'bg-surface-100 dark:bg-surface-800 text-surface-600 dark:text-surface-300 hover:bg-surface-200 dark:hover:bg-surface-700'"
                            :style="categoryFilter === String(cat.id) ? 'background-color:' + (cat.color || '#6366f1') : ''"
                            class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full" :style="'background-color:' + (cat.color || '#6366f1')"></span>
                        <span x-text="cat.name"></span>
                    </button>
                </template>
            </div>

            <!-- View toggle + Nav + Date Picker + Settings -->
            <div class="flex flex-wrap items-center gap-2">
                <button @click="calendarChangeView('timeGridDay')"
                        :class="currentView === 'timeGridDay' ? 'bg-primary-500 text-white' : 'bg-surface-100 dark:bg-surface-800 text-surface-600 dark:text-surface-300'"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all">Day</button>
                <button @click="calendarChangeView('timeGridWeek')"
                        :class="currentView === 'timeGridWeek' ? 'bg-primary-500 text-white' : 'bg-surface-100 dark:bg-surface-800 text-surface-600 dark:text-surface-300'"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all">Week</button>
                <button @click="calendarChangeView('dayGridMonth')"
                        :class="currentView === 'dayGridMonth' ? 'bg-primary-500 text-white' : 'bg-surface-100 dark:bg-surface-800 text-surface-600 dark:text-surface-300'"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all">Month</button>

                <div class="w-px h-6 bg-surface-200 dark:bg-surface-700 mx-1"></div>

                <button @click="calendarPrev()" class="p-1.5 rounded-lg bg-surface-100 dark:bg-surface-800 hover:bg-surface-200 dark:hover:bg-surface-700 text-surface-600 dark:text-surface-300 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button @click="calendarToday()" class="px-3 py-1.5 rounded-lg bg-surface-100 dark:bg-surface-800 hover:bg-surface-200 dark:hover:bg-surface-700 text-surface-600 dark:text-surface-300 text-xs font-medium transition-all">Today</button>
                <button @click="calendarNext()" class="p-1.5 rounded-lg bg-surface-100 dark:bg-surface-800 hover:bg-surface-200 dark:hover:bg-surface-700 text-surface-600 dark:text-surface-300 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>

                <span class="ml-1 text-sm font-semibold text-surface-800 dark:text-surface-100" x-text="calendarTitle"></span>

                <div class="w-px h-6 bg-surface-200 dark:bg-surface-700 mx-1"></div>

                <!-- Flatpickr Date Picker -->
                <div class="relative">
                    <input id="ms-datepicker" type="text" readonly
                           class="w-[130px] pl-8 pr-3 py-1.5 rounded-lg bg-surface-100 dark:bg-surface-800 border border-surface-200 dark:border-surface-700 text-xs text-surface-600 dark:text-surface-300 cursor-pointer outline-none focus:ring-2 focus:ring-primary-500/20"
                           placeholder="Jump to date">
                    <svg class="w-3.5 h-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-surface-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>

                <!-- Settings Gear -->
                <button @click="showSettingsModal = true"
                        class="p-1.5 rounded-lg bg-surface-100 dark:bg-surface-800 hover:bg-surface-200 dark:hover:bg-surface-700 text-surface-500 dark:text-surface-400 transition-all"
                        title="Display Settings">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Calendar Container -->
    <div class="rounded-2xl border border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900 shadow-soft overflow-hidden">
        <div id="master-schedule-calendar" class="min-h-[700px]" style="--fc-border-color: #e2e8f0; --fc-today-bg-color: rgba(99,102,241,0.05); --fc-page-bg-color: transparent; --fc-neutral-bg-color: rgba(148,163,184,0.08);"></div>
    </div>

    <!-- ===== EVENT ACTIONS MODAL ===== -->
    <template x-teleport="body">
        <div x-show="showActionsModal" x-cloak @keydown.escape.window="showActionsModal = false" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5);" @click.self="showActionsModal = false">
            <div class="bg-white dark:bg-surface-900 rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto" @click.stop>
                <!-- Header -->
                <div class="flex items-center justify-between px-5 py-4 border-b border-surface-200 dark:border-surface-800">
                    <div>
                        <h3 class="text-base font-semibold text-surface-800 dark:text-surface-100" x-text="selectedEvent?.title || 'Event Actions'"></h3>
                        <p class="text-xs text-surface-400 mt-0.5" x-text="selectedEvent ? formatEventTime(selectedEvent) : ''"></p>
                    </div>
                    <button @click="showActionsModal = false" class="p-1 rounded-lg hover:bg-surface-100 dark:hover:bg-surface-800 text-surface-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Event Summary -->
                <div class="px-5 py-3 bg-surface-50 dark:bg-surface-800/50 border-b border-surface-200 dark:border-surface-800" x-show="selectedEvent">
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div>
                            <div class="text-lg font-bold text-primary-600 dark:text-primary-400" x-text="selectedEvent?.extendedProps?.booked + '/' + selectedEvent?.extendedProps?.capacity"></div>
                            <div class="text-[10px] text-surface-400 uppercase tracking-wider">Booked</div>
                        </div>
                        <div>
                            <div class="text-lg font-bold text-surface-700 dark:text-surface-200" x-text="'$' + (selectedEvent?.extendedProps?.price || 0).toFixed(2)"></div>
                            <div class="text-[10px] text-surface-400 uppercase tracking-wider">Price</div>
                        </div>
                        <div>
                            <div class="text-lg font-bold" :class="selectedEvent?.extendedProps?.bookingStatus ? 'text-green-600' : 'text-red-500'" x-text="selectedEvent?.extendedProps?.bookingStatus ? 'Open' : 'Closed'"></div>
                            <div class="text-[10px] text-surface-400 uppercase tracking-wider">Booking</div>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-1.5 mt-2 justify-center" x-show="selectedEvent?.extendedProps?.coachName || selectedEvent?.extendedProps?.hotDeal || selectedEvent?.extendedProps?.earlyBird || selectedEvent?.extendedProps?.courtNames">
                        <span x-show="selectedEvent?.extendedProps?.coachName" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span x-text="selectedEvent?.extendedProps?.coachName"></span>
                        </span>
                        <span x-show="selectedEvent?.extendedProps?.courtNames" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-teal-50 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                            <span x-text="selectedEvent?.extendedProps?.courtNames"></span>
                        </span>
                        <span x-show="selectedEvent?.extendedProps?.hotDeal" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400">Hot Deal</span>
                        <span x-show="selectedEvent?.extendedProps?.earlyBird" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400">Early Bird</span>
                    </div>
                </div>

                <!-- Action Grid -->
                <div class="p-5 grid grid-cols-3 gap-2">
                    <button @click="openModal('editType')" class="flex flex-col items-center gap-1.5 p-3 rounded-xl hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors border border-transparent hover:border-surface-200 dark:hover:border-surface-700">
                        <div class="w-9 h-9 rounded-lg bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center"><svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></div>
                        <span class="text-[11px] font-medium text-surface-600 dark:text-surface-300">Edit Type</span>
                    </button>
                    <button @click="openModal('notes')" class="flex flex-col items-center gap-1.5 p-3 rounded-xl hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors border border-transparent hover:border-surface-200 dark:hover:border-surface-700 relative">
                        <div class="w-9 h-9 rounded-lg bg-yellow-50 dark:bg-yellow-900/30 flex items-center justify-center"><svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-7 0l9-9m0 0h-6m6 0v6"/></svg></div>
                        <span class="text-[11px] font-medium text-surface-600 dark:text-surface-300">Notes</span>
                        <span x-show="selectedEvent?.extendedProps?.notesCount > 0" class="absolute top-1 right-1 bg-yellow-500 text-white text-[9px] font-bold rounded-full w-4 h-4 flex items-center justify-center" x-text="selectedEvent?.extendedProps?.notesCount"></span>
                    </button>
                    <button @click="openModal('editTime')" class="flex flex-col items-center gap-1.5 p-3 rounded-xl hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors border border-transparent hover:border-surface-200 dark:hover:border-surface-700">
                        <div class="w-9 h-9 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center"><svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                        <span class="text-[11px] font-medium text-surface-600 dark:text-surface-300">Edit Time</span>
                    </button>
                    <button @click="openModal('courts')" class="flex flex-col items-center gap-1.5 p-3 rounded-xl hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors border border-transparent hover:border-surface-200 dark:hover:border-surface-700 relative">
                        <div class="w-9 h-9 rounded-lg bg-teal-50 dark:bg-teal-900/30 flex items-center justify-center"><svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg></div>
                        <span class="text-[11px] font-medium text-surface-600 dark:text-surface-300">Courts</span>
                        <span x-show="selectedEvent?.extendedProps?.courtsCount > 0" class="absolute top-1 right-1 bg-teal-500 text-white text-[9px] font-bold rounded-full w-4 h-4 flex items-center justify-center" x-text="selectedEvent?.extendedProps?.courtsCount"></span>
                    </button>
                    <button @click="openModal('facilitator')" class="flex flex-col items-center gap-1.5 p-3 rounded-xl hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors border border-transparent hover:border-surface-200 dark:hover:border-surface-700">
                        <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center"><svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                        <span class="text-[11px] font-medium text-surface-600 dark:text-surface-300">Facilitator</span>
                    </button>
                    <button @click="openModal('attendees')" class="flex flex-col items-center gap-1.5 p-3 rounded-xl hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors border border-transparent hover:border-surface-200 dark:hover:border-surface-700 relative">
                        <div class="w-9 h-9 rounded-lg bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center"><svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                        <span class="text-[11px] font-medium text-surface-600 dark:text-surface-300">Attendees</span>
                        <span x-show="selectedEvent?.extendedProps?.attendeesCount > 0" class="absolute top-1 right-1 bg-purple-500 text-white text-[9px] font-bold rounded-full w-4 h-4 flex items-center justify-center" x-text="selectedEvent?.extendedProps?.attendeesCount"></span>
                    </button>
                    <button @click="openModal('capacity')" class="flex flex-col items-center gap-1.5 p-3 rounded-xl hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors border border-transparent hover:border-surface-200 dark:hover:border-surface-700">
                        <div class="w-9 h-9 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center"><svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg></div>
                        <span class="text-[11px] font-medium text-surface-600 dark:text-surface-300">Capacity</span>
                    </button>
                    <button @click="toggleBooking()" class="flex flex-col items-center gap-1.5 p-3 rounded-xl hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors border border-transparent hover:border-surface-200 dark:hover:border-surface-700">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center" :class="selectedEvent?.extendedProps?.bookingStatus ? 'bg-green-50 dark:bg-green-900/30' : 'bg-red-50 dark:bg-red-900/30'">
                            <svg class="w-4 h-4" :class="selectedEvent?.extendedProps?.bookingStatus ? 'text-green-500' : 'text-red-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="text-[11px] font-medium text-surface-600 dark:text-surface-300" x-text="selectedEvent?.extendedProps?.bookingStatus ? 'Close Booking' : 'Open Booking'"></span>
                    </button>
                    <button @click="openModal('hotDeal')" class="flex flex-col items-center gap-1.5 p-3 rounded-xl hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors border border-transparent hover:border-surface-200 dark:hover:border-surface-700">
                        <div class="w-9 h-9 rounded-lg bg-orange-50 dark:bg-orange-900/30 flex items-center justify-center"><span class="text-base"></span></div>
                        <span class="text-[11px] font-medium text-surface-600 dark:text-surface-300">Hot Deal</span>
                    </button>
                    <button @click="openModal('earlyBird')" class="flex flex-col items-center gap-1.5 p-3 rounded-xl hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors border border-transparent hover:border-surface-200 dark:hover:border-surface-700">
                        <div class="w-9 h-9 rounded-lg bg-green-50 dark:bg-green-900/30 flex items-center justify-center"><span class="text-base"></span></div>
                        <span class="text-[11px] font-medium text-surface-600 dark:text-surface-300">Early Bird</span>
                    </button>
                    <button @click="openModal('feedback')" class="flex flex-col items-center gap-1.5 p-3 rounded-xl hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors border border-transparent hover:border-surface-200 dark:hover:border-surface-700">
                        <div class="w-9 h-9 rounded-lg bg-pink-50 dark:bg-pink-900/30 flex items-center justify-center"><svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg></div>
                        <span class="text-[11px] font-medium text-surface-600 dark:text-surface-300">Feedback</span>
                    </button>
                    <button @click="copyLink()" class="flex flex-col items-center gap-1.5 p-3 rounded-xl hover:bg-surface-50 dark:hover:bg-surface-800 transition-colors border border-transparent hover:border-surface-200 dark:hover:border-surface-700">
                        <div class="w-9 h-9 rounded-lg bg-surface-100 dark:bg-surface-800 flex items-center justify-center"><svg class="w-4 h-4 text-surface-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg></div>
                        <span class="text-[11px] font-medium text-surface-600 dark:text-surface-300">Copy Link</span>
                    </button>
                    <button @click="openModal('delete')" class="flex flex-col items-center gap-1.5 p-3 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors border border-transparent hover:border-red-200 dark:hover:border-red-800">
                        <div class="w-9 h-9 rounded-lg bg-red-50 dark:bg-red-900/30 flex items-center justify-center"><svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></div>
                        <span class="text-[11px] font-medium text-red-600 dark:text-red-400">Delete</span>
                    </button>
                </div>
            </div>
        </div>
    </template>
    <!-- ===== SETTINGS MODAL ===== -->
    <template x-teleport="body">
        <div x-show="showSettingsModal" x-cloak @keydown.escape.window="showSettingsModal = false" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5);" @click.self="showSettingsModal = false">
            <div class="bg-white dark:bg-surface-900 rounded-2xl shadow-xl w-full max-w-md" @click.stop>
                <div class="flex items-center justify-between px-5 py-4 border-b border-surface-200 dark:border-surface-800">
                    <h3 class="text-sm font-semibold text-surface-800 dark:text-surface-100">Calendar Display Settings</h3>
                    <button @click="showSettingsModal = false" class="p-1 rounded-lg hover:bg-surface-100 dark:hover:bg-surface-800 text-surface-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="p-5 space-y-4">
                    <p class="text-xs text-surface-400">Choose which details to display on calendar events.</p>
                    <div class="space-y-2">
                        <template x-for="field in settingsFields" :key="field.key">
                            <label class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-surface-50 dark:hover:bg-surface-800 cursor-pointer">
                                <input type="checkbox" :checked="displaySettings[field.key]" @change="toggleDisplaySetting(field.key)" class="rounded border-surface-300 text-primary-500 focus:ring-primary-500/20">
                                <div>
                                    <span class="text-sm text-surface-700 dark:text-surface-200" x-text="field.label"></span>
                                    <p class="text-[10px] text-surface-400" x-text="field.desc"></p>
                                </div>
                            </label>
                        </template>
                    </div>
                    <div class="pt-2 border-t border-surface-200 dark:border-surface-800">
                        <button @click="resetDisplaySettings()" class="text-xs text-primary-500 hover:text-primary-600 font-medium">Reset to defaults</button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- ===== SUB-MODALS ===== -->
    <template x-teleport="body">
    <div>
        <!-- Facilitator Modal -->
        <div x-show="activeModal === 'facilitator'" x-cloak @keydown.escape.window="activeModal = null" class="fixed inset-0 z-[110] flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5);" @click.self="activeModal = null">
            <div class="bg-white dark:bg-surface-900 rounded-2xl shadow-xl w-full max-w-md" @click.stop>
                <div class="flex items-center justify-between px-5 py-4 border-b border-surface-200 dark:border-surface-800">
                    <h3 class="text-sm font-semibold text-surface-800 dark:text-surface-100">Assign Facilitator</h3>
                    <button @click="activeModal = null" class="p-1 rounded-lg hover:bg-surface-100 dark:hover:bg-surface-800 text-surface-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="p-5 space-y-3">
                    <input type="text" x-model="facilitatorSearch" placeholder="Search users..." class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white">
                    <select x-model="modalData.coachId" class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white">
                        <option value="">No Facilitator</option>
                        <template x-for="u in filteredFacilitators" :key="u.id">
                            <option :value="u.id" x-text="u.first_name + ' ' + (u.last_name || '')"></option>
                        </template>
                    </select>
                    <button @click="saveFacilitator()" :disabled="saving" class="w-full py-2.5 rounded-xl bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium transition-colors disabled:opacity-50">
                        <span x-show="!saving">Save Facilitator</span><span x-show="saving">Saving...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Edit Time Modal -->
        <div x-show="activeModal === 'editTime'" x-cloak @keydown.escape.window="activeModal = null" class="fixed inset-0 z-[110] flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5);" @click.self="activeModal = null">
            <div class="bg-white dark:bg-surface-900 rounded-2xl shadow-xl w-full max-w-md" @click.stop>
                <div class="flex items-center justify-between px-5 py-4 border-b border-surface-200 dark:border-surface-800">
                    <h3 class="text-sm font-semibold text-surface-800 dark:text-surface-100">Edit Date & Time</h3>
                    <button @click="activeModal = null" class="p-1 rounded-lg hover:bg-surface-100 dark:hover:bg-surface-800 text-surface-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="p-5 space-y-3">
                    <label class="block text-xs font-medium text-surface-600 dark:text-surface-400">Date & Time</label>
                    <input type="datetime-local" x-model="modalData.scheduledAt" class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white">
                    <button @click="saveTime()" :disabled="saving" class="w-full py-2.5 rounded-xl bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium transition-colors disabled:opacity-50">
                        <span x-show="!saving">Update Time</span><span x-show="saving">Saving...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Capacity Modal -->
        <div x-show="activeModal === 'capacity'" x-cloak @keydown.escape.window="activeModal = null" class="fixed inset-0 z-[110] flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5);" @click.self="activeModal = null">
            <div class="bg-white dark:bg-surface-900 rounded-2xl shadow-xl w-full max-w-md" @click.stop>
                <div class="flex items-center justify-between px-5 py-4 border-b border-surface-200 dark:border-surface-800">
                    <h3 class="text-sm font-semibold text-surface-800 dark:text-surface-100">Update Capacity</h3>
                    <button @click="activeModal = null" class="p-1 rounded-lg hover:bg-surface-100 dark:hover:bg-surface-800 text-surface-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="p-5 space-y-3">
                    <label class="block text-xs font-medium text-surface-600 dark:text-surface-400">Total Slots</label>
                    <input type="number" x-model.number="modalData.slots" min="0" class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white">
                    <button @click="saveCapacity()" :disabled="saving" class="w-full py-2.5 rounded-xl bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium transition-colors disabled:opacity-50">
                        <span x-show="!saving">Update Capacity</span><span x-show="saving">Saving...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Notes Modal -->
        <div x-show="activeModal === 'notes'" x-cloak @keydown.escape.window="activeModal = null" class="fixed inset-0 z-[110] flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5);" @click.self="activeModal = null">
            <div class="bg-white dark:bg-surface-900 rounded-2xl shadow-xl w-full max-w-lg max-h-[80vh] flex flex-col" @click.stop>
                <div class="flex items-center justify-between px-5 py-4 border-b border-surface-200 dark:border-surface-800">
                    <h3 class="text-sm font-semibold text-surface-800 dark:text-surface-100">Class Notes</h3>
                    <button @click="activeModal = null" class="p-1 rounded-lg hover:bg-surface-100 dark:hover:bg-surface-800 text-surface-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="flex-1 overflow-y-auto p-5 space-y-3">
                    <div class="flex gap-2">
                        <input x-model="modalData.newNote" @keydown.enter="addNote()" placeholder="Add a note..." class="flex-1 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-primary-500/20 dark:text-white">
                        <button @click="addNote()" :disabled="saving || !modalData.newNote?.trim()" class="px-4 py-2 rounded-xl bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium disabled:opacity-50">Add</button>
                    </div>
                    <div class="space-y-2">
                        <template x-for="n in modalData.notes" :key="n.id">
                            <div class="flex items-start gap-3 p-3 rounded-xl bg-surface-50 dark:bg-surface-800/50">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-surface-700 dark:text-surface-200" x-text="n.note"></p>
                                    <p class="text-[10px] text-surface-400 mt-1" x-text="(n.first_name || 'System') + ' \u2014 ' + new Date(n.created_at).toLocaleString()"></p>
                                </div>
                                <button @click="deleteNote(n.id)" class="p-1 rounded hover:bg-red-100 dark:hover:bg-red-900/30 text-red-400 hover:text-red-600 flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </template>
                        <p x-show="!modalData.notes?.length" class="text-sm text-surface-400 text-center py-4">No notes yet</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Courts Modal -->
        <div x-show="activeModal === 'courts'" x-cloak @keydown.escape.window="activeModal = null" class="fixed inset-0 z-[110] flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5);" @click.self="activeModal = null">
            <div class="bg-white dark:bg-surface-900 rounded-2xl shadow-xl w-full max-w-md" @click.stop>
                <div class="flex items-center justify-between px-5 py-4 border-b border-surface-200 dark:border-surface-800">
                    <h3 class="text-sm font-semibold text-surface-800 dark:text-surface-100">Assign Courts</h3>
                    <button @click="activeModal = null" class="p-1 rounded-lg hover:bg-surface-100 dark:hover:bg-surface-800 text-surface-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="p-5 space-y-3">
                    <div class="space-y-2 max-h-60 overflow-y-auto">
                        <template x-for="court in modalData.allCourts" :key="court.id">
                            <label class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-surface-50 dark:hover:bg-surface-800 cursor-pointer">
                                <input type="checkbox" :value="court.id" x-model="modalData.selectedCourtIds" class="rounded border-surface-300 text-primary-500 focus:ring-primary-500/20">
                                <span class="text-sm text-surface-700 dark:text-surface-200" x-text="court.name || ('Court #' + court.court_number)"></span>
                            </label>
                        </template>
                        <p x-show="!modalData.allCourts?.length" class="text-sm text-surface-400 text-center py-4">No courts available</p>
                    </div>
                    <button @click="saveCourts()" :disabled="saving" class="w-full py-2.5 rounded-xl bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium transition-colors disabled:opacity-50">
                        <span x-show="!saving">Save Courts</span><span x-show="saving">Saving...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Attendees Modal (Enhanced) -->
        <!-- ===== ATTENDEES SIDEBAR DRAWER ===== -->
        <div x-show="activeModal === 'attendees'" x-cloak @keydown.escape.window="activeModal = null"
             class="fixed inset-0 z-[110]"
             style="background: rgba(0,0,0,0.5);"
             @click.self="activeModal = null"
             x-transition:enter="transition-opacity ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <!-- Slide-in panel from right -->
            <div class="absolute right-0 top-0 bottom-0 w-full max-w-2xl bg-white dark:bg-surface-900 flex flex-col shadow-2xl"
                 @click.stop
                 x-transition:enter="transition transform ease-out duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition transform ease-in duration-200"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full">

                <!-- Header -->
                <div class="flex items-start justify-between px-5 py-4 border-b border-surface-200 dark:border-surface-700 bg-surface-50 dark:bg-surface-800/50 flex-shrink-0">
                    <div>
                        <h2 class="text-base font-bold text-surface-800 dark:text-surface-100" x-text="selectedEvent?.title || 'Attendees'"></h2>
                        <p class="text-xs text-surface-400 mt-0.5" x-text="selectedEvent ? formatEventTime(selectedEvent) : ''"></p>
                    </div>
                    <button @click="activeModal = null" class="p-1.5 rounded-lg hover:bg-surface-200 dark:hover:bg-surface-700 text-surface-400 ml-4 flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Stats Strip -->
                <div class="grid grid-cols-4 divide-x divide-surface-200 dark:divide-surface-700 border-b border-surface-200 dark:border-surface-700 flex-shrink-0">
                    <div class="py-2.5 text-center">
                        <div class="text-lg font-bold text-primary-600 dark:text-primary-400" x-text="attendeeStats.total"></div>
                        <div class="text-[9px] uppercase tracking-wider text-surface-400">Total</div>
                    </div>
                    <div class="py-2.5 text-center">
                        <div class="text-lg font-bold text-green-600" x-text="attendeeStats.checkedIn"></div>
                        <div class="text-[9px] uppercase tracking-wider text-surface-400">Checked In</div>
                    </div>
                    <div class="py-2.5 text-center">
                        <div class="text-lg font-bold text-surface-700 dark:text-surface-200" x-text="attendeeStats.registered"></div>
                        <div class="text-[9px] uppercase tracking-wider text-surface-400">Registered</div>
                    </div>
                    <div class="py-2.5 text-center">
                        <div class="text-lg font-bold" :class="attendeeStats.spotsLeft > 3 ? 'text-emerald-600' : attendeeStats.spotsLeft > 0 ? 'text-amber-500' : 'text-red-500'" x-text="attendeeStats.spotsLeft"></div>
                        <div class="text-[9px] uppercase tracking-wider text-surface-400">Spots Left</div>
                    </div>
                </div>

                <!-- Tab Navigation -->
                <div class="flex border-b border-surface-200 dark:border-surface-700 flex-shrink-0">
                    <button @click="modalData.attendeeTab = 'list'" class="flex-1 py-2.5 text-xs font-semibold transition-colors"
                            :class="modalData.attendeeTab === 'list' ? 'text-primary-600 border-b-2 border-primary-500' : 'text-surface-500 hover:text-surface-700 dark:hover:text-surface-300'">
                        <span x-text="'\uD83D\uDC65 Attendees (' + attendeeStats.total + ')'"></span>
                    </button>
                    <button @click="modalData.attendeeTab = 'add'; modalData.addMode = 'search'; modalData.playerQuery = ''; modalData.playerResults = []; modalData.selectedPlayer = null;" class="flex-1 py-2.5 text-xs font-semibold transition-colors"
                            :class="modalData.attendeeTab === 'add' ? 'text-primary-600 border-b-2 border-primary-500' : 'text-surface-500 hover:text-surface-700 dark:hover:text-surface-300'">
                        ➕ Add New
                    </button>
                    <button @click="modalData.attendeeTab = 'partners'" class="flex-1 py-2.5 text-xs font-semibold transition-colors"
                            :class="modalData.attendeeTab === 'partners' ? 'text-primary-600 border-b-2 border-primary-500' : 'text-surface-500 hover:text-surface-700 dark:hover:text-surface-300'">
                        <span x-text="'\uD83E\uDD1D Partners (' + pairedAttendees.length + ')'"></span>
                    </button>
                </div>

                <!-- Tab Body (scrollable) -->
                <div class="flex-1 overflow-y-auto">

                    <!-- ── LIST TAB ── -->
                    <div x-show="modalData.attendeeTab === 'list'" class="p-4 space-y-3">
                        <!-- Search + Bulk actions -->
                        <div class="flex gap-2 items-center">
                            <div class="relative flex-1">
                                <svg class="w-3.5 h-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-surface-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                                <input type="text" x-model="modalData.attendeeSearch" placeholder="Search attendees..." class="w-full pl-8 pr-3 py-1.5 rounded-lg border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 text-xs text-surface-700 dark:text-surface-200 outline-none focus:ring-2 focus:ring-primary-500/20">
                            </div>
                            <button @click="bulkCheckIn()" class="px-3 py-1.5 rounded-lg bg-green-500 hover:bg-green-600 text-white text-xs font-medium transition-colors flex-shrink-0" title="Check in all registered attendees">
                                ✓ All In
                            </button>
                        </div>

                        <!-- Attendee rows -->
                        <template x-for="att in filteredAttendees" :key="att.id">
                            <div class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden">
                                <div class="flex items-center gap-3 px-3 py-2.5">
                                    <!-- Avatar -->
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                         :style="'background:' + (att.checked_in ? '#10b981' : '#6366f1')">
                                        <span x-text="(att.first_name || '?').charAt(0).toUpperCase()"></span>
                                    </div>
                                    <!-- Name / details -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="text-sm font-semibold text-surface-800 dark:text-surface-100" x-text="att.first_name + ' ' + (att.last_name || '')"></span>
                                            <!-- Status badge -->
                                            <span class="text-[9px] px-1.5 py-0.5 rounded-full font-bold uppercase tracking-wider"
                                                  :class="{
                                                    'bg-green-100 text-green-700': att.status === 'registered',
                                                    'bg-amber-100 text-amber-700': att.status === 'waitlisted',
                                                    'bg-red-100 text-red-600':     att.status === 'cancelled',
                                                    'bg-surface-100 text-surface-500': att.status === 'no_show'
                                                  }"
                                                  x-text="att.status"></span>
                                            <!-- Partner badge -->
                                            <span x-show="att.partner_id" class="text-[9px] px-1.5 py-0.5 rounded-full font-medium bg-blue-100 text-blue-600">Paired</span>
                                        </div>
                                        <div class="flex items-center gap-2 mt-0.5 text-[10px] text-surface-400 flex-wrap">
                                            <span x-show="att.email" x-text="att.email"></span>
                                            <span x-show="att.phone" x-text="att.phone"></span>
                                            <span x-show="att.amount_paid" class="text-green-600 dark:text-green-400 font-medium" x-text="'$' + parseFloat(att.amount_paid || 0).toFixed(2)"></span>
                                        </div>
                                        <div x-show="att.notes" class="mt-0.5 text-[10px] text-surface-400 truncate" x-text="att.notes"></div>
                                    </div>
                                    <!-- Actions -->
                                    <div class="flex items-center gap-1 flex-shrink-0">
                                        <!-- Check-in toggle -->
                                        <button @click="att.checked_in = att.checked_in ? 0 : 1; updateAttendee(att)"
                                                class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors"
                                                :class="att.checked_in ? 'bg-green-500 text-white' : 'bg-surface-100 dark:bg-surface-700 text-surface-400 hover:bg-green-100 hover:text-green-600'"
                                                :title="att.checked_in ? 'Checked In — click to undo' : 'Mark as Checked In'">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                        <!-- Status dropdown -->
                                        <select x-model="att.status" @change="updateAttendee(att)"
                                                class="text-[10px] rounded-lg border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-1.5 py-1 outline-none dark:text-white">
                                            <option value="registered">Registered</option>
                                            <option value="waitlisted">Waitlisted</option>
                                            <option value="cancelled">Cancelled</option>
                                            <option value="no_show">No Show</option>
                                        </select>
                                        <!-- Remove -->
                                        <button @click="removeAttendee(att.id)"
                                                class="w-7 h-7 rounded-lg flex items-center justify-center bg-surface-100 dark:bg-surface-700 text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </div>
                                <!-- Edit amount row (collapsed inline) -->
                                <div class="px-3 pb-2 flex gap-2 items-center border-t border-surface-100 dark:border-surface-700/50 pt-2">
                                    <span class="text-[10px] text-surface-400">Amount paid:</span>
                                    <input type="number" step="0.01" x-model.number="att.amount_paid" @change="updateAttendeeAmount(att)"
                                           class="w-24 text-xs rounded border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-2 py-0.5 outline-none focus:ring-1 focus:ring-primary-500/30 dark:text-white">
                                    <span class="text-[10px] text-surface-400 ml-2">Payment:</span>
                                    <select x-model="att.payment_status" @change="updateAttendeeAmount(att)"
                                            class="text-[10px] rounded border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-1.5 py-0.5 outline-none dark:text-white">
                                        <option value="paid">Paid</option>
                                        <option value="pending">Pending</option>
                                        <option value="free">Free</option>
                                        <option value="refunded">Refunded</option>
                                    </select>
                                </div>
                            </div>
                        </template>
                        <p x-show="!filteredAttendees.length && !modalData.attendeeSearch" class="text-sm text-surface-400 text-center py-8">No attendees yet. Use the Add New tab to register someone.</p>
                        <p x-show="!filteredAttendees.length && modalData.attendeeSearch" class="text-sm text-surface-400 text-center py-8">No attendees match your search.</p>
                    </div>

                    <!-- ── ADD NEW TAB ── -->
                    <div x-show="modalData.attendeeTab === 'add'" class="p-4 space-y-4">
                        <!-- Mode toggle -->
                        <div class="flex rounded-xl overflow-hidden border border-surface-200 dark:border-surface-700">
                            <button @click="modalData.addMode = 'search'; modalData.playerQuery = ''; modalData.playerResults = []; modalData.selectedPlayer = null;" class="flex-1 py-2 text-xs font-semibold transition-colors"
                                    :class="modalData.addMode === 'search' ? 'bg-primary-500 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-300 hover:bg-surface-50'">
                                🔍 Search Player
                            </button>
                            <button @click="modalData.addMode = 'manual'; modalData.selectedPlayer = null; modalData.newAttendee = { player_id: '', first_name: '', last_name: '', email: '', phone: '', amount_paid: '', quote_amount: '', payment_status: 'pending', status: 'registered', notes: '' }" class="flex-1 py-2 text-xs font-semibold transition-colors"
                                    :class="modalData.addMode === 'manual' ? 'bg-primary-500 text-white' : 'bg-white dark:bg-surface-800 text-surface-600 dark:text-surface-300 hover:bg-surface-50'">
                                ✏️ Manual Entry
                            </button>
                        </div>

                        <!-- Player Search -->
                        <div x-show="modalData.addMode === 'search'" class="space-y-3">
                            <div class="relative">
                                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-surface-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                                <input type="text" x-model="modalData.playerQuery"
                                       @input.debounce.400ms="searchPlayers()"
                                       placeholder="Search by name or email..."
                                       class="w-full pl-10 pr-3 py-2.5 rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 text-sm text-surface-700 dark:text-surface-200 outline-none focus:ring-2 focus:ring-primary-500/20">
                                <div x-show="modalData.playerLoading" class="absolute right-3 top-1/2 -translate-y-1/2">
                                    <div class="w-4 h-4 border-2 border-primary-500 border-t-transparent rounded-full animate-spin"></div>
                                </div>
                            </div>
                            <!-- Search results dropdown -->
                            <div x-show="modalData.playerResults.length > 0" class="rounded-xl border border-surface-200 dark:border-surface-700 overflow-hidden">
                                <template x-for="pl in modalData.playerResults" :key="pl.id">
                                    <button @click="selectPlayerForAttendee(pl)" class="w-full flex items-center gap-3 px-3 py-2.5 hover:bg-primary-50 dark:hover:bg-primary-900/20 border-b border-surface-100 dark:border-surface-700/50 last:border-0 transition-colors text-left">
                                        <div class="w-7 h-7 rounded-full bg-primary-100 dark:bg-primary-900/40 flex items-center justify-center text-primary-600 text-xs font-bold flex-shrink-0">
                                            <span x-text="(pl.first_name || '?').charAt(0)"></span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium text-surface-800 dark:text-surface-100" x-text="pl.first_name + ' ' + (pl.last_name || '')"></div>
                                            <div class="text-[10px] text-surface-400" x-text="pl.email || pl.phone || ''"></div>
                                        </div>
                                    </button>
                                </template>
                            </div>
                            <!-- Selected player preview -->
                            <div x-show="modalData.selectedPlayer" class="p-3 rounded-xl bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-700/50">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span class="text-sm font-semibold text-primary-700 dark:text-primary-300" x-text="modalData.selectedPlayer ? (modalData.selectedPlayer.first_name + ' ' + (modalData.selectedPlayer.last_name || '')) : ''"></span>
                                    <button @click="modalData.selectedPlayer = null; modalData.playerQuery = ''; modalData.newAttendee.player_id = '';" class="ml-auto text-primary-400 hover:text-primary-600">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                                <div class="text-[10px] text-surface-400 mt-1" x-text="(modalData.selectedPlayer?.email || '') + (modalData.selectedPlayer?.phone ? ' · ' + modalData.selectedPlayer.phone : '')"></div>
                            </div>
                            <p x-show="modalData.playerQuery.length > 1 && !modalData.playerResults.length && !modalData.playerLoading" class="text-xs text-surface-400 text-center py-2">No players found. Switch to Manual Entry to add a guest.</p>
                        </div>

                        <!-- Booking / Payment Form (shown after player selected OR in manual mode) -->
                        <div x-show="modalData.addMode === 'manual' || modalData.selectedPlayer" class="space-y-3">
                            <!-- Name fields (manual only) -->
                            <div x-show="modalData.addMode === 'manual'" class="grid grid-cols-2 gap-2">
                                <input x-model="modalData.newAttendee.first_name" placeholder="First Name *" class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-2 text-sm outline-none dark:text-white focus:ring-2 focus:ring-primary-500/20">
                                <input x-model="modalData.newAttendee.last_name" placeholder="Last Name" class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-2 text-sm outline-none dark:text-white focus:ring-2 focus:ring-primary-500/20">
                            </div>
                            <div x-show="modalData.addMode === 'manual'" class="grid grid-cols-2 gap-2">
                                <input x-model="modalData.newAttendee.email" placeholder="Email" type="email" class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-2 text-sm outline-none dark:text-white focus:ring-2 focus:ring-primary-500/20">
                                <input x-model="modalData.newAttendee.phone" placeholder="Phone" class="rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-2 text-sm outline-none dark:text-white focus:ring-2 focus:ring-primary-500/20">
                            </div>
                            <!-- Booking fields (always shown) -->
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[10px] font-medium text-surface-500 dark:text-surface-400 mb-1">Booking Status</label>
                                    <select x-model="modalData.newAttendee.status" class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-2 text-sm outline-none dark:text-white focus:ring-2 focus:ring-primary-500/20">
                                        <option value="registered">Registered</option>
                                        <option value="waitlisted">Waitlisted</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-medium text-surface-500 dark:text-surface-400 mb-1">Payment Status</label>
                                    <select x-model="modalData.newAttendee.payment_status" class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-2 text-sm outline-none dark:text-white focus:ring-2 focus:ring-primary-500/20">
                                        <option value="pending">Pending</option>
                                        <option value="paid">Paid</option>
                                        <option value="free">Free / Comp</option>
                                        <option value="refunded">Refunded</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[10px] font-medium text-surface-500 dark:text-surface-400 mb-1">Amount Paid ($)</label>
                                    <input type="number" step="0.01" x-model.number="modalData.newAttendee.amount_paid" placeholder="0.00" class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-2 text-sm outline-none dark:text-white focus:ring-2 focus:ring-primary-500/20">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-medium text-surface-500 dark:text-surface-400 mb-1">Quoted Price ($)</label>
                                    <input type="number" step="0.01" x-model.number="modalData.newAttendee.quote_amount" placeholder="0.00" class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-2 text-sm outline-none dark:text-white focus:ring-2 focus:ring-primary-500/20">
                                </div>
                            </div>
                            <!-- Session price reference -->
                            <div class="text-[10px] text-surface-400 bg-surface-50 dark:bg-surface-800/50 rounded-lg px-3 py-2">
                                Session price: <strong class="text-surface-600 dark:text-surface-300" x-text="'$' + (selectedEvent?.extendedProps?.price || 0).toFixed(2)"></strong>
                                <span x-show="selectedEvent?.extendedProps?.hotDeal"> · Hot deal: <strong class="text-orange-500" x-text="selectedEvent?.extendedProps?.hotDeal ? '$' + selectedEvent.extendedProps.hotDeal.price.toFixed(2) : ''"></strong></span>
                                <span x-show="selectedEvent?.extendedProps?.earlyBird"> · Early bird: <strong class="text-green-500" x-text="selectedEvent?.extendedProps?.earlyBird ? '$' + selectedEvent.extendedProps.earlyBird.price.toFixed(2) : ''"></strong></span>
                            </div>
                            <div>
                                <label class="block text-[10px] font-medium text-surface-500 dark:text-surface-400 mb-1">Internal Notes</label>
                                <textarea x-model="modalData.newAttendee.notes" placeholder="Optional note about this booking..." rows="2"
                                          class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-3 py-2 text-sm outline-none dark:text-white resize-none focus:ring-2 focus:ring-primary-500/20"></textarea>
                            </div>
                            <!-- Spots check -->
                            <div x-show="attendeeStats.spotsLeft <= 0" class="text-xs text-amber-600 bg-amber-50 dark:bg-amber-900/20 rounded-lg px-3 py-2">
                                ⚠️ This session is at capacity. You can still add as Waitlisted.
                            </div>
                            <!-- Submit -->
                            <button @click="addAttendee()"
                                    :disabled="saving || (modalData.addMode === 'manual' && !modalData.newAttendee.first_name?.trim()) || (modalData.addMode === 'search' && !modalData.selectedPlayer)"
                                    class="w-full py-3 rounded-xl bg-primary-500 hover:bg-primary-600 text-white text-sm font-semibold transition-colors disabled:opacity-50">
                                <span x-show="!saving">✓ Register Attendee</span>
                                <span x-show="saving">Registering...</span>
                            </button>
                        </div>
                    </div>

                    <!-- ── PARTNER PAIRING TAB ── -->
                    <div x-show="modalData.attendeeTab === 'partners'" class="p-4 space-y-4">
                        <!-- Stats -->
                        <div class="grid grid-cols-3 gap-2">
                            <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-3 text-center">
                                <div class="text-xl font-bold text-green-600" x-text="pairedAttendees.length"></div>
                                <div class="text-[9px] text-green-600 uppercase tracking-wider">Paired Teams</div>
                            </div>
                            <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-3 text-center">
                                <div class="text-xl font-bold text-amber-600" x-text="unpairedAttendees.length"></div>
                                <div class="text-[9px] text-amber-600 uppercase tracking-wider">Unpaired</div>
                            </div>
                            <div class="bg-primary-50 dark:bg-primary-900/20 rounded-xl p-3 text-center">
                                <div class="text-xl font-bold text-primary-600" x-text="attendeeStats.total"></div>
                                <div class="text-[9px] text-primary-600 uppercase tracking-wider">Total Players</div>
                            </div>
                        </div>

                        <!-- Paired Teams -->
                        <div x-show="pairedAttendees.length > 0">
                            <h4 class="text-xs font-semibold text-surface-500 uppercase tracking-wider mb-2">✅ Paired Teams</h4>
                            <div class="space-y-2">
                                <template x-for="pair in pairedAttendees" :key="pair[0].id">
                                    <div class="flex items-center gap-2 p-2.5 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700/50">
                                        <div class="flex-1 flex items-center gap-2">
                                            <span class="w-6 h-6 rounded-full bg-green-200 flex items-center justify-center text-green-700 text-[10px] font-bold" x-text="(pair[0].first_name || '?').charAt(0)"></span>
                                            <span class="text-xs font-medium text-surface-700 dark:text-surface-200" x-text="pair[0].first_name + ' ' + (pair[0].last_name || '')"></span>
                                            <span class="text-surface-400 text-xs">↔️</span>
                                            <span class="w-6 h-6 rounded-full bg-green-200 flex items-center justify-center text-green-700 text-[10px] font-bold" x-text="(pair[1].first_name || '?').charAt(0)"></span>
                                            <span class="text-xs font-medium text-surface-700 dark:text-surface-200" x-text="pair[1].first_name + ' ' + (pair[1].last_name || '')"></span>
                                        </div>
                                        <button @click="unpairAttendee(pair[0].id, pair[1].id)" class="text-red-400 hover:text-red-600 p-1 rounded" title="Remove pairing">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Quick Pair (2+ unpaired) -->
                        <div x-show="unpairedAttendees.length >= 2" class="p-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700/50 space-y-2">
                            <h4 class="text-xs font-semibold text-blue-700 dark:text-blue-300">⚡ Quick Pair</h4>
                            <div class="grid grid-cols-2 gap-2">
                                <select x-model="modalData.pairPlayer1" class="rounded-lg border border-blue-200 dark:border-blue-700 bg-white dark:bg-surface-800 text-xs px-2 py-1.5 outline-none dark:text-white">
                                    <option value="">Select Player 1</option>
                                    <template x-for="att in unpairedAttendees" :key="att.id">
                                        <option :value="att.id" x-text="att.first_name + ' ' + (att.last_name || '')"></option>
                                    </template>
                                </select>
                                <select x-model="modalData.pairPlayer2" class="rounded-lg border border-blue-200 dark:border-blue-700 bg-white dark:bg-surface-800 text-xs px-2 py-1.5 outline-none dark:text-white">
                                    <option value="">Select Player 2</option>
                                    <template x-for="att in unpairedAttendees" :key="att.id">
                                        <option :value="att.id" x-text="att.first_name + ' ' + (att.last_name || '')"></option>
                                    </template>
                                </select>
                            </div>
                            <button @click="pairAttendees()" :disabled="!modalData.pairPlayer1 || !modalData.pairPlayer2 || saving"
                                    class="w-full py-2 rounded-lg bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold transition-colors disabled:opacity-50">
                                🔗 Pair These Players
                            </button>
                        </div>

                        <!-- Unpaired list -->
                        <div x-show="unpairedAttendees.length > 0">
                            <h4 class="text-xs font-semibold text-surface-500 uppercase tracking-wider mb-2">⏳ Needs Partner</h4>
                            <div class="space-y-1.5">
                                <template x-for="att in unpairedAttendees" :key="att.id">
                                    <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800/30">
                                        <span class="w-6 h-6 rounded-full bg-amber-200 flex items-center justify-center text-amber-700 text-[10px] font-bold" x-text="(att.first_name || '?').charAt(0)"></span>
                                        <div class="flex-1">
                                            <span class="text-xs font-medium text-surface-700 dark:text-surface-200" x-text="att.first_name + ' ' + (att.last_name || '')"></span>
                                            <span x-show="att.email" class="text-[9px] text-surface-400 block" x-text="att.email"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <p x-show="unpairedAttendees.length === 0 && pairedAttendees.length === 0" class="text-sm text-surface-400 text-center py-8">No attendees to pair yet.</p>
                        <p x-show="unpairedAttendees.length < 2 && unpairedAttendees.length > 0 && pairedAttendees.length > 0" class="text-xs text-surface-400 text-center">
                            Only 1 unpaired player remaining.
                        </p>
                    </div>

                </div><!-- end scrollable tab body -->
            </div><!-- end slide-in panel -->
        </div><!-- end attendees sidebar -->

        <!-- Hot Deal Modal -->
        <div x-show="activeModal === 'hotDeal'" x-cloak @keydown.escape.window="activeModal = null" class="fixed inset-0 z-[110] flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5);" @click.self="activeModal = null">
            <div class="bg-white dark:bg-surface-900 rounded-2xl shadow-xl w-full max-w-md" @click.stop>
                <div class="flex items-center justify-between px-5 py-4 border-b border-surface-200 dark:border-surface-800">
                    <h3 class="text-sm font-semibold text-surface-800 dark:text-surface-100">Hot Deal</h3>
                    <button @click="activeModal = null" class="p-1 rounded-lg hover:bg-surface-100 dark:hover:bg-surface-800 text-surface-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="p-5 space-y-3">
                    <div><label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1">Discount Price ($)</label>
                    <input type="number" step="0.01" x-model.number="modalData.hotDeal.discount_price" class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none dark:text-white"></div>
                    <div><label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1">Original Price ($)</label>
                    <input type="number" step="0.01" x-model.number="modalData.hotDeal.original_price" class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none dark:text-white"></div>
                    <div class="flex gap-2">
                        <button @click="saveHotDeal()" :disabled="saving" class="flex-1 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium transition-colors disabled:opacity-50">Save</button>
                        <button @click="removeHotDeal()" :disabled="saving" class="px-4 py-2.5 rounded-xl bg-surface-100 dark:bg-surface-800 hover:bg-surface-200 dark:hover:bg-surface-700 text-surface-600 dark:text-surface-300 text-sm font-medium transition-colors disabled:opacity-50">Remove</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Early Bird Modal -->
        <div x-show="activeModal === 'earlyBird'" x-cloak @keydown.escape.window="activeModal = null" class="fixed inset-0 z-[110] flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5);" @click.self="activeModal = null">
            <div class="bg-white dark:bg-surface-900 rounded-2xl shadow-xl w-full max-w-md" @click.stop>
                <div class="flex items-center justify-between px-5 py-4 border-b border-surface-200 dark:border-surface-800">
                    <h3 class="text-sm font-semibold text-surface-800 dark:text-surface-100">Early Bird</h3>
                    <button @click="activeModal = null" class="p-1 rounded-lg hover:bg-surface-100 dark:hover:bg-surface-800 text-surface-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="p-5 space-y-3">
                    <div><label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1">Discount Price ($)</label>
                    <input type="number" step="0.01" x-model.number="modalData.earlyBird.discount_price" class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none dark:text-white"></div>
                    <div><label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1">Cutoff (hours before class)</label>
                    <input type="number" x-model.number="modalData.earlyBird.cutoff_hours" class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none dark:text-white"></div>
                    <div class="flex gap-2">
                        <button @click="saveEarlyBird()" :disabled="saving" class="flex-1 py-2.5 rounded-xl bg-green-500 hover:bg-green-600 text-white text-sm font-medium transition-colors disabled:opacity-50">Save</button>
                        <button @click="removeEarlyBird()" :disabled="saving" class="px-4 py-2.5 rounded-xl bg-surface-100 dark:bg-surface-800 hover:bg-surface-200 dark:hover:bg-surface-700 text-surface-600 dark:text-surface-300 text-sm font-medium transition-colors disabled:opacity-50">Remove</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feedback Modal -->
        <div x-show="activeModal === 'feedback'" x-cloak @keydown.escape.window="activeModal = null" class="fixed inset-0 z-[110] flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5);" @click.self="activeModal = null">
            <div class="bg-white dark:bg-surface-900 rounded-2xl shadow-xl w-full max-w-md" @click.stop>
                <div class="flex items-center justify-between px-5 py-4 border-b border-surface-200 dark:border-surface-800">
                    <h3 class="text-sm font-semibold text-surface-800 dark:text-surface-100">Feedback Request</h3>
                    <button @click="activeModal = null" class="p-1 rounded-lg hover:bg-surface-100 dark:hover:bg-surface-800 text-surface-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="p-5 space-y-3">
                    <div><label class="block text-xs font-medium text-surface-600 dark:text-surface-400 mb-1">Message</label>
                    <textarea x-model="modalData.feedback.message" rows="3" class="w-full rounded-xl border border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 px-4 py-2.5 text-sm outline-none dark:text-white resize-none" placeholder="How was your experience?"></textarea></div>
                    <div class="flex gap-2">
                        <button @click="saveFeedback()" :disabled="saving" class="flex-1 py-2.5 rounded-xl bg-pink-500 hover:bg-pink-600 text-white text-sm font-medium transition-colors disabled:opacity-50">Save</button>
                        <button @click="sendFeedback()" :disabled="saving" class="px-4 py-2.5 rounded-xl bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium transition-colors disabled:opacity-50">Send to Attendees</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div x-show="activeModal === 'delete'" x-cloak @keydown.escape.window="activeModal = null" class="fixed inset-0 z-[110] flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5);" @click.self="activeModal = null">
            <div class="bg-white dark:bg-surface-900 rounded-2xl shadow-xl w-full max-w-sm" @click.stop>
                <div class="p-5 text-center space-y-4">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <h3 class="text-base font-semibold text-surface-800 dark:text-surface-100">Delete this class?</h3>
                    <p class="text-sm text-surface-500 dark:text-surface-400">This will permanently remove this scheduled class. This action cannot be undone.</p>
                    <div class="flex gap-2">
                        <button @click="activeModal = null" class="flex-1 py-2.5 rounded-xl bg-surface-100 dark:bg-surface-800 hover:bg-surface-200 dark:hover:bg-surface-700 text-surface-600 dark:text-surface-300 text-sm font-medium">Cancel</button>
                        <button @click="deleteClass()" :disabled="saving" class="flex-1 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white text-sm font-medium disabled:opacity-50">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </template>

    <!-- Toast -->
    <template x-teleport="body">
        <div x-show="toast.show" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4"
             class="fixed bottom-6 right-6 z-[200] px-4 py-3 rounded-xl shadow-lg text-sm font-medium text-white"
             :class="toast.type === 'error' ? 'bg-red-500' : 'bg-green-500'">
            <span x-text="toast.message"></span>
        </div>
    </template>
</div>
<script>
function masterSchedule() {
    const baseApi = window.APP_BASE || '';
    let calendar = null;
    let datepicker = null;

    const defaultDisplaySettings = {
        showTitle: true,
        showSlots: true,
        showPrice: true,
        showCourts: true,
        showFacilitator: true,
        showTimeRange: true,
        showCategory: false,
        showBookingBadge: true,
        showDealBadges: true,
        showSpots: true,
        showNotes: false,
        showAttendees: false,
        showDuration: false,
        textColorWhite: true,
    };

    function loadDisplaySettings() {
        try {
            const saved = localStorage.getItem('ms_display_settings');
            return saved ? { ...defaultDisplaySettings, ...JSON.parse(saved) } : { ...defaultDisplaySettings };
        } catch(e) { return { ...defaultDisplaySettings }; }
    }

    return {
        currentFacilityId: '',
        categories: [],
        categoryFilter: '',
        orgUsers: [],
        facilitatorSearch: '',
        currentView: 'timeGridDay',
        calendarTitle: '',
        showActionsModal: false,
        showSettingsModal: false,
        activeModal: null,
        selectedEvent: null,
        saving: false,
        toast: { show: false, message: '', type: 'success' },
        displaySettings: loadDisplaySettings(),

        settingsFields: [
            { key: 'showTitle',        label: 'Session Title',     desc: 'Show the session type name' },
            { key: 'showSlots',        label: 'Booked / Capacity', desc: 'Show booked vs total slots' },
            { key: 'showPrice',        label: 'Price',             desc: 'Show session price' },
            { key: 'showCourts',       label: 'Courts',            desc: 'Show assigned court names' },
            { key: 'showFacilitator',  label: 'Facilitator',       desc: 'Show assigned facilitator name' },
            { key: 'showTimeRange',    label: 'Time Range',        desc: 'Show start - end time' },
            { key: 'showCategory',     label: 'Category Name',     desc: 'Show the category label' },
            { key: 'showSpots',        label: 'Available Spots',   desc: 'Show remaining spots count' },
            { key: 'showNotes',        label: 'Notes Preview',     desc: 'Show latest note preview on event (hover to expand)' },
            { key: 'showAttendees',    label: 'Attendee Count',    desc: 'Show number of attendees' },
            { key: 'showDuration',     label: 'Duration',          desc: 'Show session duration in minutes' },
            { key: 'showBookingBadge', label: 'Booking Status',    desc: 'Show Open/Closed badge on event' },
            { key: 'showDealBadges',   label: 'Deal Badges',       desc: 'Show Hot Deal / Early Bird badges' },
            { key: 'textColorWhite',   label: 'White Text',        desc: 'Use white text on colored events (uncheck for dark text)' },
        ],

        modalData: {
            coachId: '',
            scheduledAt: '',
            slots: 0,
            notes: [],
            newNote: '',
            allCourts: [],
            selectedCourtIds: [],
            attendees: [],
            attendeeSearch: '',
            attendeeTab: 'list', // 'list' | 'add' | 'partners'
            addMode: 'search',   // 'search' | 'manual'
            playerQuery: '',
            playerResults: [],
            playerLoading: false,
            selectedPlayer: null,
            newAttendee: { player_id: '', first_name: '', last_name: '', email: '', phone: '', amount_paid: '', quote_amount: '', payment_status: 'pending', status: 'registered', notes: '' },
            hotDeal: { discount_price: 0, original_price: 0 },
            earlyBird: { discount_price: 0, cutoff_hours: 24 },
            feedback: { message: '' },
            pairPlayer1: '',
            pairPlayer2: '',
        },

        get filteredAttendees() {
            const q = (this.modalData.attendeeSearch || '').toLowerCase();
            if (!q) return this.modalData.attendees;
            return this.modalData.attendees.filter(a => {
                const name = ((a.first_name || '') + ' ' + (a.last_name || '') + ' ' + (a.email || '')).toLowerCase();
                return name.includes(q);
            });
        },
        get attendeeStats() {
            const l = this.modalData.attendees;
            return {
                total:      l.length,
                checkedIn:  l.filter(a => a.checked_in).length,
                registered: l.filter(a => a.status === 'registered').length,
                waitlisted: l.filter(a => a.status === 'waitlisted').length,
                capacity:   this.selectedEvent?.extendedProps?.capacity || 0,
                spotsLeft:  this.selectedEvent?.extendedProps?.slotsAvailable || 0,
            };
        },
        get pairedAttendees() {
            const l = this.modalData.attendees;
            const seen = new Set();
            const pairs = [];
            l.forEach(a => {
                if (a.partner_id && !seen.has(a.id)) {
                    const partner = l.find(b => b.id === Number(a.partner_id));
                    if (partner) { pairs.push([a, partner]); seen.add(a.id); seen.add(partner.id); }
                }
            });
            return pairs;
        },
        get unpairedAttendees() {
            const paired = new Set(this.pairedAttendees.flat().map(a => a.id));
            return this.modalData.attendees.filter(a => !paired.has(a.id) && a.status !== 'cancelled');
        },

        get filteredFacilitators() {
            const q = (this.facilitatorSearch || '').toLowerCase();
            if (!q) return this.orgUsers;
            return this.orgUsers.filter(u => {
                const name = ((u.first_name || '') + ' ' + (u.last_name || '')).toLowerCase();
                return name.includes(q) || (u.email || '').toLowerCase().includes(q);
            });
        },

        async init() {
            await this.loadOrgUsers();
            this.initDatepicker();
            this.$watch('categoryFilter', () => { if (calendar) calendar.refetchEvents(); });
        },

        initDatepicker() {
            const self = this;
            this.$nextTick(() => {
                const el = document.getElementById('ms-datepicker');
                if (!el || !window.flatpickr) return;
                datepicker = flatpickr(el, {
                    dateFormat: 'M d, Y',
                    theme: 'airbnb',
                    onChange(selectedDates) {
                        if (selectedDates.length && calendar) {
                            calendar.gotoDate(selectedDates[0]);
                            calendar.changeView('timeGridDay');
                            self.currentView = 'timeGridDay';
                        }
                    }
                });
            });
        },

        toggleDisplaySetting(key) {
            this.displaySettings[key] = !this.displaySettings[key];
            localStorage.setItem('ms_display_settings', JSON.stringify(this.displaySettings));
            if (calendar) calendar.refetchEvents();
        },

        resetDisplaySettings() {
            this.displaySettings = { ...defaultDisplaySettings };
            localStorage.setItem('ms_display_settings', JSON.stringify(this.displaySettings));
            if (calendar) calendar.refetchEvents();
        },

        handleFacilityChange(fid) {
            const newFid = String(fid || '');
            if (newFid === this.currentFacilityId) return;
            this.currentFacilityId = newFid;
            if (!newFid) return;
            this.loadCategories();
            this.$nextTick(() => {
                if (!calendar) {
                    this.initCalendar();
                } else {
                    calendar.refetchEvents();
                }
            });
        },

        getFacilityId() {
            return this.currentFacilityId || '';
        },

        initCalendar() {
            const el = document.getElementById('master-schedule-calendar');
            if (!el || !window.FullCalendar) return;

            const self = this;
            calendar = new FullCalendar.Calendar(el, {
                initialView: 'timeGridDay',
                headerToolbar: false,
                height: 'auto',
                allDaySlot: false,
                slotMinTime: '06:00:00',
                slotMaxTime: '22:00:00',
                slotDuration: '00:30:00',
                expandRows: true,
                slotEventOverlap: false,
                nowIndicator: true,
                editable: false,
                selectable: false,
                dayMaxEvents: true,
                weekends: true,
                eventDisplay: 'block',
                events: function(info, successCallback, failureCallback) {
                    const facilityId = self.getFacilityId();
                    if (!facilityId) { successCallback([]); return; }
                    const params = new URLSearchParams({
                        facility_id: facilityId,
                        start: info.startStr,
                        end: info.endStr,
                    });
                    if (self.categoryFilter) params.set('category_id', self.categoryFilter);

                    authFetch(baseApi + '/api/calendar?' + params.toString())
                        .then(r => r.json())
                        .then(json => successCallback(json.data || []))
                        .catch(e => { console.error('Calendar fetch error', e); failureCallback(e); });
                },
                eventContent: function(arg) {
                    const ep = arg.event.extendedProps;
                    const ds = self.displaySettings;
                    const booked = ep.booked || 0;
                    const cap = ep.capacity || 0;
                    const price = ep.price || 0;
                    const txtColor = ds.textColorWhite ? '' : 'color:#1f2937;';
                    const lines = [];

                    // Line 1: Title - booked/cap - $price
                    const line1Parts = [];
                    if (ds.showTitle) line1Parts.push(self.esc(arg.event.title));
                    if (ds.showSlots) line1Parts.push(booked + '/' + cap);
                    if (ds.showPrice) line1Parts.push('$' + price.toFixed(2));
                    if (line1Parts.length) lines.push('<div class="ms-ev-line1">' + line1Parts.join(' - ') + '</div>');

                    // Line 2: Courts · Facilitator · Category · Duration · Attendees
                    const line2Parts = [];
                    if (ds.showCourts && ep.courtNames) line2Parts.push(self.esc(ep.courtNames));
                    if (ds.showFacilitator && ep.coachName) line2Parts.push(self.esc(ep.coachName));
                    if (ds.showCategory && ep.categoryName) line2Parts.push(self.esc(ep.categoryName));
                    if (ds.showDuration && ep.duration) line2Parts.push(ep.duration + 'm');
                    if (ds.showAttendees && ep.attendeesCount > 0) line2Parts.push(ep.attendeesCount + ' att.');
                    if (line2Parts.length) lines.push('<div class="ms-ev-line2">' + line2Parts.join(' &middot; ') + '</div>');

                    // Line 3: Time range
                    if (ds.showTimeRange && ep.startTime && ep.endTime) {
                        lines.push('<div class="ms-ev-line2" style="opacity:0.7">' + ep.startTime + ' - ' + ep.endTime + '</div>');
                    }

                    // Notes preview — show truncated text with clickable "more"
                    if (ds.showNotes && ep.firstNoteText) {
                        const noteText = ep.firstNoteText;
                        const short = self.esc(noteText.substring(0, 28));
                        const hasMore = noteText.length > 28 || ep.notesCount > 1;
                        const moreCount = ep.notesCount > 1 ? ' (+' + (ep.notesCount - 1) + ')' : '';
                        const encodedNote = encodeURIComponent(ep.firstNoteText);
                        lines.push(
                            '<div class="ms-ev-note-preview">' +
                            '\uD83D\uDCDD ' + short +
                            (hasMore ? '<span class="ms-ev-note-more" onclick="msShowNotePopup(event,this)" data-note="' + encodedNote + '" data-count="' + ep.notesCount + '"> ...more' + moreCount + '</span>' : '') +
                            '</div>'
                        );
                    } else if (ds.showNotes && ep.notesCount > 0) {
                        lines.push('<div class="ms-ev-note-preview">\uD83D\uDCDD ' + ep.notesCount + ' note' + (ep.notesCount > 1 ? 's' : '') + '</div>');
                    }

                    // Badges row
                    const badges = [];
                    if (ds.showBookingBadge && !ep.bookingStatus) {
                        badges.push('<span class="ms-badge ms-badge-closed">Closed</span>');
                    }
                    if (ds.showDealBadges && ep.hotDeal) {
                        badges.push('<span class="ms-badge ms-badge-deal">Deal</span>');
                    }
                    if (ds.showDealBadges && ep.earlyBird) {
                        badges.push('<span class="ms-badge ms-badge-early">Early</span>');
                    }
                    if (ds.showSpots && ep.slotsAvailable > 0 && ep.bookingStatus) {
                        badges.push('<span class="ms-badge ms-badge-spots">' + ep.slotsAvailable + ' spots</span>');
                    }
                    if (badges.length) lines.push('<div class="ms-ev-badges">' + badges.join('') + '</div>');

                    return {
                        html: '<div class="ms-ev-content" style="' + txtColor + '">' + lines.join('') + '</div>'
                    };
                },
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    self.selectedEvent = info.event;
                    self.showActionsModal = true;
                },
                datesSet: function(info) {
                    self.calendarTitle = info.view.title;
                    self.currentView = info.view.type;
                    if (datepicker && info.view.currentStart) {
                        datepicker.setDate(info.view.currentStart, false);
                    }
                },
            });
            calendar.render();
        },

        esc(str) {
            if (!str) return '';
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        },

        calendarPrev() { if (calendar) calendar.prev(); },
        calendarNext() { if (calendar) calendar.next(); },
        calendarToday() { if (calendar) calendar.today(); },
        calendarChangeView(view) {
            if (calendar) {
                calendar.changeView(view);
                this.currentView = view;
            }
        },

        formatEventTime(event) {
            if (!event) return '';
            const s = new Date(event.start);
            const e = event.end ? new Date(event.end) : null;
            const opts = { weekday: 'short', month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' };
            let str = s.toLocaleDateString('en-US', opts);
            if (e) str += ' \u2013 ' + e.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
            return str;
        },

        async loadCategories() {
            const fid = this.getFacilityId();
            if (!fid) return;
            try {
                const res = await authFetch(baseApi + '/api/calendar/categories?facility_id=' + fid);
                const json = await res.json();
                this.categories = json.data || [];
            } catch(e) { console.error('Load categories error', e); }
        },

        async loadOrgUsers() {
            try {
                const res = await authFetch(baseApi + '/api/users?per_page=200');
                const json = await res.json();
                this.orgUsers = json.data || [];
            } catch(e) { console.error('Load org users error', e); }
        },

        _stId()  { return this.selectedEvent?.extendedProps?.sessionTypeId; },
        _clId()  { return this.selectedEvent?.extendedProps?.classId; },
        _apiBase() { return baseApi + '/api/session-types/' + this._stId() + '/classes/' + this._clId(); },

        async openModal(name) {
            this.showActionsModal = false;
            this.activeModal = name;

            if (name === 'attendees') {
                this.modalData.attendeeTab  = 'list';
                this.modalData.attendeeSearch = '';
                this.modalData.addMode      = 'search';
                this.modalData.playerQuery  = '';
                this.modalData.playerResults = [];
                this.modalData.selectedPlayer = null;
                this.modalData.pairPlayer1  = '';
                this.modalData.pairPlayer2  = '';
                this.modalData.newAttendee  = { player_id: '', first_name: '', last_name: '', email: '', phone: '', amount_paid: '', quote_amount: '', payment_status: 'pending', status: 'registered', notes: '' };
                await this.loadAttendees();
                return;
            }

            if (name === 'facilitator') {
                this.modalData.coachId = this.selectedEvent?.extendedProps?.coachId || '';
                this.facilitatorSearch = '';
            } else if (name === 'editTime') {
                const dt = this.selectedEvent?.start;
                if (dt) {
                    const y = dt.getFullYear(), m = String(dt.getMonth()+1).padStart(2,'0'), d = String(dt.getDate()).padStart(2,'0'),
                          h = String(dt.getHours()).padStart(2,'0'), mi = String(dt.getMinutes()).padStart(2,'0');
                    this.modalData.scheduledAt = `${y}-${m}-${d}T${h}:${mi}`;
                }
            } else if (name === 'capacity') {
                this.modalData.slots = this.selectedEvent?.extendedProps?.capacity || 0;
            } else if (name === 'notes') {
                await this.loadNotes();
            } else if (name === 'courts') {
                await this.loadCourts();
            } else if (name === 'attendees') {
                this.modalData.newAttendee = { first_name: '', last_name: '', email: '', phone: '', amount_paid: '', status: 'registered', notes: '' };
                await this.loadAttendees();
            } else if (name === 'hotDeal') {
                const hd = this.selectedEvent?.extendedProps?.hotDeal;
                this.modalData.hotDeal = hd ? { ...hd } : { discount_price: 0, original_price: this.selectedEvent?.extendedProps?.price || 0 };
            } else if (name === 'earlyBird') {
                const eb = this.selectedEvent?.extendedProps?.earlyBird;
                this.modalData.earlyBird = eb ? { ...eb } : { discount_price: 0, cutoff_hours: 24 };
            } else if (name === 'feedback') {
                await this.loadFeedback();
            } else if (name === 'editType') {
                this.activeModal = null;
                this.showActionsModal = false;
                const stId = this._stId();
                if (stId) {
                    const sdEl = document.querySelector('[x-data*="scheduleDashboard"]');
                    if (sdEl && window.Alpine) {
                        const parentData = Alpine.$data(sdEl);
                        if (parentData.startEdit) parentData.startEdit(stId);
                    }
                }
            }
        },

        showToast(msg, type = 'success') {
            this.toast = { show: true, message: msg, type };
            setTimeout(() => this.toast.show = false, 3000);
        },

        async saveFacilitator() {
            this.saving = true;
            try {
                await authFetch(this._apiBase(), { method: 'PUT', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ coach_id: this.modalData.coachId || null }) });
                this.showToast('Facilitator updated');
                this.activeModal = null;
                if (calendar) calendar.refetchEvents();
            } catch(e) { this.showToast('Error saving facilitator', 'error'); }
            this.saving = false;
        },

        async saveTime() {
            this.saving = true;
            try {
                const dt = this.modalData.scheduledAt.replace('T', ' ') + ':00';
                await authFetch(this._apiBase(), { method: 'PUT', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ scheduled_at: dt }) });
                this.showToast('Time updated');
                this.activeModal = null;
                if (calendar) calendar.refetchEvents();
            } catch(e) { this.showToast('Error saving time', 'error'); }
            this.saving = false;
        },

        async saveCapacity() {
            this.saving = true;
            try {
                await authFetch(this._apiBase(), { method: 'PUT', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ slots: this.modalData.slots }) });
                this.showToast('Capacity updated');
                this.activeModal = null;
                if (calendar) calendar.refetchEvents();
            } catch(e) { this.showToast('Error saving capacity', 'error'); }
            this.saving = false;
        },

        async toggleBooking() {
            this.saving = true;
            const newStatus = this.selectedEvent?.extendedProps?.bookingStatus ? 0 : 1;
            try {
                await authFetch(this._apiBase(), { method: 'PUT', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ booking_status: newStatus }) });
                this.showToast(newStatus ? 'Booking opened' : 'Booking closed');
                this.showActionsModal = false;
                if (calendar) calendar.refetchEvents();
            } catch(e) { this.showToast('Error toggling booking', 'error'); }
            this.saving = false;
        },

        async loadNotes() {
            try {
                const res = await authFetch(this._apiBase() + '/notes');
                const json = await res.json();
                this.modalData.notes = json.data || [];
            } catch(e) { this.modalData.notes = []; }
            this.modalData.newNote = '';
        },

        async addNote() {
            if (!this.modalData.newNote?.trim()) return;
            this.saving = true;
            try {
                await authFetch(this._apiBase() + '/notes', { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ note: this.modalData.newNote }) });
                await this.loadNotes();
                this.showToast('Note added');
                if (calendar) calendar.refetchEvents();
            } catch(e) { this.showToast('Error adding note', 'error'); }
            this.saving = false;
        },

        async deleteNote(noteId) {
            try {
                await authFetch(this._apiBase() + '/notes/' + noteId, { method: 'DELETE' });
                await this.loadNotes();
                if (calendar) calendar.refetchEvents();
            } catch(e) { this.showToast('Error deleting note', 'error'); }
        },

        async loadCourts() {
            const fid = this.getFacilityId();
            try {
                const [assignedRes, allRes] = await Promise.all([
                    authFetch(this._apiBase() + '/courts'),
                    authFetch(baseApi + '/api/courts?facility_id=' + fid + '&per_page=100'),
                ]);
                const assignedJson = await assignedRes.json();
                const allJson = await allRes.json();
                this.modalData.allCourts = allJson.data || [];
                this.modalData.selectedCourtIds = (assignedJson.data || []).map(c => String(c.court_id));
            } catch(e) { this.modalData.allCourts = []; this.modalData.selectedCourtIds = []; }
        },

        async saveCourts() {
            this.saving = true;
            try {
                await authFetch(this._apiBase() + '/courts', { method: 'PUT', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ court_ids: this.modalData.selectedCourtIds.map(Number) }) });
                this.showToast('Courts updated');
                this.activeModal = null;
                if (calendar) calendar.refetchEvents();
            } catch(e) { this.showToast('Error saving courts', 'error'); }
            this.saving = false;
        },

        async loadAttendees() {
            try {
                const res = await authFetch(this._apiBase() + '/attendees');
                const json = await res.json();
                this.modalData.attendees = json.data || [];
            } catch(e) { this.modalData.attendees = []; }
        },

        async searchPlayers() {
            const q = (this.modalData.playerQuery || '').trim();
            if (q.length < 2) { this.modalData.playerResults = []; return; }
            this.modalData.playerLoading = true;
            try {
                const res = await authFetch(baseApi + '/api/players?search=' + encodeURIComponent(q) + '&per_page=20');
                const json = await res.json();
                this.modalData.playerResults = json.data || [];
            } catch(e) { this.modalData.playerResults = []; }
            this.modalData.playerLoading = false;
        },

        selectPlayerForAttendee(player) {
            this.modalData.selectedPlayer = player;
            this.modalData.playerResults  = [];
            this.modalData.playerQuery    = player.first_name + ' ' + (player.last_name || '');
            this.modalData.newAttendee.player_id   = player.id;
            this.modalData.newAttendee.first_name  = player.first_name || '';
            this.modalData.newAttendee.last_name   = player.last_name  || '';
            this.modalData.newAttendee.email       = player.email      || '';
            this.modalData.newAttendee.phone       = player.phone      || '';
            // Pre-fill quote with session price
            const price = this.selectedEvent?.extendedProps?.price || 0;
            const hdPrice = this.selectedEvent?.extendedProps?.hotDeal?.price;
            const ebPrice = this.selectedEvent?.extendedProps?.earlyBird?.price;
            this.modalData.newAttendee.quote_amount = hdPrice || ebPrice || price;
        },

        async addAttendee() {
            // Validate
            if (this.modalData.addMode === 'manual' && !this.modalData.newAttendee?.first_name?.trim()) return;
            if (this.modalData.addMode === 'search' && !this.modalData.selectedPlayer) return;
            this.saving = true;
            try {
                const payload = { ...this.modalData.newAttendee };
                Object.keys(payload).forEach(k => { if (payload[k] === '' || payload[k] === null || payload[k] === undefined) delete payload[k]; });
                if (!payload.first_name) { this.saving = false; return; }
                await authFetch(this._apiBase() + '/attendees', { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
                // Reset form
                this.modalData.newAttendee  = { player_id: '', first_name: '', last_name: '', email: '', phone: '', amount_paid: '', quote_amount: '', payment_status: 'pending', status: 'registered', notes: '' };
                this.modalData.selectedPlayer = null;
                this.modalData.playerQuery  = '';
                await this.loadAttendees();
                this.modalData.attendeeTab  = 'list';
                this.showToast('Attendee registered successfully');
                if (calendar) calendar.refetchEvents();
            } catch(e) { this.showToast('Error adding attendee', 'error'); }
            this.saving = false;
        },

        async updateAttendeeAmount(att) {
            try {
                await authFetch(this._apiBase() + '/attendees/' + att.id, {
                    method: 'PUT', headers: {'Content-Type':'application/json'},
                    body: JSON.stringify({ amount_paid: att.amount_paid, payment_status: att.payment_status })
                });
                this.showToast('Payment updated');
            } catch(e) { this.showToast('Error updating payment', 'error'); }
        },

        async bulkCheckIn() {
            const toCheck = this.modalData.attendees.filter(a => !a.checked_in && a.status === 'registered');
            if (!toCheck.length) { this.showToast('All registered attendees already checked in'); return; }
            this.saving = true;
            try {
                await Promise.all(toCheck.map(a => {
                    a.checked_in = 1;
                    return authFetch(this._apiBase() + '/attendees/' + a.id, {
                        method: 'PUT', headers: {'Content-Type':'application/json'},
                        body: JSON.stringify({ checked_in: 1 })
                    });
                }));
                this.showToast(toCheck.length + ' attendee(s) checked in');
                if (calendar) calendar.refetchEvents();
            } catch(e) { this.showToast('Error during bulk check-in', 'error'); }
            this.saving = false;
        },

        async pairAttendees() {
            const id1 = Number(this.modalData.pairPlayer1);
            const id2 = Number(this.modalData.pairPlayer2);
            if (!id1 || !id2 || id1 === id2) { this.showToast('Select two different players', 'error'); return; }
            this.saving = true;
            try {
                await Promise.all([
                    authFetch(this._apiBase() + '/attendees/' + id1, { method: 'PUT', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ partner_id: id2 }) }),
                    authFetch(this._apiBase() + '/attendees/' + id2, { method: 'PUT', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ partner_id: id1 }) }),
                ]);
                // Update local state
                const a1 = this.modalData.attendees.find(a => a.id === id1);
                const a2 = this.modalData.attendees.find(a => a.id === id2);
                if (a1) a1.partner_id = id2;
                if (a2) a2.partner_id = id1;
                this.modalData.pairPlayer1 = '';
                this.modalData.pairPlayer2 = '';
                this.showToast('Players paired successfully');
            } catch(e) { this.showToast('Error pairing players', 'error'); }
            this.saving = false;
        },

        async unpairAttendee(id1, id2) {
            this.saving = true;
            try {
                await Promise.all([
                    authFetch(this._apiBase() + '/attendees/' + id1, { method: 'PUT', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ partner_id: null }) }),
                    authFetch(this._apiBase() + '/attendees/' + id2, { method: 'PUT', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ partner_id: null }) }),
                ]);
                const a1 = this.modalData.attendees.find(a => a.id === id1);
                const a2 = this.modalData.attendees.find(a => a.id === id2);
                if (a1) a1.partner_id = null;
                if (a2) a2.partner_id = null;
                this.showToast('Pairing removed');
            } catch(e) { this.showToast('Error removing pair', 'error'); }
            this.saving = false;
        },

        async updateAttendee(att) {
            try {
                await authFetch(this._apiBase() + '/attendees/' + att.id, {
                    method: 'PUT',
                    headers: {'Content-Type':'application/json'},
                    body: JSON.stringify({ status: att.status, checked_in: att.checked_in ? 1 : 0 })
                });
                this.showToast('Attendee updated');
                if (calendar) calendar.refetchEvents();
            } catch(e) { this.showToast('Error updating attendee', 'error'); }
        },

        async removeAttendee(attId) {
            try {
                await authFetch(this._apiBase() + '/attendees/' + attId, { method: 'DELETE' });
                await this.loadAttendees();
                if (calendar) calendar.refetchEvents();
            } catch(e) { this.showToast('Error removing attendee', 'error'); }
        },

        async saveHotDeal() {
            this.saving = true;
            try {
                await authFetch(this._apiBase() + '/hot-deal', { method: 'PUT', headers: {'Content-Type':'application/json'}, body: JSON.stringify(this.modalData.hotDeal) });
                this.showToast('Hot deal saved');
                this.activeModal = null;
                if (calendar) calendar.refetchEvents();
            } catch(e) { this.showToast('Error saving hot deal', 'error'); }
            this.saving = false;
        },

        async removeHotDeal() {
            this.saving = true;
            try {
                await authFetch(this._apiBase() + '/hot-deal', { method: 'DELETE' });
                this.showToast('Hot deal removed');
                this.activeModal = null;
                if (calendar) calendar.refetchEvents();
            } catch(e) { this.showToast('Error removing hot deal', 'error'); }
            this.saving = false;
        },

        async saveEarlyBird() {
            this.saving = true;
            try {
                await authFetch(this._apiBase() + '/early-bird', { method: 'PUT', headers: {'Content-Type':'application/json'}, body: JSON.stringify(this.modalData.earlyBird) });
                this.showToast('Early bird saved');
                this.activeModal = null;
                if (calendar) calendar.refetchEvents();
            } catch(e) { this.showToast('Error saving early bird', 'error'); }
            this.saving = false;
        },

        async removeEarlyBird() {
            this.saving = true;
            try {
                await authFetch(this._apiBase() + '/early-bird', { method: 'DELETE' });
                this.showToast('Early bird removed');
                this.activeModal = null;
                if (calendar) calendar.refetchEvents();
            } catch(e) { this.showToast('Error removing early bird', 'error'); }
            this.saving = false;
        },

        async loadFeedback() {
            try {
                const res = await authFetch(this._apiBase() + '/feedback');
                const json = await res.json();
                this.modalData.feedback = json.data || { message: '' };
            } catch(e) { this.modalData.feedback = { message: '' }; }
        },

        async saveFeedback() {
            this.saving = true;
            try {
                await authFetch(this._apiBase() + '/feedback', { method: 'PUT', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ message: this.modalData.feedback.message }) });
                this.showToast('Feedback saved');
            } catch(e) { this.showToast('Error saving feedback', 'error'); }
            this.saving = false;
        },

        async sendFeedback() {
            this.saving = true;
            try {
                await authFetch(this._apiBase() + '/feedback/send', { method: 'POST' });
                this.showToast('Feedback request sent');
                this.activeModal = null;
            } catch(e) { this.showToast('Error sending feedback', 'error'); }
            this.saving = false;
        },

        copyLink() {
            const uuid = this.selectedEvent?.extendedProps?.classUuid;
            if (uuid) {
                const link = window.location.origin + '/session/' + uuid;
                navigator.clipboard.writeText(link).then(() => this.showToast('Link copied!')).catch(() => this.showToast('Copy failed', 'error'));
            }
            this.showActionsModal = false;
        },

        async deleteClass() {
            this.saving = true;
            try {
                await authFetch(this._apiBase(), { method: 'DELETE' });
                this.showToast('Class deleted');
                this.activeModal = null;
                this.showActionsModal = false;
                this.selectedEvent = null;
                if (calendar) calendar.refetchEvents();
            } catch(e) { this.showToast('Error deleting class', 'error'); }
            this.saving = false;
        },
    };
}
</script>

<script>
// Global helper: show a small popup with full note text when "...more" is clicked
window.msShowNotePopup = function(ev, el) {
    ev.stopPropagation();
    ev.preventDefault();
    document.querySelectorAll('.ms-notes-popup').forEach(function(p){ p.remove(); });
    var raw = el.getAttribute('data-note') || '';
    var note = '';
    try { note = decodeURIComponent(raw); } catch(e) { note = raw; }
    if (!note) return;
    var count = parseInt(el.getAttribute('data-count') || '1', 10);
    var popup = document.createElement('div');
    popup.className = 'ms-notes-popup';
    var countBadge = count > 1 ? ' <span style="font-size:9px;background:#6366f1;color:#fff;border-radius:10px;padding:1px 6px;vertical-align:middle;">' + count + ' notes</span>' : '';
    popup.innerHTML =
        '<div class="ms-notes-popup-title">' +
        '\uD83D\uDCDD Note' + countBadge +
        '<button onclick="this.closest(\'.ms-notes-popup\').remove()" style="background:none;border:none;cursor:pointer;color:#9ca3af;font-size:14px;line-height:1;padding:0;margin-left:8px;">&times;</button>' +
        '</div>' +
        '<div style="white-space:pre-wrap;word-break:break-word;">' + escMsPopup(note) + '</div>';
    document.body.appendChild(popup);
    var rect = el.getBoundingClientRect();
    var top  = rect.bottom + 6 + window.scrollY;
    var left = rect.left   + window.scrollX;
    var pw = 260;
    if (left + pw + 10 > window.innerWidth) left = window.innerWidth - pw - 10;
    if (left < 8) left = 8;
    popup.style.top  = top + 'px';
    popup.style.left = left + 'px';
    setTimeout(function() {
        document.addEventListener('click', function closer(e) {
            if (!popup.contains(e.target)) { popup.remove(); document.removeEventListener('click', closer); }
        });
    }, 50);
};
function escMsPopup(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>

<style>
    /* FullCalendar theme overrides - large timeline spacing like legacy */
    #master-schedule-calendar .fc-timegrid-slot {
        height: 3.5rem;
    }
    #master-schedule-calendar .fc-timegrid-slot-label-cushion {
        font-size: 0.8125rem;
        font-weight: 500;
        padding: 4px 8px;
    }
    #master-schedule-calendar .fc-col-header-cell-cushion {
        font-size: 0.8125rem;
        font-weight: 600;
        padding: 10px 4px;
    }
    /* Dark mode FC colors */
    .dark #master-schedule-calendar {
        --fc-border-color: #334155;
        --fc-page-bg-color: transparent;
        --fc-neutral-bg-color: rgba(148,163,184,0.05);
        --fc-today-bg-color: rgba(99,102,241,0.08);
    }
    #master-schedule-calendar .fc-theme-standard td,
    #master-schedule-calendar .fc-theme-standard th {
        border-color: var(--fc-border-color);
    }
    .dark #master-schedule-calendar .fc-col-header-cell-cushion,
    .dark #master-schedule-calendar .fc-timegrid-slot-label-cushion,
    .dark #master-schedule-calendar .fc-daygrid-day-number {
        color: #cbd5e1;
    }
    /* Event cards - colored backgrounds from category */
    #master-schedule-calendar .fc-event {
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-size: 11px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }
    /* Vertical centering of event content */
    #master-schedule-calendar .fc-event .fc-event-main {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
    }
    /* Hover: pop above overlapping events */
    #master-schedule-calendar .fc-timegrid-event-harness:hover {
        z-index: 100 !important;
    }
    #master-schedule-calendar .fc-event:hover {
        box-shadow: 0 6px 20px rgba(0,0,0,0.22);
        transform: scale(1.03);
        z-index: 100 !important;
    }
    #master-schedule-calendar .fc-timegrid-event {
        border-radius: 8px;
        overflow: visible !important;
    }
    /* Event inner content — centered */
    .ms-ev-content {
        padding: 4px 7px;
        line-height: 1.35;
        width: 100%;
        overflow: hidden;
        text-align: center;
        color: #fff; /* default; overridden per-event by textColorWhite setting */
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .ms-ev-line1 {
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ms-ev-line2 {
        font-size: 10px;
        opacity: 0.85;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    /* Notes preview on event */
    .ms-ev-note-preview {
        font-size: 9px;
        font-weight: 400;
        opacity: 0.85;
        max-width: 100%;
        word-break: break-word;
        line-height: 1.4;
        text-align: center;
    }
    .ms-ev-note-more {
        font-weight: 700;
        cursor: pointer;
        text-decoration: underline;
        opacity: 1;
        white-space: nowrap;
    }
    .ms-notes-popup {
        position: fixed;
        z-index: 99999;
        background: #fff;
        color: #1f2937;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 14px;
        width: 260px;
        max-width: 90vw;
        box-shadow: 0 8px 24px rgba(0,0,0,0.16);
        font-size: 12px;
        line-height: 1.6;
    }
    .ms-notes-popup-title {
        font-weight: 700;
        font-size: 11px;
        color: #6366f1;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    /* Badges */
    .ms-badge {
        display: inline-block;
        border-radius: 3px;
        padding: 0 4px;
        font-size: 8px;
        font-weight: 700;
        line-height: 14px;
        vertical-align: middle;
    }
    .ms-badge-closed { background: rgba(239,68,68,0.9); color: #fff; text-transform: uppercase; }
    .ms-badge-deal   { background: rgba(251,146,60,0.9); color: #fff; animation: pulse 2s infinite; }
    .ms-badge-early  { background: rgba(52,211,153,0.9); color: #fff; }
    .ms-badge-spots  { background: rgba(255,255,255,0.25); color: #fff; font-weight: 500; }
    .ms-ev-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 2px;
        margin-top: 2px;
        justify-content: center;
    }
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }
    /* Hide FC default toolbar */
    #master-schedule-calendar .fc-button,
    #master-schedule-calendar .fc-toolbar {
        display: none;
    }
    /* Now indicator */
    #master-schedule-calendar .fc-now-indicator-line {
        border-color: #ef4444;
        border-width: 2px;
    }
    #master-schedule-calendar .fc-now-indicator-arrow {
        border-color: #ef4444;
    }
    .dark #master-schedule-calendar .fc-day-today {
        background-color: rgba(99,102,241,0.08) !important;
    }
    /* Responsive */
    @media (max-width: 640px) {
        #master-schedule-calendar .fc-timegrid-slot {
            height: 2.5rem;
        }
        #master-schedule-calendar .fc-timegrid-slot-label-cushion {
            font-size: 0.6875rem;
        }
    }
</style>