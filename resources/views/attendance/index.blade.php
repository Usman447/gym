@extends('app')

@section('content')

    <div class="rightside bg-grey-100">
        <!-- BEGIN PAGE HEADING -->
        <div class="page-head bg-grey-100 padding-top-15 no-padding-bottom">
            @include('flash::message')
            
            <!-- TEMPORARY TEST BUTTON -->
            <!-- <div class="alert alert-warning" style="margin-bottom: 15px;">
                <strong>TEST MODE:</strong> 
                <button type="button" id="add-test-attendance-btn" class="btn btn-warning btn-sm">
                    <i class="fa fa-plus"></i> Add Test Attendance (Random Member)
                </button>
                <span id="test-attendance-status" style="margin-left: 10px;"></span>
                <small class="text-muted" style="display: block; margin-top: 5px;">This button adds a random member's attendance to test the sync script (no page reload)</small>
            </div> -->
            
            <h1 class="page-title no-line-height">Member Attendance
                <small>Track member check-ins at the gym</small>
            </h1>
            @permission(['manage-gymie','pagehead-stats'])
            <h1 class="font-size-30 text-right color-blue-grey-600 animated fadeInDown total-count pull-right">
                <span data-toggle="counter" data-start="0" data-from="0" data-to="{{ $totalVisits }}" data-speed="600" data-refresh-interval="10"></span>
                <small class="color-blue-grey-600 display-block margin-top-5 font-size-14">Total Today's Visits</small>
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

                                        <div class="col-sm-6">
                                            <label for="date">Sort By Date</label>
                                            <input type="text" name="date" value="{{ $selectedDate }}" class="form-control datepicker-default" id="date" placeholder="Select date">
                                        </div>

                                        <div class="col-xs-6">
                                            <label for="search">Keyword Search</label>
                                            <input value="{{ old('search') }}" name="search" id="search" type="text" class="form-control padding-right-35" placeholder="Search by member name or code...">
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

                            @if($attendances->count() == 0)
                                <h4 class="text-center padding-top-15">Sorry! No attendance records found</h4>
                            @else
                                <table id="attendance-table" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Member Name</th>
                                        <th>Subscription Status</th>
                                        <th>Check-in Time</th>
                                        <th>Total Today's Visits</th>
                                    </tr>
                                    </thead>
                                    <tbody id="attendance-tbody">
                                    @foreach ($attendances as $attendance)
                                        <tr data-attendance-id="{{ $attendance->id }}">
                                            <td>
                                                @if($attendance->member)
                                                    <a href="{{ url('members/' . $attendance->member_id . '/show') }}">
                                                        {{ $attendance->member->name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">Member Deleted (ID: {{ $attendance->member_id }})</span>
                                                @endif
                                            </td>
                                            <td>
                                                <?php
                                                    $latestSubscription = null;
                                                    if (isset($attendance->member) && $attendance->member) {
                                                        $subscriptions = $attendance->member->subscriptions;
                                                        if ($subscriptions && count($subscriptions) > 0) {
                                                            foreach ($subscriptions->sortByDesc('created_at') as $sub) {
                                                                if ($sub->status == \constSubscription::onGoing) {
                                                                    $latestSubscription = $sub;
                                                                    break;
                                                                }
                                                            }
                                                            if (!$latestSubscription) {
                                                                $latestSubscription = $subscriptions->sortByDesc('created_at')->first();
                                                            }
                                                        }
                                                    }
                                                ?>
                                                @if($latestSubscription)
                                                    <span class="{{ Utilities::getSubscriptionLabel($latestSubscription->status) }}">
                                                        {{ Utilities::getSubscriptionStatus($latestSubscription->status) }}
                                                    </span>
                                                @else
                                                    <span class="label label-default">No Subscription</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $attendance->check_in_time->format('d-m-Y H:i:s') }}
                                            </td>
                                            <td>
                                                {{ $attendance->today_visits }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="dataTables_info" id="clients-table_info" role="status" aria-live="polite">
                                            Showing {{ $attendances->firstItem() }} to {{ $attendances->lastItem() }} of {{ $attendances->total() }} entries
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="dataTables_paginate paging_bootstrap">
                                            {!! $attendances->appends(request()->query())->render() !!}
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@section('footer_scripts')
<script src="{{ URL::asset('assets/js/gymie.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {
        // Initialize datepicker with date range (last 30 days to today)
        var thirtyDaysAgo = new Date();
        thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 29); // 30 days ago (29 days back from today)
        var today = new Date();
        
        $('#date').datepicker({
            format: "yyyy-mm-dd",
            autoclose: true,
            todayHighlight: true,
            orientation: "bottom auto",
            startDate: thirtyDaysAgo,
            endDate: today,
            maxViewMode: 0 // Only allow day view
        });
        
        // Auto-submit form when date changes
        $('#date').on('changeDate', function(e) {
            // Auto-submit the form when date is selected from calendar
            $(this).closest('form').submit();
        });
        
        // Also handle regular change event (for manual input or programmatic changes)
        $('#date').on('change', function() {
            $(this).closest('form').submit();
        });
        
        // Test attendance button - AJAX without page reload
        $('#add-test-attendance-btn').on('click', function() {
            var $btn = $(this);
            var $status = $('#test-attendance-status');
            
            // Disable button and show loading
            $btn.prop('disabled', true);
            $status.html('<i class="fa fa-spinner fa-spin"></i> Adding...');
            
            $.ajax({
                url: '{{ url("attendance/add-test") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $status.html('<span class="text-success"><i class="fa fa-check"></i> ' + response.message + '</span>');
                        // Clear status after 3 seconds
                        setTimeout(function() {
                            $status.html('');
                        }, 3000);
                    } else {
                        $status.html('<span class="text-danger"><i class="fa fa-times"></i> ' + response.message + '</span>');
                    }
                },
                error: function(xhr) {
                    var message = 'Error adding test attendance';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    $status.html('<span class="text-danger"><i class="fa fa-times"></i> ' + message + '</span>');
                },
                complete: function() {
                    // Re-enable button
                    $btn.prop('disabled', false);
                }
            });
        });
        
        // Auto-refresh: Check for new attendance records every 5 seconds
        var autoRefreshInterval;
        var lastRecordId = {{ $attendances->count() > 0 ? $attendances->first()->id : 0 }};
        var selectedDate = '{{ $selectedDate }}';
        var isAutoRefreshEnabled = true;
        
        function checkForNewRecords() {
            if (!isAutoRefreshEnabled) return;
            
            $.ajax({
                url: '{{ url("attendance/new-records") }}',
                method: 'GET',
                data: {
                    date: selectedDate,
                    last_id: lastRecordId
                },
                success: function(response) {
                    if (response.success && response.new_records && response.new_records.length > 0) {
                        // Add new records to the top of the table
                        var $tbody = $('#attendance-tbody');
                        var $firstRow = $tbody.find('tr:first');
                        
                        $.each(response.new_records, function(index, record) {
                            // Create new row
                            var $newRow = $('<tr data-attendance-id="' + record.id + '" style="background-color: #d4edda; animation: fadeIn 0.5s;">');
                            
                            // Member name cell
                            var memberNameCell = '<td>';
                            if (record.member_id) {
                                memberNameCell += '<a href="{{ url("members") }}/' + record.member_id + '/show">' + 
                                    escapeHtml(record.member_name) + '</a>';
                            } else {
                                memberNameCell += '<span class="text-muted">Member Deleted (ID: ' + record.member_id + ')</span>';
                            }
                            memberNameCell += '</td>';
                            
                            // Subscription status cell
                            var subscriptionCell = '<td><span class="label ' + record.subscription_label + '">' + 
                                escapeHtml(record.subscription_status) + '</span></td>';
                            
                            // Check-in time cell
                            var timeCell = '<td>' + escapeHtml(record.check_in_time) + '</td>';
                            
                            // Today's visits cell
                            var visitsCell = '<td>' + record.today_visits + '</td>';
                            
                            $newRow.html(memberNameCell + subscriptionCell + timeCell + visitsCell);
                            
                            // Insert at the top
                            if ($firstRow.length) {
                                $firstRow.before($newRow);
                            } else {
                                $tbody.prepend($newRow);
                            }
                            
                            // Remove highlight after 3 seconds
                            setTimeout(function() {
                                $newRow.css('background-color', '');
                            }, 3000);
                            
                            // Update lastRecordId
                            if (record.id > lastRecordId) {
                                lastRecordId = record.id;
                            }
                        });
                        
                        // Update total visits counter
                        if (response.total_visits !== undefined) {
                            var $counterSpan = $('.total-count span');
                            var currentCount = parseInt($counterSpan.text().replace(/[^0-9]/g, '')) || 0;
                            var newCount = response.total_visits;
                            
                            // Update the data-to attribute for counter plugin
                            $counterSpan.attr('data-to', newCount);
                            
                            // Try to use counter plugin if available
                            if (typeof $counterSpan.counter === 'function') {
                                $counterSpan.counter();
                            } else if (typeof $counterSpan.data('counter') === 'function') {
                                $counterSpan.data('counter')();
                            } else {
                                // Manual counter animation if plugin not available
                                animateCounter($counterSpan, currentCount, newCount);
                            }
                        }
                    }
                },
                error: function(xhr) {
                    // Silently fail - don't show errors for background polling
                    console.log('Error checking for new records');
                }
            });
        }
        
        // Helper function to escape HTML
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
        // Animate counter from current value to new value
        function animateCounter($element, from, to) {
            if (from === to) return;
            
            var duration = 600; // milliseconds
            var startTime = Date.now();
            var difference = to - from;
            
            function updateCounter() {
                var elapsed = Date.now() - startTime;
                var progress = Math.min(elapsed / duration, 1);
                
                // Easing function for smooth animation
                var easeOutQuad = 1 - (1 - progress) * (1 - progress);
                var currentValue = Math.round(from + (difference * easeOutQuad));
                
                $element.text(currentValue);
                
                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                } else {
                    $element.text(to);
                }
            }
            
            updateCounter();
        }
        
        // Start auto-refresh (check every 5 seconds)
        autoRefreshInterval = setInterval(checkForNewRecords, 5000);
        
        // Stop auto-refresh when page is hidden (to save resources)
        $(document).on('visibilitychange', function() {
            if (document.hidden) {
                isAutoRefreshEnabled = false;
            } else {
                isAutoRefreshEnabled = true;
            }
        });
    });
</script>
<style>
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
@stop

