<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use JavaScript;
use App\Invoice;
use App\Plan;
use Carbon\Carbon;
use App\ChequeDetail;
use App\Subscription;
use App\InvoiceDetail;
use App\PaymentDetail;
use Illuminate\Http\Request;

class InvoicesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Check if current user can access an invoice based on member timings
     * Admin users with 'manage-gymie' permission can access all invoices
     * Other users can only access invoices for members with matching timings
     * 
     * @param Invoice $invoice
     * @return bool
     */
    private function canAccessInvoice($invoice)
    {
        $user = Auth::user();
        
        // If user is not authenticated, deny access
        if (!$user) {
            return false;
        }
        
        // Admin users with manage-gymie permission can access all invoices
        if ($user->can('manage-gymie')) {
            return true;
        }
        
        // Load member relationship if not already loaded
        if (!$invoice->relationLoaded('member')) {
            $invoice->load('member');
        }
        
        $member = $invoice->member;
        
        if (!$member) {
            return false;
        }
        
        // If user has no timings set, they can't access any invoices (except admin)
        if (empty($user->timings)) {
            return false;
        }
        
        // If member has no timings set, only admin can access
        if (empty($member->timings)) {
            return false;
        }
        
        // User can only access invoices for members with matching timings
        return $user->timings === $member->timings;
    }

    /**
     * Apply timings filter to invoice query
     * Admin users with 'manage-gymie' permission see all invoices
     * Other users only see invoices for members with matching timings
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyTimingsFilter($query)
    {
        $user = Auth::user();
        
        // If user is not authenticated, return empty result
        if (!$user) {
            return $query->whereRaw('1=0');
        }
        
        // Admin users with manage-gymie permission see all invoices
        if ($user->can('manage-gymie')) {
            return $query;
        }
        
        // If user has no timings set, they see no invoices (except admin)
        if (empty($user->timings)) {
            return $query->whereRaw('1=0'); // Return empty result
        }
        
        // Filter invoices by joining with members and matching timings
        return $query->whereHas('member', function($q) use ($user) {
            $q->where('mst_members.timings', $user->timings);
        });
    }

    public function index(Request $request)
    {
        $query = Invoice::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end);
        
        // Apply timings filter (users can only see invoices for members with their timings, admin sees all)
        $query = $this->applyTimingsFilter($query);
        
        // Apply status filter if provided and sort_field is 'status'
        if ($request->has('status_filter') && $request->status_filter !== '' && $request->sort_field === 'status') {
            $query->where('trn_invoice.status', (int)$request->status_filter);
        }
        
        $invoices = $query->search('"'.$request->input('search').'"')->paginate(10);
        $count = $invoices->total();

        if (! $request->has('drp_start') or ! $request->has('drp_end')) {
            $drp_placeholder = 'Select daterange filter';
        } else {
            $drp_placeholder = $request->drp_start.' - '.$request->drp_end;
        }

        $request->flash();

        return view('invoices.index', compact('invoices', 'count', 'drp_placeholder'));
    }

    public function unpaid(Request $request)
    {
        $query = Invoice::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end);
        
        // Apply timings filter (users can only see invoices for members with their timings, admin sees all)
        $query = $this->applyTimingsFilter($query);
        
        $invoices = $query->search('"'.$request->input('search').'"')->where('trn_invoice.status', 0)->paginate(10);
        $invoicesTotal = Invoice::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end);
        $invoicesTotal = $this->applyTimingsFilter($invoicesTotal);
        $invoicesTotal = $invoicesTotal->search('"'.$request->input('search').'"')->where('trn_invoice.status', 0)->get();
        $count = $invoicesTotal->count();

        if (! $request->has('drp_start') or ! $request->has('drp_end')) {
            $drp_placeholder = 'Select daterange filter';
        } else {
            $drp_placeholder = $request->drp_start.' - '.$request->drp_end;
        }

        $request->flash();

        return view('invoices.unpaid', compact('invoices', 'count', 'drp_placeholder'));
    }

    public function paid(Request $request)
    {
        $query = Invoice::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end);
        
        // Apply timings filter (users can only see invoices for members with their timings, admin sees all)
        $query = $this->applyTimingsFilter($query);
        
        $invoices = $query->search('"'.$request->input('search').'"')->where('trn_invoice.status', 1)->paginate(10);
        $invoicesTotal = Invoice::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end);
        $invoicesTotal = $this->applyTimingsFilter($invoicesTotal);
        $invoicesTotal = $invoicesTotal->search('"'.$request->input('search').'"')->where('trn_invoice.status', 1)->get();
        $count = $invoicesTotal->count();

        if (! $request->has('drp_start') or ! $request->has('drp_end')) {
            $drp_placeholder = 'Select daterange filter';
        } else {
            $drp_placeholder = $request->drp_start.' - '.$request->drp_end;
        }

        $request->flash();

        return view('invoices.paid', compact('invoices', 'count', 'drp_placeholder'));
    }

    public function partial(Request $request)
    {
        $query = Invoice::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end);
        
        // Apply timings filter (users can only see invoices for members with their timings, admin sees all)
        $query = $this->applyTimingsFilter($query);
        
        $invoices = $query->search('"'.$request->input('search').'"')->where('trn_invoice.status', 2)->paginate(10);
        $invoicesTotal = Invoice::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end);
        $invoicesTotal = $this->applyTimingsFilter($invoicesTotal);
        $invoicesTotal = $invoicesTotal->search('"'.$request->input('search').'"')->where('trn_invoice.status', 2)->get();
        $count = $invoicesTotal->count();

        if (! $request->has('drp_start') or ! $request->has('drp_end')) {
            $drp_placeholder = 'Select daterange filter';
        } else {
            $drp_placeholder = $request->drp_start.' - '.$request->drp_end;
        }

        $request->flash();

        return view('invoices.partial', compact('invoices', 'count', 'drp_placeholder'));
    }

    public function overpaid(Request $request)
    {
        $query = Invoice::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end);
        
        // Apply timings filter (users can only see invoices for members with their timings, admin sees all)
        $query = $this->applyTimingsFilter($query);
        
        $invoices = $query->search('"'.$request->input('search').'"')->where('trn_invoice.status', 3)->paginate(10);
        $invoicesTotal = Invoice::indexQuery($request->sort_field, $request->sort_direction, $request->drp_start, $request->drp_end);
        $invoicesTotal = $this->applyTimingsFilter($invoicesTotal);
        $invoicesTotal = $invoicesTotal->search('"'.$request->input('search').'"')->where('trn_invoice.status', 3)->get();
        $count = $invoicesTotal->count();

        if (! $request->has('drp_start') or ! $request->has('drp_end')) {
            $drp_placeholder = 'Select daterange filter';
        } else {
            $drp_placeholder = $request->drp_start.' - '.$request->drp_end;
        }

        $request->flash();

        return view('invoices.overpaid', compact('invoices', 'count', 'drp_placeholder'));
    }

    public function show($id)
    {
        $invoice = Invoice::findOrFail($id);
        
        // Check if user can access this invoice
        if (!$this->canAccessInvoice($invoice)) {
            flash()->error('You do not have permission to view this invoice');
            return redirect(action('InvoicesController@index'));
        }
        
        $settings = \Utilities::getSettings();

        return view('invoices.show', compact('invoice', 'settings'));
    }

    public function createPayment($id, Request $request)
    {
        $invoice = Invoice::findOrFail($id);
        
        // Check if user can access this invoice
        if (!$this->canAccessInvoice($invoice)) {
            flash()->error('You do not have permission to create a payment for this invoice');
            return redirect(action('InvoicesController@index'));
        }

        return view('payments.create', compact('invoice'));
    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {
            $invoice = Invoice::findOrFail($id);
            
            // Check if user can access this invoice
            if (!$this->canAccessInvoice($invoice)) {
                DB::rollback();
                flash()->error('You do not have permission to delete this invoice');
                return back();
            }
            
            InvoiceDetail::where('invoice_id', $id)->delete();
            $payment_details = PaymentDetail::where('invoice_id', $id)->get();

            foreach ($payment_details as $payment_detail) {
                ChequeDetail::where('payment_id', $payment_detail->id)->delete();
                $payment_detail->delete();
            }

            Subscription::where('invoice_id', $id)->delete();
            Invoice::destroy($id);

            DB::commit();

            return back();
        } catch (\Exception $e) {
            DB::rollback();

            return back();
        }
    }

    public function discount($id)
    {
        $invoice = Invoice::findOrFail($id);
        
        // Check if user can access this invoice
        if (!$this->canAccessInvoice($invoice)) {
            flash()->error('You do not have permission to add a discount to this invoice');
            return redirect(action('InvoicesController@index'));
        }

        JavaScript::put([
            'gymieToday' => Carbon::today()->format('Y-m-d'),
            'servicesCount' => Plan::count(),
        ]);

        return view('invoices.discount', compact('invoice'));
    }

    public function applyDiscount($id, Request $request)
    {
        DB::beginTransaction();

        try {
            $invoice = Invoice::findOrFail($id);
            
            // Check if user can access this invoice
            if (!$this->canAccessInvoice($invoice)) {
                DB::rollback();
                flash()->error('You do not have permission to apply a discount to this invoice');
                return back();
            }
            
            $invoice_total = ($request->admission_amount ?: 0)
                            + ($request->subscription_amount ?: 0)
                            + ($request->additional_fees ?: 0)
                            - ($request->discount_amount ?: 0);
            $already_paid = PaymentDetail::leftJoin('trn_cheque_details', 'trn_payment_details.id', '=', 'trn_cheque_details.payment_id')
                                       ->whereRaw("trn_payment_details.invoice_id = $id AND (trn_cheque_details.`status` = 2 or trn_cheque_details.`status` IS NULL)")
                                       ->sum('trn_payment_details.payment_amount');

            $pending = $invoice_total - $already_paid;

            $status = \Utilities::setInvoiceStatus($pending, $invoice_total);

            Invoice::where('id', $id)->update(['invoice_number'=> $request->invoice_number,
                                         'total'=> $invoice_total,
                                         'status'=> $status,
                                         'pending_amount'=> $pending,
                                         'discount_amount'=> $request->discount_amount,
                                         'discount_note'=> $request->discount_note,
                                         
                                         'additional_fees'=> $request->additional_fees,
                                         'note'=>' ', ]);

            DB::commit();
            flash()->success('Discount was successfully updated');

            return redirect(action('InvoicesController@show', ['id' => $id]));
        } catch (\Exception $e) {
            DB::rollback();
            flash()->error('Error while updating discount. Please try again');

            return back();
        }
    }
}
