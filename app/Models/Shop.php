<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Shop extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'market_id','shop_number','owner_id','type',
        'date_of_payment','total_amount','paid_amount','rent_amount','status',
        'instalment_start_date','monthly_instalment',
    ];
    protected $casts = [
        'date_of_payment'       => 'date',
        'instalment_start_date' => 'date',
        'total_amount'          => 'decimal:2',
        'paid_amount'           => 'decimal:2',
        'rent_amount'           => 'decimal:2',
        'monthly_instalment'    => 'decimal:2',
    ];

    public function market()    { return $this->belongsTo(Market::class); }
    public function owner()     { return $this->belongsTo(Owner::class, 'owner_id'); }
    public function payments()  { return $this->hasMany(ShopPayment::class); }
    public function documents() { return $this->hasMany(ShopDocument::class); }
    public function rentEntries(){ return $this->hasMany(RentEntry::class); }
    public function customers() { return $this->hasMany(Customer::class); }

    /**
     * Calculate instalment status based on start date + monthly_instalment.
     *
     * monthsDue = completed months since start (NOT including current month).
     * e.g. start=Jan 2026, today=Apr 13 → Jan/Feb/Mar = 3 months due.
     */
    public function instalmentStatus(): array
    {
        $startDate   = $this->instalment_start_date;
        $monthly     = (float) $this->monthly_instalment;
        $totalAmount = (float) $this->total_amount;
        $paidAmount  = (float) $this->paid_amount;
        $balance     = max(0, $totalAmount - $paidAmount);

        if (!$startDate || !$monthly || $monthly <= 0) {
            return [
                'months_due'       => 0,
                'months_paid'      => 0,
                'months_missed'    => 0,
                'missed_amount'    => $balance,
                'total_due_so_far' => $totalAmount,
                'paid_amount'      => $paidAmount,
                'balance'          => $balance,
                'has_start_date'   => false,
            ];
        }

        $now              = \Carbon\Carbon::today();
        $start            = \Carbon\Carbon::parse($startDate)->startOfMonth();
        $currentMonthStart = $now->copy()->startOfMonth();

        // Only completed months (not current month)
        if ($currentMonthStart->lte($start)) {
            $monthsDue = 0;
        } else {
            $monthsDue = (int) $start->diffInMonths($currentMonthStart);
        }

        // Cap at total instalments
        if ($totalAmount > 0) {
            $totalInstalments = (int) ceil($totalAmount / $monthly);
            $monthsDue = min($monthsDue, $totalInstalments);
        }

        $totalDueSoFar = min($monthsDue * $monthly, $totalAmount);
        $monthsPaid    = (int) floor($paidAmount / $monthly);
        $monthsMissed  = max(0, $monthsDue - $monthsPaid);
        $missedAmount  = max(0, $totalDueSoFar - $paidAmount);

        return [
            'months_due'       => $monthsDue,
            'months_paid'      => $monthsPaid,
            'months_missed'    => $monthsMissed,
            'missed_amount'    => $missedAmount,
            'total_due_so_far' => $totalDueSoFar,
            'paid_amount'      => $paidAmount,
            'balance'          => $balance,
            'has_start_date'   => true,
        ];
    }
}
