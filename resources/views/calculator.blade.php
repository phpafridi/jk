<x-app-layout>
    <x-slot name="header">Calculator</x-slot>

    <div class="max-w-4xl mx-auto" x-data="{ tab: 'standard' }" x-init="$watch('tab', v => $el.dataset.tab = v); $el.dataset.tab = 'standard'">

        <!-- Tab Buttons -->
        <div class="flex gap-2 mb-6 bg-white rounded-2xl border border-slate-200 p-1.5 shadow-sm w-fit">
            <button @click="tab='standard'"
                    :class="tab==='standard' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'"
                    class="px-5 py-2.5 rounded-xl text-sm font-medium transition-all flex items-center gap-2">
                <i class="fas fa-calculator"></i> Standard
            </button>
            <button @click="tab='property'"
                    :class="tab==='property' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'"
                    class="px-5 py-2.5 rounded-xl text-sm font-medium transition-all flex items-center gap-2">
                <i class="fas fa-store"></i> Property / Instalment
            </button>
        </div>

        <!-- Standard Calculator -->
        <div x-show="tab==='standard'" x-transition>
            <p class="text-center text-xs text-slate-400 mb-3"><i class="fas fa-keyboard mr-1"></i> Keyboard supported: digits, <kbd class="bg-slate-100 px-1 rounded">+ - * /</kbd>, <kbd class="bg-slate-100 px-1 rounded">Enter</kbd>, <kbd class="bg-slate-100 px-1 rounded">Backspace</kbd>, <kbd class="bg-slate-100 px-1 rounded">Esc</kbd> to clear</p>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden max-w-sm mx-auto" x-data="stdCalc()" x-init="window._calc = $data" x-destroy="window._calc = null">
                <!-- Display -->
                <div class="bg-gradient-to-br from-slate-800 to-slate-900 p-5 text-right">
                    <p class="text-slate-400 text-sm h-5 truncate" x-text="expression || ' '"></p>
                    <p class="text-white text-4xl font-light mt-1 truncate" x-text="display"></p>
                </div>
                <!-- Buttons -->
                <div class="grid grid-cols-4 gap-px bg-slate-200">
                    <!-- Row 1 -->
                    <button @click="clear(); $el.blur()" class="calc-btn bg-red-50 text-red-600 hover:bg-red-100 font-semibold">AC</button>
                    <button @click="toggleSign(); $el.blur()" class="calc-btn bg-slate-50 text-slate-700 hover:bg-slate-100">+/−</button>
                    <button @click="percent(); $el.blur()" class="calc-btn bg-slate-50 text-slate-700 hover:bg-slate-100">%</button>
                    <button @click="op('÷'); $el.blur()" :class="activeOp==='÷'?'bg-indigo-600 text-white':'bg-indigo-50 text-indigo-700 hover:bg-indigo-100'" class="calc-btn font-bold text-lg">÷</button>
                    <!-- Row 2 -->
                    <button @click="digit('7'); $el.blur()" class="calc-btn bg-white text-slate-800 hover:bg-slate-50">7</button>
                    <button @click="digit('8'); $el.blur()" class="calc-btn bg-white text-slate-800 hover:bg-slate-50">8</button>
                    <button @click="digit('9'); $el.blur()" class="calc-btn bg-white text-slate-800 hover:bg-slate-50">9</button>
                    <button @click="op('×'); $el.blur()" :class="activeOp==='×'?'bg-indigo-600 text-white':'bg-indigo-50 text-indigo-700 hover:bg-indigo-100'" class="calc-btn font-bold text-lg">×</button>
                    <!-- Row 3 -->
                    <button @click="digit('4'); $el.blur()" class="calc-btn bg-white text-slate-800 hover:bg-slate-50">4</button>
                    <button @click="digit('5'); $el.blur()" class="calc-btn bg-white text-slate-800 hover:bg-slate-50">5</button>
                    <button @click="digit('6'); $el.blur()" class="calc-btn bg-white text-slate-800 hover:bg-slate-50">6</button>
                    <button @click="op('−'); $el.blur()" :class="activeOp==='−'?'bg-indigo-600 text-white':'bg-indigo-50 text-indigo-700 hover:bg-indigo-100'" class="calc-btn font-bold text-lg">−</button>
                    <!-- Row 4 -->
                    <button @click="digit('1'); $el.blur()" class="calc-btn bg-white text-slate-800 hover:bg-slate-50">1</button>
                    <button @click="digit('2'); $el.blur()" class="calc-btn bg-white text-slate-800 hover:bg-slate-50">2</button>
                    <button @click="digit('3'); $el.blur()" class="calc-btn bg-white text-slate-800 hover:bg-slate-50">3</button>
                    <button @click="op('+'); $el.blur()" :class="activeOp==='+'?'bg-indigo-600 text-white':'bg-indigo-50 text-indigo-700 hover:bg-indigo-100'" class="calc-btn font-bold text-lg">+</button>
                    <!-- Row 5 -->
                    <button @click="digit('0'); $el.blur()" class="calc-btn bg-white text-slate-800 hover:bg-slate-50 col-span-2">0</button>
                    <button @click="dot(); $el.blur()" class="calc-btn bg-white text-slate-800 hover:bg-slate-50">.</button>
                    <button @click="equals(); $el.blur()" class="calc-btn bg-indigo-600 text-white hover:bg-indigo-700 font-bold text-lg">=</button>
                </div>
            </div>
        </div>

        <!-- Property / Instalment Calculator -->
        <div x-show="tab==='property'" x-transition x-data="propCalc()">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Input Panel -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-edit text-indigo-500"></i> Enter Details
                    </h3>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Total Property Price (Rs)</label>
                        <input type="number" x-model.number="totalPrice" @input="calc()" min="0"
                               class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. 3000000">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Advance / Down Payment (Rs)</label>
                        <input type="number" x-model.number="advance" @input="calc()" min="0"
                               class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. 800000">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Monthly Instalment (Rs)</label>
                        <input type="number" x-model.number="monthly" @input="calc()" min="0"
                               class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. 20000">
                        <p class="text-xs text-slate-400 mt-1">Leave blank to auto-calculate from duration</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Duration (Months)</label>
                        <input type="number" x-model.number="months" @input="calc()" min="1"
                               class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="e.g. 60">
                        <p class="text-xs text-slate-400 mt-1">Leave blank to auto-calculate from monthly amount</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Already Paid (Rs) <span class="text-slate-400 font-normal">(optional)</span></label>
                        <input type="number" x-model.number="alreadyPaid" @input="calc()" min="0"
                               class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="0">
                    </div>

                    <button @click="reset()" class="w-full py-2.5 rounded-xl border border-slate-300 text-sm font-medium text-slate-600 hover:bg-slate-50">
                        <i class="fas fa-undo mr-1"></i> Reset
                    </button>
                </div>

                <!-- Results Panel -->
                <div class="space-y-4">
                    <!-- Summary Card -->
                    <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl p-5 text-white">
                        <p class="text-indigo-200 text-sm mb-1">Total Property Price</p>
                        <p class="text-3xl font-bold" x-text="'Rs ' + fmt(totalPrice || 0)"></p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-center">
                            <p class="text-xs text-slate-500 mb-1">Advance Paid</p>
                            <p class="text-xl font-bold text-blue-600" x-text="'Rs ' + fmt(advance || 0)"></p>
                        </div>
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-center">
                            <p class="text-xs text-slate-500 mb-1">Remaining Balance</p>
                            <p class="text-xl font-bold text-slate-800" x-text="'Rs ' + fmt(remaining)"></p>
                        </div>
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-center">
                            <p class="text-xs text-slate-500 mb-1">Monthly Instalment</p>
                            <p class="text-xl font-bold text-indigo-600" x-text="monthly ? 'Rs ' + fmt(monthly) : '—'"></p>
                        </div>
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-center">
                            <p class="text-xs text-slate-500 mb-1">Total Months</p>
                            <p class="text-xl font-bold text-slate-800" x-text="calcMonths || '—'"></p>
                        </div>
                    </div>

                    <!-- Progress -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-slate-600 font-medium">Payment Progress</span>
                            <span class="font-bold text-indigo-600" x-text="pct + '%'"></span>
                        </div>
                        <div class="bg-slate-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-3 rounded-full transition-all duration-500"
                                 :style="'width:' + pct + '%'"></div>
                        </div>
                        <div class="flex justify-between text-xs text-slate-500 mt-2">
                            <span>Paid: Rs <span x-text="fmt(totalPaidSoFar)"></span></span>
                            <span>Left: Rs <span x-text="fmt(Math.max(0, (totalPrice||0) - totalPaidSoFar))"></span></span>
                        </div>
                    </div>

                    <!-- Instalment Schedule (first 6 months) -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden" x-show="monthly > 0 && remaining > 0">
                        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50">
                            <p class="text-sm font-semibold text-slate-700"><i class="fas fa-list-ol text-indigo-500 mr-1"></i> Instalment Schedule Preview</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="border-b border-slate-100">
                                        <th class="text-left px-4 py-2.5 text-slate-500 font-semibold">Month</th>
                                        <th class="text-right px-4 py-2.5 text-slate-500 font-semibold">Instalment</th>
                                        <th class="text-right px-4 py-2.5 text-slate-500 font-semibold">Balance Left</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    <template x-for="row in schedule" :key="row.month">
                                        <tr class="hover:bg-slate-50">
                                            <td class="px-4 py-2" x-text="'Month ' + row.month"></td>
                                            <td class="px-4 py-2 text-right font-medium text-indigo-600" x-text="'Rs ' + fmt(row.instalment)"></td>
                                            <td class="px-4 py-2 text-right text-slate-600" x-text="'Rs ' + fmt(row.balance)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-xs text-center text-slate-400 py-2" x-show="calcMonths > 6">Showing first 6 of <span x-text="calcMonths"></span> months</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .calc-btn { @apply py-5 text-lg font-medium flex items-center justify-center transition-colors cursor-pointer select-none; }
    </style>

    <script>
    function stdCalc() {
        return {
            display: '0', expression: '', current: '0', operator: null, prevVal: null, newInput: true, activeOp: null,
            digit(d) {
                if (this.newInput) { this.current = d; this.newInput = false; }
                else { this.current = this.current === '0' ? d : this.current + d; }
                this.display = this.current;
            },
            dot() {
                if (this.newInput) { this.current = '0.'; this.newInput = false; }
                else if (!this.current.includes('.')) this.current += '.';
                this.display = this.current;
            },
            op(o) {
                if (this.operator && !this.newInput) this.equals();
                this.prevVal = parseFloat(this.current);
                this.operator = o; this.activeOp = o;
                this.expression = this.current + ' ' + o;
                this.newInput = true;
            },
            equals() {
                if (!this.operator || this.prevVal === null) return;
                const a = this.prevVal, b = parseFloat(this.current);
                let r;
                if (this.operator === '+') r = a + b;
                else if (this.operator === '−') r = a - b;
                else if (this.operator === '×') r = a * b;
                else if (this.operator === '÷') r = b !== 0 ? a / b : 'Error';
                this.expression = a + ' ' + this.operator + ' ' + b + ' =';
                this.current = r === 'Error' ? 'Error' : this.round(r).toString();
                this.display = this.current;
                this.operator = null; this.activeOp = null; this.newInput = true; this.prevVal = null;
            },
            clear() { this.display = '0'; this.expression = ''; this.current = '0'; this.operator = null; this.prevVal = null; this.newInput = true; this.activeOp = null; },
            toggleSign() { if (this.current !== '0') { this.current = (parseFloat(this.current) * -1).toString(); this.display = this.current; } },
            percent() { this.current = (parseFloat(this.current) / 100).toString(); this.display = this.current; },
            round(n) { return Math.round(n * 1e10) / 1e10; },
            backspace() {
                if (this.newInput || this.current === 'Error') { this.current = '0'; this.display = '0'; this.newInput = true; return; }
                this.current = this.current.length > 1 ? this.current.slice(0, -1) : '0';
                this.display = this.current;
            }
        }
    }

    function propCalc() {
        return {
            totalPrice: null, advance: null, monthly: null, months: null, alreadyPaid: null,
            remaining: 0, calcMonths: null, pct: 0, totalPaidSoFar: 0, schedule: [],
            calc() {
                const tp = this.totalPrice || 0;
                const adv = this.advance || 0;
                this.remaining = Math.max(0, tp - adv);

                if (this.monthly && this.monthly > 0 && !this.months) {
                    this.calcMonths = this.remaining > 0 ? Math.ceil(this.remaining / this.monthly) : 0;
                } else if (this.months && this.months > 0 && !this.monthly) {
                    this.monthly = this.remaining > 0 ? Math.ceil(this.remaining / this.months) : 0;
                    this.calcMonths = this.months;
                } else {
                    this.calcMonths = this.months || (this.monthly > 0 && this.remaining > 0 ? Math.ceil(this.remaining / this.monthly) : null);
                }

                const paid = (this.alreadyPaid || 0) + adv;
                this.totalPaidSoFar = paid;
                this.pct = tp > 0 ? Math.min(100, Math.round((paid / tp) * 100)) : 0;

                // Schedule preview (up to 6 rows)
                this.schedule = [];
                if (this.monthly > 0 && this.remaining > 0) {
                    let bal = this.remaining;
                    const rows = Math.min(6, this.calcMonths || 6);
                    for (let i = 1; i <= rows; i++) {
                        const inst = Math.min(this.monthly, bal);
                        bal = Math.max(0, bal - inst);
                        this.schedule.push({ month: i, instalment: inst, balance: bal });
                        if (bal === 0) break;
                    }
                }
            },
            reset() { this.totalPrice = null; this.advance = null; this.monthly = null; this.months = null; this.alreadyPaid = null; this.calc(); },
            fmt(n) { return Number(Math.round(n)).toLocaleString('en-PK'); }
        }
    }

    // Single keyboard listener — registered once, never duplicated
    window.addEventListener('keydown', function(e) {
        const c = window._calc;
        if (!c) return;
        // Only fire when standard tab is active
        const wrapper = document.querySelector('[data-tab]');
        if (!wrapper || wrapper.dataset.tab !== 'standard') return;
        // Ignore if an input/textarea/select is focused
        const tag = document.activeElement.tagName;
        if (['INPUT','TEXTAREA','SELECT'].includes(tag)) return;
        // Ignore if a calc button is focused (prevents double-fire after mouse click)
        if (tag === 'BUTTON' && document.activeElement.closest('.grid')) return;
        const k = e.key;
        if (k >= '0' && k <= '9')                { e.preventDefault(); c.digit(k); }
        else if (k === '.')                       { e.preventDefault(); c.dot(); }
        else if (k === '+')                       { e.preventDefault(); c.op('+'); }
        else if (k === '-')                       { e.preventDefault(); c.op('\u2212'); }
        else if (k === '*')                       { e.preventDefault(); c.op('\u00d7'); }
        else if (k === '/')                       { e.preventDefault(); c.op('\u00f7'); }
        else if (k === 'Enter' || k === '=')      { e.preventDefault(); c.equals(); }
        else if (k === 'Backspace')               { e.preventDefault(); c.backspace(); }
        else if (k === 'Delete' || k === 'Escape'){ e.preventDefault(); c.clear(); }
        else if (k === '%')                       { e.preventDefault(); c.percent(); }
    });
    </script>
</x-app-layout>
