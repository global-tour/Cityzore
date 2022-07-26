"use strict";
// begin first table
let table = $('#paymentlogs-table').DataTable({
    responsive: true,

    dom: "<'.row d-flex justify-content-center align-items-center'<'col-sm-4'l><'col-sm-4'f><'.col-sm-4 'p>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'.row d-flex justify-content-center align-items-center'<'col-sm-5'i><'col-sm-7 d-flex justify-content-end'p>>",

    lengthMenu: [10, 25, 50, 75],
    pageLength: 25,
    order: [[5, 'desc']],
    searchDelay: 500,
    processing: true,
    serverSide: true,
    searchable: true,
    fixedHeader: true,
    fixedColumns: {
        leftColumns: 1
    },
    scrollX: true,
    select: {
        style: 'multi'
    },
    ajax: {
        url: '/get-rows-for-payment-logs',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
        },
        type: 'POST',
    },
    columns: [
        {
            className: 'dt-control',
            orderable: false,
            data: null,
            defaultContent: '',
        },
        {
            title: 'Process ID',
            data: 'processID',
            orderable: false,
        },
        {
            title: 'User ID',
            data: 'userID',
            orderable: false,
        },
        {
            title: 'Option Title',
            data: 'option_title',
            orderable: false
        },
        {
            title: 'Status',
            data: 'code',
            orderable: false
        },
        {
            title: 'Date',
            data: 'created_at'
        },
    ],
    initComplete: function () {},
    columnDefs: [
        {
            targets: -2,
            render: function (data, type, full ,meta) {
                if (full.code === 'Success') {
                    return `<span class="db-done">${full.code}</span>`
                }else{
                    return `<span class="db-not-done">${full.code}</span>`
                }
            }
        }
    ],
});

function format(d) {

    return (
        `<table class="table table-bordered table-hover" cellpadding="6" cellspacing="0"  style="padding-left:50px;">
            <tr>
                <th>From: </th>
                <td colspan="1">${d.childRow.from}</td>
            </tr>
            <tr>
                <th>Full Name: </th>
                <td colspan="1">${d.childRow.fullName}</td>
            </tr>
            <tr>
                <th>Email: </th>
                <td>${d.childRow.email}</td>
            </tr>
            <tr>
                <th>Phone: </th>
                <td>${d.childRow.phone}</td>
            </tr>
            <tr>
                <th>Travelled Date: </th>
                <td>${d.childRow.travelledDate}</td>
            </tr>
            <tr>
                <th>Price: </th>
                <td>${d.childRow.totalPrice} €</td>
            </tr>
            <tr>
                <th>Item: </th>
                <td>${d.childRow.items}</td>
            </tr>
         </table>`
    )
}

$('#paymentlogs-table tbody').on('click', 'td.dt-control', function () {
    var tr = $(this).closest('tr');
    var row = table.row(tr);

    if (row.child.isShown()) {
        row.child.hide();
        tr.removeClass('shown');
    } else {
        row.child(format(row.data())).show();
        tr.addClass('shown');
    }
})

