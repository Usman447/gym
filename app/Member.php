<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;

class Member extends Model implements HasMedia
{
    use InteractsWithMedia;
    use createdByUser, updatedByUser;

    protected $table = 'mst_members';

    protected $fillable = [
        'member_code',
        'name',
        'age',
        'gender',
        'contact',
        'timings',
        'health_issues',
        'status',
        'credit_balance',
        'height_ft',
        'height_in',
        'weight_kg',
        'opf_residence',
        'address',
        'in_biometric_device',
        'has_fingerprint',
        'created_by',
        'created_by_user_name',
        'created_by_user_email',
        'updated_by',
        'updated_by_user_name',
        'updated_by_user_email',
    ];

    protected $dates = ['created_at', 'updated_at'];

    protected $searchableColumns = [
        'member_code' => 20,
        'name' => 20,
        'contact' => 20,
    ];

    // Media i.e. Image size conversion
    public function registerMediaConversions(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('thumb')
             ->width(50)
             ->height(50)
             ->quality(100)
             ->performOnCollections('profile');

        $this->addMediaConversion('form')
             ->width(70)
             ->height(70)
             ->quality(100)
             ->performOnCollections('profile', 'proof');
    }

    //Relationships
    public function subscriptions()
    {
        return $this->hasMany('App\Subscription');
    }

    public function invoices()
    {
        return $this->hasMany('App\Invoice');
    }

    //Scope Queries
    public function scopeIndexQuery($query, $sorting_field, $sorting_direction, $drp_start, $drp_end)
    {
        $sorting_field = ($sorting_field != null ? $sorting_field : 'created_at');
        $sorting_direction = ($sorting_direction != null ? $sorting_direction : 'desc');

        if ($drp_start == null or $drp_end == null) {
            return $query->select('mst_members.id', 'mst_members.member_code', 'mst_members.name', 'mst_members.contact', 'mst_members.timings', 'mst_members.created_at', 'mst_members.status')->where('mst_members.status', '!=', \constStatus::Archive)->orderBy($sorting_field, $sorting_direction);
        }

        return $query->select('mst_members.id', 'mst_members.member_code', 'mst_members.name', 'mst_members.contact', 'mst_members.timings', 'mst_members.created_at', 'mst_members.status')->where('mst_members.status', '!=', \constStatus::Archive)->whereBetween('mst_members.created_at', [
            $drp_start,
            $drp_end,
        ])->orderBy($sorting_field, $sorting_direction);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '=', \constStatus::Active);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', '=', \constStatus::InActive);
    }

    public function scopeRecent($query)
    {
        return $query->where('created_at', '<=', Carbon::today())->take(10)->orderBy('created_at', 'desc');
    }

    public function scopeBirthday($query)
    {
        // DOB column removed; return none to avoid errors on dashboard birthday widget
        return $query->whereRaw('1=0');
    }

    // Laravel issue: Workaroud Needed
    public function scopeRegistrations($query, $month, $year)
    {
        return $query->whereMonth('created_at', '=', $month)->whereYear('created_at', '=', $year)->count();
    }

    /**
     * Search scope to replace Eloquence search functionality
     * Searches across member_code, name, and contact fields
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $searchTerm Search term (may be quoted like '"term"')
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $searchTerm)
    {
        if (empty($searchTerm)) {
            return $query;
        }

        // Remove quotes if present (old Eloquence format)
        $searchTerm = trim($searchTerm, '"\'');
        
        if (empty($searchTerm)) {
            return $query;
        }

        // Search across searchable columns
        return $query->where(function($q) use ($searchTerm) {
            $q->where('member_code', 'LIKE', '%' . $searchTerm . '%')
              ->orWhere('name', 'LIKE', '%' . $searchTerm . '%')
              ->orWhere('contact', 'LIKE', '%' . $searchTerm . '%');
        });
    }

    /**
     * Calculate and sync member credit balance from invoices
     * Credit balance = sum of negative pending_amount (overpaid) - sum of positive pending_amount (outstanding)
     * Returns: positive = credit available, negative = due amount
     * 
     * @return int
     */
    public function calculateCreditBalance()
    {
        // Get all invoices for this member
        $invoices = $this->invoices()->get();
        
        $creditBalance = 0;
        $invoiceDetails = [];
        
        foreach ($invoices as $invoice) {
            // If pending_amount is negative, it's an overpayment (credit)
            // If pending_amount is positive, it's outstanding (due)
            $invoiceContribution = -(int)$invoice->pending_amount;
            $creditBalance += $invoiceContribution;
            
            $invoiceDetails[] = [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'pending_amount' => $invoice->pending_amount,
                'contribution_to_credit_balance' => $invoiceContribution
            ];
        }
        
        \Log::info('Credit Balance Calculation (Member Model)', [
            'member_id' => $this->id,
            'member_code' => $this->member_code,
            'invoice_count' => $invoices->count(),
            'invoice_details' => $invoiceDetails,
            'calculated_credit_balance' => (int)$creditBalance
        ]);
        
        return (int)$creditBalance;
    }

    /**
     * Sync credit balance to database
     * Call this after payment operations to keep balance updated
     * 
     * @return void
     */
    public function syncCreditBalance()
    {
        $calculatedBalance = $this->calculateCreditBalance();
        $this->credit_balance = $calculatedBalance;
        $this->save();
    }

    /**
     * Get available credit (positive balance)
     * For display on renewal page
     * Always calculated on-the-fly (no database dependency)
     * 
     * @return int
     */
    public function getAvailableCredit()
    {
        // Always calculate on-the-fly, never use stored value
        $balance = (int)$this->calculateCreditBalance();
        return max(0, $balance); // Return 0 if negative
    }

    /**
     * Get due amount (negative balance)
     * For display on renewal page
     * Always calculated on-the-fly (no database dependency)
     * 
     * @return int
     */
    public function getDueAmount()
    {
        // Always calculate on-the-fly, never use stored value
        $balance = (int)$this->calculateCreditBalance();
        return abs(min(0, $balance)); // Return positive value of negative balance
    }

    /**
     * Check if member has available credit
     * Always calculated on-the-fly (no database dependency)
     * 
     * @return bool
     */
    public function hasCredit()
    {
        // Always calculate on-the-fly, never use stored value
        $balance = $this->calculateCreditBalance();
        return $balance > 0;
    }

    /**
     * Check if member has outstanding due amount
     * Always calculated on-the-fly (no database dependency)
     * 
     * @return bool
     */
    public function hasDueAmount()
    {
        // Always calculate on-the-fly, never use stored value
        $balance = $this->calculateCreditBalance();
        return $balance < 0;
    }

    /**
     * Get outstanding invoices ordered by creation date (oldest first)
     * Returns invoices with Unpaid or Partial status
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOutstandingInvoices()
    {
        return $this->invoices()
            ->whereIn('status', [\constPaymentStatus::Unpaid, \constPaymentStatus::Partial])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Get overpaid invoices (for reference, though credit is now tracked in credit_balance)
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOverpaidInvoices()
    {
        return $this->invoices()
            ->where('status', \constPaymentStatus::Overpaid)
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
