@extends('panel.ticket.app')
@push('customCss')
    <style>
        .modal-body{
            height: 650px;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">{{ __('Dashboard') }}</div>

                    <div class="card-body">
                        <form id="take-ticket">
                            <div class="row">
                                <label for="date" class="col-form-label col-3">Select Date</label>
                                <div class="col-4 d-flex align-items-center">
                                    <input type="text" class="form-control" name="date" placeholder="YYYY-mm-dd (2022-04-13)">
                                </div>
                            </div>

                            <div class="row mt-4">
                                <label class="col-form-label col-3">Date Time</label>
                                <div class="col-4">
                                    <input type="text" name="time" class="form-control" placeholder="HH:ii (12:00) ">
                                </div>
                            </div>

                            <div class="row mt-4">
                                <label class="col-form-label col-3">Quantity</label>
                                <div class="col-4">
                                    <input type="text" name="quantity" class="form-control" >
                                </div>
                            </div>

                            <div class="row justify-content-end my-3">
                                <div class="col-9">
                                    <button type="submit" class="btn btn-outline-success btn-sm ">Take Ticket</button>
                                </div>
                                <div class="basketId">

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center mt-5">
            <div class="col-md-10">
                <div id="iframe">

                </div>
            </div>
        </div>
    </div>

    <!-- Eiffel Login Modal -->
    <div class="modal fade" id="eiffelLoginModal" tabindex="-1" aria-labelledby="eiffelLoginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eiffelLoginModalLabel">Sign In</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe src="https://ticketpro.toureiffel.paris" allow id="eiffel-iframe" width="100%" height="100%" frameborder="0"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('customScript')
    <script>

        $(document).ready(function () {

            $("#eiffelLoginModal").modal('show');

            $('form').on('submit', function (e) {
                e.preventDefault()

                const form = $(this).serialize(),
                    _token = $('meta[name="csrf-token"]').attr('content');

                if($('input[name="date"]').val() === ''
                    || $('input[name="time"]').val() === ''
                    || $('input[name="quantity"]').val() === ''){
                    alert('All input fields required!')
                    return false;
                }

                $.ajax({
                    url: '{{ route('post.payment') }}',
                    type: 'POST',
                    data: {form, _token},
                    beforeSend: function () {
                        cardWaitMe(false)
                    },
                    success: function (res) {
                        cardWaitMe(true)

                        $('.basketId').html(`<div class="text-center d-block fw-bold"><b>Basket ID: </b> ${res.data.BasketId}</div>`)

                        const iframe = document.createElement('iframe')
                        $('#iframe').html(iframe)
                        iframe.id = 'payment-form'
                        iframe.name = 'nameFrame'
                        iframe.width = '100%'
                        iframe.height = '500'
                        iframe.onload = function () {

                        }

                        setTimeout(() => {
                            $(iframe).contents().find("body").html(res.data.Form);
                        })

                    },
                    error: function (res) {
                        alert(res.data)
                        cardWaitMe(true)
                    }
                })
            })

        });

        function cardWaitMe(close) {
            if (close) {
                $('.card').waitMe('hide')
            } else {
                $('.card').waitMe({
                    effect: 'win8',
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
            }
        }

    </script>
@endpush
