@extends('app')

@section('content')

    <div class="rightside bg-grey-100">
        <!-- BEGIN PAGE HEADING -->
        <div class="page-head bg-grey-100 padding-top-15 no-padding-bottom">
            @include('flash::message')
            <h1 class="page-title no-line-height">Invoices
                <small>Details of all gym invoices</small>
            </h1>
            @permission(['manage-gymie','pagehead-stats'])
            <h1 class="font-size-30 text-right color-blue-grey-600 animated fadeInDown total-count pull-right"><span data-toggle="counter" data-start="0"
                                                                                                                     data-from="0" data-to="{{ $count }}"
                                                                                                                     data-speed="600"
                                                                                                                     data-refresh-interval="10"></span>
                <small class="color-blue-grey-600 display-block margin-top-5 font-size-14">Total Invoices</small>
            </h1>
            @endpermission
        </div><!-- / PageHead -->

        <div class="container-fluid">
            <div class="row"><!-- Main row -->
                <div class="col-lg-12"><!-- Main Col -->
                    <div class="panel no-border ">
                        <div class="panel-title bg-blue-grey-50">
                            <div class="panel-head font-size-15">

                                <div class="row">
                                    <div class="col-sm-12 no-padding">
                                        <form method="GET">

                                        <div class="col-sm-3">

                                            <label for="invoice-daterangepicker">Date range</label>

                                            <div id="invoice-daterangepicker"
                                                 class="gymie-daterangepicker btn bg-grey-50 daterange-padding no-border color-grey-600 hidden-xs no-shadow">
                                                <i class="ion-calendar margin-right-10"></i>
                                                <span>{{$drp_placeholder}}</span>
                                                <i class="ion-ios-arrow-down margin-left-5"></i>
                                            </div>

                                            <input type="text" name="drp_start" value="" class="hidden" id="drp_start">
                                            <input type="text" name="drp_end" value="" class="hidden" id="drp_end">
                                        </div>

                                        <div class="col-sm-2">
                                            <label for="sort_field">Sort By</label>
                                            <select name="sort_field" class="form-control selectpicker show-tick show-menu-arrow" id="sort_field">
                                                <option value="status" {{ old('sort_field') == 'status' ? 'selected' : '' }}>Status</option>
                                                <option value="created_at" {{ old('sort_field') == 'created_at' ? 'selected' : '' }}>Date</option>
                                                <option value="invoice_number" {{ old('sort_field') == 'invoice_number' ? 'selected' : '' }}>Invoice number</option>
                                                <option value="member_name" {{ old('sort_field') == 'member_name' ? 'selected' : '' }}>Member name</option>
                                            </select>
                                        </div>

                                        <div class="col-sm-2" id="status_filter_container" style="display: none;">
                                            <label for="status_filter">Status Filter</label>
                                            <select name="status_filter" class="form-control selectpicker show-tick show-menu-arrow" id="status_filter">
                                                <option value="" {{ old('status_filter') == '' ? 'selected' : '' }}>All Statuses</option>
                                                <option value="0" {{ old('status_filter') == '0' ? 'selected' : '' }}>Unpaid</option>
                                                <option value="1" {{ old('status_filter') == '1' ? 'selected' : '' }}>Paid</option>
                                                <option value="2" {{ old('status_filter') == '2' ? 'selected' : '' }}>Partial</option>
                                                <option value="3" {{ old('status_filter') == '3' ? 'selected' : '' }}>Overpaid</option>
                                            </select>
                                        </div>

                                        <div class="col-sm-2">
                                            <label for="sort_direction">Order</label>
                                            <select name="sort_direction" class="form-control selectpicker show-tick show-menu-arrow" id="sort_direction">
                                                <option value="desc" {{ old('sort_direction') == 'desc' ? 'selected' : '' }}>Descending</option>
                                                <option value="asc" {{ old('sort_direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
                                            </select>
                                        </div>

                                        <div class="col-xs-2">
                                            <label for="search">Keyword</label>
                                            <input value="{{ old('search') }}" name="search" id="search" type="text" class="form-control padding-right-35"
                                                   placeholder="Search...">
                                        </div>

                                        <div class="col-xs-2">
                                            <label>&nbsp;</label> <br/>
                                            <button type="submit" class="btn btn-primary active no-border">GO</button>
                                        </div>

                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="panel-body bg-white">
                            @if($invoices->count() == 0)
                                <h4 class="text-center">Sorry! No records found</h4>
                            @else
                                <table id="invoices" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Invoice No</th>
                                        <th>Member Name</th>
                                        <th>Total Amount</th>
                                        <th>Pending</th>
                                        <th>Discount</th>
                                        <th>Status</th>
                                        <th>Created On</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($invoices as $invoice)
                                        <tr>
                                            <td><a href="{{ action('InvoicesController@show',['id' => $invoice->id]) }}">{{ $invoice->invoice_number}}</a></td>
                                            <td><a href="{{ action('MembersController@show',['id' => $invoice->member->id]) }}">{{ $invoice->member_name}}</a>
                                            </td>
                                            <td>{{ $invoice->total}}</td>
                                            <td>{{ $invoice->pending_amount}}</td>
                                            <td>{{ $invoice->discount_amount}}</td>
                                            <td>
                                                <span class="{{ Utilities::getInvoiceLabel ($invoice->status) }}">{{ Utilities::getInvoiceStatus ($invoice->status) }}</span>
                                            </td>
                                            <td>{{ $invoice->created_at->format('d-m-Y')}}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-info">Actions</button>
                                                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                        <span class="caret"></span>
                                                        <span class="sr-only">Toggle Dropdown</span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        <li>
                                                            @permission(['manage-gymie','manage-invoices','view-invoice'])
                                                            <a href="{{ action('InvoicesController@show',['id' => $invoice->id]) }}">
                                                                View invoice
                                                            </a>
                                                            @endpermission
                                                        </li>
                                                        @if($invoice->discount_amount > 0)

                                                            @permission(['manage-gymie','manage-invoices','add-discount'])
                                                            <li>
                                                                <a href="{{ action('InvoicesController@discount',['id' => $invoice->id]) }}">
                                                                    Edit Discount
                                                                </a>
                                                            </li>
                                                            @endpermission

                                                        @elseif($invoice->discount_amount == 0)

                                                            @permission(['manage-gymie','manage-invoices','add-discount'])
                                                            <li>
                                                                <a href="{{ action('InvoicesController@discount',['id' => $invoice->id]) }}">
                                                                    Add Discount
                                                                </a>
                                                            </li>
                                                            @endpermission

                                                        @endif
                                                        <li>
                                                            @permission(['manage-gymie','manage-invoices','delete-invoice'])
                                                            <a href="#" class="delete-record" data-delete-url="{{ url('invoices/'.$invoice->id.'/delete') }}"
                                                               data-record-id="{{$invoice->id}}">
                                                                Delete invoice
                                                            </a>
                                                            @endpermission
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>

                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="gymie_paging_info">
                                            Showing page {{ $invoices->currentPage() }} of {{ $invoices->lastPage() }}
                                        </div>
                                    </div>

                                    <div class="col-xs-6">
                                        <div class="gymie_paging pull-right">
                                            {!! str_replace('/?', '?', $invoices->appends(request()->only(['search', 'sort_field', 'sort_direction', 'status_filter', 'drp_start', 'drp_end']))->render()) !!}
                                        </div>
                                    </div>
                                </div>

                        </div><!-- / Panel-Body -->
                        @endif
                    </div><!-- / Panel-no-Border -->
                </div><!-- / Main-Col -->
            </div><!-- / Main-Row -->
        </div><!-- / Container -->
    </div><!-- / RightSide -->
@stop
@section('footer_script_init')
    <script type="text/javascript">
        $(document).ready(function () {
            gymie.deleterecord();
            
            // Show/hide status filter based on sort_field selection
            function toggleStatusFilter() {
                var sortField = $('#sort_field').val();
                if (sortField === 'status') {
                    $('#status_filter_container').show();
                    // Refresh selectpicker to ensure proper rendering
                    $('#status_filter').selectpicker('refresh');
                } else {
                    $('#status_filter_container').hide();
                    // Clear status filter when hidden
                    $('#status_filter').val('').selectpicker('refresh');
                }
            }
            
            // Check on page load
            toggleStatusFilter();
            
            // Check when sort_field changes
            $('#sort_field').on('change', function() {
                toggleStatusFilter();
            });
        });
    </script>
@stop 