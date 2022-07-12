"use strict";
// begin first table
let table = $('#bulkmail-table').DataTable({
    responsive: true,

    dom: "<'.row d-flex justify-content-center align-items-center'<'col-sm-4'l><'col-sm-4'f><'.col-sm-4 'p>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'.row d-flex justify-content-center align-items-center'<'col-sm-5'i><'col-sm-7 d-flex justify-content-end'p>>",

    lengthMenu: [10, 25, 50, 75],
    pageLength: 25,
    order: [[1, 'desc']],
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
        url: '/get-booking-for-mail',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
        },
        type: 'POST',
    },
    columns: [
        {
            title: '',
            data: 'check',
        },
        {
            title: 'Travelled Date',
            data: 'dateForSort'
        },
        {
            title: 'Tour',
            data: 'option'
        },
        {
            title: 'Reference Code',
            data: 'test2'
        },
        {
            title: 'Platform',
            data: 'platform'
        },
        {
            title: 'Status',
            data: 'status'
        },
        {
            title: 'Booked Date',
            data: 'created_at'
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
                case 'Travelled Date':
                    input = $(`<input type="text" class="datatable-input dateranger" placeholder="${title}"  data-col-index="${column.index()}" readonly/>`);
                    break;
                case 'Booked Date':
                    input = $(`<input type="text" class="datatable-input dateranger" placeholder="${title}"  data-col-index="${column.index()}" readonly/>`);
                    break;

                case 'Tour':
                    input = $(`<select class="custom-select datatable-input select2" multiple name="selectInputs2[]" id="selectInputs2" data-col-index="${column.index()}">
                             </select>`);

                    break;
                case 'Platform':
                    input = $(`<select class="custom-select select2 select2-hidden-accessible datatable-input" multiple name="selectInputs[]" id="selectInputs" data-col-index="${column.index()}">
                             </select>`);
                    break;

                case 'Status':
                    input = $(`<select class="custom-select select2 select2-hidden-accessible datatable-input" multiple name="selectInputs3[]" id="selectInputs3" data-col-index="${column.index()}">
                                <option value="0">Approved</option>
                                <option value="2">Pending</option>
                                <option value="3">Canceled</option>
                             </select>`);

                    break;
                case 'Reference Code':
                    input = $(`<input type="text" class="form-control form-control-sm form-filter datatable-input" data-col-index="` + column.index() + `" placeholder="${title}"/>`);
                    break;
                case 'Actions':
                    var search = $(`
                                <button class="btn btn-primary" style="margin-right: 5px">
                    		        <span>
                    		            <span>Search</span>
                    		        </span>
                    		    </button>`);

                    var reset = $(`
                                <button class="btn btn-secondary">
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

                        if (error) {
                            $('#sendToFiltered').attr('disabled', false);
                        }
                        table.table().draw();
                    });

                    $(reset).on('click', function (e) {
                        e.preventDefault();
                        $(rowFilter).find('.datatable-input').each(function (i) {
                            $(this).val('');
                            table.column($(this).data('col-index')).search('', false, false);
                        });
                        $('#selectInputs, #selectInputs2, #selectInputs3').val(null).trigger('change')
                        $('#sendToFiltered').attr('disabled', true)
                        setTimeout(() => {
                            table.table().draw();
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
                autoUpdateInput: false,
                autoApply: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });

            $('input.dateranger').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            });

            $('input.dateranger').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
            });

            $('#selectInputs3').select2({
                placeholder: 'Select status',
            });

            $('#selectInputs2').select2({
                ajax: {
                    url: '/get-options',
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
                                    text: item.title,
                                    id: item.referenceCode
                                }
                            })
                        }
                    }
                },
                placeholder: 'Search for a tour',
            })


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
            orderable: false,
            checkboxes: {
                selectRow: true
            }
        },
        {
            targets: 1,
            width: 200
        },
        {
            targets: 2,
            width: 350
        },
        {
            targets: 3,
            width: 200
        },
        {
            targets: 4,
            width: 300
        },
        {
            targets: 5,
            width: 100,
            render: function (data, type, full, meta) {
                switch (full.status) {
                    case 0:
                        return `<div class="badge" style="background: #3c763d"><span class="text-white">Approved</span></div>`
                        break;
                    case 2:
                        return `<div class="badge" style="background: #8a6d3b"><span class="text-white">Pending</span></div>`
                        break;
                    case 3:
                        return `<div class="badge" style="background: #a94442"><span class="text-white">Canceled</span></div>`
                        break;
                    default:
                        return '---'
                        break;

                }
            }
        },
        {
            targets: -2,
            width: 200
        },
        {
            targets: -1,
            orderable: false,
            width: 150,
            render: function (data, type, full, meta) {
                return `<a href="mailto:${full.actions}" class="text-danger text-center">${full.actions}</a>`
            }
        },
    ],
});

let rows_selected;

$('[data-target="#sendToSelectedModal"]').on('click', function () {

    rows_selected = table.column(0).checkboxes.selected();

    $('#sendToSelectedModal #sendToSelectedModalLabel').html(`${rows_selected.length} rows selected`)


})

const editor = KEDITOR.create('mailContent');

editor.setOptions({
    buttonList: [
        ['undo', 'redo'],
        ['fontSize', 'formatBlock'],
        ['bold', 'underline', 'italic'],
        ['removeFormat'],
        ['fontColor'],
        ['outdent', 'indent'],
        ['align', 'horizontalRule', 'list', 'table'],
        ['link', 'video'],
        ['fullScreen', 'codeView'],
        ['preview', 'print', 'template'],
    ],
    templates: [
        {
            name: 'EN - Template',
            html: '<p>EN HTML Source 1</p>'
        },
        {
            name: 'FR - Template',
            html: '<p>FR HTML source2</p>'
        },
        {
            name: 'RU - Template',
            html: '<p>RU HTML source2</p>'
        }
    ],
    minHeight: '500px',
})


$('#sendToSelected').on('click', function () {

    const content = editor.getContents(),
        subject = $('input[name="subject"]').val();

    if (confirm(`Are you sure to submit for ${rows_selected.length} records?`)) {
        if (!rows_selected.length) {

            alert('Select minimum 1 record!')

        } else {

            $.ajax({
                url: 'send-mail',
                type: 'post',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    selected: rows_selected.join(","),
                    content,
                    subject
                },
                success: function (data) {
                    if (confirm(`${data}`)) {
                        location.reload()
                    }
                },
                error: function (data) {
                    alert('An error has occurred!')
                },
            })

        }
    }
})

// $('#sendToFiltered').on('click', function () {
//     var params = {}, error = 0;
//     $('tr.filter').find('.datatable-input').each(function () {
//         var i = $(this).data('col-index');
//         if (params[i]) {
//             params[i] += $(this).val();
//         } else {
//             params[i] = $(this).val();
//         }
//     });
//

// })


