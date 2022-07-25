
<script src="{{asset('js/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('js/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('js/datatables/buttons.html5.min.js')}}"></script>
<script src="{{asset('js/datatables/buttons.print.min.js')}}"></script>
<script src="{{asset('js/datatables/dataTables.scroller.min.js')}}"></script>
<script src="{{asset('js/datatables/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('js/datatables/jszip.min.js')}}"></script>
<script src="{{asset('js/datatables/pdfmake.min.js')}}"></script>
<script src="{{asset('js/datatables/vfs_fonts.js')}}"></script>
<script src="{{asset('js/waitme/waitMe.min.js')}}"></script>
<script src="//cdn.datatables.net/plug-ins/1.10.21/pagination/input.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>



<script>

    $(document).ready(function() {
        let urlParams = new URLSearchParams(window.location.search);
        let pageID = urlParams.get('page') == null ? 1 : urlParams.get('page');
        // Datatables for each page. Else part initializes the tables without lazy load.
        // Code can be simplified

        @if($page == 'attraction-index')
        $('#datatable').dataTable({
            dom: 'Bfrtip',
            lengthMenu: [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
            ],
            "ordering": false,
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'ajax': {
                'url': '/getRowsForDataTable',
                'data': {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    model: 'attraction',
                },
            },
            'columns': [
                { data: 'name', name: 'name' },
                { data: 'status', name: 'status' },
                { data: 'actions', name: 'actions' },
            ]
        });

    @elseif($page == 'attraction-indexpct')
        $('#datatable').dataTable({
            dom: 'Bfrtip',
            lengthMenu: [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
            ],
            "ordering": false,
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'ajax': {
                'url': '/getRowsForDataTable',
                'data': {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    model: 'attractionpct',
                },
            },
            'columns': [
                { data: 'name', name: 'name' },
                { data: 'status', name: 'status' },
                { data: 'actions', name: 'actions' },
            ]
        });

            @elseif($page == 'attraction-indexpctcom')
        $('#datatable').dataTable({
            dom: 'Bfrtip',
            lengthMenu: [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
            ],
            "ordering": false,
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'ajax': {
                'url': '/getRowsForDataTable',
                'data': {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    model: 'attractionpctcom',
                },
            },
            'columns': [
                { data: 'name', name: 'name' },
                { data: 'status', name: 'status' },
                { data: 'actions', name: 'actions' },
            ]
        });


      @elseif($page == 'attraction-indexctp')
        $('#datatable').dataTable({
            dom: 'Bfrtip',
            lengthMenu: [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
            ],
            "ordering": false,
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'ajax': {
                'url': '/getRowsForDataTable',
                'data': {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    model: 'attractionctp',
                },
            },
            'columns': [
                { data: 'name', name: 'name' },
                { data: 'status', name: 'status' },
                { data: 'actions', name: 'actions' },
            ]
        });

        @elseif($page == 'comment-index')

        let dtComment = createDTComment();

        $('#applyFiltersButton').on('click', function() {
            let country = $('#country').val();
            if (country === '') {
                Materialize.toast('You have to select at least one country to use filtering!', 4000, 'toast-alert');
                return;
            }
            let attraction = $('#attraction').val();
            if (attraction === '') {
                Materialize.toast('You have to select at least one attraction to use filtering!', 4000, 'toast-alert');
                return;
            }
            let supplier = $('#supplier').val();
            let one = $('#one').val();
            let two = $('#two').val();
            let three = $('#three').val();
            let four = $('#four').val();
            let five = $('#five').val();
            let confirmed = $('#confirmed').val();
            let notConfirmed = $('#notConfirmed').val();

            let filters = {
                country: country,
                attraction: attraction,
                supplier: supplier,
                one: one,
                two: two,
                three: three,
                four: four,
                five: five,
                confirmed: confirmed,
                notConfirmed: notConfirmed
            };
            dtComment.destroy();
            dtComment = createDTComment(filters);
        });

        $('#clearFiltersButton').on('click', function() {
            dtComment.destroy();
            dtComment = createDTComment();
            $('#country').val('');
            $('#attraction').val('');
            $('#supplier').val('');
            let one = $('#one');
            let two = $('#two');
            let three = $('#three');
            let four = $('#four');
            let five = $('#five');
            let confirmed = $('#confirmed');
            let notConfirmed = $('#notConfirmed');
            if (one.val() === '0') {
                one.click();
            }
            if (two.val() === '0') {
                two.click();
            }
            if (three.val() === '0') {
                three.click();
            }
            if (three.val() === '0') {
                three.click();
            }
            if (four.val() === '0') {
                four.click();
            }
            if (five.val() === '0') {
                five.click();
            }
            if (confirmed.val() === '0') {
                confirmed.click();
            }
            if (notConfirmed.val() === '0') {
                notConfirmed.click();
            }
        });

        function createDTComment(filters = {}) {
            let country = typeof filters.country === 'undefined' ? '' : filters.country;
            let attraction = typeof filters.attraction === 'undefined' ? '' : filters.attraction;
            let supplier = typeof filters.supplier === 'undefined' ? '' : filters.supplier;
            let one = filters.one;
            let two = filters.two;
            let three = filters.three;
            let four = filters.four;
            let five = filters.five;
            let confirmed = filters.confirmed;
            let notConfirmed = filters.notConfirmed;

            let dtComment = $('#datatable').DataTable({
                dom: 'Bfrtip',
                lengthMenu: [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
                ],
                "ordering": false,
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                    'url': '/getRowsForDataTable',
                    'data': {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        model: 'comment',
                        country: country,
                        attraction: attraction,
                        supplierId: supplier,
                        one: one,
                        two: two,
                        three: three,
                        four: four,
                        five: five,
                        confirmed: confirmed,
                        notConfirmed: notConfirmed,
                    }
                },
                'columns': [
                    {data: 'productRefCode', name: 'productRefCode', width: '10%' },
                    {data: 'productName', name: 'productName', width: '10%' },
                    {data: 'userName', name: 'userName'},
                    {data: 'title', name: 'title'},
                    {data: 'description', name: 'description', width: '10%' },
                    {data: 'rate', name: 'rate'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action'},
                ],
                rowCallback: function(row, data) {
                    $('.toggle-class5', row).bootstrapToggle();
                }
            });

            return dtComment;
        }

        $('body').on('change', '.toggle-class5', function() {
            let status = $(this).prop('checked') == true ? 1 : 0;
            let id = $(this).data('id');

            $.ajax({
                type: "GET",
                dataType: "json",
                url: '/changeCommentStatus',
                data: {
                    'status': status,
                    'id': id,
                    'type': 'cz'
                },
                success: function(data) {
                    //
                }
            });
        });

        @elseif($page == 'supplier-index')
        let dtSupplier = createDTSupplier();

        $('#applyFiltersButton').on('click', function() {
            let country = $('#country').val();
            if (country === '') {
                Materialize.toast('You have to select at least one country to use filtering!', 4000, 'toast-alert');
                return;
            }
            let filters = {
                country: country,
            };
            dtSupplier.destroy();
            dtSupplier = createDTSupplier(filters);
        });

        $('#clearFiltersButton').on('click', function() {
            dtSupplier.destroy();
            dtSupplier = createDTSupplier();
            $('#country').val('');
        });

        function createDTSupplier(filters = {}) {
            let country = typeof filters.country === 'undefined' ? '' : filters.country;
            let dtSupplier = $('#datatable').DataTable({
                dom: 'Bfrtip',
                lengthMenu: [
                    [ 10, 25, 50, -1 ],
                    [ '10 rows', '25 rows', '50 rows', 'Show all' ]
                ],
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
                ],
                "ordering": false,
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                    'url': '/getRowsForDataTable',
                    'data': {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        model: 'supplier',
                        country: country,
                    }
                },
                'columns': [
                    { data: 'companyName', name: 'companyName' },
                    { data: 'contact', name: 'contact' },
                    { data: 'email', name: 'email' },
                    { data: 'country', name: 'country' },
                    { data: 'city', name: 'city' },
                    { data: 'isRestaurant', name: 'isRestaurant' },
                    { data: 'status', name: 'status', width: '20%' },
                    { data: 'edit', name: 'edit' },
                    { data: 'licenses', name: 'licenses' },
                    // { data: 'delete', name: 'delete' },
                ],
                rowCallback: function(row, data) {
                    $('.toggle-class', row).bootstrapToggle();
                }
            });

            return dtSupplier;
        }
        @elseif($page == 'on-goings')
        let dtOnGoings = createDTOnGoings();

        $('#applyFiltersButton').on('click', function() {
            let product = $('#productSelect').val();
            let option = $('#optionSelect').val();

            if(product.length > 0) {
                if(product.length == 1 && product[0] == -1) {
                    // Do nothing
                } else {
                    if(option.length == 0) {
                        Materialize.toast('You have chosen product, option selection is required!', 4000, 'toast-alert');
                        return false;
                    }
                }
            }

            let filters = {
                product: product,
                option: option
            };
            dtOnGoings.destroy();
            dtOnGoings = createDTOnGoings(filters);
        });

        $('#clearFiltersButton').on('click', function() {
            dtOnGoings.destroy();
            dtOnGoings = createDTOnGoings();
            $('#productSelect').val(-1).trigger('change');
            $('#optionSelect').empty();
        });

        function createDTOnGoings(filters = {}) {
            let product = typeof filters.product === 'undefined' ? '' : filters.product;
            let option = typeof filters.option === 'undefined' ? '' : filters.option;

            let dtOnGoings = $('#datatable').DataTable({
                dom: 'Bfrtip',
                lengthMenu: [
                    [ 10, 25, 50, -1 ],
                    [ '10 rows', '25 rows', '50 rows', 'Show all' ]
                ],
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
                ],
                "ordering": false,
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                    'url': '/getRowsForDataTable',
                    'data': {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        model: 'on-goings',
                        product: product,
                        option: option
                    }
                },
                'columns': [
                    { data: 'bookingItems', name: 'bookingItems' },
                    { data: 'createdAt', name: 'createdAt' },
                    { data: 'productTitle', name: 'productTitle' },
                    { data: 'optionTitle', name: 'optionTitle' },
                    { data: 'totalPrice', name: 'totalPrice' },
                    { data: 'from', name: 'from' },
                    { data: 'dateTime', name: 'dateTime' },
                ],
                rowCallback: function(row, data) {
                    $('.toggle-class', row).bootstrapToggle();
                }
            });

            return dtOnGoings;
        }
        @elseif($page == 'paymentlogs-index')

        $('#datatable').dataTable({
            dom: 'Bfrtip',
            lengthMenu: [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
            ],
            "ordering": false,
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'ajax': {
                'url': '/getRowsForDataTable',
                'data': {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    model: 'bookingLog',
                },
            },
            'columns': [
                { data: 'processID', name: 'processID' },
                { data: 'userID', name: 'userID' },
                { data: 'optionTitle', name: 'optionTitle' },
                { data: 'cartID', name: 'cartID' },
                { data: 'code', name: 'code' },
                { data: 'paymentDate', name: 'paymentDate' },
            ]
        });
        @elseif($page == 'product-indexpct')
        let dtProductPCT = createDTProductPCT();

        $('#applyFiltersButton').on('click', function() {
            let attraction = $('#attractions').val();
            let category = $('#categoryId').val();
            let supplier = $('#supplierId').val() === null ? '-1' : $('#supplierId').val();
            let published = $('#publishedFilter').val();
            let notPublished = $('#notPublishedFilter').val();
            let orderBy = $('#orderBy').val();
            if (attraction === '' && category === '') {
                Materialize.toast('You have to select at least one attraction or category to use filtering!', 4000, 'toast-alert');
                return;
            }
            let filters = {
                attraction: attraction,
                category: category,
                supplier: supplier,
                published: published,
                notPublished: notPublished,
                orderBy: orderBy
            };
            dtProductPCT.destroy();
            dtProductPCT = createDTProductPCT(filters);
        });

        $('#clearFiltersButton').on('click', function() {
            dtProductPCT.destroy();
            dtProductPCT = createDTProductPCT();
            $('#attractions').val('');
            $('#categoryId').val('');
            $('#supplierId').val('-1');
            $('#orderBy').val('newest');
            let published = $('#publishedFilter');
            let notPublished = $('#notPublishedFilter');
            if (published.val() === '0') {
                published.click();
            }
            if (notPublished.val() === '0') {
                notPublished.click();
            }
        });

        function createDTProductPCT(filters = {}) {
            let attraction = typeof filters.attraction === 'undefined' ? '' : filters.attraction;
            let category = typeof filters.category === 'undefined' ? '' : filters.category;
            let supplier = typeof filters.supplier === 'undefined' ? '-1' : filters.supplier;
            let published = filters.published;
            let notPublished = filters.notPublished;
            let orderBy = typeof filters.orderBy === 'undefined' ? 'newest' : filters.orderBy;
            let dtProductPCT = $('#datatable').DataTable({
                dom: 'Bfrtip',
                lengthMenu: [
                    [ 10, 25, 50, -1 ],
                    [ '10 rows', '25 rows', '50 rows', 'Show all' ]
                ],
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
                ],
                "ordering": false,
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                "pagingType": "numbers",
                "displayStart": (pageID*10)-10,
                'ajax': {
                    'url': '/getRowsForDataTable',
                    'data': {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        model: 'productPCT',
                        attraction: attraction,
                        category: category,
                        supplier: supplier,
                        published: published,
                        notPublished: notPublished,
                        orderBy: orderBy,
                    }
                },
                'initComplete' : function(settings, json, e) {
                    let pageID = json.pageID;
                    if (pageID !== null && pageID !== 'null' && pageID !== 'undefined') {
                        $('.paginate_button a').each(function() {
                            if ($(this).text() === pageID) {
                                $(this).click();
                            }
                        });
                    }
                },

                'columns': [
                    { data: 'index', name: 'index' },
                    { data: 'image', name: 'image' },
                    { data: 'referenceCode', name: 'referenceCode' },
                    { data: 'companyName', name: 'companyName' },
                    { data: 'title', name: 'title' },
                    { data: 'confirmed', name: 'confirmed' },
                    { data: 'published', name: 'published' },
                    { data: 'actions', name: 'actions', width: '20%' },
                ],
                  rowCallback: function(row, data) {
                    $('.toggle-class', row).bootstrapToggle();
                    $('.toggle-class2', row).bootstrapToggle();
                }
            });

            return dtProductPCT;
        }

            $('body').on('change', '.toggle-class', function() {
            let $this = $(this);
            let isDraft = $(this).prop('checked') === false ? 0 : 1;
            let id = $(this).data('id');
            let platform = $this.attr("data-platform");

            $.ajax({
                type: "GET",
                dataType: "json",
                url: '/changeProductDraftStatus',
                data: {'isDraft': isDraft, 'id': id, platform: platform},
                success: function(data) {
                    if(data.status === "0"){
                         Materialize.toast(data.error, 5000, 'toast-alert');
                         //$this.prop('checked', true);
                         setTimeout(function(){
                          $this.bootstrapToggle('toggle');
                         },1000)

                    }
                },
            });
        });

        $('body').on('change', '.toggle-class2', function() {
            let isPublished = $(this).prop('checked') === true ? 1 : 0;
            let id = $(this).data('id');
            let platform = $(this).attr("data-platform");

            $.ajax({
                type: "GET",
                dataType: "json",
                url: '/changeProductPublishedStatus',
                data: {'isPublished': isPublished, 'id': id, platform: platform},
                success: function(data) {
                    //
                }
            });
        });


        @elseif($page == 'product-indexctp')
        let dtProductCTP = createDTProductPCT();

        $('#applyFiltersButton').on('click', function() {
            let attraction = $('#attractions').val();
            let category = $('#categoryId').val();
            let supplier = $('#supplierId').val() === null ? '-1' : $('#supplierId').val();
            let published = $('#publishedFilter').val();
            let notPublished = $('#notPublishedFilter').val();
            let orderBy = $('#orderBy').val();
            if (attraction === '' && category === '') {
                Materialize.toast('You have to select at least one attraction or category to use filtering!', 4000, 'toast-alert');
                return;
            }
            let filters = {
                attraction: attraction,
                category: category,
                supplier: supplier,
                published: published,
                notPublished: notPublished,
                orderBy: orderBy
            };
            dtProductCTP.destroy();
            dtProductCTP = createDTProductPCT(filters);
        });

        $('#clearFiltersButton').on('click', function() {
            dtProductCTP.destroy();
            dtProductCTP = createDTProductPCT();
            $('#attractions').val('');
            $('#categoryId').val('');
            $('#supplierId').val('-1');
            $('#orderBy').val('newest');
            let published = $('#publishedFilter');
            let notPublished = $('#notPublishedFilter');
            if (published.val() === '0') {
                published.click();
            }
            if (notPublished.val() === '0') {
                notPublished.click();
            }
        });

        function createDTProductPCT(filters = {}) {
            let attraction = typeof filters.attraction === 'undefined' ? '' : filters.attraction;
            let category = typeof filters.category === 'undefined' ? '' : filters.category;
            let supplier = typeof filters.supplier === 'undefined' ? '-1' : filters.supplier;
            let published = filters.published;
            let notPublished = filters.notPublished;
            let orderBy = typeof filters.orderBy === 'undefined' ? 'newest' : filters.orderBy;
            let dtProductCTP = $('#datatable').DataTable({
                dom: 'Bfrtip',
                lengthMenu: [
                    [ 10, 25, 50, -1 ],
                    [ '10 rows', '25 rows', '50 rows', 'Show all' ]
                ],
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
                ],
                "ordering": false,
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                "pagingType": "numbers",
                "displayStart": (pageID*10)-10,
                'ajax': {
                    'url': '/getRowsForDataTable',
                    'data': {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        model: 'productCTP',
                        attraction: attraction,
                        category: category,
                        supplier: supplier,
                        published: published,
                        notPublished: notPublished,
                        orderBy: orderBy,
                    }
                },
                'initComplete' : function(settings, json, e) {
                    let pageID = json.pageID;
                    if (pageID !== null && pageID !== 'null' && pageID !== 'undefined') {
                        $('.paginate_button a').each(function() {
                            if ($(this).text() === pageID) {
                                $(this).click();
                            }
                        });
                    }
                },
                'columns': [
                    { data: 'index', name: 'index' },
                    { data: 'image', name: 'image' },
                    { data: 'referenceCode', name: 'referenceCode' },
                    { data: 'companyName', name: 'companyName' },
                    { data: 'title', name: 'title' },
                    { data: 'confirmed', name: 'confirmed' },
                    { data: 'published', name: 'published' },
                    { data: 'actions', name: 'actions', width: '20%' },
                ],
                   rowCallback: function(row, data) {
                    $('.toggle-class', row).bootstrapToggle();
                    $('.toggle-class2', row).bootstrapToggle();
                }
            });

            return dtProductCTP;
        }

         $('body').on('change', '.toggle-class', function() {
            let $this = $(this);
            let isDraft = $(this).prop('checked') === false ? 0 : 1;
            let id = $(this).data('id');
            let platform = $this.attr("data-platform");

            $.ajax({
                type: "GET",
                dataType: "json",
                url: '/changeProductDraftStatus',
                data: {'isDraft': isDraft, 'id': id, platform: platform},
                success: function(data) {
                    if(data.status === "0"){
                         Materialize.toast(data.error, 5000, 'toast-alert');
                         //$this.prop('checked', true);
                         setTimeout(function(){
                          $this.bootstrapToggle('toggle');
                         },1000)

                    }
                },
            });
        });

        $('body').on('change', '.toggle-class2', function() {
            let isPublished = $(this).prop('checked') === true ? 1 : 0;
            let id = $(this).data('id');
            let platform = $(this).attr("data-platform");

            $.ajax({
                type: "GET",
                dataType: "json",
                url: '/changeProductPublishedStatus',
                data: {'isPublished': isPublished, 'id': id, platform: platform},
                success: function(data) {
                    //
                }
            });
        });

        @elseif($page == 'product-indexpctcom')
        let dtProductPCTcom = createDTProductPCT();

        $('#applyFiltersButton').on('click', function() {
            let attraction = $('#attractions').val();
            let category = $('#categoryId').val();
            let supplier = $('#supplierId').val() === null ? '-1' : $('#supplierId').val();
            let published = $('#publishedFilter').val();
            let notPublished = $('#notPublishedFilter').val();
            let orderBy = $('#orderBy').val();
            if (attraction === '' && category === '') {
                Materialize.toast('You have to select at least one attraction or category to use filtering!', 4000, 'toast-alert');
                return;
            }
            let filters = {
                attraction: attraction,
                category: category,
                supplier: supplier,
                published: published,
                notPublished: notPublished,
                orderBy: orderBy
            };
            dtProductPCTcom.destroy();
            dtProductPCTcom = createDTProductPCT(filters);
        });

        $('#clearFiltersButton').on('click', function() {
            dtProductPCTcom.destroy();
            dtProductPCTcom = createDTProductPCT();
            $('#attractions').val('');
            $('#categoryId').val('');
            $('#supplierId').val('-1');
            $('#orderBy').val('newest');
            let published = $('#publishedFilter');
            let notPublished = $('#notPublishedFilter');
            if (published.val() === '0') {
                published.click();
            }
            if (notPublished.val() === '0') {
                notPublished.click();
            }
        });

        function createDTProductPCT(filters = {}) {
            let attraction = typeof filters.attraction === 'undefined' ? '' : filters.attraction;
            let category = typeof filters.category === 'undefined' ? '' : filters.category;
            let supplier = typeof filters.supplier === 'undefined' ? '-1' : filters.supplier;
            let published = filters.published;
            let notPublished = filters.notPublished;
            let orderBy = typeof filters.orderBy === 'undefined' ? 'newest' : filters.orderBy;
            let dtProductPCTcom = $('#datatable').DataTable({
                dom: 'Bfrtip',
                lengthMenu: [
                    [ 10, 25, 50, -1 ],
                    [ '10 rows', '25 rows', '50 rows', 'Show all' ]
                ],
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
                ],
                "ordering": false,
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                "pagingType": "numbers",
                "displayStart": (pageID*10)-10,
                'ajax': {
                    'url': '/getRowsForDataTable',
                    'data': {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        model: 'productPCTcom',
                        attraction: attraction,
                        category: category,
                        supplier: supplier,
                        published: published,
                        notPublished: notPublished,
                        orderBy: orderBy,
                    }
                },
                'initComplete' : function(settings, json, e) {
                    let pageID = json.pageID;
                    if (pageID !== null && pageID !== 'null' && pageID !== 'undefined') {
                        $('.paginate_button a').each(function() {
                            if ($(this).text() === pageID) {
                                $(this).click();
                            }
                        });
                    }
                },
                'columns': [
                    { data: 'index', name: 'index' },
                    { data: 'image', name: 'image' },
                    { data: 'referenceCode', name: 'referenceCode' },
                    { data: 'companyName', name: 'companyName' },
                    { data: 'title', name: 'title' },
                    { data: 'confirmed', name: 'confirmed' },
                    { data: 'published', name: 'published' },
                    { data: 'actions', name: 'actions', width: '20%' },
                ],
                   rowCallback: function(row, data) {
                    $('.toggle-class', row).bootstrapToggle();
                    $('.toggle-class2', row).bootstrapToggle();
                }
            });

            return dtProductPCTcom;
        }

        $('body').on('change', '.toggle-class', function() {
            let $this = $(this);
            let isDraft = $(this).prop('checked') === false ? 0 : 1;
            let id = $(this).data('id');
            let platform = $this.attr("data-platform");

            $.ajax({
                type: "GET",
                dataType: "json",
                url: '/changeProductDraftStatus',
                data: {'isDraft': isDraft, 'id': id, platform: platform},
                success: function(data) {
                    if(data.status === "0"){
                         Materialize.toast(data.error, 5000, 'toast-alert');
                         //$this.prop('checked', true);
                         setTimeout(function(){
                          $this.bootstrapToggle('toggle');
                         },1000)

                    }
                },
            });
        });

        $('body').on('change', '.toggle-class2', function() {
            let isPublished = $(this).prop('checked') === true ? 1 : 0;
            let id = $(this).data('id');
            let platform = $(this).attr("data-platform");

            $.ajax({
                type: "GET",
                dataType: "json",
                url: '/changeProductPublishedStatus',
                data: {'isPublished': isPublished, 'id': id, platform: platform},
                success: function(data) {
                    //
                }
            });
        });

        @elseif($page == 'product-index')

        let dtProduct = createDTProduct();

        $('#applyFiltersButton').on('click', function() {

            let country = $('#countries').val();
            let city = $('#cities').val();
            let attraction = $('#attractions').val();
            let category = $('#categoryId').val();
            let supplier = $('#supplierId').val() === null ? '-1' : $('#supplierId').val();
            let published = $('#publishedFilter').val();
            let notPublished = $('#notPublishedFilter').val();
            let pendingApproval = $('#pendingApproval').is(':checked') ? '1' : '0';
            let orderBy = $('#orderBy').val();
            let specialOffer = $('#specialOffer').is(':checked') ? '1' : '0';
            if (attraction === '' && category === '') {
                Materialize.toast('You have to select at least one attraction or category to use filtering!', 4000, 'toast-alert');
                return;
            }
            let filters = {
                country: country,
                city: city,
                attraction: attraction,
                category: category,
                supplier: supplier,
                published: published,
                notPublished: notPublished,
                pendingApproval: pendingApproval,
                orderBy: orderBy,
                specialOffer: specialOffer
            };
            dtProduct.destroy();
            dtProduct = createDTProduct(filters);
        });

        $('#clearFiltersButton').on('click', function() {
            dtProduct.destroy();
            dtProduct = createDTProduct();
            $('#attractions').val('');
            $('#categoryId').val('');
            $('#supplierId').val('-1');
            $('#orderBy').val('newest');
            let published = $('#publishedFilter');
            let notPublished = $('#notPublishedFilter');
            let specialOffer = $('#specialOffer');
            if (published.val() === '0') {
                published.click();
            }
            if (notPublished.val() === '0') {
                notPublished.click();
            }
            if (specialOffer.is(':checked')) {
                specialOffer.click();
            }
        });

        function createDTProduct(filters = {}) {
            console.log(filters.pendingApproval);
            let country = typeof filters.country === 'undefined' ? '' : filters.country;
            let city = typeof filters.city === 'undefined' ? '' : filters.city;
            let attraction = typeof filters.attraction === 'undefined' ? '' : filters.attraction;
            let category = typeof filters.category === 'undefined' ? '' : filters.category;
            let supplier = typeof filters.supplier === 'undefined' ? '-1' : filters.supplier;
            let published = filters.published;
            let notPublished = filters.notPublished;
            let pendingApproval = filters.pendingApproval;
            let orderBy = typeof filters.orderBy === 'undefined' ? 'newest' : filters.orderBy;
            let specialOffer = filters.specialOffer;
            let dtProduct = $('#datatable').DataTable({
                dom: 'Bfrtip',
                lengthMenu: [
                    [ 10, 25, 50, -1 ],
                    [ '10 rows', '25 rows', '50 rows', 'Show all' ]
                ],
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
                ],
                "ordering": false,
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                "pagingType": "numbers",
                "displayStart": (pageID*10)-10,
                'ajax': {
                    'url': '/getRowsForDataTable',
                    'data': {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        model: 'product',
                        country: country,
                        city: city,
                        attraction: attraction,
                        category: category,
                        supplier: supplier,
                        published: published,
                        notPublished: notPublished,
                        pendingApproval: pendingApproval,
                        orderBy: orderBy,
                        specialOffer: specialOffer
                    }
                },
                'initComplete': function(settings, json, e) {
                    let pageID = json.pageID;
                    if (pageID !== null && pageID !== 'null' && pageID !== 'undefined') {
                        $('.paginate_button a').each(function() {
                            if ($(this).text() === pageID) {
                                $(this).click();
                            }
                        });
                    }
                },
                'columns': [
                    { data: 'index', name: 'index' },
                    { data: 'image', name: 'image' },
                    { data: 'referenceCode', name: 'referenceCode' },
                    { data: 'companyName', name: 'companyName' },
                    { data: 'title', name: 'title' },
                    { data: 'category', name: 'category' },
                    { data: 'options', name: 'options' },
                    { data: 'confirmed', name: 'confirmed' },
                    { data: 'published', name: 'published' },
                    { data: 'actions', name: 'actions', width: '20%' },
                ],
                rowCallback: function(row, data) {
                    $('.toggle-class', row).bootstrapToggle();
                    $('.toggle-class2', row).bootstrapToggle();
                }
            });

            return dtProduct;
        }

        $('body').on('change', '.toggle-class', function() {
            let $this = $(this);
            let isDraft = $(this).prop('checked') === false ? 0 : 1;
            let id = $(this).data('id');
            let platform = $this.attr("data-platform");

            $.ajax({
                type: "GET",
                dataType: "json",
                url: '/changeProductDraftStatus',
                data: {'isDraft': isDraft, 'id': id, platform: platform},
                success: function(data) {
                    if(data.status === "0"){
                         Materialize.toast(data.error, 5000, 'toast-alert');
                         //$this.prop('checked', true);
                         setTimeout(function(){
                          $this.bootstrapToggle('toggle');
                         },1000)

                    }
                },
            });
        });

        $('body').on('change', '.toggle-class2', function() {
            let isPublished = $(this).prop('checked') === true ? 1 : 0;
            let id = $(this).data('id');
            let platform = $(this).attr("data-platform");

            $.ajax({
                type: "GET",
                dataType: "json",
                url: '/changeProductPublishedStatus',
                data: {'isPublished': isPublished, 'id': id, platform: platform},
                success: function(data) {
                    //
                }
            });
        });

        @elseif($page == 'option-index')

        let dtOption = createDTOption();

        $('#applyFiltersButton').on('click', function() {
            let availabilitySelect = $('#availabilitySelect').val();

            let filters = {
                availabilitySelect: availabilitySelect
            };
            dtOption.destroy();
            dtOption = createDTOption(filters);
        });

        function createDTOption(filters = {}) {
            let availabilitySelect = typeof filters.availabilitySelect === 'undefined' ? '' : filters.availabilitySelect;
            let columnsArr = [];
            if ($('#userType').val() === '1') {
                columnsArr = [
                    { data: 'referenceCode', name: 'referenceCode' },
                    { data: 'title', name: 'title' },
                    { data: 'pricing', name: 'pricing' },
                    { data: 'availability', name: 'availability' },
                    { data: 'connectedProducts', name: 'connectedProducts'},
                    { data: 'comission', name: 'comission' },
                    { data: 'supplier', name: 'supplier'},
                    { data: 'published', name: 'published' },
                    { data: 'actions', name: 'actions', width: '0%' },
                    { data: 'api', name: 'api', width: '0%'},
                ];
            } else {
                columnsArr = [
                    { data: 'referenceCode', name: 'referenceCode' },
                    { data: 'title', name: 'title' },
                    { data: 'pricing', name: 'pricing' },
                    { data: 'availability', name: 'availability' },
                    { data: 'connectedProducts', name: 'connectedProducts'},
                    { data: 'comission', name: 'comission' },
                    { data: 'published', name: 'published' },
                    { data: 'actions', name: 'actions', width: '0%' },
                ];
            }
            let dtOption = $('#datatable').DataTable({
                dom: 'Bfrtip',
                lengthMenu: [
                    [ 10, 25, 50, -1 ],
                    [ '10 rows', '25 rows', '50 rows', 'Show all' ]
                ],
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
                ],
                "ordering": false,
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                "pagingType": "numbers",
                "displayStart": (pageID*10)-10,
                'ajax': {
                    'url': '/getRowsForDataTable',
                    'data': {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        model: 'option',
                        availabilitySelect: availabilitySelect
                    }
                },
                'initComplete': function(settings, json, e) {
                    let pageID = json.pageID;
                    if (pageID !== null && pageID !== 'null' && pageID !== 'undefined') {
                        $('.paginate_button a').each(function() {
                            if ($(this).text() === pageID) {
                                $(this).click();
                            }
                        });
                    }
                    if (window.location.href.indexOf('productId') > -1) {
                        $('#datatable_paginate').html('');
                        $('#datatable_info').html();
                    }
                },
                'columns': columnsArr,
                rowCallback: function(row, data) {
                    $('.toggle-class4', row).bootstrapToggle();
                    if (window.location.href.indexOf('productId') > -1) {
                        $('#datatable_paginate').hide();
                        $('#datatable_info').hide();
                    }
                }
            });

            return dtOption;
        }

        @elseif($page == 'mails')
            let dtMails = createDTMails();

            $('#applyFiltersButton').on('click', function() {
                let typeSelect = $('#typeSelect').val();
                let from = $('.datepicker-from').val();
                let to = $('.datepicker-to').val();

                if ((from === '' && to !== '') || (to === '' && from !== '')) {
                    Materialize.toast('You have to select both from and to fields!', 4000, 'toast-alert');
                    return;
                }

                let filters = {
                    typeSelect: typeSelect,
                    from: from,
                    to: to
                };
                dtMails.destroy();
                dtMails = createDTMails(filters);
            });

            $('#clearFiltersButton').on('click', function() {
                dtMails.destroy();
                dtMails = createDTMails();

                $('.datepicker-from').val('');
                $('.datepicker-to').val('');
                $('#typeSelect').val('');
            });

            function createDTMails(filters = {}) {
                let typeSelect = typeof filters.typeSelect === 'undefined' ? '' : filters.typeSelect;
                let from = typeof filters.from === 'undefined' ? '' : filters.from;
                let to  = typeof filters.to === 'undefined' ? '' : filters.to;

                let dtMails = $('#datatable').DataTable({
                    dom: 'Bfrtip',
                    lengthMenu: [
                        [ 10, 25, 50, -1 ],
                        [ '10 rows', '25 rows', '50 rows', 'Show all' ]
                    ],
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
                    ],
                    "ordering": false,
                    'processing': true,
                    'serverSide': true,
                    'serverMethod': 'post',
                    'ajax': {
                        'url': '/getRowsForDataTable',
                        'data': {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            model: 'mail',
                            typeSelect: typeSelect,
                            from: from,
                            to: to
                        }
                    },
                    'columns': [
                        { data: 'index', name: 'index' },
                        { data: 'to', name: 'to' },
                        { data: 'data', name: 'data' },
                        { data: 'type', name: 'type' },
                        { data: 'date', name: 'date', width: '150px' },
                        { data: 'status', name: 'status', width: '100px'},
                    ],
                    rowCallback: function(row, data) {
                        $('.toggle-class', row).bootstrapToggle();
                    }
                });

                return dtMails;
            }

        @elseif($page == 'availability-index')
        var pageIDEx=pageID;
        if(sessionStorage.hasOwnProperty("lastPage")){
            pageIDEx=sessionStorage.getItem("lastPage");
        }
        let filters = {
            lastPageNumber: pageIDEx
        };
        let dtAvailability = createDTAvailability(filters);

        $('#applyFiltersButton').on('click', function() {
            let expiredAvailabilities = $('#expiredAvailabilities').is(':checked');

            let filters = {
                expiredAvailabilities: expiredAvailabilities
            };
            dtAvailability.destroy();
            dtAvailability = createDTAvailability(filters);
        });

        function createDTAvailability(filters = {}) {
            let expiredAvailabilities = filters.expiredAvailabilities ? '1' : '0';
            let dtAvailability = $('#datatable').DataTable({
                dom: 'Bfrtip',
                lengthMenu: [
                    [ 10, 25, 50, -1 ],
                    [ '10 rows', '25 rows', '50 rows', 'Show all' ]
                ],
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
                ],
                "ordering": false,
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                "pagingType": "numbers",
                "displayStart": (filters.lastPageNumber*10)-10,
                'ajax': {
                    'url': '/getRowsForDataTable',
                    'data': {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        model: 'availability',
                        expiredAvailabilities: expiredAvailabilities
                    }
                },
                'initComplete': function(settings, json, e) {
                    let pageID = json.pageID;
                    if (pageID !== null && pageID !== 'null' && pageID !== 'undefined') {
                        $('.paginate_button a').each(function() {
                            if ($(this).text() === pageID) {
                                $(this).click();
                            }
                        });
                    }
                },
                'columns': [
                    { data: 'companyName', name: 'companyName' },
                    { data: 'name', name: 'name' },
                    { data: 'type', name: 'type' },
                    { data: 'connectedOptions', name: 'connectedOptions' },
                    { data: 'valid', name: 'valid', width: '20%'},
                    { data: 'actions', name: 'actions'},
                ],
            });

            return dtAvailability;
        }

        @elseif($page == 'pricings-index')

        let dtPricing = createDTPricing();

        function createDTPricing(filters = {}) {
            let dtPricing = $('#datatable').DataTable({
                dom: 'Bfrtip',
                lengthMenu: [
                    [ 10, 25, 50, -1 ],
                    [ '10 rows', '25 rows', '50 rows', 'Show all' ]
                ],
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
                ],
                "ordering": false,
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                "pagingType": "numbers",
                "displayStart": (pageID*10)-10,
                'ajax': {
                    'url': '/getRowsForDataTable',
                    'data': {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        model: 'pricing',
                    }
                },
                'initComplete': function(settings, json, e) {
                    let pageID = json.pageID;
                    if (pageID !== null && pageID !== 'null' && pageID !== 'undefined') {
                        $('.paginate_button a').each(function() {
                            if ($(this).text() === pageID) {
                                $(this).click();
                            }
                        });
                    }
                    if (window.location.href.indexOf('optionId') > -1) {
                        $('#datatable_paginate').hide();
                        $('#datatable_info').hide();
                    }
                },
                'columns': [
                    { data: 'name', name: 'name' },
                    { data: 'type', name: 'type' },
                    { data: 'companyName', name: 'companyName' },
                    { data: 'actions', name: 'actions'},
                ],
            });

            return dtPricing;
        }


        @elseif($page == 'bookings-index')

        function urlencode (str) {
  //       discuss at: https://locutus.io/php/urlencode/
  //      original by: Philip Peterson
  //      improved by: Kevin van Zonneveld (https://kvz.io)
  //      improved by: Kevin van Zonneveld (https://kvz.io)
  //      improved by: Brett Zamir (https://brett-zamir.me)
  //      improved by: Lars Fischer
  //      improved by: Waldo Malqui Silva (https://fayr.us/waldo/)
  //         input by: AJ
  //         input by: travc
  //         input by: Brett Zamir (https://brett-zamir.me)
  //         input by: Ratheous
  //      bugfixed by: Kevin van Zonneveld (https://kvz.io)
  //      bugfixed by: Kevin van Zonneveld (https://kvz.io)
  //      bugfixed by: Joris
  // reimplemented by: Brett Zamir (https://brett-zamir.me)
  // reimplemented by: Brett Zamir (https://brett-zamir.me)
  //           note 1: This reflects PHP 5.3/6.0+ behavior
  //           note 1: Please be aware that this function
  //           note 1: expects to encode into UTF-8 encoded strings, as found on
  //           note 1: pages served as UTF-8
  //        example 1: urlencode('Kevin van Zonneveld!')
  //        returns 1: 'Kevin+van+Zonneveld%21'
  //        example 2: urlencode('https://kvz.io/')
  //        returns 2: 'https%3A%2F%2Fkvz.io%2F'
  //        example 3: urlencode('https://www.google.nl/search?q=Locutus&ie=utf-8')
  //        returns 3: 'https%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3DLocutus%26ie%3Dutf-8'
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





$(document).on('click', '#click_to_change_values', function(event) {
    event.preventDefault();

    $(this).closest(".form-group").next(".group-wrap").slideToggle();
});

$(document).on('click', '#access-checkins-form-submit-button', function(event) {
    event.preventDefault();


    var control = 0;
    $("#access-checkins-form input[type='number']").each(function(index, el) {
        if($(this).val() == ""){
            control++;
        }
    });

    if($("#access-checkins-form input[name='refCode']").val().trim() == ""){
        Materialize.toast("You can not pass empty data!", 4000, 'toast-alert');
        return false;
    }


    if(control){
        Materialize.toast("You can not pass empty data!", 4000, 'toast-alert');
        return false;
    }

   var booking_id = $("#access-checkins-form input[name='booking_id']").val();
         $("#access-checkins-modal .modal-content").waitMe({
                    effect : 'bounce',
                    text : '',
                    bg : 'rgba(255,255,255,0.7)',
                    color : '#000',
                    maxSize : '',
                    waitTime : -1,
                    textPos : 'vertical',
                    fontSize : '',
                    source : '',
                    onClose : function() {}
                });


   $.ajax({
       url: '{{url("booking/ajax")}}',
       type: 'POST',
       dataType: 'json',
       data: $("#access-checkins-form").serialize(),
   })
   .done(function(response) {
    if(response.status == "success"){
         Materialize.toast('Checkin Has been Done Successfully!', 4000, 'toast-success');
         $("a[data-target-booking-id-for-voucher='"+booking_id+"']").find("button").removeAttr('disabled');
         $("a[data-target-booking-id-for-voucher='"+booking_id+"']").attr("href", response.voucher_url);
         $("strong[data-target-booking-id-for-voucher='"+booking_id+"']").text(response.ref_code);
    }else{
        Materialize.toast(response.message, 4000, 'toast-alert');
    }

       console.log(response);
   })
   .fail(function() {
       console.log("error");
   })
   .always(function() {
       console.log("complete");
        $("#access-checkins-modal .modal-content").waitMe('hide');
   });


});




$(document).on('keyup', '#access-checkins-form input[type="number"]', function(event) {
    event.preventDefault();
    var thisVal = parseInt($(this).val());
    var maxVal = parseInt($(this).data("max"));

    if(thisVal > maxVal){
        $(this).val(maxVal);
        Materialize.toast('You cannot exceed the maximum number of categories, maximum category exceeded', 4000, 'toast-alert');
    }


     if(thisVal < 0){
        $(this).val(0);
        Materialize.toast('The number of tickets entered cannot be less than 0!');
    }
});


$(document).on('change', '#access-checkins-form input[type="number"]', function(event) {
    event.preventDefault();
    var thisVal = parseInt($(this).val());
    var maxVal = parseInt($(this).data("max"));

    if(thisVal > maxVal){
        $(this).val(maxVal);
        Materialize.toast('You cannot exceed the maximum number of categories, maximum category exceeded', 4000, 'toast-alert');
    }


     if(thisVal < 0){
        $(this).val(0);
        Materialize.toast('The number of tickets entered cannot be less than 0!', 4000, 'toast-alert');
    }
});




   // booking access checkins operations

        $(document).on('click', '.access-checkins-button', function(event) {
         event.preventDefault();
         let $this = $(this);
         let booking_id = $this.attr("data-id");
         let action = 'insert_layout_for_access_checkins_modal';
         let token = "{{csrf_token()}}";
         $("#access-checkins-modal .modal-content").empty();


         $.ajax({
             url: '{{url("booking/ajax")}}',
             type: 'POST',
             dataType: 'json',
             data: {action: action, booking_id: booking_id, _token: token},
         })
         .done(function(response) {
            $("#access-checkins-modal .modal-content").html(response.view);
         })
         .fail(function(jqXHR) {
             console.log(jqXHR.resposneText);
         })
         .always(function() {
             console.log("complete");
         });



     });








      $(document).on('click', '.send-whatsapp-message-button', function(event) {



              var whatsapp_message = $('textarea[name="whatsapp_message"]').val();
              var whatsapp_to = $("#whatsapp-form input[name='whatsapp_to']").val();
              var fullText = "whatsapp://send?text="+urlencode(whatsapp_message)+"&phone="+whatsapp_to;
              if(fullText.length > 2057){
                 event.preventDefault();
                Materialize.toast('exceeded the maximum character (2057) limit! current character size = '+fullText.length, 4000, 'toast-alert');
                return false;
              }else{

              }


      });






        /* Bu fonksiyonun kullanıldığı yere collapse eklendi
        $(document).on('click', '.before_senders_lists li', function(event) {
            event.preventDefault();
            var data_message = $(this).attr("data-contact-message");
            alert(data_message);
        });
        */


        $(document).on('click', '.change-mail-message-language li', function(event) {
            event.preventDefault();
            var whatsapp_to = $("input[name='whatsapp_to']").val();
            var booking_id = $(this).attr("data-id");
            var lang = $(this).attr("data-lang");
            var action = "change_customer_template_message_language";
            var token = "{{csrf_token()}}";

                $("#customer-contact-modal .modal-content").waitMe({
                    effect : 'bounce',
                    text : '',
                    bg : 'rgba(255,255,255,0.7)',
                    color : '#000',
                    maxSize : '',
                    waitTime : -1,
                    textPos : 'vertical',
                    fontSize : '',
                    source : '',
                    onClose : function() {}
                });




             $.ajax({
                url: '{{url("booking/ajax")}}',
                type: 'POST',
                dataType: 'json',
                data: {action: action, _token: token, booking_id: booking_id, lang: lang},
            })
            .done(function(response) {
                if(response.status == "success"){
                    $("textarea[name='mail_message']").val(response.messageForMail);
                    $("textarea[name='whatsapp_message']").val(response.message);
                    $(".send-whatsapp-message-button").attr("href", "whatsapp://send?text="+urlencode(response.message)+"&phone="+whatsapp_to);
                     Materialize.toast('Language Been Changed Successfully!', 4000, 'toast-success');
                }else{
                    Materialize.toast('An Error Occurred , An Error Occurred!', 4000, 'toast-alert');
                }
                console.log("success");
            })
            .fail(function() {
                $("#customer-contact-modal .modal-content").waitMe('hide');
                 Materialize.toast('An Error Occurred , An Error Occurred!', 4000, 'toast-alert');
                console.log("error");
            })
            .always(function() {
                $("#customer-contact-modal .modal-content").waitMe('hide');
                console.log("complete");
            });


        });

          $(document).on('click', '.send-mail-message-button', function(event) {
              event.preventDefault();

              var booking_id = $("#mail-form input[name='booking_id']").val();
              var mail_to = $("#mail-form input[name='mail_to']").val();
              var mail_title = $("#mail-form input[name='mail_title']").val();
              var mail_message = $("#mail-form textarea[name='mail_message']").val();
              var action = "booking-customer-contact-send-mail-operation";
              var result=null;
              var url=window.location.href;
              var tablename='Bookings';
              var page='Bookings/Contact/MailSend';
              var status=1;
              var blade='bookings.mail-information'

              $("#customer-contact-modal .modal-content").waitMe({
                  effect: 'bounce',
                  text: '',
                  bg: 'rgba(255,255,255,0.7)',
                  color: '#000',
                  maxSize: '',
                  waitTime: -1,
                  textPos: 'vertical',
                  fontSize: '',
                  source: '',
                  onClose: function () {
                  }
              });

              $.ajax({
                  url: '{{url("booking/ajax")}}',
                  type: 'POST',
                  dataType: 'json',
                  data: {
                      action: action,
                      _token: "{{csrf_token()}}",
                      booking_id: booking_id,
                      mail_to: mail_to,
                      mail_title: mail_title,
                      mail_message: mail_message,
                  },
              })
                  .done(function (response) {
                      if (response.status == "success") {
                          Materialize.toast('Mail Has Been Sent Successfully!', 4000, 'toast-success');
                          result="successful";

                          $.ajax({
                              url: '/mailLogs',
                              type: 'POST',
                              dataType: 'json',
                              data: {
                                  action: action,
                                  status:status,
                                  _token: "{{csrf_token()}}",
                                  booking_id: booking_id,
                                  mail_to: mail_to,
                                  mail_title: mail_title,
                                  mail_message: mail_message,
                                  blade: blade,
                              },
                          })
                              .done(function (response) {

                                  if (response.status!="success"){
                                      Materialize.toast('Error[MailLogs]. Please Call Your IT Personnal.', 4000, 'toast-alert');
                                  }
                              })
                              .fail(function () {
                                  Materialize.toast('Fail[MailLogs]. Please Call Your IT Personnal.', 4000, 'toast-alert');
                              })
                              .always(function () {
                                  $("#customer-contact-modal .modal-content").waitMe("hide");
                              });

                          $('.contactModal').click();
                          setTimeout(function() {
                              /*
                              $('button[data-target="#customer-contact-modal"]').each(function() {
                                  let el = $(this);
                                  if(el.attr('data-id') == booking_id)
                                      el.click();
                              });
                               */

                              executeVisibilityChange();
                          }, 1000);

                      } else {
                          Materialize.toast('An Error Occurred , Mail Cannot send to Customer!', 4000, 'toast-alert');
                          result="error";
                      }
                      $.ajax({
                          url: '/adminLogs',
                          type: 'POST',
                          dataType: 'json',
                          data: {
                              action: action,
                              _token: "{{csrf_token()}}",
                              booking_id: booking_id,
                              mail_to: mail_to,
                              mail_title: mail_title,
                              mail_message: mail_message,
                              tablename:tablename,
                              url:url,
                              result:result,
                              page:page
                          },
                      })
                          .done(function (response) {

                              if (response.status!="success"){
                                  Materialize.toast('Error[AdminLogs]. Please Call Your IT Personnal.', 4000, 'toast-alert');
                              }
                          })
                          .fail(function () {
                              Materialize.toast('Fail[AdminLogs]. Please Call Your IT Personnal.', 4000, 'toast-alert');
                          })
                          .always(function () {
                              $("#customer-contact-modal .modal-content").waitMe("hide");
                          });
                  })
                  .fail(function () {
                      Materialize.toast('An Error Occurred , Mail Cannot send to Customer!', 4000, 'toast-alert');
                      result="failed";

                      $.ajax({
                          url: '/adminLogs',
                          type: 'POST',
                          dataType: 'json',
                          data: {
                              action: action,
                              _token: "{{csrf_token()}}",
                              booking_id: booking_id,
                              mail_to: mail_to,
                              mail_title: mail_title,
                              mail_message: mail_message,
                              tablename:tablename,
                              url:url,
                              result:result,
                              page:page
                          },
                      })
                          .done(function (response) {
                              if (response.status != "success"){
                                  Materialize.toast('Error[AdminLogs]. Please Call Your IT Personnal.', 4000, 'toast-success');
                              }
                          })
                          .fail(function () {
                              Materialize.toast('Fail[AdminLogs]. Please Call Your IT Personnal.', 4000, 'toast-success');
                          })
                          .always(function () {
                              $("#customer-contact-modal .modal-content").waitMe("hide");
                          });
                  })
                  .always(function () {
                      $("#customer-contact-modal .modal-content").waitMe("hide");
                  });
          });

          $(document).on("change", "#mailCheck", function() {
              let bookingID = $('#bookingID').val();
              $.ajax({
                  url: '/checkMailForCustomer',
                  type: 'POST',
                  dataType: 'json',
                  data: {
                      _token: "{{csrf_token()}}",
                      bookingID: bookingID
                  },
              })
                  .done(function (response) {
                      if(response.status) {
                          $('.contactModal').click();
                          setTimeout(function() {
                              /*
                              $('button[data-target="#customer-contact-modal"]').each(function() {
                                  let el = $(this);
                                  if(el.attr('data-id') == bookingID)
                                      el.click();
                              });
                               */

                              executeVisibilityChange();
                          }, 1000);
                      }
                  })
                  .fail(function () {
                      Materialize.toast('Fail on the event of check box.', 4000, 'toast-alert');
                  });
          });

          $(document).on("keyup", "#whatsapp-form input[name='whatsapp_to']", function(event) {
              event.preventDefault();
              var whatsapp_message = $('textarea[name="whatsapp_message"]').val();
              var whatsapp_to = $(this).val();
              var fullText = "whatsapp://send?text="+urlencode(whatsapp_message)+"&phone="+whatsapp_to;
              if(fullText.length > 2057){
                Materialize.toast('exceeded the maximum character (2057) limit! current character size = '+fullText.length, 4000, 'toast-alert');
                $('textarea[name="whatsapp_message"]').css("background-color","#F5B7B1");
              }else{
                 $('textarea[name="whatsapp_message"]').css("background-color","aliceblue");
              }
              $(".send-whatsapp-message-button").attr("href", fullText);
              $(".character-length").html("Character "+fullText.length);
          });




        $(document).on('keyup', 'textarea[name="whatsapp_message"]', function(event) {
            event.preventDefault();

            var whatsapp_to = $("#whatsapp-form input[name='whatsapp_to']").val();
            var whatsapp_message = $(this).val();
            var fullText = "whatsapp://send?text="+urlencode(whatsapp_message)+"&phone="+whatsapp_to;

            if(fullText.length > 2057){
                Materialize.toast('exceeded the maximum character (2057) limit! current character size = '+fullText.length, 4000, 'toast-alert');
                $('textarea[name="whatsapp_message"]').css("background-color","#F5B7B1");
              }else{
                $('textarea[name="whatsapp_message"]').css("background-color","aliceblue");
              }
            $(".send-whatsapp-message-button").attr("href", fullText);
            $(".character-length").html("Character "+fullText.length);
            //document.getElementById("send-whatsapp-message-button").click();
            //$("#send-whatsapp-message-button").click();


            //window.open(`whatsapp://send?text=${window.encodeURIComponent(whatsapp_message)}`, '_blank');
        });



   // booking customer-conact modal  operations

        $(document).on('click', '.fire-booking-customer-contact-button', function(event) {
         event.preventDefault();
         let $this = $(this);
         let booking_id = $this.attr("data-id");
         let action = 'insert_layout_for_customer-contact_modal';
         let token = "{{csrf_token()}}";
         $("#customer-contact-modal .modal-content").empty();


         $.ajax({
             url: '{{url("booking/ajax")}}',
             type: 'POST',
             dataType: 'json',
             data: {action: action, booking_id: booking_id, _token: token},
         })
         .done(function(response) {
            $("#customer-contact-modal .modal-content").html(response.view);
         })
         .fail(function() {
             console.log("error");
         })
         .always(function() {
             console.log("complete");
         });



     });














    $(document).on("submit","#booking-extra-file-import-form",function(e){

        e.preventDefault();
        var formData = new FormData(this);




            $("#file-import-modal .modal-content").waitMe({
                    effect : 'bounce',
                    text : 'Loading Files...',
                    bg : 'rgba(255,255,255,0.7)',
                    color : '#000',
                    maxSize : '',
                    waitTime : -1,
                    textPos : 'vertical',
                    fontSize : '',
                    source : '',
                    onClose : function() {}
                });

        $.ajax({
            url: "{{ url('/bookings-extra-file-import') }}",
            type: 'POST',
            dataType: 'json',
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        })
        .done(function(response) {
           if(response.status == "success"){
            Materialize.toast(response.message, 4000, 'toast-success');

            $("#booking-extra-file-import-form input[name='filename[]']").val('');
            $("#booking-extra-file-import-form input[name='file[]']").val('');
           }else{
            Materialize.toast(response.message, 4000, 'toast-alert');
           }

        })
        .fail(function() {

            Materialize.toast('An Error Occurred , Files Cant Loading!', 4000, 'toast-alert');
        })
        .always(function() {
           $("#file-import-modal .modal-content").waitMe('hide');
        });





    });







        $(document).on('click', '.remove-unload-item', function(event) {
            event.preventDefault();

            if($("#booking-extra-file-import-form .modal-body .form-group").length <= 1){
                return false;
            }

            $(this).closest(".form-group").remove();

        });


    $(document).on('click', '#booking-extra-file-import-form .plus-button', function(event) {
        event.preventDefault();

         var firstGroup = $("#booking-extra-file-import-form .modal-body .form-group").eq(-1).clone(true);
         firstGroup = firstGroup.find("input[type='file']").val('').end();
         $("#booking-extra-file-import-form .modal-body").append(firstGroup);

    });





        $(document).on('click', '.delete-booking-extra-file', function(event) {
            event.preventDefault();
            if(!confirm("Are You Sure!")){
                return false;
            }

            let $this = $(this);
            let file_id = $this.attr("data-id");
            let action = "delete_extra_booking_file";
            let token = "{{csrf_token()}}";

             $.ajax({
                 url: '{{url("booking/ajax")}}',
                 type: 'POST',
                 dataType: 'json',
                 data: {file_id: file_id, action: action, _token: token},
             })
             .done(function(response) {
                 if(response.status){
                  $this.closest('.extra-image-wrap').fadeOut(400, function(){
                    $(this).remove();
                  })
                 }else{
                  Materialize.toast('An Error Occurred , File Cant delete!', 4000, 'toast-alert');
                 }
             })
             .fail(function() {
                 console.log("error");
                 Materialize.toast('An Error Occurred!', 4000, 'toast-alert');
             })
             .always(function() {
                 console.log("complete");
             });

        });



     $(document).on('click', '.fire-booking-file-import-button', function(event) {
         event.preventDefault();
         let $this = $(this);
         let booking_id = $this.attr("data-id");
         let action = 'insert_layout_for_extra_booking_files_modal';
         let token = "{{csrf_token()}}";
         $("#file-import-modal .modal-content").empty();


         $.ajax({
             url: '{{url("booking/ajax")}}',
             type: 'POST',
             dataType: 'json',
             data: {action: action, booking_id: booking_id, _token: token},
         })
         .done(function(response) {
            $("#file-import-modal .modal-content").html(response.view);
         })
         .fail(function() {
             console.log("error");
         })
         .always(function() {
             console.log("complete");
         });



     });




      $(document).on('click', '.invoice-check', function(event) {
           event.preventDefault();
         let $this = $(this);
         let booking_id = $this.attr("data-id");
         let action = 'insert_layout_for_invoice_number_modal';
         let token = "{{csrf_token()}}";
         $("#file-invoice-modal .modal-content").empty();


         $.ajax({
             url: '{{url("booking/ajax")}}',
             type: 'POST',
             dataType: 'json',
             data: {action: action, booking_id: booking_id, _token: token},
         })
         .done(function(response) {
            $("#file-invoice-modal .modal-content").html(response.view);
         })
         .fail(function(xhr) {
             console.log(xhr);
         })
         .always(function() {
             console.log("complete");
         });

       });

        $(document).on('change', '#input-file-invoice-part', function(e) {
            event.preventDefault();
            var booking_id = $(this).attr("data-id");
            var action = 'insert_layout_for_invoice_number_modal_new_file';
            var token = "{{csrf_token()}}";
            $("#file-invoice-modal .modal-content").empty();


            const file = e.target.files[0];
            var fileName=file.name;
            var fileTypeStr=file.type;
            var fileType=0;

            if (fileTypeStr=="image/jpeg" || fileTypeStr=="image/png" || fileTypeStr=="image/webp" || fileTypeStr=="application/pdf") {
                switch (fileTypeStr) {
                    case 'image/jpeg':
                        fileType=11;
                        break;
                    case 'image/png':
                        fileType=12;
                        break;
                    case 'image/webp':
                        fileType=13;
                        break;
                    case 'application/pdf':
                        fileType=21;
                        break;
                }
                const reader = new FileReader();

                reader.onloadend = () => {
                    // log to console
                    // logs data:<type>;base64,wL2dvYWwgbW9yZ...
                    var raw_file = reader.result;

                    $.ajax(
                        {
                        url: '{{url("booking/ajax")}}',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: action,
                            booking_id: booking_id,
                            _token: token,
                            raw_file: raw_file,
                            fileName: fileName,
                            fileType: fileType,
                        },
                    })
                        .done(function (response) {
                            if(response.error){
                                Materialize.toast(response.error, 4000, 'toast-alert');
                            }else{
                                $("#file-invoice-modal .modal-content").html(response.view);
                            }

                        })
                        .fail(function (xhr) {
                            console.log(xhr);
                        })
                        .always(function () {
                            console.log("complete");
                        });


                };
                reader.readAsDataURL(file);
            }else{
                Materialize.toast('You can not upload this('+(file.type).split('/')[1]+') type file. Allowable types jpeg,png,webp,pdf', 4000, 'toast-alert');

            }



        });

        $(document).on('click', '.add-booking-invoice-file-part-btn', function(file) {
            event.preventDefault();
            $('#input-file-invoice-part').click();
        });

      $(document).on('click', '.add-booking-invoice-part', function(event) {
          event.preventDefault();


          let invoice_clone = $(".live-wrap .form-group").eq(0).clone(true);
          $(".live-wrap").append(invoice_clone);
      });


       $(document).on('click', '.remove-booking-invoice-part', function(event) {
          event.preventDefault();
          if($(".live-wrap .form-group").length > 1){
            let invoice_clone = $(".live-wrap .form-group").eq(-1).remove();
          }


      });


        $(document).on('click', '.send-booking-invoice-form-button', function(event) {
          event.preventDefault();

          $("#booking-extra-invoice-number-import-form").submit();
          Materialize.toast('Invoice File/Number  added Successfully!', 4000, 'toast-success');

      });


        $(document).on('click', '.remove-booking-old-invoice', function(event) {
            event.preventDefault();

            if(!confirm("Are You Sure ?")){
                return false;
            }

            let $this = $(this);
            let invoice_id = $this.attr("data-id");
            let action = "delete_booking_invoice";
            let token = "{{csrf_token()}}";

            $.ajax({
                url: '{{url("booking/ajax")}}',
                type: 'POST',
                dataType: 'json',
                data: {invoice_id: invoice_id, action: action, _token: token},
            })
            .done(function(response) {
                if(response.status){
                    $this.closest('.form-group').fadeOut(400, function(){
                        $(this).remove();
                    })
                    Materialize.toast(response.success, 4000, 'toast-success');

                }else{
                    Materialize.toast(response.error, 4000, 'toast-alert');
                }
            })
            .fail(function() {
                console.log("error");
                Materialize.toast('An Error Occurred , Invoice Number Cant delete!', 4000, 'toast-alert');
            })
            .always(function() {
                console.log("complete");
            });

        });


        $(document).on('click', '.remove-booking-live-invoice', function(event) {
            event.preventDefault();

            if($(".live-wrap .form-group").length > 1){
                $(this).closest('.form-group').fadeOut(400, function() {
                    $(this).remove();
                });
            }
        });




        let dtBooking = createDTBooking();

        // $(function() {
        //
        //     var start = moment().subtract(29, 'days');
        //     var end = moment();
        //
        //     function cb(start, end) {
        //         $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        //     }
        //
        //     $('#reportrange').daterangepicker({
        //         startDate: start,
        //         endDate: end,
        //         ranges: {
        //             'Today': [moment(), moment()],
        //             'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        //             'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        //             'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        //             'This Month': [moment().startOf('month'), moment().endOf('month')],
        //             'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        //         }
        //     }, cb);
        //
        //     cb(start, end);
        //
        // });

        $('#applyFiltersButton').on('click', function() {

            var platforms=$('#selectInputs').val();

            var bookingStatus=$('#bookingStatus').val();
            let payment_supplier = $('#payment_supplier').val();
            let payment_affiliate = $('#payment_affiliate').val();
            let paymentMethod = $('#paymentMethod').val();
            let commissioner = $('#commissioner').val();

            let from = $('.datepicker-from').val();
            let to = $('.datepicker-to').val();
            let cFrom = $('.c-datepicker-from').val();
            let cTo = $('.c-datepicker-to').val();

            let approvedBookings = jQuery.inArray('approvedBookings',bookingStatus) <0 ? 0:1;
            let pendingBookings = jQuery.inArray('pendingBookings',bookingStatus) <0 ? 0:1;
            let cancelledBookings = jQuery.inArray('cancelledBookings',bookingStatus) <0 ? 0:1;

            let selectedProduct = $('#productSelect').val();
            let selectedOption = $('#optionSelect').val();
            let selectedRestaurant = $('#restaurantSelect').val() ?? null;
            let withImported = $('#withImported').val();
            console.log(withImported);

            if(selectedProduct.length > 0) {
                if(selectedProduct.length == 1 && selectedProduct[0] == -1) {
                    // Do nothing
                } else {
                    if(selectedOption.length == 0) {
                        Materialize.toast('You have chosen product, option selection is required!', 4000, 'toast-alert');
                        return false;
                    }
                }
            }

            if ((from === '' && to !== '') || (to === '' && from !== '')) {
                Materialize.toast('You have to select both from and to fields!', 4000, 'toast-alert');
                return;
            }

            let filters = {
                payment_supplier: payment_supplier,
                payment_affiliate: payment_affiliate,
                from: from,
                to: to,
                cFrom: cFrom,
                cTo: cTo,
                platforms:platforms,
                paymentMethod: paymentMethod,
                commissioner: commissioner,
                approvedBookings: approvedBookings,
                pendingBookings: pendingBookings,
                cancelledBookings: cancelledBookings,
                selectedOption: selectedOption,
                selectedRestaurant: selectedRestaurant,
                isFilter:true,
                withImported: withImported,
            };
            dtBooking.destroy();
            dtBooking = createDTBooking(filters);
            $('#datatable_filter').hide();
        });

        $('#clearFiltersButton').on('click', function() {
            dtBooking.destroy();
            dtBooking = createDTBooking();
            $('.datepicker-from').val('');
            $('.datepicker-to').val('');
            $('.c-datepicker-from').val('');
            $('.c-datepicker-to').val('');
            $('.advancedSearch').val('');

            $('.sm-btn-danger').click();
            $('#datatable_filter').hide();

            $('.mdb-select').material_select('destroy');
            $('#bookingStatus').html('');
            $('#bookingStatus').append(' <option selected value="approvedBookings">Approved</option>');
            $('#bookingStatus').append(' <option selected value="pendingBookings">Pending</option>');
            $('#bookingStatus').append(' <option selected value="cancelledBookings">Cancelled</option>');
            $('.mdb-select').material_select();


            let approvedBookings = $('#approvedBookings');
            let pendingBookings = $('#pendingBookings');
            let cancelledBookings = $('#cancelledBookings');

            if (approvedBookings.val() === '0') approvedBookings.click();
            if (pendingBookings.val() === '0') pendingBookings.click();
            if (cancelledBookings.val() === '0') cancelledBookings.click();

            $('#payment_supplier').val(null).trigger('change');
            $('#payment_affiliate').val(null).trigger('change');
            $('#paymentMethod').val(null).trigger('change');
            $('#commissioner').val(0).trigger('change');
            $('#productSelect').val(-1).trigger('change');
            $('#optionSelect').empty();
            $('#restaurantSelect').val([]).trigger('change');
            $('.select2-selection__choice').hide();

            $('#productSelect').next().parent().css('height', '32px');
            $('#optionSelect').next().parent().css('height', '32px');
            $('#restaurantSelect').next().parent().css('height', '32px');
        });
        $('#datatable_filter').hide();

        $('.advancedSearch').on('keyup', function () {
            var t = $(this);
            var value = t.val();
            var searchVal = '';
            var id = t.attr('id');
            searchVal = value + '-' + id + '-';
            setTimeout(function(){
                $('#datatable').DataTable().search(searchVal).draw();
            }, 250);

            $('.advancedSearch').each(function () {
                if ($(this).attr('id') != id) {
                    $(this).val('');
                }
            });
        });

        function createDTBooking(filters = {}) {
            let from = typeof filters.from === 'undefined' ? '' : filters.from;
            let to  = typeof filters.to === 'undefined' ? '' : filters.to;
            let cFrom = typeof filters.cFrom === 'undefined' ? '' : filters.cFrom;
            let cTo  = typeof filters.cTo === 'undefined' ? '' : filters.cTo;

            let platforms=filters.platforms;
            let payment_supplier = filters.payment_supplier;
            let payment_affiliate = filters.payment_affiliate;
            let paymentMethod = filters.paymentMethod;
            let commissioner = filters.commissioner;
            let approvedBookings = filters.approvedBookings;
            let pendingBookings = filters.pendingBookings;
            let cancelledBookings = filters.cancelledBookings;
            let selectedOption = filters.selectedOption;
            let selectedRestaurant = filters.selectedRestaurant;
            let withImported = filters.withImported;
            let isFilter = typeof filters.isFilter === 'undefined' ? false : filters.isFilter;
            let dtBooking = $('#datatable').DataTable({
                dom: 'Bpifrtip',
                lengthMenu: [
                    [ 10, 25, 50, -1 ],
                    [ '10 rows', '25 rows', '50 rows', 'Show all' ]
                ],
                buttons: [
                    'copy', 'pdf', 'print', 'pageLength'
                ],
                "ordering": true,
                'order' : [],
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                    'url': '/getRowsForDataTable',
                    'data': {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        model: 'booking',
                        from: from,
                        to: to,
                        cFrom: cFrom,
                        cTo: cTo,
                        platforms:platforms,
                        payment_supplier: payment_supplier,
                        payment_affiliate: payment_affiliate,
                        paymentMethod: paymentMethod,
                        commissioner: commissioner,
                        approvedBookings: approvedBookings,
                        pendingBookings: pendingBookings,
                        cancelledBookings: cancelledBookings,
                        selectedOption: selectedOption,
                        selectedRestaurant: selectedRestaurant,
                        isFilter: isFilter,
                        withImported
                    },
                },
                'columns': [
                    { data: 'date', name: 'date', orderable: true },
                    { data: 'tour', name: 'tour', orderable: false },
                    { data: 'bookingRef', name: 'bookingRef', orderable: false },
                    { data: 'status', name: 'status', orderable: false },
                    { data: 'rCode', name: 'rCode', orderable: false },
                    { data: 'salesInformations', name: 'salesInformations', orderable: false },
                    { data: 'more', name: 'more', orderable: false }
                ],
                 "createdRow": function( row, data, dataIndex ) {
                    if ( data["invoice_status"]  ) {
                      $(row).addClass( 'tr-danger' );
                    }else{

                      $(row).addClass( 'tr-success' );

                    }
                 },
            });
            return dtBooking;
        }
        $(function() {
            $('.select2-selection__choice').hide();
            $('#productSelect').on('select2:select', function (e) {
              $('.select2-selection__rendered').children('li:first').remove();
            });
        });
        @elseif($page == 'errorlogs-index')
        $('#datatable').dataTable({
            dom: 'Bfrtip',
            lengthMenu: [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
            ],
            "ordering": false,
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'ajax': {
                'url': '/getRowsForDataTable',
                'data': {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    model: 'errorLog',
                },
            },
            'columns': [
                { data: 'url', name: 'url' },
                { data: 'code', name: 'code' },
                { data: 'file', name: 'file' },
                { data: 'line', name: 'line' },
                { data: 'message', name: 'line' },
                { data: 'dateTime', name: 'dateTime' },
            ]
        });
        @elseif($page == 'apilogs-index')

        let dtApilog = createDTApilog();

        $('#applyFiltersButton').on('click', function() {
            let getAvailabilities = $('#getAvailabilities').val();
            let reserve = $('#reserve').val();
            let cancelReservation = $('#cancelReservation').val();
            let book = $('#book').val();
            let cancelBooking = $('#cancelBooking').val();
            let notifyPush = $('#notifyPush').val();
            let filters = {
                getAvailabilities: getAvailabilities,
                reserve: reserve,
                cancelReservation: cancelReservation,
                book: book,
                cancelBooking: cancelBooking,
                notifyPush: notifyPush
            };
            dtApilog.destroy();
            dtApilog = createDTApilog(filters);
        });

        $('#clearFiltersButton').on('click', function() {
            dtApilog.destroy();
            dtApilog = createDTApilog();
            let getAvailabilities = $('#getAvailabilities');
            let reserve = $('#reserve');
            let cancelReservation = $('#cancelReservation');
            let book = $('#book');
            let cancelBooking = $('#cancelBooking');
            let notifyPush = $('#notifyPush');
            if (getAvailabilities.val() === '0') {
                getAvailabilities.click();
            }
            if (reserve.val() === '0') {
                reserve.click();
            }
            if (cancelReservation.val() === '0') {
                cancelReservation.click();
            }
            if (book.val() === '0') {
                book.click();
            }
            if (cancelBooking.val() === '0') {
                cancelBooking.click();
            }
            if (notifyPush.val() === '0') {
                notifyPush.click();
            }
        });

        function createDTApilog(filters = {}) {
            let getAvailabilities = filters.getAvailabilities;
            let reserve = filters.reserve;
            let cancelReservation = filters.cancelReservation;
            let book = filters.book;
            let cancelBooking = filters.cancelBooking;
            let notifyPush = filters.notifyPush;
            let dtApilog = $('#datatable').DataTable({
                dom: 'Bfrtip',
                lengthMenu: [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
                ],
                "ordering": false,
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                    'url': '/getRowsForDataTable',
                    'data': {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        model: 'apilog',
                        getAvailabilities: getAvailabilities,
                        reserve: reserve,
                        cancelReservation: cancelReservation,
                        book: book,
                        cancelBooking: cancelBooking,
                        notifyPush: notifyPush
                    }
                },
                'columns': [
                    {data: 'requestType', name: 'requestType'},
                    {data: 'query', name: 'query'},
                    {data: 'request', name: 'request'},
                    {data: 'optionRefCode', name: 'optionRefCode'},
                    {data: 'requestTime', name: 'requestTime'},
                ],
            });
            return dtApilog;
        }
        @elseif($page == 'userlogs-index')
        $('#datatable').dataTable({
            dom: 'Bfrtip',
            lengthMenu: [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
            ],
            "ordering": false,
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'ajax': {
                'url': '/getRowsForDataTable',
                'data': {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    model: 'adminlog',
                }
            },
            'columns': [
                { data: 'name', name: 'name' },
                { data: 'productRefCode', name: 'productRefCode' },
                { data: 'optionRefCode', name: 'optionRefCode' },
                { data: 'page', name: 'page' },
                { data: 'action', name: 'action' },
                { data: 'details', name: 'details' },
                { data: 'date', name: 'date' },
            ]
        });
          @elseif($page == 'meetinglogs-index')
        $('#datatable').dataTable({
            dom: 'Bfrtip',
            lengthMenu: [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
            ],
            "ordering": false,
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'ajax': {
                'url': '/getRowsForDataTable',
                'data': {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    model: 'meetinglog',
                }
            },
            'columns': [
                { data: 'processID', name: 'processID' },
                { data: 'meeting-date', name: 'meeting-date' },
                { data: 'meeting-time', name: 'meeting-time' },
                { data: 'meeting-option', name: 'meeting-option' },
                { data: 'logger_id', name: 'logger_id' },
                { data: 'logger_email', name: 'logger_email' },
                { data: 'affected_name', name: 'affected_name' },
                { data: 'action', name: 'action' },
                { data: 'date', name: 'date' },
            ]
        });


                @elseif($page == 'customerlogs-index')
        $('#datatable').dataTable({
            dom: 'Bfrtip',
            lengthMenu: [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
            ],
            "ordering": false,
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'ajax': {
                'url': '/getRowsForDataTable',
                'data': {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    model: 'customerlog',
                }
            },
            'columns': [
                { data: 'processID', name: 'processID' },
                { data: 'bookingID', name: 'bookingID' },
                { data: 'referenceCode', name: 'referenceCode' },
                { data: 'customerEmail', name: 'customerEmail' },
                { data: 'customerName', name: 'customerName' },
                { data: 'option', name: 'option' },
                { data: 'action', name: 'action' },
                { data: 'date', name: 'date' },
                { data: 'total', name: 'total' }
            ]
        });

        $(document).on('click', '.fetchCustomerLogs', function () {
            let customerEmail = $(this).attr('data-email');
            $.ajax({
                method: 'GET',
                url: '/fetchCustomerLogs',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    customerEmail: customerEmail
                },
                success: function(data) {
                    let customerLogs = data.data;
                    $('#customerLogsModal .modal-body').html('');
                    let modalBody = "";
                    if(customerLogs.length > 0) {
                        modalBody += '<table id="modalTable"><tr>';
                        modalBody += '<th>Process ID</th>';
                        modalBody += '<th>Booking ID</th>';
                        modalBody += '<th>Reference Number</th>';
                        modalBody += '<th>Customer Email</th>';
                        modalBody += '<th>Customer Name</th>';
                        modalBody += '<th>Option</th>';
                        modalBody += '<th>Action</th>';
                        modalBody += '<th>Date</th>';
                        modalBody += '</tr>';
                        for(let i=0; i<customerLogs.length; i++) {
                            modalBody += '<tr>';
                            modalBody += '<td>' + customerLogs[i]["id"] + '</td>';
                            modalBody += '<td>' + customerLogs[i]["booking_id"] + '</td>';
                            modalBody += '<td>' + customerLogs[i]["referenceCode"] + '</td>';
                            modalBody += '<td>' + customerLogs[i]["customerEmail"] + '</td>';
                            modalBody += '<td>' + customerLogs[i]["customerName"] + '</td>';
                            modalBody += '<td>' + customerLogs[i]["option"] + '</td>';
                            modalBody += '<td>' + customerLogs[i]["action"] + '</td>';
                            modalBody += '<td>' + customerLogs[i]["created_at"] + '</td>';
                            modalBody += '</tr>';
                        }
                        modalBody += '</table>';
                    } else {
                        modalBody += '<p><strong>Customer logs not found</strong></p>';
                    }
                    $('#customerLogsModal .modal-body').append(modalBody);

                    if(customerLogs.length > 0) {
                        $('#modalTable th').css({"color": "#333D87", "font-weight": "bold"});
                        $('#modalTable, #modalTable th, #modalTable td').css("border", "1px solid #ededed");
                    }
                },
                error: function(t) {

                    Materialize.toast(t.error, 5000, 'toast-alert');
                }
            });
        });

        @elseif($page == 'barcodes-index')


         $(document).on('click', '.log-info', function(event) {
             event.preventDefault();
             var log = $(this).data("log");
             var html = "";
             var decodedLog = log;

             decodedLog.reverse().forEach(function(element, index){

            html += "<div style='background-color: #f2f2f2; padding: 7px 14px; margin-top:15px;' class='log-wrap'>";

             html += "<p>bknCode: "+(typeof element.bknCode !== 'undefined' ? element.bknCode : '-')+"</p>";
             html += "<p>oldBookingID: "+element.oldBookingID+"</p>";
             html += "<p>cancelReason: "+element.cancelReason+"</p>";
             html += "<p>cancelBy: "+element.cancelBy+"</p>";
             html += "<p>cancelDate: "+element.cancelDate+"</p>";



             html += "</div>";


             });





             $("#logModal .modal-body").html('');
             $("#logModal .modal-body").append(html);


         });


        $('#datatable').dataTable({
            dom: 'Bfrtip',
            lengthMenu: [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
            ],
            "ordering": false,
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'ajax': {
                'url': '/getRowsForDataTable',
                'data': {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    model: 'barcode',
                }
            },
            'columns': [
                { data: 'barcode', name: 'barcode' },
                { data: 'barcodeType', name: 'barcodeType' },
                { data: 'status', name: 'status' },
                { data: 'endtime', name: 'endtime' },
                { data: 'description', name: 'description' },
                { data: 'usedDate', name: 'usedDate' },
                { data: 'actions', name: 'actions' },
                { data: 'info', name: 'info' },

            ],
            rowCallback: function(row, data) {


                if(data.isExpired == "1"){
                    $(row).addClass("danger");

                    $('.toggle-class-isUsed', row).bootstrapToggle().prop("disabled", true);
                }else{
                    $('.toggle-class-isUsed', row).bootstrapToggle();
                }
            }
        });

        @elseif($page == 'producttranslations')

        $('#datatable').dataTable({

            "ordering": false,
            dom: 'Bfrtip',
            "displayStart": (pageID*10)-10,
            lengthMenu: [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
            ],

        });
        $(document).on('click', '.paginate_button a', function() {
            $('#isRun').val('0');
            console.log('s1')
            let pageID = parseInt($('#pageID').val($(this).text()));
        });


        @elseif($page == 'attractiontranslations')

        $('#datatable').dataTable({

            "ordering": false,
            dom: 'Bfrtip',
            "displayStart": (pageID*10)-10,
            lengthMenu: [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
            ],

        });
        $(document).on('click', '.paginate_button a', function() {
            console.log('s2')

            $('#isRun').val('0');
            let pageID = parseInt($('#pageID').val($(this).text()));
        });




        @elseif($page == 'optiontranslations')

        $('#datatable').dataTable({

            "ordering": false,
            dom: 'Bfrtip',
            "displayStart": (pageID*10)-10,
            lengthMenu: [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
            ],

        });
        $(document).on('click', '.paginate_button a', function() {
            console.log('s3')

            $('#isRun').val('0');
            let pageID = parseInt($('#pageID').val($(this).text()));
        });

        @elseif($page == 'prodmetatagstrans')
        $('#datatable').dataTable({

            "ordering": false,
            dom: 'Bfrtip',
            "displayStart": (pageID*10)-10,
            lengthMenu: [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
            ],

        });
        $('.paginate_button a').on('click', function() {
            $('#isRun').val('0');
            let pageID = parseInt($('#pageID').val($(this).text()));
        });


        @elseif($page == 'pagemetatagstrans')
        $('#datatable').dataTable({

            "ordering": false,
            dom: 'Bfrtip',
            "displayStart": (pageID*10)-10,
            lengthMenu: [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
            ],

        });
        $(document).on('click', '.paginate_button a',  function() {
            console.log('s4')

            $('#isRun').val('0');
            let pageID = parseInt($('#pageID').val($(this).text()));
        });

        @else
        $('#datatable').dataTable({
            "ordering": false,
            dom: 'Bfrtip',
            lengthMenu: [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
            ],

        });
        @endif




        $('body').on('click', '.seoButtonForProduct', function() {
            var cloneMetaTagDiv = $(this).parent().parent().find('.metaTagDiv').clone();
            var targetTR = $(this).closest('tr');
            targetTR.after("<tr class='virtual-tr'><td colspan='10'></td></tr>");
            targetTR.next("tr.virtual-tr").find("td").html(cloneMetaTagDiv);
            cloneMetaTagDiv.slideToggle();

             $(this).hide();
            $(this).parent().parent().find('.metaTagCloseButton').show();

            /* $(this).parent().parent().find('.metaTagDiv').show();
            $(this).hide();
            $(this).parent().parent().find('.metaTagCloseButton').show();*/
        });

        $('body').on('click', '.metaTagCloseButton', function() {

            var targetTR = $(this).closest('tr');
            targetTR.next("tr.virtual-tr").find(".metaTagDiv").slideToggle(400, function(){
             targetTR.next("tr.virtual-tr").remove();
            });

            $(this).hide();
            $(this).parent().find('.seoButtonForProduct').show();



          /*  $(this).parent().parent().find('.metaTagDiv').hide();
            $(this).hide();
            $(this).parent().find('.seoButtonForProduct').show();*/
        });

        $(document).on('keyup', '.metaTagTitle', function() {
            var data = $(this).val();
            $(this).closest('tr.virtual-tr').prev("tr").find(".metaTagTitle").val(data);

            $(this).closest('tr.virtual-tr').prev("tr").find('.metaTagSaveButton').show();
            $(this).closest('tr.virtual-tr').prev("tr").find('.seoButtonForProduct').hide();
            $(this).closest('tr.virtual-tr').prev("tr").find('.metaTagCloseButton').hide();


         /*  $(this).parent().parent().find('.metaTagSaveButton').show();
            $(this).parent().parent().find('.seoButtonForProduct').hide();
            $(this).parent().parent().find('.metaTagCloseButton').hide();*/
        });

        $(document).on('keyup', '.metaTagDescription', function() {
                 var data = $(this).val();
                 $(this).closest('tr.virtual-tr').prev("tr").find(".metaTagDescription").val(data);

            $(this).closest('tr.virtual-tr').prev("tr").find('.metaTagSaveButton').show();
            $(this).closest('tr.virtual-tr').prev("tr").find('.seoButtonForProduct').hide();
            $(this).closest('tr.virtual-tr').prev("tr").find('.metaTagCloseButton').hide();

         /*   $(this).parent().parent().find('.metaTagSaveButton').show();
            $(this).parent().parent().find('.seoButtonForProduct').hide();
            $(this).parent().parent().find('.metaTagCloseButton').hide();*/
        });

        $(document).on('keyup', '.metaTagKeywords', function() {

            var data = $(this).val();
            $(this).closest('tr.virtual-tr').prev("tr").find(".metaTagKeywords").val(data);

           $(this).closest('tr.virtual-tr').prev("tr").find('.metaTagSaveButton').show();
            $(this).closest('tr.virtual-tr').prev("tr").find('.seoButtonForProduct').hide();
            $(this).closest('tr.virtual-tr').prev("tr").find('.metaTagCloseButton').hide();



        /*    $(this).parent().parent().find('.metaTagSaveButton').show();
            $(this).parent().parent().find('.seoButtonForProduct').hide();
            $(this).parent().parent().find('.metaTagCloseButton').hide();*/
        });

        $('body').on('click', '.metaTagSaveButton', function() {
            let productType = $('#productType').val();
            let saveButton = $(this);
            let metaTagDiv = $(this).closest('tr').next("tr.virtual-tr").find('.metaTagDiv');
            let productID =  $(this).attr('data-id');
            let seoButton = $(this).parent().find('.seoButtonForProduct');


          /*  let metaTagTitle = $(this).parent().parent().find('.metaTagDiv .metaTagTitle').val();
            let metaTagDescription = $(this).parent().parent().find('.metaTagDiv .metaTagDescription').val();
            let metaTagKeywords = $(this).parent().parent().find('.metaTagDiv .metaTagKeywords').val();*/

            let metaTagTitle = $(this).closest('tr').next("tr.virtual-tr").find('.metaTagDiv .metaTagTitle').val();
            let metaTagDescription = $(this).closest('tr').next("tr.virtual-tr").find('.metaTagDiv .metaTagDescription').val();
            let metaTagKeywords = $(this).closest('tr').next("tr.virtual-tr").find('.metaTagDiv .metaTagKeywords').val();

            //console.log(metaTagTitle+ "\n"+metaTagDescription+"\n"+metaTagKeywords);
            //return false;
            if (metaTagTitle === '' || metaTagDescription === '' || metaTagKeywords === '') {
                Materialize.toast('Please enter all values correctly', 4000, 'toast-alert');
                metaTagDiv.hide();
                saveButton.hide();
                seoButton.show();
            } else {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: '/metaTagSave',
                    data: {
                        _token: '<?=csrf_token()?>',
                        productID: productID,
                        metaTagTitle: metaTagTitle,
                        metaTagDescription: metaTagDescription,
                        metaTagKeywords: metaTagKeywords,
                        productType: productType
                    },
                    success: function(data) {
                        let product = data.product;
                        Materialize.toast('Meta tags has been added successfully for ' + product.referenceCode, 4000, 'toast-success');
                        saveButton.hide();
                        seoButton.show();
                        metaTagDiv.hide();
                    },
                    error: function(data) {

                    }
                });
            }
        });
    });

</script>
@if($page == 'supplier-index')
<script>

    $('#supplierSelect').on('select2:opening select2:closing', function( event ) {
        var $searchfield = $(this).parent().find('.select2-search__field');
        $searchfield.prop('disabled', true);
    }).select2({
        placeholder: 'Select an option',

    });

    function stringMatch(term, candidate) {
        return candidate && candidate.toLowerCase().indexOf(term.toLowerCase()) >= 0;
    }

    function matchCustom(params, data) {
        // If there are no search terms, return all of the data
        if ($.trim(params.term) === '') {
            return data;
        }
        // Do not display the item if there is no 'text'     property
        if (typeof data.text === 'undefined') {
            return null;
        }
        // Match text of option
        if (stringMatch(params.term, data.text)) {
            return data;
        }
        // Match attribute "data-foo" of option
        if (stringMatch(params.term, $(data.element).attr('data-foo'))) {
            return data;
        }
        // Return `null` if the term should not be displayed
        return null;
    }

    function formatCustom(state) {
        let divBlock = '<div><div>' + state.text + '</div><div class="foo">';
        if (typeof $(state.element).attr('data-foo') !== 'undefined') {
            divBlock += $(state.element).attr('data-foo');
        }
        divBlock += '</div></div>';
        return $(divBlock);
    }

    let supplierID;

        $(document).on('click','.remove-modal', function(e) {
            e.preventDefault();
            $.modal.close();
        });

    $('body').on('click', '.modalOpen', function() {
        $('#supplierSelect').html('');
        supplierID = $(this).attr('data-supplier-id');

        $.ajax({
            type: 'POST',
            url: '/supplier/supplierid',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                supplierID: supplierID
            },
            success: function(data) {
                var options = data.options;
                var block = '';
                //console.log(options);
                for (let i = 0; i < options.length; i++) {

                    block += '<option ';
                    if(options[i].rCodeID){
                        const opt = options[i].rCodeID.toString().split(',').some(x => {
                            return x === supplierID;
                        });

                        if(opt) {
                            block += 'selected ';
                        }
                    }
                    block += 'value="'+options[i].id+'">'+options[i].title+'</option>';

                }
                $('#supplierSelect').html(block);
            }
        });
    });

    $('body').on('click', '#sendRestaurants', function() {
        let options = $('#supplierSelect').val();
        $.ajax({
            type: 'POST',
            url: '/supplier/sendRCode',
            data:{
                _token: $('meta[name="csrf-token"]').attr('content'),
                supplierID: supplierID,
                options: options,
            },
            success: function() {
                Materialize.toast("It's successful", 4000, 'toast-success');
            }
        });
    });


    $('#supplierSelect').on("select2:unselect", function(e) {
        let optionID = e.params.data.id;
        $.ajax({
            type: 'POST',
            url: '/supplier/removeOption',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                optionID: optionID,
                supplierID
            },
            success: function(data) {
                if (data.success) {
                    Materialize.toast(data.success, 4000, 'toast-success');
                }
            }
        });
    });
</script>
@endif
