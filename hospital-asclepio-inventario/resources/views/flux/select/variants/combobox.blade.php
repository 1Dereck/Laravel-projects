@blaze

@props([
    'name' => $attributes->whereStartsWith('wire:model')->first(),
    'placeholder' => 'Selecione...',
    'invalid' => null,
    'size' => null,
    'searchable' => true,
])

@php
$invalid ??= ($name && $errors->has($name));
@endphp

<div
    x-data="{
        open: false,
        displaySearch: '',
        selectedVal: '',
        selectedLabel: '',
        options: [],
        highlightedIndex: 0,

        init() {
            this.readOptions();
            this.updateSelected();
            
            if (this.$refs.nativeSelect) {
                const observer = new MutationObserver(() => {
                    this.readOptions();
                    this.updateSelected();
                });
                observer.observe(this.$refs.nativeSelect, { childList: true, subtree: true, attributes: true });
            }
        },

        readOptions() {
            if (!this.$refs.nativeSelect) return;
            const opts = Array.from(this.$refs.nativeSelect.options);
            this.options = opts.map(opt => ({
                value: opt.value,
                label: opt.text.trim(),
                disabled: opt.disabled,
                isPlaceholder: opt.hasAttribute('disabled') && opt.value === ''
            }));
        },

        updateSelected() {
            if (!this.$refs.nativeSelect) return;
            const val = this.$refs.nativeSelect.value;
            this.selectedVal = val;
            const found = this.options.find(o => String(o.value) === String(val));
            if (found && found.label) {
                this.selectedLabel = found.label;
                if (!this.open) {
                    this.displaySearch = found.label;
                }
            } else {
                this.selectedLabel = '';
                if (!this.open) {
                    this.displaySearch = '';
                }
            }
        },

        get filteredOptions() {
            const list = this.options.filter(o => !o.isPlaceholder);
            if (!this.displaySearch.trim() || this.displaySearch === this.selectedLabel) {
                return list;
            }
            const q = this.displaySearch.toLowerCase().trim();
            return list.filter(o => 
                o.label.toLowerCase().includes(q) || 
                String(o.value).toLowerCase().includes(q)
            );
        },

        selectOption(opt) {
            if (!opt || opt.disabled) return;
            this.selectedVal = opt.value;
            this.selectedLabel = opt.label;
            this.displaySearch = opt.label;
            this.open = false;
            if (this.$refs.nativeSelect) {
                this.$refs.nativeSelect.value = opt.value;
                this.$refs.nativeSelect.dispatchEvent(new Event('change', { bubbles: true }));
                this.$refs.nativeSelect.dispatchEvent(new Event('input', { bubbles: true }));
            }
        },

        onInputFocus() {
            this.open = true;
            this.readOptions();
            if (this.displaySearch === this.selectedLabel) {
                this.$nextTick(() => {
                    if (this.$refs.inputEl) this.$refs.inputEl.select();
                });
            }
        },

        handleKeydown(e) {
            if (!this.open) {
                if (e.key === 'ArrowDown' || e.key === 'Enter') {
                    this.open = true;
                    e.preventDefault();
                }
                return;
            }

            const filtered = this.filteredOptions;
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                this.highlightedIndex = (this.highlightedIndex + 1) % (filtered.length || 1);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                this.highlightedIndex = (this.highlightedIndex - 1 + filtered.length) % (filtered.length || 1);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (filtered[this.highlightedIndex]) {
                    this.selectOption(filtered[this.highlightedIndex]);
                }
            } else if (e.key === 'Escape') {
                this.open = false;
                this.displaySearch = this.selectedLabel;
            }
        }
    }"
    @click.outside="open = false; if (selectedLabel) displaySearch = selectedLabel; else displaySearch = ''"
    class="relative w-full"
>
    <!-- Hidden native select for Livewire wire:model binding -->
    <select
        x-ref="nativeSelect"
        {{ $attributes->class('hidden') }}
        @change="updateSelected()"
        data-flux-control
    >
        @if ($placeholder)
            <option value="" disabled class="placeholder">{{ $placeholder }}</option>
        @endif
        {{ $slot }}
    </select>

    <!-- Main Direct Input / Select Field -->
    <div class="relative">
        @if ($searchable)
            <input
                x-ref="inputEl"
                type="text"
                x-model="displaySearch"
                @focus="onInputFocus()"
                @input="open = true; highlightedIndex = 0"
                @keydown="handleKeydown($event)"
                placeholder="{{ $placeholder }}"
                class="w-full pl-4 pr-10 py-2.5 rounded-xl bg-slate-50 focus:bg-white dark:bg-slate-950 dark:focus:bg-slate-900 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-slate-100 text-sm font-medium placeholder-slate-400 dark:placeholder-slate-500 selection:bg-emerald-500/30 selection:text-emerald-900 dark:selection:text-emerald-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition shadow-xs {{ $invalid ? 'border-red-500' : '' }}"
            />
        @else
            <button
                type="button"
                @click="open = !open; if(open) readOptions()"
                @keydown="handleKeydown($event)"
                class="w-full pl-4 pr-10 py-2.5 rounded-xl bg-slate-50 hover:bg-slate-100/80 dark:bg-slate-950 dark:hover:bg-slate-900 border border-slate-300 dark:border-slate-800 text-left text-slate-900 dark:text-slate-100 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition shadow-xs cursor-pointer flex items-center justify-between {{ $invalid ? 'border-red-500' : '' }}"
            >
                <span x-text="selectedLabel || '{{ $placeholder }}'" :class="{ 'text-slate-400 dark:text-slate-500': !selectedLabel }"></span>
            </button>
        @endif

        <button
            type="button"
            @click="open = !open; if(open && $refs.inputEl) $nextTick(() => $refs.inputEl.focus())"
            tabindex="-1"
            class="absolute right-0 top-0 bottom-0 px-3 flex items-center justify-center text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 cursor-pointer"
        >
            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    </div>

    <!-- Dropdown List of Options -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 mt-1.5 w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl overflow-hidden flex flex-col"
        style="display: none;"
    >
        <div class="overflow-y-auto p-1.5 max-h-56 space-y-0.5">
            <template x-for="(opt, idx) in filteredOptions" :key="opt.value + '-' + idx">
                <button
                    type="button"
                    @mousedown.prevent="selectOption(opt)"
                    @mouseenter="highlightedIndex = idx"
                    :class="{
                        'bg-emerald-600 text-white dark:bg-emerald-600 dark:text-white font-semibold shadow-xs': highlightedIndex === idx,
                        'bg-emerald-100 text-emerald-900 dark:bg-emerald-950/80 dark:text-emerald-200 font-bold border-l-4 border-emerald-500': String(selectedVal) === String(opt.value) && highlightedIndex !== idx,
                        'text-slate-800 dark:text-slate-200': String(selectedVal) !== String(opt.value) && highlightedIndex !== idx
                    }"
                    class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs sm:text-sm flex items-center justify-between transition cursor-pointer"
                >
                    <span x-text="opt.label" class="truncate"></span>
                    <template x-if="String(selectedVal) === String(opt.value)">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </template>
                </button>
            </template>

            <template x-if="filteredOptions.length === 0">
                <div class="px-4 py-4 text-center text-xs sm:text-sm text-slate-500 dark:text-slate-400 italic bg-slate-50 dark:bg-slate-950/40 rounded-xl">
                    Nenhuma opção encontrada.
                </div>
            </template>
        </div>
    </div>
</div>
