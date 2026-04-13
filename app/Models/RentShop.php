<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RentShop extends Model
{
    protected $fillable = [
        'rent_market_id','shop_number','tenant_name','tenant_phone','tenant_cnic',
        'status','rent_amount','notes','rent_start_date'
    ];
    protected $casts = [
        'rent_start_date' => 'date',
        'rent_amount'     => 'decimal:2',
    ];

    public function rentMarket()  { return $this->belongsTo(RentMarket::class); }
    public function rentEntries() { return $this->hasMany(RentEntry::class); }
    public function documents()   { return $this->morphMany(EntryDocument::class, 'documentable'); }

    /**
     * Calculate rent status: how many months missed since rent_start_date.
     *
     * Logic:
     *  - monthsDue  = number of COMPLETED months from start up to (not including) current month
     *                 e.g. start=Jan 2026, today=Apr 2026 → Jan/Feb/Mar = 3 months due
     *  - totalDue   = monthsDue × rent_amount
     *  - paidAmount = sum of all amount_paid on entries
     *  - missed     = max(0, totalDue - paidAmount)   [in money, not entry count]
     *  - monthsMissed = ceil(missed / rent_amount)
     *
     * This way, paying March when Jan+Feb are missed still shows 2 months pending.
     */
    public function rentStatus(): array
    {
        $startDate  = $this->rent_start_date;
        $rentAmount = (float) $this->rent_amount;

        if (!$startDate || !$rentAmount || $rentAmount <= 0) {
            // No start date — fall back to entry-based pending
            $pending = $this->rentEntries->sum(fn($e) => max(0, (float)$e->rent - (float)$e->amount_paid));
            $paid    = $this->rentEntries->sum('amount_paid');
            return [
                'months_due'    => 0,
                'months_paid'   => 0,
                'months_missed' => 0,
                'missed_amount' => $pending,
                'paid_amount'   => (float)$paid,
                'has_start_date'=> false,
            ];
        }

        $now   = Carbon::today();
        // Start of the month rent began
        $start = Carbon::parse($startDate)->startOfMonth();
        // Start of the CURRENT month — rent for current month is not yet due
        $currentMonthStart = $now->copy()->startOfMonth();

        // Only count fully-elapsed months (not the current month)
        if ($currentMonthStart->lte($start)) {
            $monthsDue = 0;
        } else {
            $monthsDue = (int) $start->diffInMonths($currentMonthStart);
        }

        $totalDue   = $monthsDue * $rentAmount;
        $paidAmount = (float) $this->rentEntries->sum('amount_paid');

        $missedAmount = max(0, $totalDue - $paidAmount);
        $monthsMissed = $rentAmount > 0 ? (int) ceil($missedAmount / $rentAmount) : 0;
        // months "paid" = how many full months worth of payments received
        $monthsPaid   = $rentAmount > 0 ? (int) floor($paidAmount / $rentAmount) : 0;

        return [
            'months_due'     => $monthsDue,
            'months_paid'    => $monthsPaid,
            'months_missed'  => $monthsMissed,
            'missed_amount'  => $missedAmount,
            'total_due'      => $totalDue,
            'paid_amount'    => $paidAmount,
            'has_start_date' => true,
        ];
    }
}
