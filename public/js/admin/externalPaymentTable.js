"use strict";
// begin first table
let table = $('#external-payment').DataTable({
    responsive: true,

    dom: "<'.row d-flex justify-content-center align-items-center'<'col-sm-4'l><'col-sm-4'f><'.col-sm-4 'p>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'.row d-flex justify-content-center align-items-center'<'col-sm-5'i><'col-sm-7 d-flex justify-content-end'p>>",

    lengthMenu: [10, 25, 50, 75],
    pageLength: 25,
    order: [[0, 'desc']],
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
        url: '/get-rows-for-external-payment',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
        },
        type: 'POST',
    },
    columns: [
        {
            title: 'ID',
            data: 'id'
        },
        {
            title: 'Payment Link',
            data: 'payment_link',
        },
        {
            title: 'Ref. Code',
            data: 'referenceCode'
        },
        {
            title: 'Booking Ref. Code',
            data: 'bookingRefCode'
        },
        {
            title: 'E-Mail',
            data: 'email'
        },
        {
            title: 'Message',
            data: 'message'
        },
        {
            title: 'Price',
            data: 'price'
        },
        {
            title: 'Paid',
            data: 'paid'
        },
        {
            title: 'Created Date',
            data: 'created_at'
        },
        {
            title: 'Actions',
            data: 'actions',
            responsivePriority: -1
        }
    ],
    initComplete: function () {

    },
    columnDefs: [
        {
            targets: -4,
            orderable: false,
            width: '60',
        },
        {
            targets: -3,
            orderable: false,
            width: '100',
            render: function (data, type, full, meta) {
                if (full.paid) {
                    return `<span class="db-done">Paid</span>`
                }else{
                    return `<span class="db-not-done">Not Paid</span>`
                }
            }
        },
        {
            targets: -2,
            orderable: false,
            width: '150',
        },
        {
            targets: -1,
            orderable: false,
            width: 150,
            render: function (data, type, full, meta) {
                let block = `<button data-id="${full.id}" class="btn btn-xs btn-primary btn-block resendEmail" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">Re-send Email</button>`;

                if (full.paid) {
                    block += `<button class="btn btn-xs btn-primary btn-block" style="width: 120px; padding: 0px;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;"><a href="external-payments-pdf/${full.id}" target="_blank">Download Invoice</a></button>`
                }

                block += `<a href="/external-payment/${full.id}" class="btn btn-xs btn-primary btn-block" style="width: 120px; padding: 0px;margin-bottom: 3px; color: white; background-color: #54bb49; border-color: #8852E4; font-size: 10px; font-weight: bold;">Edit</a>`

                return block;
            }
        },
    ],
});

