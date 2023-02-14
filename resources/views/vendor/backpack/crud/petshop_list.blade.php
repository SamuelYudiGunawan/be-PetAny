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

@section('header')
    <div class="container-fluid">
        <h2>
            <span class="text-capitalize">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</span>
            {{-- <small id="datatable_info_stack">{!! $crud->getSubheading() ?? '' !!}</small> --}}
        </h2>
    </div>
@endsection

@section('content')
    {{-- Default box --}}
    <div class="row">

        {{-- THE ACTUAL CONTENT --}}
        <div class="{{ $crud->getListContentClass() }}">

            {{-- <div class="row mb-0">
                <div class="col-sm-6">
                    @if ($crud->buttons()->where('stack', 'top')->count() ||
    $crud->exportButtons())
                        <div class="d-print-none {{ $crud->hasAccess('create') ? 'with-border' : '' }}">

                            @include('crud::inc.button_stack', ['stack' => 'top'])

                        </div>
                    @endif
                </div>
                <div class="col-sm-6">
                    <div id="datatable_search_stack" class="mt-sm-0 mt-2 d-print-none"></div>
                </div>
            </div> --}}

            {{-- Backpack List Filters --}}
            @if ($crud->filtersEnabled())
                @include('crud::inc.filters_navbar')
            @endif

            <div class="tab-container mb-2 mt-2">
                <div class="nav-tabs-custom " id="form_tabs">
                    <ul class="nav nav-tabs " role="tablist">
                        <li role="presentation" class="nav-item" id="pending">
                            <a href="#tab_open" aria-controls="tab_open" role="tab" tab_name="open" data-toggle="tab"
                                class="nav-link active">Pending</a>
                        </li>
                        <li role="presentation" class="nav-item" id="accepted">
                            <a href="#tab_accepted" aria-controls="tab_accepted" role="tab" tab_name="accepted"
                                data-toggle="tab" class="nav-link">Accepted</a>
                        </li>
                        <li role="presentation" class="nav-item" id="rejected">
                            <a href="#tab_rejected" aria-controls="tab_rejected" role="tab" tab_name="rejected"
                                data-toggle="tab" class="nav-link">Rejected</a>
                        </li>
                    </ul>

                    <!-- <div class="tab-content p-0 col-md-12"> -->
                    <div class="tab-content p-0 ">
                        <div role="tabpanel" class="tab-pane active" id="tab_open">
                            <div class="row">
                                <div class="col-md-12 bold-labels" style="width:100%">
                                    <table id="pending_petshop_verification_table"
                                        class="bg-white table table-striped table-hover rounded shadow-xs border-xs mt-2"
                                        style="width:100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Owner</th>
                                                <th>Petshop Name</th>
                                                <th>Company Name</th>
                                                <th>Requested At</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                    </table>

                                    @if ($crud->buttons()->where('stack', 'bottom')->count())
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
                                <div class="col-md-12 bold-labels" style="width:100%">
                                    <table id="accepted_petshop_verification_table" class="bg-white table table-striped border-xs mt-2"
                                        style="width:100%" cellspacing="0">
                                        <thead>
                                          <tr>
                                                <th>Owner</th>
                                                <th>Petshop Name</th>
                                                <th>Company Name</th>
                                                <th>Requested At</th>
                                                <th>Actions</th>
                                          </tr>
                                        </thead>
                                    </table>

                                    @if ($crud->buttons()->where('stack', 'bottom')->count())
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
                                <div class="col-md-12 bold-labels" style="width:100%">
                                    <table id="rejected_petshop_verification_table" class="bg-white table table-striped border-xs mt-2"
                                        style="width:100%" cellspacing="0">
                                        <thead>
                                          <tr>
                                                <th>Owner</th>
                                                <th>Petshop Name</th>
                                                <th>Company Name</th>
                                                <th>Requested At</th>
                                                <th>Actions</th>
                                          </tr>
                                        </thead>
                                    </table>

                                    @if ($crud->buttons()->where('stack', 'bottom')->count())
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

            @if ($crud->buttons()->where('stack', 'bottom')->count())
                <div id="bottom_buttons" class="d-print-none text-center text-sm-left">
                    @include('crud::inc.button_stack', ['stack' => 'bottom'])

                    <div id="datatable_button_stack" class="float-right text-right hidden-xs"></div>
                </div>
            @endif

        </div>
    </div>
@endsection

{{-- Modal Detail --}}
<div class="modal fade" id="detail_modal" tabindex="-1" aria-labelledby="myModalLabel" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Modal title</h4>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <div class="container">
                        <div class="col-sm">
                            <div class="card">
                                <div class="card-header">
                                    <b>Petshop Info</b>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-2"><label>Name</label></div>
                                        <div class="col-md-10"><label id="detail_name"></label></div>

                                        <div class="col-md-2"><label>Email</label></div>
                                        <div class="col-md-10"><label id="detail_email"></label></div>

                                        <div class="col-md-2"><label>Phone</label></div>
                                        <div class="col-md-10"><label id="detail_phone"></label></div>
                                        <div class="col-md-2"><label>Company Name</label></div>
                                        <div class="col-md-10"><label id="detail_company"></label></div>

                                        <div class="col-md-2"><label>City</label></div>
                                        <div class="col-md-10"><label id="detail_city"></label></div>

                                        <div class="col-md-2"><label>Petshop Address</label></div>
                                        <div class="col-md-10"><label id="detail_address"></label></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row text-center mt-1">
                        <div class="col-md-6">
                            <div><b>Petshop Image</b></div>
                            <img id="detail_petshop_image" width="50%" height="auto">
                        </div>
                        <div class="col-md-6">
                            <div><b>Permit</b></div>
                            <img id="detail_permit" width="50%" height="auto">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                {{-- <button class="btn btn-primary" type="button">Save changes</button> --}}
            </div>
        </div>
        <!-- /.modal-content-->
    </div>
    <!-- /.modal-dialog-->
</div>

@section('after_styles')
    {{-- DATA TABLES --}}
    <link rel="stylesheet" type="text/css"
        href="{{ asset('packages/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('packages/datatables.net-fixedheader-bs4/css/fixedHeader.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('packages/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}">

    {{-- CRUD LIST CONTENT - crud_list_styles stack --}}
    @stack('crud_list_styles')
@endsection

@section('after_styles')
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
    const url = "{{ backpack_url() }}";
  </script>
    <script>
    dataTable('pending');
    dataTable('accepted');
    dataTable('rejected');

    $('#pending').click(function () { 
        $('#pending_petshop_verification_table').DataTable().destroy()
        dataTable('pending')
    });

    $('#accepted').click(function () { 
        $('#accepted_petshop_verification_table').DataTable().destroy()
        dataTable('accepted')
    });

    $('#rejected').click(function () { 
        $('#rejected_petshop_verification_table').DataTable().destroy()
        dataTable('rejected')
    });

    window.addEventListener('resize', function () {
        $($.fn.dataTable.tables(true)).DataTable()
           .columns.adjust();
    });


    function detailModal() {
          $('.btn_detail').on('click', function() {
            // User 
            $('#detail_name').html(': '+ $(this).data('owner'));
            $('#detail_email').html(': '+ $(this).data('petshop_email'));
            $('#detail_phone').html(': '+ $(this).data('phone_number'));

            // Doctor Info
            $('#detail_company').html(': '+ $(this).data('company_name'));
            $('#detail_city').html(': '+ $(this).data('city'));
            $('#detail_address').html(': '+ $(this).data('petshop_address'));

            // Images
            $('#detail_petshop_image').attr('src', $(this).data('petshop_image'));
            $('#detail_permit').attr('src', $(this).data('permit'));

            $('#detail_modal').modal('show');
          });
        }

    function dataTable(status){
        $(`#${status}_petshop_verification_table`).DataTable({
            // processing: true,
            // fixedHeader: true,
            ajax: {
                url: `${url}/get_petshop_list?`,
                type: 'POST',
                dataSrc: '',
                data: {
                    status: status
                } 
            },
            // scrollX: true,
            // responsive: true,
            // autoWidth: false,
            dom:
            "<'row hidden'<'col-sm-6 hidden-xs'i><'col-sm-6 hidden-print'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row mt-2 '<'col-sm-6 col-md-4'l><'col-sm-2 col-md-4 text-center'B><'col-sm-6 col-md-4 hidden-print'p>>",
            paging: true,
            // columnDefs: [
            //     { width: "15%", targets: 0 },//user
            //     { width: "10%", targets: 1 },//status
            //     { width: "10%", targets: 2 },//experience
            //     { width: "15%", targets: 3 },//datetime
            //     { width: "15%", targets: 4 },//actions
            // ],
            columns: [
                {data: 'user_id.name', name: 'name'},
                {data: 'petshop_name', name: 'petshop_name'},
                {data: 'company_name', name: 'company_name'},
                {data: 'created_at', render: function(data){

                    var date = new Date(data);
                    return date.toLocaleString();
                }},
                {data: 'id', render: function(data, type, row, meta) {
                    let html = '';

                    html += '<button class="btn_detail btn btn-inline-block btn-secondary mr-1" type="button" data-toggle="modal" onclick="detailModal()" data-id="'+data+'" data-owner="'+row.user_id.name+'" data-petshop_name="'+row.petshop_name+'" data-company_name="'+row.company_name+'"" data-petshop_email="'+row.petshop_email+'" data-phone_number="'+row.phone_number+'" data-province="'+row.province+'" data-city="'+row.city+'" data-petshop_address="'+row.petshop_address+'" data-route="'+url+'/accept_petshop" data-toggle="tooltip" data-placement="bottom" title="Accepted" data-button-type="accepted" data-petshop_image="' + row.petshop_image +'" data-permit="' + row.permit +'">Detail</button>'
                    
                    if(row.status == 'pending'){
                    html += '<button class="btn_accept btn btn-inline-block btn-success mr-1" type="button" data-id="'+data+'" data-user_id="'+row.user_id+'" onclick="acceptEntry()">Accept</button>'
                    html += '<button class="btn_reject btn btn-inline-block btn-danger mr-1" type="button" data-id="'+data+'" data-user_id="'+row.user_id+'" onclick="rejectEntry()">Reject</button>'
                    }
                    return html;
                }}
            ],
            
        });
    }
    function acceptEntry() {
            let id = $('.btn_accept').data('id');
            let userId = $('.btn_accept').data('user_id');
            swal({
                title: "Accept Petshop?",
                text: "Are you sure? This action can't be undone",
                icon: "warning",
                buttons: {
                    cancel: true,
                    confirm: {
                        text: "Accept",
                        className: "btn-success"
                    },
                },
            }).then((value) => {
                if (value) {
                    swal({
                        title: 'Processing...',
                        text: 'Please wait a moment',
                        buttons: false,
                        closeOnEsc: false,
                        closeOnClickOutside: false,
                    });
                    $.ajax({
                        url: url + `/accept_petshop/${id}`,
                        type: 'POST',
                        success: function(result) {
                            swal({
                                icon: 'success',
                                title: 'Accepted',
                                text: result.message,
                                buttons: false,
                                closeOnEsc: false,
                                closeOnClickOutside: false,
                                timer: 2000,
                            }).then(() => {
                              $( "#accepted" ).find('a').trigger( "click" );
                            });
                        },
                        error: function(result) {
                            swal({
                                title: "Error",
                                text: result.responseJSON.message,
                                icon: "error",
                                timer: 4000,
                                buttons: false,
                            });
                        }
                    });
                }
            })
        }

        function rejectEntry() {
           let id = $('.btn_reject').data('id');
           let userId = $('.btn_reject').data('user_id');
            swal({
                title: "Reject Petshop?",
                text: "Are you sure? This action can't be undone",
                icon: "warning",
                buttons: {
                    cancel: true,
                    confirm: {
                        text: "Reject",
                        className: "btn-danger"
                    },
                },
            }).then((value) => {
                if (value) {
                    swal({
                        title: 'Processing...',
                        text: 'Please wait a moment',
                        buttons: false,
                        closeOnEsc: false,
                        closeOnClickOutside: false,
                    });
                    $.ajax({
                        url: url + `/reject_petshop/${id}`,
                        type: 'POST',
                        data: {
                            id: id,
                            user_id: userId,
                            status: 'rejected',
                        },
                        success: function(result) {
                            swal({
                                icon: 'success',
                                title: 'Rejected',
                                text: result.message,
                                buttons: false,
                                closeOnEsc: false,
                                closeOnClickOutside: false,
                                timer: 2000,
                            }).then(() => {
                              $( "#rejected" ).find('a').trigger( "click" );
                            });
                        },
                        error: function(result) {
                            swal({
                                title: "Error",
                                text: result.responseJSON.message,
                                icon: "error",
                                timer: 4000,
                                buttons: false,
                            });
                        }
                    });
                }
            })
        }
    </script>
  <!-- <script src="{{ asset('resources/js/petshop.js') }}"></script> -->
  <script src="{{ asset('packages/backpack/crud/js/crud.js') }}"></script>
  <script src="{{ asset('packages/backpack/crud/js/form.js') }}"></script>
  <script src="{{ asset('packages/backpack/crud/js/list.js') }}"></script>

  <!-- CRUD LIST CONTENT - crud_list_scripts stack -->
  @stack('crud_list_scripts')
@endsection
