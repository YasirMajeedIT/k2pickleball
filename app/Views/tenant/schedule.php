<?php
/**
 * Tenant Schedule Page — K2 Navy/Gold Premium Theme
 * Views: Month, Week, Today, List, Calendar-Only
 * Features: Settings-driven display, inline booking modal, multi-payment support.
 * Reads config from /api/public/schedule-settings to determine visible fields/views.
 */
?>

<div x-data="schedulePage()" x-init="load()">
    <!-- Page Header (hidden in calendar-only mode) -->
    <section x-show="viewMode !== 'calendar'" class="relative bg-navy-900 overflow-hidden py-16 sm:py-20">
        <div class="absolute inset-0 grid-bg opacity-40"></div>
        <div class="absolute inset-0 hero-glow"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full glass-card text-gold-400 text-xs font-semibold uppercase tracking-wider mb-4">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Schedule
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-white" x-text="cfg.page_title || 'Class Schedule'"></h1>
            <p class="mt-4 text-lg text-slate-400 max-w-2xl" x-text="cfg.page_subtitle || 'Browse upcoming classes and reserve your spot.'"></p>
        </div>
    </section>

    <!-- Controls Bar -->
    <section class="sticky top-16 lg:top-20 z-30 glass border-b border-gold-500/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 sm:py-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <!-- Date Navigation -->
                <div class="flex items-center gap-2">
                    <button @click="prev()" class="p-2 rounded-lg bg-navy-800 border border-navy-700 text-slate-300 hover:text-white hover:border-gold-500/30 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button @click="goToday()" class="px-4 py-2 rounded-lg text-sm font-semibold bg-navy-800 border border-navy-700 text-slate-300 hover:text-white hover:border-gold-500/30 transition-all">Today</button>
                    <button @click="next()" class="p-2 rounded-lg bg-navy-800 border border-navy-700 text-slate-300 hover:text-white hover:border-gold-500/30 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <div class="relative" x-data="{ pickerOpen: false }">
                        <button @click="pickerOpen = !pickerOpen" class="flex items-center gap-2 ml-1 px-3 py-2 rounded-lg bg-navy-800 border border-navy-700 text-sm font-medium text-white hover:border-gold-500/30 transition-all">
                            <svg class="w-4 h-4 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span x-text="dateLabel()"></span>
                        </button>
                        <input type="date" x-ref="datePicker"
                               @change="jumpToDate($event.target.value); pickerOpen = false"
                               class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                               :value="fmtDate(currentDate)">
                    </div>
                </div>

                <div class="flex items-center gap-3 flex-wrap">
                    <!-- Category Filter -->
                    <div class="flex flex-wrap gap-2" x-show="categories.length > 0 && cfg.show_category_filter !== '0'" x-cloak>
                        <button @click="categoryFilter = null; load()" class="px-3 py-1.5 rounded-full text-xs font-semibold transition-all" :class="!categoryFilter ? 'gradient-gold-bg text-navy-950' : 'bg-navy-800 text-slate-400 border border-navy-700 hover:text-white'">All</button>
                        <template x-for="cat in categories" :key="cat.id">
                            <button @click="categoryFilter = cat.id; load()" class="px-3 py-1.5 rounded-full text-xs font-semibold transition-all" :class="categoryFilter === cat.id ? 'text-white' : 'bg-navy-800 text-slate-400 border border-navy-700 hover:text-white'" :style="categoryFilter === cat.id ? 'background:' + (cat.color || '#d4af37') : ''" x-text="cat.name"></button>
                        </template>
                    </div>
                    <!-- Resource Filters -->
                    <template x-for="rf in resourceFilters" :key="rf.id">
                        <div class="flex flex-wrap gap-1.5" x-show="rf.values.length > 0">
                            <span class="text-[10px] text-slate-500 self-center mr-0.5" x-text="rf.name + ':'"></span>
                            <button @click="toggleResourceFilter(rf.id, null)" class="px-2 py-1 rounded-full text-[10px] font-medium transition-all" :class="!activeResourceFilters[rf.id] ? 'gradient-gold-bg text-navy-950' : 'bg-navy-800 text-slate-400 border border-navy-700 hover:text-white'">All</button>
                            <template x-for="rv in rf.values" :key="rv.id">
                                <button @click="toggleResourceFilter(rf.id, rv.name)" class="px-2 py-1 rounded-full text-[10px] font-medium transition-all" :class="activeResourceFilters[rf.id] === rv.name ? 'gradient-gold-bg text-navy-950' : 'bg-navy-800 text-slate-400 border border-navy-700 hover:text-white'" x-text="rv.name"></button>
                            </template>
                        </div>
                    </template>
                    <!-- View Toggle -->
                    <div class="flex bg-navy-800 border border-navy-700 rounded-lg overflow-hidden">
                        <template x-for="v in enabledViews" :key="v.key">
                            <button @click="switchView(v.key)" class="px-3 py-1.5 text-xs font-semibold transition-all" :class="viewMode === v.key ? 'gradient-gold-bg text-navy-950' : 'text-slate-400 hover:text-white'" x-text="v.label"></button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Loading -->
    <div x-show="loading" class="py-20 text-center bg-navy-950">
        <div class="inline-flex items-center gap-3 text-slate-500">
            <svg class="animate-spin w-5 h-5 text-gold-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            Loading classes...
        </div>
    </div>

    <!-- ═══ MONTH VIEW ═══ -->
    <section x-show="!loading && viewMode === 'month'" class="relative py-8 bg-navy-950 min-h-[60vh]">
        <div class="absolute inset-0 section-glow"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-7 gap-1 mb-1">
                <template x-for="d in ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']">
                    <div class="text-center text-xs font-semibold text-gold-500 uppercase tracking-wider py-2" x-text="d"></div>
                </template>
            </div>
            <div class="grid grid-cols-7 gap-1">
                <template x-for="cell in monthGrid()" :key="cell.date">
                    <div class="glass-card rounded-lg p-1.5 sm:p-2 min-h-[90px] sm:min-h-[110px] cursor-pointer transition-all hover:border-gold-500/20"
                         :class="{'border-gold-500/30 bg-gold-500/5': cell.date === todayStr, 'opacity-40': !cell.inMonth}"
                         @click="jumpToDate(cell.date); switchView('today')">
                        <div class="text-xs font-bold mb-1" :class="cell.date === todayStr ? 'text-gold-400' : cell.inMonth ? 'text-white' : 'text-slate-600'" x-text="cell.day"></div>
                        <div class="space-y-0.5">
                            <template x-for="cls in filteredClassesForDay(cell.date).slice(0, 3)" :key="cls.id">
                                <div @click.stop="openBooking(cls)"
                                     class="block text-[10px] leading-tight px-1 py-0.5 rounded truncate font-medium text-white/90 hover:brightness-110 transition-all cursor-pointer"
                                     :style="'background:' + (cls.category_color || '#d4af37')">
                                    <span x-show="cfg.show_time !== '0'" x-text="formatTime(cls.start_time)"></span>
                                    <span x-show="cfg.show_title !== '0'" x-text="cls.session_type_name || cls.session_name || ''"></span>
                                </div>
                            </template>
                            <div x-show="filteredClassesForDay(cell.date).length > 3" class="text-[10px] text-gold-400 font-medium px-1" x-text="'+' + (filteredClassesForDay(cell.date).length - 3) + ' more'"></div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </section>

    <!-- ═══ WEEK VIEW ═══ -->
    <section x-show="!loading && viewMode === 'week'" class="relative py-8 bg-navy-950 min-h-[60vh]">
        <div class="absolute inset-0 section-glow"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-4">
                <template x-for="day in weekDays()" :key="day">
                    <div class="glass-card rounded-xl p-4 gold-border" :class="{'border-gold-500/30 bg-gold-500/5': day === todayStr}">
                        <div class="text-center mb-3 pb-3 border-b border-navy-700/50">
                            <div class="text-xs text-gold-500 uppercase tracking-wider font-semibold" x-text="dayLabel(day)"></div>
                            <div class="text-lg font-bold" :class="day === todayStr ? 'text-gold-400' : 'text-white'" x-text="dayNum(day)"></div>
                        </div>
                        <div class="space-y-2">
                            <template x-for="cls in filteredClassesForDay(day)" :key="cls.id">
                                <div @click="openBooking(cls)" class="block p-2.5 rounded-lg bg-navy-800/50 border border-navy-700/50 hover:border-gold-500/30 hover:bg-navy-800 transition-all group cursor-pointer">
                                    <div x-show="cfg.show_time !== '0'" class="text-[11px] font-medium text-gold-400" x-text="formatTime(cls.start_time)"></div>
                                    <div x-show="cfg.show_title !== '0'" class="text-xs font-bold text-white group-hover:text-gold-400 transition-colors mt-0.5 line-clamp-2" x-text="cls.session_type_name || cls.session_name || 'Session'"></div>
                                    <div x-show="cfg.show_coach !== '0' && cls.coach_name" class="text-[10px] text-slate-400 mt-0.5" x-text="cls.coach_name"></div>
                                    <div class="flex items-center justify-between mt-1.5">
                                        <span x-show="cfg.show_category !== '0'" class="text-[10px] px-1.5 py-0.5 rounded font-medium text-white/80" :style="'background:' + (cls.category_color || '#d4af37')" x-text="cls.category_name || ''"></span>
                                        <span x-show="cfg.show_spots !== '0'" class="text-[10px] font-bold" :class="cls.is_full ? 'text-red-400' : 'text-emerald-400'" x-text="cls.is_full ? 'Full' : cls.spots_left + ' left'"></span>
                                    </div>
                                    <div x-show="cfg.show_price !== '0' && cls.price > 0" class="text-[10px] font-bold text-white mt-1" x-text="'$' + parseFloat(cls.price).toFixed(2)"></div>
                                </div>
                            </template>
                            <div x-show="filteredClassesForDay(day).length === 0" class="text-center py-4">
                                <span class="text-xs text-slate-600">No classes</span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </section>

    <!-- ═══ TODAY / DAY VIEW ═══ -->
    <section x-show="!loading && viewMode === 'today'" class="relative py-8 bg-navy-950 min-h-[60vh]">
        <div class="absolute inset-0 section-glow"></div>
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-14 h-14 rounded-xl glass-card flex flex-col items-center justify-center gold-border">
                    <span class="text-[10px] text-gold-500 uppercase font-bold" x-text="dayLabel(fmtDate(currentDate))"></span>
                    <span class="text-xl font-extrabold text-white leading-none" x-text="dayNum(fmtDate(currentDate))"></span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white" x-text="currentDate.toLocaleDateString('en-US', {weekday:'long', month:'long', day:'numeric', year:'numeric'})"></h2>
                    <p class="text-sm text-slate-400" x-text="filteredClassesForDay(fmtDate(currentDate)).length + ' class' + (filteredClassesForDay(fmtDate(currentDate)).length !== 1 ? 'es' : '') + ' scheduled'"></p>
                </div>
            </div>
            <div class="space-y-3">
                <template x-for="cls in filteredClassesForDay(fmtDate(currentDate))" :key="cls.id">
                    <div @click="openBooking(cls)" class="flex items-center gap-4 p-4 glass-card rounded-xl glass-card-hover group cursor-pointer">
                        <div class="flex-shrink-0 w-20 text-center">
                            <div x-show="cfg.show_time !== '0'" class="text-sm font-bold text-gold-400" x-text="formatTime(cls.start_time)"></div>
                            <div x-show="cfg.show_time !== '0'" class="text-[10px] text-slate-500" x-text="formatTime(cls.end_time)"></div>
                            <div x-show="cfg.show_duration !== '0' && cls.duration" class="text-[10px] text-slate-600 mt-0.5" x-text="cls.duration + ' min'"></div>
                        </div>
                        <div class="w-1 h-12 rounded-full" :style="'background:' + (cls.category_color || '#d4af37')"></div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <div x-show="cfg.show_title !== '0'" class="text-base font-bold text-white group-hover:text-gold-400 transition-colors" x-text="cls.session_type_name || cls.session_name || 'Session'"></div>
                                <span x-show="cfg.show_hot_deal_badge !== '0' && cls.hot_deal" class="px-1.5 py-0.5 text-[9px] font-bold bg-red-500 text-white rounded-full animate-pulse">HOT DEAL</span>
                                <span x-show="cfg.show_early_bird_badge !== '0' && cls.early_bird" class="px-1.5 py-0.5 text-[9px] font-bold bg-blue-500 text-white rounded-full">EARLY BIRD</span>
                            </div>
                            <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                                <span x-show="cfg.show_category !== '0'" class="text-[11px] px-2 py-0.5 rounded-full font-medium text-white/80" :style="'background:' + (cls.category_color || '#d4af37')" x-text="cls.category_name"></span>
                                <span x-show="cfg.show_skill_level !== '0' && cls.skill_levels" class="text-[11px] text-slate-500" x-text="cls.skill_levels"></span>
                                <span x-show="cfg.show_coach !== '0' && cls.coach_name" class="text-[11px] text-slate-400">
                                    <svg class="w-3 h-3 inline mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span x-text="cls.coach_name"></span>
                                </span>
                                <span x-show="cfg.show_courts !== '0' && cls.courts_display" class="text-[11px] text-slate-500" x-text="cls.courts_display"></span>
                            </div>
                        </div>
                        <div class="flex-shrink-0 text-right">
                            <template x-if="cfg.show_price !== '0'">
                                <div>
                                    <div x-show="cls.price > 0" class="text-base font-extrabold text-white" x-text="'$' + parseFloat(cls.price).toFixed(2)"></div>
                                    <div x-show="!cls.price || cls.price == 0" class="text-base font-extrabold gradient-gold">Free</div>
                                </div>
                            </template>
                            <div x-show="cfg.show_spots !== '0'" class="text-xs font-semibold mt-1" :class="cls.is_full ? 'text-red-400' : 'text-emerald-400'" x-text="cls.is_full ? 'Full' : cls.spots_left + ' spots left'"></div>
                        </div>
                    </div>
                </template>
            </div>
            <div x-show="filteredClassesForDay(fmtDate(currentDate)).length === 0" class="text-center py-20">
                <div class="w-20 h-20 rounded-2xl glass-card flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-lg font-semibold text-slate-400">No classes today</p>
                <p class="text-sm text-slate-600 mt-1">Try a different date or check the week view.</p>
            </div>
        </div>
    </section>

    <!-- ═══ LIST VIEW ═══ -->
    <section x-show="!loading && viewMode === 'list'" class="relative py-8 bg-navy-950 min-h-[60vh]">
        <div class="absolute inset-0 section-glow"></div>
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <template x-for="day in activeDays()" :key="day">
                <div class="mb-8">
                    <h3 class="text-sm font-bold text-gold-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span x-text="dayLabel(day) + ', ' + dayFull(day)"></span>
                        <span class="text-slate-500 text-xs font-normal normal-case" x-text="'(' + filteredClassesForDay(day).length + ')'"></span>
                        <span class="flex-1 h-px bg-navy-700"></span>
                    </h3>
                    <div class="space-y-3">
                        <template x-for="cls in filteredClassesForDay(day)" :key="cls.id">
                            <div @click="openBooking(cls)" class="flex items-center gap-4 p-4 glass-card rounded-xl glass-card-hover group cursor-pointer">
                                <div class="flex-shrink-0 w-16 text-center">
                                    <div x-show="cfg.show_time !== '0'" class="text-sm font-bold text-gold-400" x-text="formatTime(cls.start_time)"></div>
                                    <div x-show="cfg.show_time !== '0'" class="text-[10px] text-slate-500" x-text="formatTime(cls.end_time)"></div>
                                </div>
                                <div class="w-0.5 h-10 rounded-full" :style="'background:' + (cls.category_color || '#d4af37')"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <div x-show="cfg.show_title !== '0'" class="text-sm font-bold text-white group-hover:text-gold-400 transition-colors" x-text="cls.session_type_name || cls.session_name || 'Session'"></div>
                                        <span x-show="cfg.show_hot_deal_badge !== '0' && cls.hot_deal" class="px-1.5 py-0.5 text-[9px] font-bold bg-red-500 text-white rounded-full">HOT DEAL</span>
                                        <span x-show="cfg.show_early_bird_badge !== '0' && cls.early_bird" class="px-1.5 py-0.5 text-[9px] font-bold bg-blue-500 text-white rounded-full">EARLY BIRD</span>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                                        <span x-show="cfg.show_category !== '0'" class="text-[10px] px-2 py-0.5 rounded-full font-medium text-white/80" :style="'background:' + (cls.category_color || '#d4af37')" x-text="cls.category_name"></span>
                                        <span x-show="cfg.show_skill_level !== '0' && cls.skill_levels" class="text-[10px] text-slate-500" x-text="cls.skill_levels"></span>
                                        <span x-show="cfg.show_coach !== '0' && cls.coach_name" class="text-[10px] text-slate-400" x-text="cls.coach_name"></span>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 text-right">
                                    <div x-show="cfg.show_price !== '0' && cls.price > 0" class="text-sm font-extrabold text-white" x-text="'$' + parseFloat(cls.price).toFixed(2)"></div>
                                    <div x-show="cfg.show_price !== '0' && (!cls.price || cls.price == 0)" class="text-sm font-extrabold gradient-gold">Free</div>
                                    <div x-show="cfg.show_spots !== '0'" class="text-xs font-semibold mt-0.5" :class="cls.is_full ? 'text-red-400' : 'text-emerald-400'" x-text="cls.is_full ? 'Full' : cls.spots_left + ' spots'"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
            <div x-show="classes.length === 0" class="text-center py-20">
                <div class="w-20 h-20 rounded-2xl glass-card flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-lg font-semibold text-slate-400">No classes found</p>
                <p class="text-sm text-slate-600 mt-1">Try navigating to a different period or adjusting filters.</p>
            </div>
        </div>
    </section>

    <!-- ═══ CALENDAR-ONLY VIEW ═══ -->
    <section x-show="!loading && viewMode === 'calendar'" class="relative py-4 bg-navy-950 min-h-[80vh]">
        <div class="max-w-7xl mx-auto px-2 sm:px-4">
            <!-- Compact header -->
            <div class="flex items-center justify-between mb-3 px-2">
                <h2 class="text-lg font-bold text-white" x-text="currentDate.toLocaleDateString('en-US', {month:'long', year:'numeric'})"></h2>
                <div class="flex items-center gap-1">
                    <button @click="prev()" class="p-1.5 rounded bg-navy-800 text-slate-300 hover:text-white transition-all"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>
                    <button @click="goToday()" class="px-3 py-1 text-xs font-semibold rounded bg-navy-800 text-slate-300 hover:text-white transition-all">Today</button>
                    <button @click="next()" class="p-1.5 rounded bg-navy-800 text-slate-300 hover:text-white transition-all"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>
                </div>
            </div>
            <div class="grid grid-cols-7 gap-px bg-navy-800 rounded-xl overflow-hidden border border-navy-700">
                <template x-for="d in ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']">
                    <div class="text-center text-[10px] font-semibold text-gold-500 uppercase tracking-wider py-2 bg-navy-900" x-text="d"></div>
                </template>
                <template x-for="cell in monthGrid()" :key="cell.date">
                    <div class="bg-navy-900 p-1 min-h-[100px] sm:min-h-[120px]"
                         :class="{'bg-gold-500/5': cell.date === todayStr, 'opacity-30': !cell.inMonth}">
                        <div class="text-[10px] font-bold mb-0.5" :class="cell.date === todayStr ? 'text-gold-400' : 'text-slate-500'" x-text="cell.day"></div>
                        <div class="space-y-px">
                            <template x-for="cls in filteredClassesForDay(cell.date).slice(0, 4)" :key="cls.id">
                                <div @click="openBooking(cls)"
                                     class="text-[9px] leading-tight px-1 py-0.5 rounded truncate font-medium text-white/90 cursor-pointer hover:brightness-110"
                                     :style="'background:' + (cls.category_color || '#d4af37')">
                                    <span x-text="formatTime(cls.start_time)"></span>
                                    <span x-text="cls.session_type_name || ''"></span>
                                </div>
                            </template>
                            <div x-show="filteredClassesForDay(cell.date).length > 4" class="text-[9px] text-gold-400 font-medium px-1" x-text="'+' + (filteredClassesForDay(cell.date).length - 4) + ' more'"></div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </section>

    <!-- ═══ BOOKING MODAL ═══ -->
    <div x-show="bookingModal" x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center p-4"
         @keydown.escape.window="closeBooking()">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="closeBooking()"></div>
        <!-- Modal -->
        <div class="relative w-full max-w-lg max-h-[90vh] overflow-y-auto glass-card rounded-2xl border border-navy-700 shadow-2xl"
             @click.stop>
            <!-- Close -->
            <button @click="closeBooking()" class="absolute top-4 right-4 p-2 rounded-lg bg-navy-800 text-slate-400 hover:text-white z-10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <!-- Class Detail Header -->
            <div class="p-6 pb-4 border-b border-navy-700">
                <div class="flex items-start gap-3">
                    <div class="w-2 h-14 rounded-full flex-shrink-0 mt-1" :style="'background:' + (bookingClass?.category_color || '#d4af37')"></div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xl font-bold text-white" x-text="bookingClass?.session_type_name || bookingClass?.session_name || 'Session'"></h3>
                        <div class="flex items-center gap-3 mt-2 text-sm text-slate-400">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span x-text="bookingClass ? new Date(bookingClass.start_time).toLocaleDateString('en-US', {weekday:'short', month:'short', day:'numeric'}) : ''"></span>
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span x-text="formatTime(bookingClass?.start_time) + ' - ' + formatTime(bookingClass?.end_time)"></span>
                            </span>
                        </div>
                        <div class="flex items-center gap-2 mt-2">
                            <span x-show="bookingClass?.category_name" class="text-[11px] px-2 py-0.5 rounded-full font-medium text-white/80" :style="'background:' + (bookingClass?.category_color || '#d4af37')" x-text="bookingClass?.category_name"></span>
                            <span class="text-xs font-semibold" :class="bookingDetail?.is_full ? 'text-red-400' : 'text-emerald-400'" x-text="bookingDetail?.is_full ? 'Class Full' : (bookingDetail?.spots_left || 0) + ' spots left'"></span>
                        </div>
                    </div>
                </div>
                <!-- Description -->
                <div x-show="cfg.show_description !== '0' && bookingDetail?.description" class="mt-3 text-sm text-slate-400 line-clamp-3" x-text="bookingDetail?.description"></div>
                <!-- Courts -->
                <div x-show="cfg.show_courts !== '0' && bookingDetail?.courts?.length" class="mt-2 flex flex-wrap gap-1">
                    <template x-for="court in bookingDetail?.courts || []" :key="court.name">
                        <span class="text-[10px] px-2 py-0.5 rounded bg-navy-800 text-slate-400 border border-navy-700" x-text="court.name"></span>
                    </template>
                </div>
            </div>

            <!-- Pricing Section -->
            <div class="p-6 pb-4 border-b border-navy-700" x-show="!bookingDetail?.is_full">
                <h4 class="text-sm font-bold text-gold-400 uppercase tracking-wider mb-3">Pricing</h4>
                <div class="space-y-2">
                    <!-- Standard price -->
                    <label class="flex items-center justify-between p-3 rounded-lg bg-navy-800/50 border border-navy-700 cursor-pointer hover:border-gold-500/30 transition-all"
                           :class="selectedPricing === 'standard' ? 'border-gold-500/50 bg-gold-500/5' : ''">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="pricing" value="standard" x-model="selectedPricing" class="w-4 h-4 text-gold-500 bg-navy-800 border-navy-600 focus:ring-gold-500">
                            <span class="text-sm font-medium text-white">Standard</span>
                        </div>
                        <span class="text-sm font-bold text-white" x-text="bookingDetail?.price > 0 ? '$' + parseFloat(bookingDetail.price).toFixed(2) : 'Free'"></span>
                    </label>
                    <!-- Hot deal -->
                    <label x-show="bookingDetail?.hot_deal" class="flex items-center justify-between p-3 rounded-lg bg-red-500/5 border border-red-500/30 cursor-pointer hover:border-red-400/50 transition-all"
                           :class="selectedPricing === 'hot_deal' ? 'border-red-400/60 bg-red-500/10' : ''">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="pricing" value="hot_deal" x-model="selectedPricing" class="w-4 h-4 text-red-500 bg-navy-800 border-navy-600 focus:ring-red-500">
                            <div>
                                <span class="text-sm font-medium text-white">Hot Deal</span>
                                <span class="text-[10px] text-red-400 ml-2 animate-pulse" x-text="bookingDetail?.hot_deal?.label || ''"></span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-bold text-red-400" x-text="'$' + parseFloat(bookingDetail?.hot_deal?.deal_price || 0).toFixed(2)"></span>
                            <span class="text-[10px] text-slate-500 line-through ml-1" x-text="'$' + parseFloat(bookingDetail?.hot_deal?.original_price || 0).toFixed(2)"></span>
                        </div>
                    </label>
                    <!-- Early bird -->
                    <label x-show="bookingDetail?.early_bird" class="flex items-center justify-between p-3 rounded-lg bg-blue-500/5 border border-blue-500/30 cursor-pointer hover:border-blue-400/50 transition-all"
                           :class="selectedPricing === 'early_bird' ? 'border-blue-400/60 bg-blue-500/10' : ''">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="pricing" value="early_bird" x-model="selectedPricing" class="w-4 h-4 text-blue-500 bg-navy-800 border-navy-600 focus:ring-blue-500">
                            <div>
                                <span class="text-sm font-medium text-white">Early Bird</span>
                                <span class="text-[10px] text-blue-400 ml-2" x-text="bookingDetail?.early_bird?.label || ''"></span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-bold text-blue-400" x-text="'$' + parseFloat(bookingDetail?.early_bird?.discounted_price || 0).toFixed(2)"></span>
                            <span class="text-[10px] text-slate-500 line-through ml-1" x-text="'$' + parseFloat(bookingDetail?.early_bird?.original_price || 0).toFixed(2)"></span>
                        </div>
                    </label>
                    <!-- Rolling prices -->
                    <template x-for="rp in bookingDetail?.rolling_prices || []" :key="rp.weeks">
                        <label class="flex items-center justify-between p-3 rounded-lg bg-navy-800/50 border border-navy-700 cursor-pointer hover:border-gold-500/30 transition-all"
                               :class="selectedPricing === 'rolling_' + rp.weeks ? 'border-gold-500/50 bg-gold-500/5' : ''">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="pricing" :value="'rolling_' + rp.weeks" x-model="selectedPricing" class="w-4 h-4 text-gold-500 bg-navy-800 border-navy-600 focus:ring-gold-500">
                                <div>
                                    <span class="text-sm font-medium text-white" x-text="rp.weeks + '-Week Package'"></span>
                                    <span x-show="rp.savings_label" class="text-[10px] text-emerald-400 ml-2" x-text="rp.savings_label"></span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-bold text-white" x-text="'$' + parseFloat(rp.total_price).toFixed(2)"></span>
                                <span class="text-[10px] text-slate-500 ml-1" x-text="'($' + parseFloat(rp.per_session_price).toFixed(2) + '/wk)'"></span>
                            </div>
                        </label>
                    </template>
                </div>
            </div>

            <!-- Login Gate -->
            <div x-show="cfg.require_login === '1' && !isLoggedIn && !bookingDetail?.is_full" class="p-6 border-b border-navy-700">
                <div class="text-center py-4">
                    <svg class="w-10 h-10 text-gold-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <p class="text-sm text-slate-400 mb-3">Please log in to book this class</p>
                    <a href="/login?redirect=/schedule" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg gradient-gold-bg text-navy-950 font-bold text-sm hover:opacity-90 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        Log In
                    </a>
                </div>
            </div>

            <!-- Payment Section (visible when logged in or login not required, and class not full) -->
            <div x-show="(cfg.require_login !== '1' || isLoggedIn) && !bookingDetail?.is_full" class="p-6">
                <h4 class="text-sm font-bold text-gold-400 uppercase tracking-wider mb-3">Payment</h4>

                <!-- Payment method tabs -->
                <div class="flex flex-wrap gap-1 mb-4 bg-navy-800 rounded-lg p-1">
                    <button x-show="paymentMethodsEnabled.card" @click="paymentTab = 'card'" class="flex-1 px-3 py-2 rounded-md text-xs font-semibold transition-all" :class="paymentTab === 'card' ? 'gradient-gold-bg text-navy-950' : 'text-slate-400 hover:text-white'">
                        <svg class="w-4 h-4 mx-auto mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        Card
                    </button>
                    <button x-show="paymentMethodsEnabled.credit_code" @click="paymentTab = 'credit_code'" class="flex-1 px-3 py-2 rounded-md text-xs font-semibold transition-all" :class="paymentTab === 'credit_code' ? 'gradient-gold-bg text-navy-950' : 'text-slate-400 hover:text-white'">
                        <svg class="w-4 h-4 mx-auto mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        Credit Code
                    </button>
                    <button x-show="paymentMethodsEnabled.gift_certificate" @click="paymentTab = 'gift_certificate'" class="flex-1 px-3 py-2 rounded-md text-xs font-semibold transition-all" :class="paymentTab === 'gift_certificate' ? 'gradient-gold-bg text-navy-950' : 'text-slate-400 hover:text-white'">
                        <svg class="w-4 h-4 mx-auto mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                        Gift Cert
                    </button>
                    <button x-show="paymentMethodsEnabled.other" @click="paymentTab = 'other'" class="flex-1 px-3 py-2 rounded-md text-xs font-semibold transition-all" :class="paymentTab === 'other' ? 'gradient-gold-bg text-navy-950' : 'text-slate-400 hover:text-white'">
                        <svg class="w-4 h-4 mx-auto mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Other
                    </button>
                </div>

                <!-- Card Payment -->
                <div x-show="paymentTab === 'card'">
                    <div id="sq-card-container" class="mb-4 rounded-lg overflow-hidden bg-navy-800 p-3 border border-navy-700 min-h-[120px]"></div>
                    <p class="text-[10px] text-slate-500 mb-3 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Secured by Square Payment Processing
                    </p>
                </div>

                <!-- Credit Code -->
                <div x-show="paymentTab === 'credit_code'">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Credit Code</label>
                            <input type="text" x-model="creditCode" placeholder="Enter credit code" maxlength="50"
                                   class="w-full px-3 py-2 rounded-lg bg-navy-800 border border-navy-700 text-white text-sm focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none">
                        </div>
                        <button @click="validateCreditCode()" class="text-xs px-4 py-2 rounded-lg bg-navy-700 text-gold-400 hover:bg-navy-600 transition-all" :disabled="!creditCode">
                            Validate Code
                        </button>
                        <div x-show="creditCodeValid" class="flex items-center gap-2 p-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-sm text-emerald-400" x-text="'Valid! Balance: $' + parseFloat(creditCodeBalance).toFixed(2)"></span>
                        </div>
                        <div x-show="creditCodeError" class="flex items-center gap-2 p-3 rounded-lg bg-red-500/10 border border-red-500/30">
                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span class="text-sm text-red-400" x-text="creditCodeError"></span>
                        </div>
                    </div>
                </div>

                <!-- Gift Certificate -->
                <div x-show="paymentTab === 'gift_certificate'">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Gift Certificate Code</label>
                            <input type="text" x-model="giftCode" placeholder="Enter gift certificate code" maxlength="50"
                                   class="w-full px-3 py-2 rounded-lg bg-navy-800 border border-navy-700 text-white text-sm focus:border-gold-500 focus:ring-1 focus:ring-gold-500 outline-none">
                        </div>
                        <button @click="validateGiftCode()" class="text-xs px-4 py-2 rounded-lg bg-navy-700 text-gold-400 hover:bg-navy-600 transition-all" :disabled="!giftCode">
                            Validate Certificate
                        </button>
                        <div x-show="giftCodeValid" class="flex items-center gap-2 p-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-sm text-emerald-400" x-text="'Valid! Balance: $' + parseFloat(giftCodeBalance).toFixed(2)"></span>
                        </div>
                        <div x-show="giftCodeError" class="flex items-center gap-2 p-3 rounded-lg bg-red-500/10 border border-red-500/30">
                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span class="text-sm text-red-400" x-text="giftCodeError"></span>
                        </div>
                    </div>
                </div>

                <!-- Other -->
                <div x-show="paymentTab === 'other'">
                    <div class="p-4 text-center rounded-lg bg-navy-800/50 border border-navy-700">
                        <p class="text-sm text-slate-400">Pay at the facility or contact us for alternative payment options.</p>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="mt-4 p-3 rounded-lg bg-navy-800/50 border border-navy-700">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Subtotal</span>
                        <span class="text-white font-bold" x-text="'$' + computedPrice().toFixed(2)"></span>
                    </div>
                    <div x-show="creditCodeValid && paymentTab === 'credit_code'" class="flex justify-between text-sm mt-1">
                        <span class="text-emerald-400">Credit Code</span>
                        <span class="text-emerald-400 font-bold" x-text="'-$' + Math.min(creditCodeBalance, computedPrice()).toFixed(2)"></span>
                    </div>
                    <div x-show="giftCodeValid && paymentTab === 'gift_certificate'" class="flex justify-between text-sm mt-1">
                        <span class="text-emerald-400">Gift Certificate</span>
                        <span class="text-emerald-400 font-bold" x-text="'-$' + Math.min(giftCodeBalance, computedPrice()).toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between text-sm mt-2 pt-2 border-t border-navy-700">
                        <span class="text-slate-300 font-bold">Total</span>
                        <span class="text-gold-400 font-bold text-lg" x-text="'$' + computedTotal().toFixed(2)"></span>
                    </div>
                </div>

                <!-- Book Button -->
                <button @click="submitBooking()" :disabled="bookingLoading || bookingDetail?.is_full"
                        class="w-full mt-4 py-3 rounded-xl gradient-gold-bg text-navy-950 font-bold text-sm hover:opacity-90 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    <svg x-show="bookingLoading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <span x-text="bookingLoading ? 'Processing...' : (computedTotal() <= 0 ? 'Book Now — Free' : 'Book & Pay $' + computedTotal().toFixed(2))"></span>
                </button>

                <!-- Success Message -->
                <div x-show="bookingSuccess" class="mt-4 p-4 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-center">
                    <svg class="w-10 h-10 text-emerald-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-emerald-400 font-bold">Booking Confirmed!</p>
                    <p class="text-sm text-slate-400 mt-1" x-text="bookingSuccessMsg"></p>
                </div>

                <!-- Error Message -->
                <div x-show="bookingError" class="mt-4 p-4 rounded-lg bg-red-500/10 border border-red-500/30 text-center">
                    <p class="text-red-400 font-bold text-sm" x-text="bookingError"></p>
                </div>
            </div>

            <!-- Full class message -->
            <div x-show="bookingDetail?.is_full" class="p-6 text-center">
                <svg class="w-12 h-12 text-red-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                <p class="text-lg font-bold text-white mb-1">Class Full</p>
                <p class="text-sm text-slate-400">This class has reached maximum capacity.</p>
                <a :href="'/schedule/' + bookingClass?.id" class="inline-block mt-3 text-sm text-gold-400 hover:text-gold-300 underline">View Details</a>
            </div>
        </div>
    </div>
</div>

<script>
function schedulePage() {
    const today = new Date(); today.setHours(0,0,0,0);
    return {
        classes: [], categories: [], currentDate: new Date(today), weekStart: null,
        viewMode: 'week', categoryFilter: null, loading: true,
        todayStr: '',

        // Settings-driven config
        cfg: {},
        enabledViews: [],
        resourceFilters: [],
        activeResourceFilters: {},

        // Booking modal state
        bookingModal: false,
        bookingClass: null,
        bookingDetail: null,
        bookingLoading: false,
        bookingSuccess: false,
        bookingSuccessMsg: '',
        bookingError: '',
        selectedPricing: 'standard',
        paymentTab: 'card',
        paymentMethodsEnabled: { card: true, credit_code: false, gift_certificate: false, other: false },
        isLoggedIn: false,
        sqCard: null,

        // Credit code state
        creditCode: '',
        creditCodeValid: false,
        creditCodeBalance: 0,
        creditCodeError: '',

        // Gift cert state
        giftCode: '',
        giftCodeValid: false,
        giftCodeBalance: 0,
        giftCodeError: '',

        init() {
            this.todayStr = this.fmtDate(today);
            this.weekStart = this.getMonday(new Date(today));
            this.isLoggedIn = !!(localStorage.getItem('access_token') || document.cookie.includes('access_token'));
            const params = new URLSearchParams(window.location.search);
            if (params.get('date')) {
                const d = new Date(params.get('date') + 'T12:00:00');
                if (!isNaN(d)) { this.currentDate = d; this.weekStart = this.getMonday(d); }
            }
            if (params.get('category')) {
                this.categoryFilter = parseInt(params.get('category')) || null;
            }
        },

        async loadSettings() {
            try {
                const resp = await fetch(baseApi + '/api/public/schedule-settings');
                const json = await resp.json();
                if (json.data) {
                    this.cfg = json.data.settings || {};
                    this.resourceFilters = json.data.resource_filters || [];

                    // Determine enabled views
                    let views = [];
                    try { views = JSON.parse(this.cfg.enabled_views || '["month","week","today","list"]'); } catch(e) { views = ['month','week','today','list']; }
                    const viewLabels = { month: 'Month', week: 'Week', today: 'Day', list: 'List', calendar: 'Calendar' };
                    this.enabledViews = views.map(v => ({ key: v, label: viewLabels[v] || v }));

                    // Set default view
                    const params = new URLSearchParams(window.location.search);
                    const paramView = params.get('view');
                    if (paramView && views.includes(paramView)) {
                        this.viewMode = paramView;
                    } else if (this.cfg.default_view && views.includes(this.cfg.default_view)) {
                        this.viewMode = this.cfg.default_view;
                    } else if (views.length > 0) {
                        this.viewMode = views[0];
                    }

                    // Payment methods
                    let pm = [];
                    try { pm = JSON.parse(this.cfg.payment_methods || '["card"]'); } catch(e) { pm = ['card']; }
                    this.paymentMethodsEnabled = {
                        card: pm.includes('card'),
                        credit_code: pm.includes('credit_code'),
                        gift_certificate: pm.includes('gift_certificate'),
                        other: pm.includes('other')
                    };
                    if (pm.length > 0) this.paymentTab = pm[0];
                }
            } catch(e) {
                // Defaults
                this.enabledViews = [
                    { key: 'month', label: 'Month' }, { key: 'week', label: 'Week' },
                    { key: 'today', label: 'Day' }, { key: 'list', label: 'List' }
                ];
            }
        },

        switchView(mode) {
            this.viewMode = mode;
            this.load();
        },

        toggleResourceFilter(resourceId, value) {
            if (value === null) {
                delete this.activeResourceFilters[resourceId];
            } else {
                this.activeResourceFilters[resourceId] = value;
            }
            // Force reactivity
            this.activeResourceFilters = { ...this.activeResourceFilters };
        },

        filteredClassesForDay(day) {
            let list = this.classes.filter(c => c.start_time?.substring(0,10) === day);
            // Apply resource filters
            const activeKeys = Object.keys(this.activeResourceFilters);
            if (activeKeys.length > 0) {
                list = list.filter(cls => {
                    for (const rid of activeKeys) {
                        const filterVal = this.activeResourceFilters[rid];
                        if (filterVal && cls.resource_values) {
                            const match = cls.resource_values.split(',').map(v => v.trim()).includes(filterVal);
                            if (!match) return false;
                        }
                    }
                    return true;
                });
            }
            return list;
        },

        async load() {
            this.loading = true;
            if (!this.weekStart) this.weekStart = this.getMonday(this.currentDate);
            try {
                // Load settings first if not loaded
                if (!Object.keys(this.cfg).length) {
                    await this.loadSettings();
                }
                if (!this.categories.length) {
                    this.categories = (await (await fetch(baseApi + '/api/public/categories')).json()).data || [];
                }
                const range = this.getDateRange();
                let url = baseApi + '/api/public/schedule?start=' + range.start + '&end=' + range.end;
                const fac = window.tenantApp?.selectedFacility || JSON.parse(localStorage.getItem('selected_facility') || 'null');
                if (fac?.id) url += '&facility_id=' + fac.id;
                else if (ORG.facilities.length) url += '&facility_id=' + ORG.facilities[0].id;
                if (this.categoryFilter) url += '&category_id=' + this.categoryFilter;
                this.classes = (await (await fetch(url)).json()).data || [];
            } catch(e) { this.classes = []; }
            this.loading = false;
        },

        getDateRange() {
            switch (this.viewMode) {
                case 'month':
                case 'calendar': {
                    const y = this.currentDate.getFullYear(), m = this.currentDate.getMonth();
                    const first = new Date(y, m, 1);
                    const last = new Date(y, m + 1, 0);
                    const startDay = first.getDay() || 7;
                    const start = this.addDays(first, -(startDay - 1));
                    const endDay = last.getDay() || 7;
                    const end = this.addDays(last, 7 - endDay);
                    return { start: this.fmtDate(start), end: this.fmtDate(end) };
                }
                case 'today':
                    return { start: this.fmtDate(this.currentDate), end: this.fmtDate(this.currentDate) };
                case 'list':
                    return { start: this.fmtDate(this.weekStart), end: this.fmtDate(this.addDays(this.weekStart, 13)) };
                default:
                    return { start: this.fmtDate(this.weekStart), end: this.fmtDate(this.addDays(this.weekStart, 6)) };
            }
        },

        dateLabel() {
            switch (this.viewMode) {
                case 'month':
                case 'calendar':
                    return this.currentDate.toLocaleDateString('en-US', {month:'long', year:'numeric'});
                case 'today':
                    return this.currentDate.toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'});
                case 'list': {
                    const s = this.weekStart, e = this.addDays(s, 13);
                    return s.toLocaleDateString('en-US', {month:'short', day:'numeric'}) + ' — ' + e.toLocaleDateString('en-US', {month:'short', day:'numeric'});
                }
                default: {
                    const s = this.weekStart, e = this.addDays(s, 6);
                    return s.toLocaleDateString('en-US', {month:'short', day:'numeric'}) + ' — ' + e.toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'});
                }
            }
        },

        prev() {
            switch (this.viewMode) {
                case 'month': case 'calendar':
                    this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() - 1, 1); break;
                case 'today':
                    this.currentDate = this.addDays(this.currentDate, -1); break;
                case 'list':
                    this.weekStart = this.addDays(this.weekStart, -14); this.currentDate = new Date(this.weekStart); break;
                default:
                    this.weekStart = this.addDays(this.weekStart, -7); this.currentDate = new Date(this.weekStart);
            }
            this.load();
        },
        next() {
            switch (this.viewMode) {
                case 'month': case 'calendar':
                    this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 1); break;
                case 'today':
                    this.currentDate = this.addDays(this.currentDate, 1); break;
                case 'list':
                    this.weekStart = this.addDays(this.weekStart, 14); this.currentDate = new Date(this.weekStart); break;
                default:
                    this.weekStart = this.addDays(this.weekStart, 7); this.currentDate = new Date(this.weekStart);
            }
            this.load();
        },
        goToday() {
            this.currentDate = new Date(today);
            this.weekStart = this.getMonday(new Date(today));
            this.load();
        },
        jumpToDate(dateStr) {
            if (!dateStr) return;
            const d = new Date(dateStr + 'T12:00:00');
            if (isNaN(d)) return;
            this.currentDate = d;
            this.weekStart = this.getMonday(d);
            this.load();
        },

        monthGrid() {
            const y = this.currentDate.getFullYear(), m = this.currentDate.getMonth();
            const first = new Date(y, m, 1);
            const last = new Date(y, m + 1, 0);
            const startDay = first.getDay() || 7;
            const start = this.addDays(first, -(startDay - 1));
            const endDay = last.getDay() || 7;
            const end = this.addDays(last, 7 - endDay);
            const cells = [];
            let cur = new Date(start);
            while (cur <= end) {
                cells.push({ date: this.fmtDate(cur), day: cur.getDate(), inMonth: cur.getMonth() === m });
                cur = this.addDays(cur, 1);
            }
            return cells;
        },

        weekDays() {
            const days = [];
            for (let i = 0; i < 7; i++) days.push(this.fmtDate(this.addDays(this.weekStart, i)));
            return days;
        },
        activeDays() {
            const range = this.getDateRange();
            const days = [];
            let cur = new Date(range.start + 'T12:00:00');
            const endD = new Date(range.end + 'T12:00:00');
            while (cur <= endD) {
                const d = this.fmtDate(cur);
                if (this.filteredClassesForDay(d).length > 0) days.push(d);
                cur = this.addDays(cur, 1);
            }
            return days;
        },
        classesForDay(day) { return this.classes.filter(c => c.start_time?.substring(0,10) === day); },

        dayLabel(d) { return new Date(d + 'T12:00:00').toLocaleDateString('en-US', {weekday:'short'}); },
        dayNum(d) { return new Date(d + 'T12:00:00').toLocaleDateString('en-US', {day:'numeric'}); },
        dayFull(d) { return new Date(d + 'T12:00:00').toLocaleDateString('en-US', {month:'long', day:'numeric'}); },

        getMonday(d) { const dt = new Date(d); const day = dt.getDay(); const diff = dt.getDate() - day + (day === 0 ? -6 : 1); dt.setDate(diff); dt.setHours(0,0,0,0); return dt; },
        addDays(d, n) { const dt = new Date(d); dt.setDate(dt.getDate() + n); return dt; },
        fmtDate(d) { return d.toISOString().split('T')[0]; },
        formatTime(dt) { return dt ? new Date(dt).toLocaleTimeString('en-US', {hour:'numeric', minute:'2-digit', hour12:true}) : ''; },

        // ── Booking Modal ──
        async openBooking(cls) {
            if (this.cfg.inline_booking === '0') {
                window.location.href = '/schedule/' + cls.id;
                return;
            }
            this.bookingClass = cls;
            this.bookingDetail = null;
            this.bookingModal = true;
            this.bookingSuccess = false;
            this.bookingError = '';
            this.selectedPricing = 'standard';
            this.creditCode = ''; this.creditCodeValid = false; this.creditCodeBalance = 0; this.creditCodeError = '';
            this.giftCode = ''; this.giftCodeValid = false; this.giftCodeBalance = 0; this.giftCodeError = '';

            // Fetch full detail
            try {
                const resp = await fetch(baseApi + '/api/public/classes/' + cls.id);
                const json = await resp.json();
                this.bookingDetail = json.data || cls;
            } catch(e) { this.bookingDetail = cls; }

            // Init Square card if card payment is enabled
            if (this.paymentMethodsEnabled.card && this.paymentTab === 'card') {
                this.$nextTick(() => this.initSquareCard());
            }
        },

        closeBooking() {
            this.bookingModal = false;
            this.bookingClass = null;
            this.bookingDetail = null;
            if (this.sqCard) { try { this.sqCard.destroy(); } catch(e) {} this.sqCard = null; }
        },

        async initSquareCard() {
            if (this.sqCard) return;
            try {
                const appId = typeof SQUARE_APP_ID !== 'undefined' ? SQUARE_APP_ID : null;
                const locId = typeof SQUARE_LOCATION_ID !== 'undefined' ? SQUARE_LOCATION_ID : null;
                if (!appId || !locId || !window.Square) return;
                const payments = window.Square.payments(appId, locId);
                this.sqCard = await payments.card();
                await this.sqCard.attach('#sq-card-container');
            } catch(e) {
                console.warn('Square card init:', e);
            }
        },

        computedPrice() {
            if (!this.bookingDetail) return 0;
            if (this.selectedPricing === 'hot_deal' && this.bookingDetail.hot_deal) {
                return parseFloat(this.bookingDetail.hot_deal.deal_price) || 0;
            }
            if (this.selectedPricing === 'early_bird' && this.bookingDetail.early_bird) {
                return parseFloat(this.bookingDetail.early_bird.discounted_price) || 0;
            }
            if (this.selectedPricing.startsWith('rolling_')) {
                const weeks = parseInt(this.selectedPricing.split('_')[1]);
                const rp = (this.bookingDetail.rolling_prices || []).find(r => r.weeks === weeks);
                if (rp) return parseFloat(rp.total_price) || 0;
            }
            return parseFloat(this.bookingDetail.price) || 0;
        },

        computedTotal() {
            let price = this.computedPrice();
            if (this.paymentTab === 'credit_code' && this.creditCodeValid) {
                price = Math.max(0, price - this.creditCodeBalance);
            }
            if (this.paymentTab === 'gift_certificate' && this.giftCodeValid) {
                price = Math.max(0, price - this.giftCodeBalance);
            }
            return price;
        },

        async validateCreditCode() {
            this.creditCodeError = '';
            this.creditCodeValid = false;
            if (!this.creditCode.trim()) return;
            try {
                const token = localStorage.getItem('access_token');
                const resp = await fetch(baseApi + '/api/public/validate-credit-code?code=' + encodeURIComponent(this.creditCode.trim()), {
                    headers: token ? { 'Authorization': 'Bearer ' + token } : {}
                });
                const json = await resp.json();
                if (json.data && json.data.valid) {
                    this.creditCodeValid = true;
                    this.creditCodeBalance = parseFloat(json.data.balance) || 0;
                } else {
                    this.creditCodeError = json.message || 'Invalid credit code';
                }
            } catch(e) {
                this.creditCodeError = 'Could not validate code';
            }
        },

        async validateGiftCode() {
            this.giftCodeError = '';
            this.giftCodeValid = false;
            if (!this.giftCode.trim()) return;
            try {
                const resp = await fetch(baseApi + '/api/public/validate-gift-code?code=' + encodeURIComponent(this.giftCode.trim()));
                const json = await resp.json();
                if (json.data && json.data.valid) {
                    this.giftCodeValid = true;
                    this.giftCodeBalance = parseFloat(json.data.balance) || 0;
                } else {
                    this.giftCodeError = json.message || 'Invalid gift certificate';
                }
            } catch(e) {
                this.giftCodeError = 'Could not validate certificate';
            }
        },

        async submitBooking() {
            if (this.bookingLoading || !this.bookingClass) return;
            this.bookingLoading = true;
            this.bookingError = '';
            this.bookingSuccess = false;

            try {
                const token = localStorage.getItem('access_token');
                if (this.cfg.require_login === '1' && !token) {
                    this.bookingError = 'Please log in to book.';
                    this.bookingLoading = false;
                    return;
                }

                const price = this.computedPrice();
                const total = this.computedTotal();
                const body = {
                    first_name: 'Guest',
                    payment_method: total <= 0 ? 'free' : this.paymentTab,
                    quote_amount: price,
                    amount_paid: total,
                    status: 'registered',
                    send_email: true,
                };

                // Attach player info from token if available
                if (token) {
                    try {
                        const payload = JSON.parse(atob(token.split('.')[1]));
                        body.first_name = payload.first_name || payload.name || 'Guest';
                        body.last_name = payload.last_name || '';
                        body.email = payload.email || '';
                        if (payload.player_id) body.player_id = payload.player_id;
                    } catch(e) {}
                }

                // Rolling weeks
                if (this.selectedPricing.startsWith('rolling_')) {
                    body.rolling_package_weeks = parseInt(this.selectedPricing.split('_')[1]);
                }

                // Payment-specific
                if (this.paymentTab === 'card' && total > 0) {
                    if (!this.sqCard) {
                        this.bookingError = 'Card payment not initialized. Please try again.';
                        this.bookingLoading = false;
                        return;
                    }
                    const tokenResult = await this.sqCard.tokenize();
                    if (tokenResult.status !== 'OK') {
                        this.bookingError = tokenResult.errors?.[0]?.message || 'Card verification failed';
                        this.bookingLoading = false;
                        return;
                    }
                    body.source_id = tokenResult.token;
                    body.payment_method = 'card';
                } else if (this.paymentTab === 'credit_code' && this.creditCodeValid) {
                    body.credit_code = this.creditCode.trim();
                    body.credit_amount = Math.min(this.creditCodeBalance, price);
                    body.payment_method = body.credit_amount >= price ? 'free' : 'card';
                } else if (this.paymentTab === 'gift_certificate' && this.giftCodeValid) {
                    body.gift_code = this.giftCode.trim();
                    body.gift_amount = Math.min(this.giftCodeBalance, price);
                    body.payment_method = body.gift_amount >= price ? 'free' : 'card';
                } else if (this.paymentTab === 'other') {
                    body.payment_method = 'manual';
                }

                const stId = this.bookingClass.session_type_id;
                const classId = this.bookingClass.id;
                const headers = { 'Content-Type': 'application/json' };
                if (token) headers['Authorization'] = 'Bearer ' + token;

                const resp = await fetch(baseApi + '/api/public/book-class/' + stId + '/' + classId, {
                    method: 'POST', headers, body: JSON.stringify(body)
                });
                const json = await resp.json();
                if (resp.ok && json.data) {
                    this.bookingSuccess = true;
                    this.bookingSuccessMsg = 'You are booked for ' + (this.bookingClass.session_type_name || 'this class') + '!';
                    // Refresh schedule
                    setTimeout(() => this.load(), 2000);
                } else {
                    this.bookingError = json.message || 'Booking failed. Please try again.';
                }
            } catch(e) {
                this.bookingError = 'An error occurred. Please try again.';
            }
            this.bookingLoading = false;
        },
    };
}
window.addEventListener('facility-changed', () => {
    document.querySelectorAll('[x-data]').forEach(el => { if (el.__x?.$data?.load) el.__x.$data.load(); });
});
</script>
