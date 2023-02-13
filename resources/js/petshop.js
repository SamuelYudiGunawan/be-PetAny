$(function(){
    console.log('ini ajaja');
    // openDataTable()
    // // acceptedDataTable()

    // $('#open').click(function () { 
    //     $('#open_withdraw_table').DataTable().destroy()
    //     openDataTable()
    // });

    // $('#accepted').click(function () { 
    //     $('#accepted_withdraw_table').DataTable().destroy()
    //     acceptedDataTable()
    // });

    // $('#rejected').click(function () { 
    //     $('#rejected_withdraw_table').DataTable().destroy()
    //     rejectedDataTable()
    // });

    // $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
    //     $($.fn.dataTable.tables(true)).DataTable()
    //        .columns.adjust();
    // });

    // window.addEventListener('resize', function () {
    //     $($.fn.dataTable.tables(true)).DataTable()
    //        .columns.adjust();
    // })

    function openDataTable(){
        $('.box__datatable .loading').show()
        $('#open_withdraw_table').DataTable({
            order: [],
            processing: true,
            fixedHeader: true,
            ajax: {
                url: url+'/get_petshop',
                dataSrc: '',
                type: 'POST',
                data: {
                    status: 'open'
                }
            },
            scrollX: true,
            lengthMenu: [30, 50, 100],
            responsive: true,
            autoWidth: false,
            dom:
            "<'row hidden'<'col-sm-6 hidden-xs'i><'col-sm-6 hidden-print'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row mt-2 '<'col-sm-6 col-md-4'l><'col-sm-2 col-md-4 text-center'B><'col-sm-6 col-md-4 hidden-print'p>>",
            paging: true,
            columnDefs: [
                { width: "20%", targets: 0 },//user
                { width: "10%", targets: 1 },//status
                { width: "20%", targets: 2 },//account number
                { width: "15%", targets: 3 },//bank name
                { width: "20%", targets: 4 },//holder name
                { width: "20%", targets: 5 },//branch bank name
                { width: "20%", targets: 6 },//requested at
                { width: "19%", targets: 7 },//actions
            ],
            columns: [
                {data: 'user' , render : function ( data, type, row, meta ) {
                    var html = ' [#'+row.user_id+'] ' + data;

                    return html;
                }},

                {data: 'status', name: 'status'},

                {data: 'account_number', name: 'account_number'},

                {data: 'bank_name' , name: 'bank_name'},

                {data: 'holder_name' , name: 'holder_name'},

                {data: 'branch_name' , name: 'branch_name'},

                {data: 'requested_at' , name: 'requested_at'},

                {data: 'id' , render : function ( data, type, row, meta ) {
                    var html = '';
                    var type = row.type;

                    //personal
                    if (type == 1) {
                        html += '<a href="javascript:void(0)" onclick="acceptedEntry(this)" class="btn btn-sm btn-success" data-id="'+data+'" data-type="'+row.type+'" data-user="'+row.user+'" data-amount="'+row.amount+'" data-bank="'+row.bank_name+'" data-branch="'+row.branch_name+'" data-holder="'+row.holder_name+'" data-account="'+row.account_number+'" data-route="'+url+'/accept_withdraw" data-toggle="tooltip" data-placement="bottom" title="Accepted" data-button-type="accepted" data-personal_selfie_image="'+ hallo_url + row.personal_selfie_image +'" data-savings_book_cover_image="'+ hallo_url + row.savings_book_cover_image +'" data-personal_identity_image="'+ hallo_url + row.personal_identity_image +'"> Accepted</a>'
                    
                    }
                    //company
                    else {
                        html += '<a href="javascript:void(0)" onclick="acceptedEntry(this)" class="btn btn-sm btn-success" data-id="'+data+'" data-type="'+row.type+'" data-user="'+row.user+'" data-amount="'+row.amount+'" data-company_name="'+row.company_name+'" data-company_account_number="'+row.company_account_number+'" data-company_bank_name="'+row.company_bank_name+'" data-company_branch_name="'+row.company_branch_name+'" data-route="'+url+'/accept_withdraw" data-toggle="tooltip" data-placement="bottom" title="Accepted" data-button-type="accepted" data-deed_of_company_image="'+ hallo_url + row.deed_of_company_image +'" data-director_identity_image="'+ hallo_url + row.director_identity_image +'" data-director_image="'+ hallo_url + row.director_image +'"> Accepted</a>'

                    }
                    
                    html += '<a href="javascript:void(0)" onclick="rejectedEntry(this)" class="btn ml-1 btn-sm btn-danger" data-id="'+data+'" data-user="'+row.user+'" data-bank="'+row.bank_name+'" data-branch="'+row.branch_name+'" data-holder="'+row.holder_name+'" data-account="'+row.account_number+'" data-toggle="tooltip" data-placement="bottom" title="Rejected" data-button-type="rejected"> Rejected</a>'

                    return html;
                }},
            ],
            drawCallback: function( settings ) {
                $('[data-toggle="tooltip"]').tooltip('dispose').tooltip({boundary: 'window'}); 
            },
            // rowCallback: function (row, data, index) {
            //     if (data.status == "open") {
            //         $("td:eq(2)", row).addClass('text-center text-uppercase bg-default')
            //     }
            // },
        });

        $('.box__datatable .loading').hide()

        $('.dt-buttons').show()
        $('.dt-buttons button').removeClass('dt-button')
        $('.dt-buttons button').addClass('btn btn-primary')
        $('.dt-buttons').css('margin-bottom', 10)
    }

    function acceptedDataTable(){
        // console.log('oke');
        $('.box__datatable .loading').show()
        $('#accepted_withdraw_table').DataTable({
            order: [[ 1, "desc" ]],
            processing: true,
            fixedHeader: true,
            ajax: {
                url: url+'/get_petshop',
                dataSrc: '',
                type: 'POST',
                data: {
                    status: 'accepted'
                }
            },
            scrollX: true,
            lengthMenu: [30, 50, 100],
            responsive: true,
            autoWidth: false,
            dom:
            "<'row hidden'<'col-sm-6 hidden-xs'i><'col-sm-6 hidden-print'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row mt-2 '<'col-sm-6 col-md-4'l><'col-sm-2 col-md-4 text-center'B><'col-sm-6 col-md-4 hidden-print'p>>",
            paging: true,
            columnDefs: [
                { width: "20%", targets: 0 },//user
                { width: "10%", targets: 1 },//status
                { width: "20%", targets: 2 },//account number
                { width: "15%", targets: 3 },//bank name
                { width: "20%", targets: 4 },//holder name
                { width: "20%", targets: 5 },//branch bank name
                { width: "20%", targets: 6 },//requested at
                { width: "20%", targets: 7 },//accepted by
                { width: "20%", targets: 8 },//accepted at
                { width: "19%", targets: 9 },//actions
            ],
            columns: [
                // {data: 'user', name: 'user'},
                {data: 'user' , render : function ( data, type, row, meta ) {
                    var html = ' [#'+row.user_id+'] ' + data;
                    console.log(row, 'test');
                    return html;
                }},

                {data: 'status', name: 'status'},

                {data: 'account_number', name: 'account_number'},

                {data: 'bank_name' , name: 'bank_name'},

                {data: 'holder_name' , name: 'holder_name'},

                {data: 'branch_name' , name: 'branch_name'},

                {data: 'requested_at' , name: 'requested_at'},

                {data: 'processed_by', name: 'processed_by'},

                {data: 'processed_at', name: 'processed_at'},

                // {data: 'id' , render : function ( data, type, row, meta ) {
                //     var html = row.note;

                //     return html;
                // }},
            ],
            drawCallback: function( settings ) {
                $('[data-toggle="tooltip"]').tooltip('dispose').tooltip({boundary: 'window'}); 
            },
            rowCallback: function (row, data, index) {
                if (data.status == "accepted") {
                    $("td:eq(2)", row).addClass('text-center text-uppercase bg-success')
                }
            },
        });

        $('.box__datatable .loading').hide()

        $('.dt-buttons').show()
        $('.dt-buttons button').removeClass('dt-button')
        $('.dt-buttons button').addClass('btn btn-primary')
        $('.dt-buttons').css('margin-bottom', 10)
    }

    function rejectedDataTable(){
        $('.box__datatable .loading').show()
        $('#rejected_withdraw_table').DataTable({
            order: [[ 1, "desc" ]],
            processing: true,
            fixedHeader: true,
            ajax: {
                url: url+'/get_petshop',
                dataSrc: '',
                type: 'POST',
                data: {
                    status: 'rejected'
                }
            },
            scrollX: true,
            lengthMenu: [30, 50, 100],
            responsive: true,
            autoWidth: false,
            dom:
            "<'row hidden'<'col-sm-6 hidden-xs'i><'col-sm-6 hidden-print'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row mt-2 '<'col-sm-6 col-md-4'l><'col-sm-2 col-md-4 text-center'B><'col-sm-6 col-md-4 hidden-print'p>>",
            paging: true,
            columnDefs: [
                { width: "10%", targets: 0 },//user
                { width: "10%", targets: 1 },//status
                { width: "20%", targets: 2 },//account number
                { width: "15%", targets: 3 },//bank name
                { width: "20%", targets: 4 },//holder name
                { width: "20%", targets: 5 },//branch bank name
                { width: "20%", targets: 6 },//requested at
                { width: "20%", targets: 7 },//rejected by
                { width: "20%", targets: 8 },//rejected at
                { width: "19%", targets: 9 },//note
                { width: "19%", targets: 10 },//actions
            ],
            columns: [
                {data: 'user' , render : function ( data, type, row, meta ) {
                    var html = ' [#'+row.user_id+'] ' + data;

                    return html;
                }},

                
                {data: 'account_number', name: 'account_number'},

                {data: 'status', name: 'status'},

                {data: 'bank_name' , name: 'bank_name'},

                {data: 'holder_name' , name: 'holder_name'},

                {data: 'branch_name' , name: 'branch_name'},

                {data: 'processed_by', name: 'processed_by'},

                {data: 'processed_at', name: 'processed_at'},

                {data: 'id' , render : function ( data, type, row, meta ) {
                    var html = '';
                    
                    html += '<a href="javascript:void(0)" onclick="rejectedEntryDetail(this)" class="btn btn-sm btn-primary" data-id="'+data+'" data-user="'+row.user+'" data-requested="'+row.requested_at+'" data-note="'+row.note+'" data-branch="'+row.branch_name+'" data-holder="'+row.holder_name+'" data-account="'+row.account_number+'" data-route="'+url+'/accept_withdraw" data-toggle="tooltip" data-placement="bottom" title="Detail" data-button-type="rejectedDetail"> Detail</a>'
                    return html;
                }},
 
    


            ],
            drawCallback: function( settings ) {
                $('[data-toggle="tooltip"]').tooltip('dispose').tooltip({boundary: 'window'}); 
            },
            rowCallback: function (row, data, index) {
                if (data.status == "rejected") {
                    $("td:eq(2)", row).addClass('text-center text-uppercase bg-danger')
                }
            },
        });

        $('.box__datatable .loading').hide()

        $('.dt-buttons').show()
        $('.dt-buttons button').removeClass('dt-button')
        $('.dt-buttons button').addClass('btn btn-primary')
        $('.dt-buttons').css('margin-bottom', 10)
    }
})