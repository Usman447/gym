<?php

namespace App\Http\Controllers;

use App\WhatsAppMessageHistory;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of WhatsApp message history.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = WhatsAppMessageHistory::indexQuery(
            $request->sort_field,
            $request->sort_direction,
            $request->drp_start,
            $request->drp_end
        );

        // Apply search if provided
        if ($request->has('search') && !empty($request->input('search'))) {
            $searchTerm = $request->input('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('mst_members.name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('mst_members.member_code', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('trn_whatsapp_message_history.phone_number', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('trn_whatsapp_message_history.message', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $messages = $query->paginate(10);

        // Get total count for the selected date range (without pagination)
        $totalCountQuery = WhatsAppMessageHistory::indexQuery(
            $request->sort_field,
            $request->sort_direction,
            $request->drp_start,
            $request->drp_end
        );

        if ($request->has('search') && !empty($request->input('search'))) {
            $searchTerm = $request->input('search');
            $totalCountQuery->where(function($q) use ($searchTerm) {
                $q->where('mst_members.name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('mst_members.member_code', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('trn_whatsapp_message_history.phone_number', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('trn_whatsapp_message_history.message', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $totalCount = $totalCountQuery->count();
        $count = $messages->total();

        if (! $request->has('drp_start') or ! $request->has('drp_end')) {
            $drp_placeholder = 'Select daterange filter';
        } else {
            $drp_placeholder = $request->drp_start.' - '.$request->drp_end;
        }

        $request->flash();

        return view('whatsapp.index', compact('messages', 'count', 'totalCount', 'drp_placeholder'));
    }
}

