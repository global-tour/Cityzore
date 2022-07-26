"use strict";
// begin first table

let table = $('#all-bookings-table').DataTable({
    dom: "<'.row'<'col-sm-12'f><'col-sm-6'B><'.col-sm-6 'p>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'.row flex-vertical-centered'<'col-sm-5'i><'col-sm-7 d-flex justify-content-end'p>>",

    lengthMenu: [[10, 50, 100, 150, 0], [10, 50, 100, 150, "All"]],
    pageLength: 10,
    language: {
        processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>',
        searchPlaceholder: '* You can search only: productRefCode, reservationRefCode, bookingRefCode, gygBookingReference, Traveler Name, Traveler Email'
    },
    order: [[2, 'desc']],
    searchDelay: 500,
    processing: true,
    serverSide: true,
    searchable: true,
    fixedHeader: {
        header: true,
        headerOffset: 50,
    },
    ajax: {
        url: '/get-rows-for-bookings',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
        },
        type: 'POST',
    },
    buttons: [ 'excel', 'pdf', 'pageLength', 'createState',
        {
            extend: 'savedStates',
            config: {
                creationModal: true
            }
        },
        {
            text: '<i class="fa fa-refresh"></i>',
            action: function (e, dt, node, config) {
                dt.ajax.reload()
            }
        },
    ],
    columns: [
        {
            title: 'Date',
            data: 'dateForSort'
        },
        {
            title: 'Tour',
            data: 'optionRefCode'
        },
        {
            title: 'Booking Ref',
            data: 'created_at'
        },
        {
            title: 'Status',
            data: 'status'
        },
        {
            title: 'Platform',
            data: 'platformID'
        },
        {
            title: 'Booking Information',
            data: 'bookingInformation'
        },
        {
            title: 'Sales Informations',
            data: 'invoiceID'
        },
        {
            title: 'Actions',
            data: 'actions',
            responsivePriority: -1
        }
    ],
    initComplete: function () {
        var api = this.api()
        var thisTable = this;
        var rowFilter = $('<tr class="filter"></tr>').appendTo($(table.table().header()));

        api.columns().every(function () {

            var column = this;
            var title = $(this.header()).text().trim();
            var input;

            switch (title) {
                case 'Date':
                    input = $(`<input type="text" class="datatable-input dateranger" placeholder="${title}"  data-col-index="${column.index()}" readonly/>`);
                    break;

                case 'Sales Informations':
                    input = $(`<input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `" placeholder="Invoice"/>
                                <select class="custom-select datatable-input select2" name="selectInputs0" id="selectInputs0" data-col-index="${column.index()}">
                                <option value="">- Payment Method -</option>
                                <option value="CREDIT CARD">Credit Card</option>
                                <option value="COMMISSION">Commission</option>
                                <option value="API">API</option>
                             </select>`);
                    break;

                case 'Tour':
                    input = $(`<select class="custom-select datatable-input select2" multiple name="selectInputs5[]" id="selectInputs5" data-col-index="${column.index()}">
                             </select>`);
                    break;

                case 'Booking Ref':
                    input = $(`<input type="text" class="datatable-input dateranger" placeholder="Booked On"  data-col-index="${column.index()}" readonly/>`);
                    break;

                case 'Platform':
                    input = $(`<select class="custom-select select2 select2-hidden-accessible datatable-input" multiple name="selectInputs[]" id="selectInputs" data-col-index="${column.index()}">
                             </select>`);
                    break;
                case 'Status':
                    input = $(`<select class="custom-select select2 select2-hidden-accessible datatable-input" multiple name="selectInputs3[]" id="selectInputs3" data-col-index="${column.index()}">
                                <option value="1">Approved</option>
                                <option value="4-5">Pending</option>
                                <option value="2-3">Canceled</option>
                             </select>`);

                    break;
                case 'Booking Information':
                    input = $(`<select class="custom-select select2 select2-hidden-accessible datatable-input" multiple name="selectInputs4[]" id="selectInputs4" data-col-index="${column.index()}">
                                <option value="1">Import Have</option>
                                <option value="2">Import Haven't</option>
                                <option value="3">Mail Sent</option>
                                <option value="4">Mail Not Sent</option>
                                <option value="5">Mail Checked</option>
                                <option value="6">Mail Uncheck</option>
                                <option value="7">Special Ref Have</option>
                                <option value="8">Special Ref Haven't</option>
                             </select>`);

                    break;

                case 'Actions':
                    var search = $(`
                                <button class="btn btn-primary" style="width: 55%; margin-bottom: 5px">
                    		        <span>
                    		            <span>Search</span>
                    		        </span>
                    		    </button>`);

                    var reset = $(`
                                <button class="btn btn-primary" style="width: 55%">
                    		        <span>
                    		           <span>Clear</span>
                    		        </span>
                    		    </button>`);

                    $('<th class="actions">').append(search).append(reset).appendTo(rowFilter);

                    $(search).on('click', function (e) {
                        e.preventDefault();
                        var params = {}, error = 0;

                        $(rowFilter).find('.datatable-input').each(function () {
                            var i = $(this).data('col-index');
                            if (params[i]) {
                                params[i] += '|' + $(this).val();
                            } else {
                                params[i] = $(this).val();
                            }
                        });

                        $.each(params, function (i, val) {
                            // apply search params to datatable
                            table.column(i).search(val ? val : '', false, false);
                            if (typeof val === 'object') {
                                error = val.length === 0 ? error : error + 1;
                            } else {
                                error = val.length === 0 ? error : error + 1;
                            }
                        });

                        table.table().draw();
                    });

                    $(reset).on('click', function (e) {
                        e.preventDefault();

                        $(rowFilter).find('.datatable-input').each(function (i) {
                            $(this).val('');
                            table.column($(this).data('col-index')).search('', false, false);
                        });

                        $(rowFilter).find('.daterangepicker-delete-val').remove()

                        $('#selectInputs, #selectInputs0, #selectInputs2, #selectInputs3, #selectInputs4, #selectInputs5').val(null).trigger('change')
                        $('#sendToFiltered').attr('disabled', true)

                        setTimeout(() => {
                            table.search('').columns().search('').draw()
                        })

                    });
                    break;
            }

            if (title !== 'Actions') {
                $(input).appendTo($('<th>').appendTo(rowFilter));
            }

            var start = moment().subtract(29, 'days');
            var end = moment();

            $('.dateranger').daterangepicker({
                startDate: start,
                endDate: end,
                autoApply: true,
                autoUpdateInput: false,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });

            $('.search-introduction').html(`* You can search only: productRefCode, reservationRefCode, bookingRefCode, gygBookingReference, Traveler Name, Traveler Email `)

            $('input.dateranger').on('apply.daterangepicker', function (ev, picker) {
                $(this).parent().find('.daterangepicker-delete-val').remove()
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                $(this).parent().append(`<a href="#" class="daterangepicker-delete-val">Clear</a>`)
            });

            $('input.dateranger').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
            });

            $('#selectInputs4').select2({
                placeholder: 'Select info',
            });

            $('#selectInputs0').select2({
                placeholder: 'Payment Method',
            });

            $('#selectInputs2').select2({
                placeholder: 'Select options',
            });

            $('#selectInputs5').select2({
                ajax: {
                    url: '/get-products',
                    type: 'post',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            q: params.term,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        }
                    },
                    delay: 800,
                    processResults: function (data) {
                        return {
                            results: data.items
                        }
                    }
                },
                placeholder: 'Search for a product',
                closeOnSelect: false,
            });

            $('#selectInputs3').select2({
                placeholder: 'Select status',
            });


            $('#selectInputs').select2({
                ajax: {
                    url: '/get-platforms',
                    type: 'post',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            q: params.term,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        }
                    },
                    delay: 800,
                    processResults: function (data) {
                        return {
                            results: $.map(data.items, function (item) {
                                return {
                                    text: item.name,
                                    id: item.id
                                }
                            })
                        }
                    }
                },
                placeholder: 'Search for a platform',
            })

        });

    },
    columnDefs: [
        {
            targets: 0,
            width: 135,
            render: function (data, type, full, meta) {
                return `<div class="date-row">
                        <span class="month-container" style="background: ${data.color}">${data.month}</span>
                        <span class="day-container"><strong>${data.day}</strong></span>
                        <span class="dayd-container"> ${data.dayD}</span>
                        <span class="year-container">${data.year}</span>
                        <span>Time</span>
                        <span class="time-container"><strong>${data.time === '00:00' ? 'Operating Hours' : data.time}</strong></span>
                    </div>`
            }
        },
        {
            targets: 1,
            width: 300,
            render: function (data, type, full, meta) {
                return `<div class="tour-row">
                        <p><strong>Product :</strong> ${data.product}</p>
                        <p><strong>Option:</strong> ${data.option}</p>
                        <p><strong>Lead Traveler:</strong> ${data.leadTraveler}</p>
                        <p><strong>Phone Number: </strong> <a href="tel:${data.phoneNumber}">${data.phoneNumber}</a></p>
                        <p><strong>E-mail Address: </strong> <a href="tel:${data.email}">${data.email}</a></p>
                    </div>`
            }
        },
        {
            targets: 2,
            width: 135,
            render: function (data, type, full, meta) {
                return `<div>
                        <strong>${data.gyg ?? ''}</strong>
                        <p><strong>Bkn Ref Code: </strong> ${data.bkn}</p>
                        <p><strong>Booked On: </strong> ${data.created_at}</p>
                        <p><strong>Participants: </strong> ${data.participants}</p>
                        <p><strong>Price: </strong> <i class="${data.currency}"></i> ${data.price}</p>
                        <p><strong>Lang: </strong>${data.lang}</p>
                    </div>`
            }
        },
        {
            targets: 3,
            width: 70,
            render: function (data, type, full, meta) {

                if (data.status === 'Canceled')
                    return `<div class="badge  status-button" style="background: ${data.bgColor};"><span class="text-white">${data.status}</span> <span>at: </span> <span>${full.bookingInformation.updated_at}</span></div>`;

                return `<div class="dropdown dropdown-inline" style=" width: 100%; text-align: center">
                            <div class="badge status-button"
                                data-toggle="dropdown"
                                aria-expanded="false"
                                style="background: ${data.bgColor}; cursor: pointer">
                                <span class="text-white">${data.status}</span>
                            </div>
                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                <ul class="nav nav-hoverable flex-column" data-id="${full.info.id}">

                                    <li class="nav-item">
                                        <a class="nav-link" href="#" data-status-id="0">
                                            <em class="nav-icon fa fa-check"></em>
                                            <span class="nav-text">Approved</span>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link" href="javascript:;" target="_blank" data-status-id="4">
                                            <em class="nav-icon fa fa-clock-o"></em>
                                            <span class="nav-text">Pending</span>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link" href="javascript:;" target="_blank" data-status-id="3">
                                            <em class="nav-icon fa fa-ban"></em>
                                            <span class="nav-text">Canceled</span>
                                        </a>
                                    </li>

                                </ul>
                            </div>
                        </div>`

            }
        },
        {
            targets: 4,
            width: 60,
            render: function (data, type, full, meta) {
                return `<div class="platform-container">
                        <strong>${data}</strong>
                    </div>`
            }
        },
        {
            targets: 5,
            width: 80,
            orderable: false,
            render: function (data, type, full, meta) {
                return `<div class="booking-information-container">
                        <p><strong>Import: </strong> <i class="fa ${data.contactCheck ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'}" style="font-size:14px"></i></p>
                        <p><strong>Mail Sent: </strong> <i class="fa ${data.mailSended ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'}" style="font-size:14px"></i></p>
                        <p><strong>Mail Checked: </strong> <i class="fa ${data.mailCheck ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'}" style="font-size:14px"></i></p>
                        <p style="display: flex; flex-direction: column; gap: 5px"><strong>Spec Ref Code: <i class="fa ${data.specialRefCode.status ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'}" style="font-size:14px"></i></strong> <span class="special-ref-code" style="background: ${data.specialRefCode.status ? '#3c763d' : 'transparent'};">${data.specialRefCode.code}</span></p>
                    </div>`;
            }
        },
        {
            targets: 6,
            width: 80,
            render: function (data, type, full, meta) {
                return `<div>
                        <p><strong>Invoice ID: </strong> ${data.invoice_id}</p>
                        <p><strong>Payment Method: </strong> ${data.payment}</p>
                        <p><strong>Affiliater: </strong> ${data.affiliater}</p>
                    </div>`
            }
        },
        {
            targets: -1,
            orderable: false,
            width: 50,
            render: function (data, type, full, meta) {
                if (full.info.auth === -1) {
                    return `<div style="display: flex; justify-content: center; align-items: center; gap: 13px; flex-direction: column">
                            <div class="dropdown dropdown-inline" style=" width: 100%; text-align: center">
                                <a href="javascript:;"
                                   class="btn-clean btn-icon"
                                   data-toggle="dropdown"
                                   aria-expanded="false" style="display:block; width: 100%; text-align: center">
                                    <em class="fa fa-cog" style="font-size: 20px"></em>
                                </a>
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                    <ul class="nav nav-hoverable flex-column">

                                        <li class="nav-item">
                                            <a class="nav-link" href="#" data-edit="${full.info.id}">
                                                <em class="nav-icon fa fa-edit"></em>
                                                <span class="nav-text">Edit</span>
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link" href="${full.info.voucher}" target="_blank">
                                                <em class="nav-icon fa fa-download"></em>
                                                <span class="nav-text">Voucher</span>
                                            </a>
                                        </li>

                                           <li class="nav-item">
                                            <a class="nav-link" href="${full.info.voucherv2}" target="_blank">
                                                <em class="nav-icon fa fa-download"></em>
                                                <span class="nav-text">Voucher V2</span>
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link" href="${full.info.invoice}" target="_blank">
                                                <em class="nav-icon fa fa-download"></em>
                                                <span class="nav-text">Invoice</span>
                                            </a>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                            <a href="#special-ref-code" class="btn-clean btn-icon" data-toggle="tooltip" title="Special Ref. Code" data-edit="${full.info.id}" style=" width: 100%; text-align: center">
                                <em class="fa fa-barcode" style="font-size: 20px"></em>
                            </a>
                            <a href="#import" class="btn-clean btn-icon" data-edit="${full.info.id}" data-toggle="tooltip" title="Import" style=" width: 100%; text-align: center">
                                <em class="fa fa-upload" style="font-size: 20px"></em>
                            </a>
                            <a href="#contact" class="btn-clean btn-icon" data-edit="${full.info.id}" data-toggle="tooltip" title="Contact" style=" width: 100%; text-align: center">
                                <em class="fa fa-envelope" style="font-size: 20px"></em>
                            </a>
                    </div>`;
                }
                return `<div style="display: flex; justify-content: center; align-items: center; gap: 13px; flex-direction: column">
                            <div class="dropdown dropdown-inline" style=" width: 100%; text-align: center">
                                <a href="javascript:;"
                                   class="btn-clean btn-icon"
                                   data-toggle="dropdown"
                                   aria-expanded="false" style="display:block; width: 100%; text-align: center">
                                    <em class="fa fa-cog" style="font-size: 20px"></em>
                                </a>
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                    <ul class="nav nav-hoverable flex-column">

                                        <li class="nav-item">
                                            <a class="nav-link" href="#" data-edit="${full.info.id}">
                                                <em class="nav-icon fa fa-edit"></em>
                                                <span class="nav-text">Edit</span>
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link" href="${full.info.voucher}" target="_blank">
                                                <em class="nav-icon fa fa-download"></em>
                                                <span class="nav-text">Voucher</span>
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link" href="${full.info.invoice}" target="_blank">
                                                <em class="nav-icon fa fa-download"></em>
                                                <span class="nav-text">Invoice</span>
                                            </a>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                            <a href="#special-ref-code" class="btn-clean btn-icon" data-toggle="tooltip" title="Special Ref. Code" data-edit="${full.info.id}" style=" width: 100%; text-align: center">
                                <em class="fa fa-barcode" style="font-size: 20px"></em>
                            </a>
                            <a href="#comment" class="btn-clean btn-icon" data-edit="${full.info.id}" data-toggle="tooltip" title="Comment" style=" width: 100%; text-align: center">
                                <em class="fa fa-comments-o" style="font-size: 20px"></em>
                            </a>
                    </div>`;
            }
        },
    ],
    createdRow: function (row, data, dataIndex) {
        if (data.bookingInformation.mailCheck && data.bookingInformation.contactCheck) {
            $(row).addClass('active-booking')
        }
    },
});

$(document).ready(function () {
    let offCanvas = $('.booking-offcanvas'),
        overlay = $('.offcanvas-overlay'),
        _token = $('meta[name="csrf-token"]').attr('content'),
        booking_id, attr;

    offCanvas.on('click', '.nav-tabs li', function (){
        attr = $(this).find('a').attr('href');
        attr = attr === '#bookingDetails' ? '#' : attr;
    })



    $('#all-bookings-table').on('stateLoaded.dt', (i, x, t) => {
        $.each(t.columns, function (k, v) {
            const input = $('table thead tr.filter .datatable-input')[k];

            $(input).parent().find('.daterangepicker-delete-val').remove()

            if (typeof input !== 'undefined') {
                $(input).val(v.search.search)

                if ($.inArray(k, [0, 2]) !== -1 && v.search.search.length > 0) {
                    $(input).parent().append('<a href="#" class="daterangepicker-delete-val">Clear</a>')
                }

                if (k === 1 && v.search.search.length > 0) {
                    var selectInputs2 = $('#selectInputs5');

                    $.ajax({
                        type: 'GET',
                        url: '/get-options/' + v.search.search
                    }).then(function (data) {
                        var option;
                        $.each(data, function (key, val) {
                            option += `<option value="${val.referenceCode}" selected>${val.title}</option>`;
                        });


                        selectInputs2.append(option).trigger('change');

                        // manually trigger the `select2:select` event
                        selectInputs2.trigger({
                            type: 'select2:select',
                            params: {
                                data: data
                            }
                        });


                    });
                }

                if (k === 4 && v.search.search.length > 0) {

                    var selectInputs = $('#selectInputs');

                    $.ajax({
                        type: 'GET',
                        url: '/get-platforms/' + v.search.search
                    }).then(function (data) {
                        var option;

                        $.each(data, function (key, val) {
                            option += `<option value="${val.id}" selected>${val.name}</option>`;
                        });


                        selectInputs.append(option).trigger('change');

                        // manually trigger the `select2:select` event
                        selectInputs.trigger({
                            type: 'select2:select',
                            params: {
                                data: data
                            }
                        });


                    });
                }
            }
        })
    })

    function cardWaitMe(el, close) {
        if (close) {
            el.waitMe('hide')
        } else {
            el.waitMe({
                effect: 'win8',
                text: '',
                bg: 'rgba(255,255,255,0.7)',
                color: '#000',
                maxSize: '',
                waitTime: -1,
                textPos: 'vertical',
                fontSize: '',
                source: '',
            });
        }
    }

    function fillBody(id, attr) {

        $.ajax({
            url: '/get-booking-detail',
            type: 'POST',
            data: {
                id,
                attr,
                _token
            },
            beforeSend: function () {
                cardWaitMe(offCanvas, false)
            },
            success: function (res) {
                $('.offcanvas-title').html(res.fullName)

                setTimeout(() => {
                    cardWaitMe(offCanvas, true)
                }, 2000)

                offCanvas.html(res)
            },
            error: function (err) {
                cardWaitMe(offCanvas, true)

                offCanvas.html(`<div class="offcanvas-body">
                                        <div class="alert alert-danger">
                                            An error has occurred. Something went wrong. Please share this code: ${id} with IT...
                                            <br> ${err.responseJSON.message}
                                        </div>
                                    </div>`)
            }
        })
    }

    $('body').on('click', '[data-edit]', function (e) {

        e.preventDefault();

        booking_id = $(this).data('edit');

        attr = $(this).attr('href');

        offCanvas.html('')

        offCanvas.addClass('active-canvas')

        overlay.addClass('active-overlay')

        $('body').css({
            overflow: 'hidden'
        })


        fillBody(booking_id, attr)

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape') {
                offCanvas.removeClass('active-canvas')
                overlay.removeClass('active-overlay')
                $('body').css({
                    overflow: 'unset'
                })
                table.ajax.reload(null, false)
            }
        })

    })

    $('body').on('click', '.offcanvas-overlay, .booking-offcanvas .offcanvas-close-button', function () {

        offCanvas.removeClass('active-canvas')
        overlay.removeClass('active-overlay')
        $('body').css({
            overflow: 'unset'
        })
        table.ajax.reload(null, false)
    })

    offCanvas.on('submit', '[data-submit]', function (e) {
        e.preventDefault();
        const formData = new FormData(this),
            url = $(this).data('submit');

            $.ajax({
                url,
                type: 'POST',
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function (){
                   cardWaitMe(offCanvas, false);
                },
                success: function (res) {
                    Materialize.toast(res.message, 4000, 'toast-success');
                    fillBody(booking_id, attr)
                },
                error: function (err) {
                    Materialize.toast(err.responseJSON.message, 4000, 'toast-alert');
                    cardWaitMe(offCanvas, true);
                }
            })
    })

    $('body').on('click', '.change-mail-message-language li', function () {
        $('.tab-content #contact textarea[name="mail_message"]').val($(this).data('lang'))
        $('.change-mail-message-language li').css({
            background: 'unset',
            color: '#000'
        })
        $(this).css({
            background: '#0e76a8',
            color: '#fff'
        })
    })

    $('body').on('click', '.change-whatsapp-message-language li', function () {
        $('.tab-content #whatsapp textarea[name="whatsapp_message"]').val($(this).data('lang'));
        $('.change-whatsapp-message-language li').css({
            background: 'unset',
            color: '#000'
        })
        $(this).css({
            background: '#0e76a8',
            color: '#fff'
        })

    })

    $('body').on('click', '#share-to-whatsapp', function (e) {
        e.preventDefault();
        const message = $('.tab-content #whatsapp textarea[name="whatsapp_message"]').val(),
            phone = $('.tab-content #whatsapp input[name="phone"]').val();


        const url = `whatsapp://send?text=${urlencode(message)}&phone=${phone}`

        window.open(url);
    })

    $('body').on('click', '#add-file-input', function (event) {
        event.preventDefault();

        var firstGroup = $(".tab-content #import .import-file-container .form-group").eq(-1).clone(true);
        firstGroup = firstGroup.find("input[type='file']").val('').end();
        $(".tab-content #import .import-file-container").append(firstGroup);

    });

    $(document).on('click', '.remove-unload-item', function (event) {
        event.preventDefault();

        if ($(".tab-content #import .import-file-container .form-group").length <= 1) {
            return false;
        }

        $(this).closest(".form-group").remove();

    });

    $(document).on('click', '[data-delete-file]', function (event) {
        event.preventDefault();

        if (!confirm("Are You Sure!")) {
            return false;
        }

        let $this = $(this);
        let file_id = $this.data('delete-file');
        let action = "delete_extra_booking_file";

        $.ajax({
            url: '/booking/ajax',
            type: 'POST',
            dataType: 'json',
            data: {
                file_id,
                action,
                _token
            },
            beforeSend: function () {
                cardWaitMe(offCanvas, false)
            },
            success: function (res) {
                cardWaitMe(offCanvas, true)
                if (res.status) {
                    Materialize.toast(res.success, 4000, 'toast-success');
                    fillBody(booking_id, attr)
                } else {
                    Materialize.toast('An Error Occurred , File Cant delete!', 4000, 'toast-alert');
                }
            }
        })

    });

    $('body').on('click', '.daterangepicker-delete-val', function () {
        $(this).parent().find('input.dateranger').val('')
        $(this).remove();
    })

    $('body').on('click', '.paginate_button', function () {
        if ($(window).scrollTop() > 100) {
            $("html, body").animate({scrollTop: 0}, 1000);
        }
    })

    $(document).on('click', '[data-status-id]', function (e) {
        e.preventDefault()
        var id = $(this).closest('ul').data('id'),
            status = $(this).data('status-id'),
            cancelReason = null;
        //$(this).closest('.open').find('.dropdown-menu')

        if (confirm('Are you sure')) {
            if (status === 3) {
                cancelReason  = prompt("please indicate your reason for cancellation", "");

            }

            $.ajax({
                url: '/booking/changeStatus/'+id,
                type: 'GET',
                data: {
                    id, status, cancelReason
                },
                success: function (res) {
                    Materialize.toast(res.message, 4000, 'toast-success');
                    table.ajax.reload(null, false)
                },
                error: function (){
                    table.ajax.reload(null, false)
                }
            })
        }

    })

    $('#expand-collapse-div').on('click', function (){
        const $parent = $(this).closest('.sb2-2');

        $parent.toggleClass('expand-collapse-div')
        table.fixedHeader.adjust();

        if ($parent.hasClass('expand-collapse-div')) {
            $(this).find('i').removeClass('fa-expand').addClass('fa-compress')
            $('.sb2-1').css({
                left: '-350px'
            })
        }else{
            $(this).find('i').removeClass('fa-compress').addClass('fa-expand')
            $('.sb2-1').css({
                left: '0'
            })
        }

    })


    function urlencode (str) {
        str = (str + '')
        return encodeURIComponent(str)
            .replace(/!/g, '%21')
            .replace(/'/g, '%27')
            .replace(/\(/g, '%28')
            .replace(/\)/g, '%29')
            .replace(/\*/g, '%2A')
            .replace(/~/g, '%7E')
            .replace(/%20/g, '+')
    }
})
