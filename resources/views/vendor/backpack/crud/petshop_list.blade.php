@extends(backpack_view('blank'))

@php
  $defaultBreadcrumbs = [
    trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
    $crud->entity_name_plural => url($crud->route),
    trans('backpack::crud.list') => false,
  ];

  // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
  $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@push('crud_fields_styles')
    <style>
        .nav-tabs-custom {
            box-shadow: none;
        }
        .nav-tabs-custom > .nav-tabs.nav-stacked > li {
            margin-right: 0;
        }

        .tab-pane .form-group h1:first-child,
        .tab-pane .form-group h2:first-child,
        .tab-pane .form-group h3:first-child {
            margin-top: 0;
        }
    </style>
@endpush

@section('header')
  <div class="container-fluid">
    <h2>
      <span class="text-capitalize">Petshop List</span>
    </h2>
  </div>
@endsection

@section('content')
  <!-- Default box -->
  <div class="row">

    <!-- THE ACTUAL CONTENT -->
    <div class="{{ $crud->getListContentClass() }}">

        <div class="row mb-0">
          <div class="col-sm-6">
            {{-- @if ( $crud->buttons()->where('stack', 'top')->count() ||  $crud->exportButtons())
              <div class="hidden-print {{ $crud->hasAccess('create')?'with-border':'' }}">

                @include('crud::inc.button_stack', ['stack' => 'top'])

              </div>
            @endif --}}
          </div>
          <div class="col-sm-6">
            <div id="datatable_search_stack" class="mt-sm-0 mt-2"></div>
          </div>
        </div>

        {{-- Backpack List Filters --}}
        @if ($crud->filtersEnabled())
          @include('crud::inc.filters_navbar')
        @endif

        <!-- TABS -->
        <!-- <div class="tab-container mb-2 mt-2">
            <div class="nav-tabs-custom" id="form_tabs"> -->
        <div class="tab-container mb-2 mt-2">
            <div class="nav-tabs-custom " id="form_tabs">
                <ul class="nav nav-tabs " role="tablist">
                    <li role="presentation" class="nav-item" id="open">
                        <a href="#tab_open" aria-controls="tab_open" role="tab" tab_name="open" data-toggle="tab" class="nav-link active">Open</a>
                    </li>
                    <li role="presentation" class="nav-item" id="accepted">
                        <a href="#tab_accepted" aria-controls="tab_accepted" role="tab" tab_name="accepted" data-toggle="tab" class="nav-link tab-acc-button">Accepted</a>
                    </li>
                    <li role="presentation" class="nav-item" id="rejected">
                        <a href="#tab_rejected" aria-controls="tab_rejected" role="tab" tab_name="rejected" data-toggle="tab" class="nav-link">Rejected</a>
                    </li>
                </ul>

                <!-- <div class="tab-content p-0 col-md-12"> -->
                <div class="tab-content p-0 ">
                    <div role="tabpanel" class="tab-pane active" id="tab_open">
                        <div class="row">
                            <div class="col-md-12 bold-labels" style="width:100%" >
                                <table id="open_withdraw_table" class="bg-white table table-striped table-hover rounded shadow-xs border-xs mt-2" style="width:100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Owner</th>
                                            <th>Petshop Name</th>
                                            <th>Company Name</th>
                                            <th>Phone Number</th>
                                            <th>Email</th>
                                            <!-- <th>Province</th>
                                            <th>City</th> -->
                                            <th>Address</th>
                                            <th>Requested At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                </table>
                                
                                @if ( $crud->buttons()->where('stack', 'bottom')->count() )
                                <div id="bottom_buttons" class="hidden-print">
                                    @include('crud::inc.button_stack', ['stack' => 'bottom'])

                                    <div id="datatable_button_stack" class="float-right text-right hidden-xs"></div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab_accepted">
                        <div class="row">
                            <div class="col-md-12 bold-labels" style="width:100%" >
                                <table id="accepted_withdraw_table" class="bg-white table table-striped border-xs mt-2" style="width:100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Owner</th>
                                            <th>Petshop Name</th>
                                            <th>Company Name</th>
                                            <th>Phone Number</th>
                                            <th>Email</th>
                                            <!-- <th>Province</th>
                                            <th>City</th> -->
                                            <th>Address</th>
                                            <th>Requested At</th>
                                            <th>Accepted By</th>
                                            <th>Accepted At</th>
                                            {{-- <th>Note</th> --}}
                                        </tr>
                                    </thead>
                                </table>
                                
                                @if ( $crud->buttons()->where('stack', 'bottom')->count() )
                                <div id="bottom_buttons" class="hidden-print">
                                    @include('crud::inc.button_stack', ['stack' => 'bottom'])
                                    <div id="datatable_button_stack" class="float-right text-right hidden-xs"></div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab_rejected">
                        <div class="row">
                            <div class="col-md-12 bold-labels" style="width:100%" >
                                <table id="rejected_withdraw_table" class="bg-white table table-striped border-xs mt-2" style="width:100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Owner</th>
                                            <th>Petshop Name</th>
                                            <th>Company Name</th>
                                            <th>Phone Number</th>
                                            <th>Email</th>
                                            <!-- <th>Province</th>
                                            <th>City</th> -->
                                            <th>Address</th>
                                            <th>Rejected By</th>
                                            <th>Rejected At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                                
                                @if ( $crud->buttons()->where('stack', 'bottom')->count() )
                                <div id="bottom_buttons" class="hidden-print">
                                    @include('crud::inc.button_stack', ['stack' => 'bottom'])

                                    <div id="datatable_button_stack" class="float-right text-right hidden-xs"></div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>

<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="rejectModalLabel">Petshop Reject - <span class="user"></span></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" class="acc_bank_id" id="acc_bank_id">
          <form>
            <input type="hidden" class="acc_bank_id" name="acc_bank_id">
            <div class="row form-group bold-labels">
                <div class="col-md-12">
                    <div class="row card" style="margin-left:2px; margin-right:2px;">
                            <table class="table table-sm" style="margin-bottom:0px;">
                                <tr>
                                    <td colspan="4"><label><b>Petshop Detail</b></label></td>
                                </tr>
                                <tr>
                                    <th width="20%">Petshop Name</th>
                                    <td><input type="text" class="remove_outline bank_name" name="bank_name" readonly></td>
                                    <th width="20%">Petshop Owner</th>
                                    <td><input type="text" class="remove_outline account_number" name="account_number" readonly></td>
                                </tr>
                                <tr>
                                    <th>Branch Name</th>
                                    <td><input type="text" class="remove_outline branch_name" name="branch_name" readonly></td>
                                    <th>Holder Name</th>
                                    <td><input type="text" class="remove_outline holder_name" name="holder_name" readonly></td>
                                </tr>
                            </table>
                    </div>
                </div>
                <label>Note:</label>
                <div class="col-md-12">
                    <textarea class="form-control" style="width:100%" id="note"></textarea>
                </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <a href="#" class="btn btn-secondary" data-dismiss="modal">{{ __('lang.close') }}</a>  
          <a href="#" id="reject" class="btn btn-danger">{{ __('lang.reject') }}</a>  
        </div>
      </div>
    </div>
</div>

<div class="modal fade" id="rejectedModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="rejectModalLabel">Detail Bank Account Reject - <span class="user"></span></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          {{-- <input type="hidden" class="acc_bank_id" id="acc_bank_id"> --}}
          {{-- <form> --}}
            {{-- <input type="hidden" class="acc_bank_id" name="acc_bank_id"> --}}
            <div class="row form-group bold-labels">
                <div class="col-md-12">
                    <div class="row card" style="margin-left:2px; margin-right:2px;">
                            <table class="table table-sm" style="margin-bottom:0px;">
                                {{-- <tr>
                                    <td colspan="4"><label><b>User's Bank Detail</b></label></td>
                                </tr> --}}
                                <tr>
                                    <th width="20%">Requested At</th>
                                    <td><input type="text" class="remove_outline requested_at" name="requested_at" readonly></td>
                                  
                                </tr>
                                <tr>
                                    <th>Note</th>
                                    <td><input type="text" class="remove_outline note" name="note" readonly></td>
                                   
                                </tr>
                            </table>
                    </div>
                </div>
                {{-- <label>Note:</label>
                <div class="col-md-12">
                    <textarea class="form-control" style="width:100%" id="note"></textarea>
                </div> --}}
            </div>
          {{-- </form> --}}
        </div>
        {{-- <div class="modal-footer">
          <a href="#" class="btn btn-secondary" data-dismiss="modal">Close</a>  
          <a href="#" id="reject" class="btn btn-danger">Reject</a>  
        </div> --}}
      </div>
    </div>
</div>


<div class="modal fade " id="acceptedModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="acceptedModalLabel">User Bank Account Accepted - <span class="user"></span></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="form-bank-change" action="{{backpack_url('accept_bank_change')}}" method="post" enctype="multipart/form-data">
            {!! csrf_field() !!}
          <input type="hidden" class="withdraw_id" id="acc_bank_id" name="acc_bank_id">
            <div class="row form-group bold-labels">
                <div class="col-md-12">
                    <div class="row card" style="margin-left:2px; margin-right:2px;">
                            <table class="table table-sm table-type-1" style="margin-bottom:0px;">
                                <tr>
                                    <td colspan="4"><label><b>User Bank Detail</b></label></td>
                                </tr>
                                <tr>
                                    <th width="20%">Bank Name</th>
                                    <td><input type="text" class="remove_outline bank_name" name="bank_name" readonly></td>
                                    <th width="20%">Bank Account</th>
                                    <td><input type="text" class="remove_outline account_number" name="account_number" readonly></td>
                                </tr>
                                <tr>
                                    <th>Branch Name</th>
                                    <td><input type="text" class="remove_outline branch_name" name="branch_name" readonly></td>
                                    <th>Holder Name</th>
                                    <td><input type="text" class="remove_outline holder_name" name="holder_name" readonly></td>
                                </tr>
                            </table>
                            <table class="table table-sm table-type-2" style="margin-bottom:0px;">
                                <tr>
                                    <td colspan="4"><label><b>Company Bank Detail</b></label></td>
                                </tr>
                                <tr>
                                    <th width="20%">Bank Name</th>
                                    <td><input type="text" class="remove_outline company_bank_name" name="bank_name" readonly></td>
                                    <th width="20%">Bank Account</th>
                                    <td><input type="text" class="remove_outline company_account_number" name="account_number" readonly></td>
                                </tr>
                                <tr>
                                    <th>Branch Name</th>
                                    <td><input type="text" class="remove_outline company_branch_name" name="branch_name" readonly></td>
                                    <th>Company Name</th>
                                    <td><input type="text" class="remove_outline company_name" name="holder_name" readonly></td>
                                </tr>
                            </table>
                    </div>
                </div>
               
                <div class="col-md-12">
                    <div class="row">

                        <div class="col-md-4 form-group">
                            <label class="control-label">Personal ID</label>
                            <img id="preview-personal_id" class="image-type-1" style="height: 150px; width: 100%">
                            <img id="preview-deed-of-company-img" class="image-type-2" style="height: 150px; width: 100%">
                        </div>
                
                        <div class="col-md-4 form-group">
                            <label>Bank Account</label>
                            <img id="preview-selfie-img" class="image-type-1" style="height: 150px; width: 100%">
                            <img id="preview-director-id" class="image-type-2" style="height: 150px; width: 100%">
                        </div>
                        
                        <div class="col-md-4 form-group">
                            <label>Branch</label>
                            <img id="preview-book-cover-img" class="image-type-1" style="height: 150px; width: 100%">
                            <img id="preview-director-img" class="image-type-2" style="height: 150px; width: 100%">
                        </div>

                    </div>
                </div>    
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <a href="#" class="btn btn-secondary" data-dismiss="modal">Close</a>  
          <a href="#" id="btn_accepted" class="btn btn-success">Accept</a>  
        </div>
      </div>
    </div>
</div>

@endsection

@section('after_styles')
  <style>
    .dataTables_filter input {
        margin-left: 0.5em !important;
        display: inline-block !important;
        width: auto !important;
        border-radius: 25px !important;
    }
    .table {
        width: 100% !important;
    } 
    .btn-bordered {
        border: 1px solid;
        padding: 2px 8px 0px 8px;
        margin-right: 8px;
        line-height: 26px;
    }
    .dataTables_scrollHeadInner{
        width:100% !important;
    }
    .dataTables_scrollHeadInner table{
        width:100% !important;
    }
    .swal-overlay .swal-modal .swal-text {
        text-align: center !important;
    }
    input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    /* display: none; <- Crashes Chrome on hover */
    -webkit-appearance: none;
    margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
}
input[type=number] {
    -moz-appearance:textfield; /* Firefox */
}

.swal-icon img{
  max-width: 90%;
  max-height: 90%;
}
.remove_outline{
    border: none;
    border-color: transparent;
}
  </style>
  <!-- DATA TABLES -->
  <link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-fixedheader-bs4/css/fixedHeader.bootstrap4.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}">

  <link rel="stylesheet" href="{{ asset('packages/backpack/crud/css/crud.css') }}">
  <link rel="stylesheet" href="{{ asset('packages/backpack/crud/css/form.css') }}">
  <link rel="stylesheet" href="{{ asset('packages/backpack/crud/css/list.css') }}">

  <!-- CRUD LIST CONTENT - crud_list_styles stack -->
  @stack('crud_list_styles')
@endsection

@section('after_scripts')
  @include('crud::inc.datatables_logic')
  <script>
    var url = "{{ backpack_url() }}";
    var front_url = "{{ url('/') }}";
    var hallo_url = " {{ env('HALLO247_URL') }}";
  </script>
  <script>
      $('#detail-img').hide();
   
      $(document).ready(function() {
        $('#fileup1').on('click', function() {
        $('#transfer_proof').click();
        return false;
        });
        $('#transfer_proof').change(function(){
            var img_proof = $('#transfer_proof').val();
            if(img_proof != '' && img_proof != null){
                let reader = new FileReader();
                reader.onload = (e) => { 
                    $('#image-src').val(e.target.result); 
                }
                reader.readAsDataURL(this.files[0]);
                $('#detail-img').show();
            }else{
                $('#detail-img').hide();
            }
            
        });
      });
    $(document).ready(function (e) {
        $('#detail-img').on('click', function(){
            swal({
                title: "",
                icon: $('#image-src').val(),
                buttons: {
                    delete:{
                        text: "Delete",
                        value: true,
                        visible: true,
                        className: "bg-danger",
                    },
                    cancel: {
                        text: "Close",
                        value: null,
                        visible: true,
                        className: "",
                        closeModal: true,
                    }
                }
            }).then((value) => {
                swal({
                    title: "{!! trans('lang.delete_image') !!}",
                    text: "",
                    icon: "warning",
                    buttons: {
                        cancel: {
                        text: "{!! trans('backpack::crud.cancel') !!}",
                        value: null,
                        visible: true,
                        className: "bg-secondary",
                        closeModal: true,
                        },
                        delete: {
                        text: "{!! trans('lang.delete') !!}",
                        value: true,
                        visible: true,
                        className: "bg-danger",
                        }
                    },
                }).then((value) => {
                    $('#transfer_proof').val("");
                    $('#transfer_proof').trigger("change");
                });
            });
        });
    });
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
    
    //REJECTED
    if (typeof rejectedEntry != 'function') {
        $("[data-button-type=rejected]").unbind('click');

        function rejectedEntry(button) {

            var button = $(button);
            var id = button.attr('data-id');
            var bank_name = button.attr('data-bank');
            var branch_name = button.attr('data-branch');
            var holder_name = button.attr('data-holder');
            var account = button.attr('data-account');
        //    console.log(id);

            $('#acc_bank_id').val(id);
            // $('span.user').html(user);
            // $('span.amount').html(amount);
            // $('span.date').html(date);
            // $('span.time').html(time);

            $('#rejectModal').appendTo('body').modal('show')
        }
    }
    
    //Confirmation Reject
    $('body').on('click', '#reject', function(){
        var id = $('.acc_bank_id').val();
        console.log(id);
        var note = $('#note').val();
        var validation_note = "{{trans('lang.please_input_the_note')}}"

        if(note != null && note != "") {
            // ask for confirmation before reject an item
            swal({
                title: "{!! trans('lang.reject_bank_confirmation_title') !!}",
                text: "{!! trans('lang.reject_bank_confirm') !!}",
                icon: "warning",
                buttons: {
                    cancel: {
                    text: "{!! trans('backpack::crud.cancel') !!}",
                    value: null,
                    visible: true,
                    className: "bg-secondary",
                    closeModal: true,
                    },
                    reject: {
                    text: "{!! trans('lang.reject') !!}",
                    value: true,
                    visible: true,
                    className: "bg-danger",
                    }
                },
            }).then((value) => {
                if (value) {
                    $.ajax({
                    url: url+'/reject_bank_change',
                    type: 'POST',
                    data: {id:id, note:note},
                    success: function(result) {
                        if (result.response) {
                            // Show a success notification bubble
                            new Noty({
                                    type: "success",
                                    text: "{!! '<strong>'.trans('lang.reject_bank_confirmation_title').'</strong><br>'.trans('lang.reject_withdraw_confirmation_message') !!}"
                                }).show();

                            // Hide the modal, if any
                            $('.modal').modal('hide');
                            
                            //reload
                            $( "#rejected" ).find('a').trigger( "click" );
                        } else {
                                if (result.message) {
                                swal({
                                    title: "",
                                    text: result.message,
                                    icon: "error",
                                    timer: 4000,
                                    buttons: true,
                                });
                                } else {// Show an error alert
                                swal({
                                    title: "{!! trans('lang.error') !!}",
                                    text: "{!! trans('lang.error_reject_bank_confirmation_message') !!}",
                                    icon: "error",
                                    timer: 4000,
                                    buttons: false,
                                });
                                }			          	  
                        }
                    },
                    error: function(result) {
                        // Show an alert with the result
                        swal({
                            title: "{!! trans('lang.error') !!}",
                            text: "{!! trans('lang.error_reject_bank_confirmation_message') !!}",
                            icon: "error",
                            timer: 4000,
                            buttons: false,
                        });
                    }
                });
                }
            });
        
        } else {
            new Noty({
                type: "error",
                text: validation_note,
            }).show();
        }
    })

    if (typeof rejectedEntryDetail != 'function') {
        $("[data-button-type=rejectedDetail]").unbind('click');

        function rejectedEntryDetail(button) {

            var button = $(button);
  
            var requested = button.attr('data-requested');
            var note = button.attr('data-note');
           

            $('.requested_at').val(requested);
            
            $('.note').val(note);
            

            $('#rejectedModal').appendTo('body').modal('show')
        }
    }

    //ACCEPTED
    if (typeof acceptedEntry != 'function') {
        $("[data-button-type=accepted]").unbind('click');

        function acceptedEntry(button) {

            var button = $(button);
            var id = button.attr('data-id');
            var user_id = button.attr('data-user');
            var type = button.attr('data-type');
            // var amount = button.attr('data-amount');
           
            //personal
            var bank_name = button.attr('data-bank');
            var branch_name = button.attr('data-branch');
            var holder_name = button.attr('data-holder');
            var account = button.attr('data-account');
            var personal_selfie_image = button.attr('data-personal_selfie_image');
            var savings_book_cover_image = button.attr('data-savings_book_cover_image');
            var personal_identity_image = button.attr('data-personal_identity_image');

            //company
            var company_name = button.attr('data-company_name');
            var company_account_number = button.attr('data-company_account_number');
            var company_bank_name = button.attr('data-company_bank_name');
            var company_branch_name = button.attr('data-company_branch_name');
            var deed_of_company_image = button.attr('data-deed_of_company_image');
            var director_identity_image = button.attr('data-director_identity_image');
            var director_image = button.attr('data-director_image');

            $('#acc_bank_id').val(id);
            
            //personal
            $('.bank_name').val(bank_name);
            $('.branch_name').val(branch_name);
            $('.holder_name').val(holder_name);
            $('.account_number').val(account);
            $('#preview-personal_id').attr('src', personal_identity_image);
            $('#preview-selfie-img').attr('src', personal_selfie_image);
            $('#preview-book-cover-img').attr('src', savings_book_cover_image);

            //personal
            $('.company_name').val(company_name);
            $('.company_account_number').val(company_account_number);
            $('.company_bank_name').val(company_bank_name);
            $('.company_branch_name').val(company_branch_name);
            $('#preview-deed-of-company-img').attr('src', deed_of_company_image);
            $('#preview-director-img').attr('src', director_identity_image);
            $('#preview-director-id').attr('src', director_image);

            if (type == 1) {
                $('.image-type-2').hide();
                $('.table-type-2').hide();

                $('.image-type-1').show();
                $('.table-type-1').show();
            }
            else {
                $('.image-type-1').hide();
                $('.table-type-1').hide();

                $('.image-type-2').show();
                $('.table-type-2').show();
            }

            $('#acceptedModal').appendTo('body').modal('show')
        }
    }
    
    //Confirmation Accepted
    $('body').on('click', '#btn_accepted', function(){
        // console.log( );
        var id = $('#acc_bank_id').val(); 
        // console.log(id);
        // $.ajax({
        //     url : url+'/accept_bank_change',
        //     type: 'POST',
        //     data: {
        //         id: id
        //     },
        //     success: function(data) {
        //     //   console.log(data);
        //     },
        //     error: function(response) {
        //         console.log(response);
        //     }
        // });
       
        var push = true;
        //   // ask for confirmation before accept an item
        if(push){
            swal({
                title: "{!! trans('lang.accept_bank_change_confirmation_title') !!}",
                text: "{!! trans('lang.accept_bank_confirm') !!}",
                icon: "warning",
                buttons: {
                    cancel: {
                    text: "{!! trans('backpack::crud.cancel') !!}",
                    value: null,
                    visible: true,
                    className: "bg-secondary",
                    closeModal: true,
                    },
                    reject: {
                    text: "{!! trans('lang.accept') !!}",
                    value: true,
                    visible: true,
                    className: "bg-success",
                    }
                },
            }).then((value) => {
                if (value) {
                    $.ajax({
                        url : url+'/accept_bank_change',
                        type: 'POST',
                        data: {
                            id: id
                        },
                        success: function(data) {
                            // console.log(data);
                            if (data) {
                                // Show a success notification bubble
                                new Noty({
                                        type: "success",
                                        text: "{!! '<strong>'.trans('lang.accept_bank_change_confirmation_title').'</strong><br>'.trans('lang.acc_bank_confirmation_message') !!}"
                                    }).show();

                                // Hide the modal, if any
                                $('.modal').modal('hide');
                                
                                //reload
                                $( ".tab-acc-button" ).trigger( "click" );
                            } else {
                                    if (result.message) {
                                        swal({
                                            title: "",
                                            text: result.message,
                                            icon: "error",
                                            timer: 4000,
                                            buttons: true,
                                        });
                                    } else {// Show an error alert
                                        swal({
                                            title: "{!! trans('lang.error') !!}",
                                            text: "{!! trans('lang.error_accept_bank_confirmation_message') !!}",
                                            icon: "error",
                                            timer: 4000,
                                            buttons: false,
                                        });
                                }			          	  
                            }
                        },
                        error: function(response) {
                            console.log(response);
                        }
                    });
                }
            });
        
        } else {
            new Noty({
                type: "error",
                text: error_message
            }).show();
        }
        
       
       
    })
    

   
    

    // make it so that the function above is run after each DataTable draw event
    // crud.addFunctionToDataTablesDrawEventQueue('deleteEntry');
    </script>
  <script src="{{ asset('packages/backpack/crud/js/account_bank.js') }}"></script>
  <script src="{{ asset('packages/backpack/crud/js/crud.js') }}"></script>
  <script src="{{ asset('packages/backpack/crud/js/form.js') }}"></script>
  <script src="{{ asset('packages/backpack/crud/js/list.js') }}"></script>

  <!-- CRUD LIST CONTENT - crud_list_scripts stack -->
  @stack('crud_list_scripts')
@endsection
