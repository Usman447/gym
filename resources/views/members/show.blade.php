@extends('app')

@section('content')
    <?php use Carbon\Carbon; ?>

    <div class="rightside bg-grey-100">
        <div class="page-head bg-grey-100 padding-top-15 no-padding-bottom">
            @include('flash::message')
        </div>
        <div class="container-fluid">

            <div class="row"><!-- Main row -->
                <div class="col-md-12"><!-- Main Col -->
                    <div class="panel no-border ">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Member Detail</div>
                            <div class="pull-right no-margin">
                                @permission(['manage-gymie','manage-members','edit-member'])
                                <a class="btn btn-primary" href="{{ action('MembersController@edit',['id' => $member->id]) }}">
                                    <span>Edit</span>
                                </a>
                                @endpermission

                                @permission(['manage-gymie','manage-members','delete-member'])
                                <button class="btn btn-danger" data-toggle="modal" data-target="#deleteModal-{{$member->id}}" data-id="{{$member->id}}">
                                    <span>Delete</span>
                                </button>
                                @endpermission

                                <!-- Modal -->
                                <div id="deleteModal-{{$member->id}}" class="modal fade" role="dialog">
                                    <div class="modal-dialog">

                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title">Confirm</h4>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete it?</p>
                                            </div>
                                            <div class="modal-footer">
                                                {!! Form::Open(['action'=>['MembersController@archive',$member->id],'method' => 'POST','id'=>'archiveform-'.$member->id]) !!}
                                                <input type="submit" class="btn btn-danger" value="Yes" id="btn-{{ $member->id }}"/>
                                                <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
                                                {!! Form::Close() !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel-body">
                            <div class="row">                <!--Main row start-->
                                <div class="col-sm-8">
                                    <div class="row">
                                        <!-- <div class="col-sm-4">
                                            Spacer
                                            <div class="row visible-md visible-lg">
                                                <div class="col-sm-4">
                                                    <label>&nbsp;</label>
                                                </div>
                                            </div>
                                            <?php
                                            $images = $member->getMedia('profile');
                                            $profileImage = ($images->isEmpty() ? 'https://placeholdit.imgix.net/~text?txtsize=22&txt=NA&w=200&h=180' : url($images[0]->getUrl()));
                                            ?>
                                            <img class="AutoFitResponsive" src="{{ $profileImage }}"/>
                                        </div> -->


                                        <div class="col-sm-12">            <!-- Outer Row Start -->

                                            <!-- Spacer -->
                                            <div class="row visible-md visible-lg">
                                                <div class="col-sm-4">
                                                    <label>&nbsp;</label>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <label>Name</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <span class="show-data">{{$member->name}}</span>
                                                </div>
                                            </div>

                                            <hr class="margin-top-0 margin-bottom-10">


                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <label>Age</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <span class="show-data">{{$member->age}}</span>
                                                </div>
                                            </div>
                                            <hr class="margin-top-0 margin-bottom-10">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <label>Gender</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <span class="show-data">{{Utilities::getGender($member->gender)}}</span>
                                                </div>
                                            </div>
                                            <hr class="margin-top-0 margin-bottom-10">

                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <label>Contact Number</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <span class="show-data">{{$member->contact}}</span>
                                                </div>
                                            </div>
                                            <hr class="margin-top-0 margin-bottom-10">

                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <label>Timings</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <span class="show-data">{{$member->timings ?: '-'}}</span>
                                                </div>
                                            </div>

                                            <!-- <hr class="margin-top-0 margin-bottom-10">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <label>Height</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <span class="show-data">{{$member->height_ft}} ft {{$member->height_in}} in</span>
                                                </div>
                                            </div>
                                            <hr class="margin-top-0 margin-bottom-10">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <label>Weight</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <span class="show-data">{{$member->weight_kg}} kg</span>
                                                </div>
                                            </div>
                                            <hr class="margin-top-0 margin-bottom-10">

                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <label>Health Issues</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <span class="show-data">{{$member->health_issues}}</span>
                                                </div>
                                            </div> -->

                                            <hr class="margin-top-0 margin-bottom-10">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <label>Address</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <span class="show-data">{{$member->address}}</span>
                                                </div>
                                            </div>
                                            <hr class="margin-top-0 margin-bottom-10">

                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <label>OPF Residence</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <span class="show-data">{{ $member->opf_residence ? 'Yes' : 'No' }}</span>
                                                </div>
                                            </div>

                                            @permission('manage-gymie')
                                                @if($member->created_by_user_name || $member->updated_by_user_name)
                                                <hr class="margin-top-0 margin-bottom-10">
                                                
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <label><strong>Entry Tracking</strong></label>
                                                    </div>
                                                </div>
                                                
                                                @if($member->created_by_user_name)
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <label>Created By</label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <span class="show-data">
                                                            {{ $member->created_by_user_name }}
                                                            @if($member->created_by_user_email)
                                                                <br><small class="text-muted">{{ $member->created_by_user_email }}</small>
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                                <hr class="margin-top-0 margin-bottom-10">
                                                @endif
                                                
                                                @if($member->updated_by_user_name && ($member->updated_by_user_name != $member->created_by_user_name || $member->updated_by_user_email != $member->created_by_user_email))
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <label>Last Updated By</label>
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <span class="show-data">
                                                            {{ $member->updated_by_user_name }}
                                                            @if($member->updated_by_user_email)
                                                                <br><small class="text-muted">{{ $member->updated_by_user_email }}</small>
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                                <hr class="margin-top-0 margin-bottom-10">
                                                @endif
                                                @endif
                                            @endpermission

                                        </div>  <!-- End of outer Row -->
                                    </div>
                                </div>   <!-- End of Outer Column -->

                                <div class="col-sm-4">
                                    <div class="row"><!-- Main row -->
                                        <div class="col-md-12"><!-- Main Col -->
                                            <div class="panel bg-grey-50">
                                                <div class="panel-title bg-transparent">
                                                    <div class="panel-head"><strong><span class="fa-stack">
							  <i class="fa fa-circle-thin fa-stack-2x"></i>
							  <i class="fa fa-ellipsis-h fa-stack-1x"></i>
							</span> Additional Details</strong></div>
                                                </div>
                                                <div class="panel-body">

                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <label>Member Code</label>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <span class="show-data">{{$member->member_code}}</span>
                                                        </div>
                                                    </div>

                                                    <hr class="margin-top-0 margin-bottom-10">

                                                    <div class="row">
                                                        <?php
                                                        $subscriptions = $member->subscriptions;
                                                        $plansArray = array();
                                                        foreach ($subscriptions as $subscription) {
                                                            $plansArray[] = $subscription->plan->plan_name;
                                                        }
                                                        ?>
                                                        <div class="col-sm-4">
                                                            <label>Plan name</label>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <span class="show-data">{{implode(",",$plansArray)}}</span>
                                                        </div>
                                                    </div>
                                                    <hr class="margin-top-0 margin-bottom-10">
                                                    
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <label>Member Since</label>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <span class="show-data">{{$member->created_at->toFormattedDateString()}}</span>
                                                        </div>
                                                    </div>
                                                    <hr class="margin-top-0 margin-bottom-10">

                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <label>Status</label>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <span class="show-data">{{Utilities::getStatusValue ($member->status)}}</span>
                                                        </div>
                                                    </div>
                                                    <hr class="margin-top-0 margin-bottom-10">

                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <label>Biometric Device</label>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <span class="show-data">
                                                                @if($member->in_biometric_device)
                                                                    <span class="text-success">
                                                                        <i class="fa fa-check-circle"></i> Member added to device
                                                                    </span>
                                                                @else
                                                                    <span class="text-danger">
                                                                        <i class="fa fa-times-circle"></i> Not added to device
                                                                    </span>
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <hr class="margin-top-0 margin-bottom-10">

                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <label>Fingerprint</label>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <span class="show-data">
                                                                @if($member->has_fingerprint)
                                                                    <span class="text-success">
                                                                        <i class="fa fa-fingerprint"></i> Fingerprint Configured
                                                                    </span>
                                                                @else
                                                                    <span class="text-muted">
                                                                        <i class="fa fa-fingerprint"></i> Fingerprint Not Configured
                                                                    </span>
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <hr class="margin-top-0 margin-bottom-10">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>   <!-- End Of Main Row -->
                        </div>
                    </div>
                </div>
            </div>

            <!--######################### Subscription history for the member ################################# -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel no-border ">
                        <div class="panel-title">
                            <div class="panel-head font-size-20">Subscription history for the member</div>
                        </div>
                        <div class="panel-body">
                            <table id="_payment" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Invoice Number</th>
                                    <th>Plan Name</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th>Payment Status</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($member->subscriptions->sortByDesc('created_at') as $subscription)
                                    <tr>
                                        <td>
                                            <a href="{{ action('InvoicesController@show',['id' => $subscription->invoice_id]) }}">{{ $subscription->invoice->invoice_number }}</a>
                                        </td>
                                        <td>{{ $subscription->plan->plan_name }}</td>
                                        <td>{{ $subscription->start_date->format('d-m-Y') }}</td>
                                        <td>{{ $subscription->end_date->format('d-m-Y') }}</td>
                                        <td>
                                            <span class="{{ Utilities::getSubscriptionLabel ($subscription->status) }}">{{ Utilities::getSubscriptionStatus ($subscription->status) }}</span>
                                        </td>
                                        <td>{{ Utilities::getInvoiceStatus ($subscription->invoice->status) }}</td>
                                        <!-- <td>
                                            @if($subscription->status == \constSubscription::onGoing)
                                                @permission(['manage-gymie','manage-subscriptions','cancel-subscription'])
                                                <button class="btn btn-xs btn-danger cancel-subscription"
                                                        data-cancel-url="{{ action('SubscriptionsController@cancelSubscription', $subscription->id) }}"
                                                        data-record-id="{{$subscription->id}}">
                                                    Cancel
                                                </button>
                                                @endpermission
                                            @endif
                                        </td> -->
                                        @if($subscription->status == \constSubscription::onGoing)
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-info">Actions</button>
                                                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                        <span class="caret"></span>
                                                        <span class="sr-only">Toggle Dropdown</span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        @permission(['manage-gymie','manage-subscriptions','renew-subscription'])
                                                        <li>
                                                            <a href="{{ action('SubscriptionsController@renew',['id' => $subscription->invoice_id]) }}">
                                                                Renew subscription
                                                            </a>
                                                        </li>
                                                        @endpermission
                                                        @permission(['manage-gymie','manage-subscriptions','change-subscription'])
                                                        <li>
                                                            <a href="{{ action('SubscriptionsController@change',['id' => $subscription->id]) }}">
                                                                Upgrade/Downgrade
                                                            </a>
                                                        </li>
                                                        @endpermission
                                                        @if($subscription->invoice->discount_amount > 0)
                                                            @permission(['manage-gymie','manage-invoices','add-discount'])
                                                            <li>
                                                                <a href="{{ action('InvoicesController@discount',['id' => $subscription->invoice->id]) }}">
                                                                    Edit Discount
                                                                </a>
                                                            </li>
                                                            @endpermission

                                                        @elseif($subscription->invoice->discount_amount == 0)

                                                            @permission(['manage-gymie','manage-invoices','add-discount'])
                                                            <li>
                                                                <a href="{{ action('InvoicesController@discount',['id' => $subscription->invoice->id]) }}">
                                                                    Add Discount
                                                                </a>
                                                            </li>
                                                            @endpermission
                                                        @endif
                                                        @permission(['manage-gymie','manage-payments','edit-payment'])
                                                        <?php
                                                            // Get the first payment detail for this invoice (if exists)
                                                            $firstPaymentDetail = $subscription->invoice->paymentDetails->last();
                                                        ?>
                                                        <li>
                                                            @if($firstPaymentDetail)
                                                                <a href="{{ action('PaymentsController@edit',['id' => $firstPaymentDetail->id]) }}">
                                                                    Edit Payment
                                                                </a>
                                                            @else
                                                                <a href="{{ action('InvoicesController@createPayment',['id' => $subscription->invoice->id]) }}">
                                                                    Add Payment
                                                                </a>
                                                            @endif
                                                        </li>
                                                        @endpermission
                                                        @permission(['manage-gymie','manage-subscriptions','cancel-subscription'])
                                                        <li>
                                                            <a href="#" class="cancel-subscription"
                                                                data-cancel-url="{{ action('SubscriptionsController@cancelSubscription', $subscription->id) }}"
                                                                data-record-id="{{$subscription->id}}">
                                                                Cancel subscription
                                                            </a>
                                                        </li>
                                                        @endpermission
                                                    </ul>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop
@section('footer_script_init')
    <script type="text/javascript">
        $(document).ready(function () {
            gymie.cancelsubscription();
        });
    </script>
@stop