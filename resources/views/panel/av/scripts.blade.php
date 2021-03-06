</body>
<script src="{{asset('js/jquery-latest.min.js')}}"></script>
<script src="{{asset('js/admin/jquery.min.js')}}"></script>
<script src="{{asset('js/admin/bootstrap.min.js')}}"></script>
<script src="{{asset('js/admin/materialize.min.js')}}"></script>
<script src="{{asset('js/admin/custom.js')}}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="{{asset('js/airdatepicker/datepicker.min.js')}}"></script>
<script src="{{asset('js/airdatepicker/datepicker.en.js')}}"></script>
<script src="{{asset('js/inputmask/dist/jquery.inputmask.js')}}"></script>
<script src="{{asset('js/inputmask/dist/bindings/inputmask.binding.js')}}"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

<script>

    // That object is necessary for firing remove minute hour function
    let globalItObject = {
        removeButtonClickedInternal: 0,
        itInternal: $('#avdateCount').val(),
        removeButtonClickedListener: function(val){},
        itListener: function(val){},
        set it(val) {
            this.itInternal = val;
            this.itListener(val);
        },
        get it() {
            return this.itInternal;
        },
        set removeButtonClicked(val) {
            this.removeButtonClickedInternal = val;
            this.removeButtonClickedListener(val);
        },
        get removeButtonClicked() {
            return this.removeButtonClickedInternal;
        },
        registerListener: function(listener) {
            this.itListener = listener;
        }
    };

    function showToggle() {
        if ($('.allDates').hasClass('hide')) {
            $('.allDates').removeClass('hide');
            $('.showToggleButton').text('Hide Dates');

        } else {
            $('.showToggleButton').text('Show Dates');
            $('.allDates').addClass('hide');
        }
    }
    function goMeeting(getHour) {
        var dateRaw=$('#dateLabel').text().split('/');
        var date=dateRaw[2]+'-'+dateRaw[1]+'-'+dateRaw[0];
        let availabilityType = $('#availabilityType').val();
        var hour='00:00:00';
        if (availabilityType !== 'Operating Hours') {
            hour=getHour+':00';
        }
        var dateTime=date+' '+hour;
        sessionStorage.setItem('moveMeetingDate',dateTime);
        window.open('/meeting/index', '_blank');
    }
    $(function() {
        isCheckedLimitless = $("#isLimitlessHidden").val();
        let minDate = $('#minDate').val();
        let maxDate = $('#maxDate').val();

        $('.datepicker-here').datepicker({
            onSelect: function(formattedDate, date, inst) {
                let availabilityId = $('#availabilityId').val();
                let availabilityType = $('#availabilityType').val();
                let dailyOrDateRange = $('input[name="radioDailyOrDateRange"]:checked').val();


                if (formattedDate.includes(',')) {
                    let formattedDate1 = formattedDate.split(',')[0];
                    let formattedDate2 = formattedDate.split(',')[1];
                    let dateToBeShown = formattedDate1 === formattedDate2 ? formattedDate1 : formattedDate1 + ' - ' + formattedDate2;
                    $('#dateLabel').html(dateToBeShown);

                    if (dailyOrDateRange !== 'Date Range') {
                        if (formattedDate1 === formattedDate2) {
                            $('#selectedDate').val(formattedDate1);
                            $('#bulkTicketDiv').hide();
                            $('#hourParentDiv').show();
                            $.ajax({
                                method: 'POST',
                                url : '/av/getAvdates',
                                data : {
                                    _token: $('meta[name="csrf-token"]').attr('content'),
                                    availabilityId: availabilityId,
                                    formattedDate: formattedDate1
                                },
                                success: function(data) {
                                    $('#hourTicketDiv').html('');
                                    $('#disableEnableDiv').hide();
                                    if (availabilityType === 'Starting Time') {
                                        $('#addNewDateTimeAndTicket').show();
                                        $('#regularNewElement').parent().show();
                                    }
                                    if (data.allDateTimes.length > 0) {
                                        let block = '';
                                        block += '<input type="hidden" id="hourTicketRowCount" value="'+data.allDateTimes.length+'">\n';
                                        block += '<table style="overflow: auto;>';
                                        for(let i=0; i<data.allDateTimes.length; i++) {
                                            let ticket = typeof data.allDateTimes[i].ticket === 'undefined' ? 0 : data.allDateTimes[i].ticket;
                                            let hourFrom = data.allDateTimes[i].hourFrom;
                                            let hourTo = data.allDateTimes[i].hourTo;
                                            let isActive = data.allDateTimes[i].isActive;
                                            let onGoingGYG = data.allDateTimes[i].onGoingGYG;
                                            let onGoingCZ = data.allDateTimes[i].onGoingCZ;
                                            let onGoingBKN = data.allDateTimes[i].onGoingBKN;
                                            let sold = data.allDateTimes[i].sold;
                                            let availabilityId = data.allDateTimes[i].availabilityId;
                                            let availabilityTimeIndex = data.allDateTimes[i].availabilityTimeIndex;
                                            let meetingGuides = data.allDateTimes[i].meetingGuides;
                                            let diffCatFromAdult = data.allDateTimes[i].diffCatFromAdult;
                                            if(meetingGuides == "")
                                                meetingGuides = "-";


                                            block += '<thead class="thead-dark">';
                                            block += '<tr>';
                                            block += '<th scope="col">From</th>';
                                            if (availabilityType === 'Operating Hours') {
                                                block += '<th scope="col">To</th>';
                                            }

                                            if(isCheckedLimitless == "0"){

                                                block += '<th scope="col">Ticket</th>';


                                            }

                                            block += '<th scope="col">Save</th>';


                                            block += '<th scope="col">Toggle</th>';
                                            block += '<th scope="col">Remove</th>';
                                            block += '<th scope="col">Sold</th>';
                                            block += '<th scope="col">Cityzore</th>';
                                            block += '<th scope="col">GYG</th>';
                                            block += '<th scope="col">Bokun</th>';
                                            block += '<th scope="col">Bookings</th>';
                                            block += '<th scope="col">On-Going</th>'
                                            block += '<th scope="col">Guide</th>';
                                            block += '<th scope="col">Go-Meeting</th>';
                                            block += '</tr>';
                                            block += '</thead>';
                                            block += '<tbody>';
                                            block += '<tr>';
                                            block += '<td><div id="hourSection">' +
                                                '<div class="hourDivFrom">\n' +
                                                '<input style="margin: 0 30px 20px 0;" type="time" data-type="updated" class="validate form-control col-md-12 s12" id="hour" name="hour[]" value="'+hourFrom+'">\n' +
                                                '<input type="hidden" id="hour-copy" value="'+hourFrom+'">\n' +
                                                '</div>';





                                            if (availabilityType === 'Operating Hours') {
                                                block += '</td>';
                                            }
                                            if (availabilityType === 'Operating Hours') {
                                                block += '<td><div id="hourToSection">\n' +
                                                    '<div class="hourDivTo">\n' +

                                                    '<input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="hourTo" name="hourTo[]" value="'+hourTo+'">\n' +
                                                    '<input type="hidden" id="hourTo-copy" value="'+hourTo+'">\n' +
                                                    '</div>' +
                                                    '</div>\n';

                                                block +=  '</td>';
                                            }
                                            block +=  '</td>';



                                            if(isCheckedLimitless == "0"){

                                                block += '<td>' +
                                                    '<div id="ticketSection">\n' +
                                                    '<div class="ticketDivFrom">\n' +
                                                    '<input style="margin: 0 30px 20px 0;" type="number" data-type="updated" class="validate form-control col-md-12 s12" id="ticket" name="ticket[]" value="'+ticket+'">\n' +
                                                    '</div>\n' +
                                                    '</div>\n'+
                                                    '</td>';


                                            }

                                            block += '<td>' +
                                                '<div class="help-tip hidden-xs hidden-sm">'+
                                                '<p>Save Tickets</p>'+
                                                '</div>'+
                                                '<button class="btn btn-primary" id="saveDateTimeAndTicketButton" style="background: #26a69a;padding: 0 1.5rem;">\n' +
                                                '<i class="icon-cz-floppy"></i>'+
                                                '</button>\n' +
                                                '</td>';
                                            let toggleStatus = 'checked';
                                            if (parseInt(isActive) === 0) {
                                                toggleStatus = '';
                                            }
                                            block += '<td>' +
                                                '<input id="hourToggle" type="checkbox" '+toggleStatus+' data-toggle="toggle" data-style="ios" data-onstyle="success" data-offstyle="danger">\n' +
                                                '</td>';
                                            let removeButton = sold > 0 ? '<button class="btn btn-primary" id="removeDateTimeAndTicketButton" style="padding: 0 1.5rem!important;background: #f4364f;" disabled>x</button>\n' : '<button class="btn btn-primary" id="removeDateTimeAndTicketButton" style="padding: 0 1.5rem!important;background: #f4364f;">x</button>\n';
                                            block += '<td>' +
                                                '<div class="help-tip hidden-xs hidden-sm">'+
                                                '<p>Delete Hour</p>'+
                                                '</div>'+
                                                removeButton +
                                                '</td>';
                                            block += '<td>' +
                                                '<input type="text" id="soldTickets" readonly="" value="'+sold+'">\n' +
                                                '</td>';
                                            block += '<td>' +
                                                '<input type="text" id="czOngoing" readonly="" value="'+onGoingCZ+'">\n' +
                                                '</td>';
                                            block += '<td>' +
                                                '<input type="text" id="gygOngoing" readonly="" value="'+onGoingGYG+'">\n' +
                                                '</td>';
                                            block += '<td>' +
                                                '<input type="text" id="bknOngoing" readonly="" value="'+onGoingBKN+'">\n' +
                                                '</td>';
                                            block += `<td>
                                                <button class="btn btn-primary modalTrigger" id="modalTrigger.${availabilityId}.${availabilityTimeIndex}" style="padding: 0 1.5rem!important;background: ${diffCatFromAdult ? 'rgb(173 117 233)' : 'rgb(38, 166, 154)'};" data-toggle="modal" data-target="#bookingsModal">+</button>
                                            </td>`;
                                            block += '<td>' +
                                                '<button class="btn btn-primary onGoingModalTrigger" style="padding: 0 1.5rem!important;background: rgb(38, 166, 154);" data-toggle="modal" data-target="#onGoingModal">+</button>\n' +
                                                '</td>';
                                            block += '<td>' + meetingGuides + '</td>';
                                            block += '<td><button title="Redirect Meeting Page" onclick="goMeeting(\'' + hourFrom + '\')" class="btn btn-warning moveMeetingDate" style="padding: 0 1.5rem!important;background: rgb(238,167,39)"><i class="icon-cz-rocket"></i></button></td>';
                                            block += '</tr>';
                                            block += '</tbody>';

                                        }
                                        block += '</table>';
                                        $('#hourTicketDiv').append(block);
                                        $('#hourTicketDiv #hourToggle').bootstrapToggle();
                                    } else {
                                        if (availabilityType === 'Operating Hours') {
                                            addHourTicketBlock();
                                        } else {
                                            let block = '<input type="hidden" id="hourTicketRowCount" value="0">\n';
                                            $('#hourTicketDiv').append(block);
                                        }
                                    }
                                    $('#disableEnableDayButton').attr('data-attr', data.isDisabled ? 'enable' : 'disable');
                                    $('#disableEnableDayButton').css('background', data.isDisabled ? '#26a69a' : '#f4364f');
                                    $('#disableEnableDayButton').html(data.isDisabled ? 'Enable Day' : 'Disable Day');
                                    if (data.allDateTimes.length > 0) {
                                        $('#disableEnableDiv').show();
                                    }
                                }
                            });
                        } else {
                            $('#selectedDate').val(formattedDate);
                            $('#bulkDateLabel').html(dateToBeShown);
                            $('#bulkTicketDiv').show();
                            $('#hourParentDiv').hide();
                        }
                    } else {
                        $('#selectedDate').val(formattedDate);
                        $('#bulkDateLabel').html(dateToBeShown);
                        $('#bulkTicketDiv').show();
                        $('#hourParentDiv').hide();
                    }
                }
            },
            //minDate: moment(minDate).isBefore(moment()) ? moment().toDate() : moment(minDate).toDate(), // Bug??n??n tarihinden itibaren g??steriyor
            minDate: moment(minDate).toDate(),
            maxDate: moment(maxDate).toDate(),
            toggleSelected: false,
            beforeShowDay: my_check
        });

        $(document).on('click', '.modalTrigger', function () {
            let element_id = $(this)[0]["id"];
            let element_arr = element_id.split(".");
            let time = $(this).parent().parent().find('.hourDivFrom input[name="hour[]"]').val();
            $('#itemsEl').text("");


            $.ajax({
                method: 'GET',
                url: '/av/getAvBookings',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    avId: element_arr[1],
                    avTimeType: element_arr[2],
                    avTimeIndex: element_arr[3],
                    avTime: time
                },
                success: function(data) {
                    let bookings = data;
                    $('#bookingsModal .modal-body').html('');
                    let modalBody = "";
                    if(bookings.length > 0) {
                        let items = bookings[bookings.length-1]["itemsSum"];
                        for( var key in items ) {
                            if(items[key] > 0)
                                $('#itemsEl').append(key + ": " + items[key] + " ");
                        }

                        modalBody += '<table id="modalTable"><tr>';
                        modalBody += '<th>Product Title</th>';
                        modalBody += '<th>Option Title</th>';
                        modalBody += '<th>Lead Traveler</th>';
                        modalBody += '<th>Phone Number</th>';
                        modalBody += '<th>E-mail Address</th>';
                        modalBody += '<th>Booking RefCode</th>';
                        modalBody += '<th>Booked On</th>';
                        modalBody += '<th>Participants</th>';
                        modalBody += '<th>Price</th>';
                        modalBody += '</tr>';
                        for(let i=0; i<bookings.length; i++) {
                            modalBody += '<tr>';
                            modalBody += '<td>' + bookings[i]["modalInformations"]["productTitle"] + '</td>';
                            modalBody += '<td>' + bookings[i]["modalInformations"]["optionTitle"] + '</td>';
                            modalBody += '<td>' + bookings[i]["modalInformations"]["leadTraveler"] + '</td>';
                            modalBody += '<td>' + bookings[i]["modalInformations"]["phoneNumber"] + '</td>';
                            modalBody += '<td>' + bookings[i]["modalInformations"]["email"] + '</td>';
                            modalBody += '<td>' + bookings[i]["modalInformations"]["bookingRefCode"] + '</td>';
                            modalBody += '<td>' + bookings[i]["modalInformations"]["bookedOn"] + '</td>';
                            modalBody += '<td>' + bookings[i]["modalInformations"]["participants"] + '</td>';
                            modalBody += '<td>' + bookings[i]["modalInformations"]["price"] + '</td>';
                            modalBody += '</tr>';
                        }
                        modalBody += '</table>';
                    } else {
                        modalBody += '<p><strong>Bookings not found</strong></p>';
                    }
                    $('#bookingsModal .modal-body').append(modalBody);

                    if(bookings.length > 0) {
                        $('#modalTable th').css({"color": "#333D87", "font-weight": "bold"});
                        $('#modalTable, #modalTable th, #modalTable td').css("border", "1px solid #ededed");
                    }
                },
                error: function(t) {

                    Materialize.toast(t.error, 5000, 'toast-alert');
                }
            });
        });

        $(document).on('click', '.onGoingModalTrigger', function () {
            let date = $('#dateLabel').text();
            let hour = $(this).parent().parent().find('#hour').val();
            let hourTo = $(this).parent().parent().find('#hourTo').val();

            $.ajax({
                method: 'POST',
                url: '/av/getAvOnGoing',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    date: date,
                    hour: hour,
                    hourTo: hourTo ? hourTo : null,
                    avId: $('#availabilityId').val()
                },
                success: function(data) {
                    let onGoing = data;
                    $('#onGoingModal .modal-body').html('');
                    let modalBody = "";
                    if(onGoing.length > 0) {
                        modalBody += '<table id="onGoingModalTable"><tr>';
                        modalBody += '<th>Items</th>';
                        modalBody += '<th>From</th>';
                        modalBody += '<th>Created At</th>';
                        modalBody += '</tr>';
                        for(let i=0; i<onGoing.length; i++) {
                            modalBody += '<tr>';
                            modalBody += '<td>' + onGoing[i]["items"] + '</td>';
                            modalBody += '<td>' + onGoing[i]["from"] + '</td>';
                            modalBody += '<td>' + onGoing[i]["created_at"] + '</td>';
                            modalBody += '</tr>';
                        }
                        modalBody += '</table>';
                    } else {
                        modalBody += '<p><strong>On-Going not found</strong></p>';
                    }
                    $('#onGoingModal .modal-body').append(modalBody);

                    if(onGoing.length > 0) {
                        $('#onGoingModalTable th').css({"color": "#333D87", "font-weight": "bold"});
                        $('#onGoingModalTable, #onGoingModalTable th, #onGoingModalTable td').css("border", "1px solid #ededed");
                    }
                },
                error: function(t) {

                    Materialize.toast(t.error, 5000, 'toast-alert');
                }
            });
        });

        function my_check(in_date) {
            in_date = in_date.getDate() + '/'
                + (in_date.getMonth() + 1) + '/' + in_date.getFullYear();
            var my_array = ['10/02/2021', '12/02/2021'];
            //$('#d1').append(in_date+'<br>')
            if (my_array.indexOf(in_date) >= 0) {
                return [false, "notav", 'Not Available'];
            } else {
                return [true, "av", "available"];
            }
        }

        // Adds an empty hour ticket block
        function addHourTicketBlock() {
            let availabilityType = $('#availabilityType').val();
            let block = '<input type="hidden" id="hourTicketRowCount" value="0">\n';
            block += '<div class="col-md-12" style="margin-top: 20px!important;">\n';
            block += '<div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" id="hourSection">\n' +
                '<div class="hourDivFrom">\n' +
                '<label>From</label>' +
                '<input style="margin: 0 30px 20px 0;" type="time" data-type="updated" class="validate form-control col-md-12 s12" id="hour" name="hour[]" value="">\n' +
                '<input type="hidden" id="hour-copy" value="">\n' +
                '</div>' +
                '</div>\n';
            if (availabilityType === 'Operating Hours') {
                block += '<div class="col-md-1" id="hourToSection">\n' +
                    '<div class="hourDivTo">\n' +
                    '<label>To</label>' +
                    '<input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="hourTo" name="hourTo[]" value="">\n' +
                    '<input type="hidden" id="hourTo-copy" value="">\n' +
                    '</div>' +
                    '</div>\n';
            }
            block += '<div class="col-md-1" id="ticketSection">\n' +
                '<div class="ticketDivFrom">\n' +
                '<label>Ticket</label>' +
                '<input style="margin: 0 30px 20px 0;" type="number" data-type="updated" class="validate form-control col-md-12 s12" id="ticket" name="ticket[]" value="0">\n' +
                '</div>\n' +
                '</div>\n';
            block += '<div class="col-md-2 col-xs-5">\n' +
                '<label>Save</label>' +
                '<button class="btn btn-primary" id="saveDateTimeAndTicketButton" style="background: #26a69a;padding: 0 1.5rem;"><i class="icon-cz-floppy"></i></button>\n' +
                '</div>\n';
            block += '<div class="col-md-2 col-xs-5">\n' +
                '<label>Toggle</label>\n' +
                '<input id="hourToggle" type="checkbox" checked data-toggle="toggle" data-style="ios" data-onstyle="success" data-offstyle="danger">\n' +
                '</div>';
            block += '<div class="col-md-1 col-xs-5">\n' +
                '<label>Remove</label>' +
                '<button class="btn btn-primary" id="removeDateTimeAndTicketButton" style="padding: 0 1.5rem!important;background: #f4364f;">x</button>\n' +
                '</div>\n';
            block += '<div class="col-md-1 col-xs-5">\n' +
                '<label>Sold</label>\n' +
                '<input type="text" id="soldTickets" readonly="" value="0">\n' +
                '</div>';
            block += '<div class="col-md-1 col-xs-5">\n' +
                '<label>Cityzore</label>\n' +
                '<input type="text" id="czOngoing" readonly="" value="0">\n' +
                '</div>';
            block += '<div class="col-md-1 col-xs-5">\n' +
                '<label>GYG</label>\n' +
                '<input type="text" id="gygOngoing" readonly="" value="0">\n' +
                '</div>';
            block += '</div>';
            $('#hourTicketDiv').append(block);
            $('#hourTicketDiv #hourToggle').bootstrapToggle();
        }

        function isArrayUnique(array) {
            return array.filter(function(el, index, arr) {
                return index === arr.indexOf(el);
            });
        }

        $('#disableDateRangeButton').on('click', function() {
            let selectedDate = $('#selectedDate').val();
            applyChanges('disableDateRange', selectedDate);
        });

        $('#enableDateRangeButton').on('click', function() {
            let selectedDate = $('#selectedDate').val();
            applyChanges('enableDateRange', selectedDate);
        });

        $('#saveBulkTicket').on('click', function() {
            let bulkTicketCount = $('#bulkTicketCount').val();
            let selectedDate = $('#selectedDate').val();
            let dailyOrDateRange = $('input[name="radioDailyOrDateRange"]:checked').val();

            let object = {
                'bulkTicketCount': bulkTicketCount,
                'selectedDate': selectedDate,
                'dailyOrDateRange': typeof dailyOrDateRange === 'undefined' ? 'Hourly' : dailyOrDateRange
            };
            applyChanges('saveBulkTicket', object);
        });

        var globArray = [];

        $('#hourTicketDiv').on('click', '#saveDateTimeAndTicketButton', function() {
            let hour = $(this).parent().parent().find('#hour');
            let hourCopy = $(this).parent().parent().find('#hour-copy');
            let hourTo = $(this).parent().parent().find('#hourTo');
            let hourToCopy = $(this).parent().parent().find('#hourTo-copy');
            let ticket = $(this).parent().parent().find('#ticket');
            let ticketVal = isNaN(parseInt(ticket.val(), 10)) ? 0 : parseInt(ticket.val(), 10);
            let availabilityType = $('#availabilityType').val();

            // Same hour check for starting time
            let allHours = $('input[name="hour[]"]');
            let sameHourCheckArr = [];
            allHours.each(function(index, item) {
                if (item.value !== '') {
                    sameHourCheckArr.push(item.value);
                }
            });



            let uniqueArr = isArrayUnique(sameHourCheckArr);
            if (sameHourCheckArr.length !== uniqueArr.length) {
                Materialize.toast('You can\'t add an existing hour!', 10000, 'toast-alert');
                return;
            }





            /*        if($(this).hasClass("newElement")){


                      var control = 0;
                      sameHourCheckArr = sameHourCheckArr.concat(globArray);

                       sameHourCheckArr.forEach( function(element, index) {
                           if(element == hour.val()){
                               control++;

                           }
                       });

                       if(control > 1){
                          Materialize.toast('You added this hour allrady!!', 10000, 'toast-alert');
                           return;
                       }
                       globArray.push(hour.val());

                         }*/



            if (availabilityType === 'Starting Time') {
                if (hour.val() !== '') {
                    let object = {
                        'day': $('#selectedDate').val(),
                        'hour': hour.val(),
                        'hourCopy': hourCopy.val(),
                        'ticket': ticketVal
                    };
                    applyChanges('addedUpdatedDateTime', object);

                    if(hourCopy.val() == ""){
                        hourCopy.val(hour.val());
                    }
                }


            } else if (availabilityType === 'Operating Hours') {

                // hourfrom < hourto validation for operating hours
                let momentHourFrom = moment(hour.val(), 'HH:mm');
                let momentHourTo = moment(hourTo.val(), 'HH:mm');
                if (momentHourFrom.isSameOrAfter(momentHourTo)) {
                    Materialize.toast('From(hour) can not be after from To(hour)!', 10000, 'toast-alert');
                    return;
                }

                if (hour.val() !== '' && hourTo.val() !== '') {
                    let object = {
                        'day': $('#selectedDate').val(),
                        'hour': hour.val(),
                        'hourTo': hourTo.val(),
                        'hourCopy': hourCopy.val(),
                        'hourToCopy': hourToCopy.val(),
                        'ticket': ticketVal
                    };
                    applyChanges('addedUpdatedDateTime', object);

                    if(hourCopy.val() == ""){
                        hourCopy.val(hour.val());
                    }
                }
            }
        });

        $('#hourTicketDiv').on('change', '#hourToggle', function() {
            let hour = $(this).parent().parent().parent().find('#hour');
            let isActive = $(this).prop('checked') ? 1 : 0;
            let availabilityType = $('#availabilityType').val();

            if (availabilityType === 'Starting Time') {
                if (hour.val() !== '') {
                    let object = {
                        'day': $('#selectedDate').val(),
                        'hour': hour.val(),
                        'isActive': isActive
                    };
                    applyChanges('toggleDateTime', object);
                }
            } else if (availabilityType === 'Operating Hours') {
                let hourTo = $(this).parent().parent().parent().find('#hourTo');
                let object = {
                    'day': $('#selectedDate').val(),
                    'hour': hour.val(),
                    'hourTo': hourTo.val(),
                    'isActive': isActive
                };
                applyChanges('toggleDateTime', object);
            }
        });

        $('#hourTicketDiv').on('click', '#removeDateTimeAndTicketButton', function() {



            // Same hour check for starting time
            let allHours = $('input[name="hour[]"]');
            let sameHourCheckArr = [];
            allHours.each(function(index, item) {
                if (item.value !== '') {
                    sameHourCheckArr.push(item.value);
                }
            });



            let uniqueArr = isArrayUnique(sameHourCheckArr);
            if (sameHourCheckArr.length !== uniqueArr.length && $(this).hasClass("newElement")) {
                $(this).closest("table").remove();
                return;
            }







            let hourTicketRowCount = $(this).parent().parent().parent().find('#hourTicketRowCount').val();
            $(this).parent().parent().parent().find('#hourTicketRowCount').val(parseInt(hourTicketRowCount) - 1);
            let hour = $(this).parent().parent().find('#hour');
            let availabilityType = $('#availabilityType').val();

            if (availabilityType === 'Starting Time') {
                if (hour.val() !== '') {
                    let object = {
                        'day': $('#selectedDate').val(),
                        'hour': hour.val()
                    };
                    applyChanges('removeDateTime', object);
                }
                $(this).parent().parent().remove();
            } else if (availabilityType === 'Operating Hours') {
                let hourTo = $(this).parent().parent().find('#hourTo');
                if (hour.val() !== '' && hourTo.val() !== '') {
                    let object = {
                        'day': $('#selectedDate').val(),
                        'hour': hour.val(),
                        'hourTo': hourTo.val()
                    };
                    applyChanges('removeDateTime', object);
                    $(this).parent().parent().remove();
                    addHourTicketBlock();
                }
            }
        });

        $('#addNewDateTimeAndTicket').on('click', function() {
            addNewDateTimeAndTicket();
        });

        $('#disableEnableDayButton').on('click', function() {
            let date = $('#selectedDate').val();
            if ($('#disableEnableDayButton').attr('data-attr') === 'enable') {
                applyChanges('enabledDates', date);
                $('#disableEnableDayButton').html('Disable Day');
                $('#disableEnableDayButton').attr('data-attr', 'disable');
                $('#disableEnableDayButton').css('background', '#f4364f');
            } else {
                applyChanges('disabledDates', date);
                $('#disableEnableDayButton').html('Enable Day');
                $('#disableEnableDayButton').attr('data-attr', 'enable');
                $('#disableEnableDayButton').css('background', '#26a69a');
            }
        });

        $('#daysOfWeekButton').on('click', function() {
            let data = [];
            $('#monday').val() === '1' ? data.push('monday') : true;
            $('#tuesday').val() === '1' ? data.push('tuesday') : true;
            $('#wednesday').val() === '1' ? data.push('wednesday') : true;
            $('#thursday').val() === '1' ? data.push('thursday') : true;
            $('#friday').val() === '1' ? data.push('friday') : true;
            $('#saturday').val() === '1' ? data.push('saturday') : true;
            $('#sunday').val() === '1' ? data.push('sunday') : true;
            applyChanges('daysOfWeek', data);
        });

        $('input[name="daysOfWeek[]"]').on('change', function() {
            $(this).val($(this).prop('checked') ? 1 : 0);
        });

        $('#monthsOfYearButton').on('click', function() {
            let data = [];
            $('#01').val() === '1' ? data.push('01') : true;
            $('#02').val() === '1' ? data.push('02') : true;
            $('#03').val() === '1' ? data.push('03') : true;
            $('#04').val() === '1' ? data.push('04') : true;
            $('#05').val() === '1' ? data.push('05') : true;
            $('#06').val() === '1' ? data.push('06') : true;
            $('#07').val() === '1' ? data.push('07') : true;
            $('#08').val() === '1' ? data.push('08') : true;
            $('#09').val() === '1' ? data.push('09') : true;
            $('#10').val() === '1' ? data.push('10') : true;
            $('#11').val() === '1' ? data.push('11') : true;
            $('#12').val() === '1' ? data.push('12') : true;
            applyChanges('monthsOfYear', data);
        });

        $('input[name="monthsOfYear[]"]').on('change', function() {
            $(this).val($(this).prop('checked') ? 1 : 0);
        });

        $('#yearsButton').on('click', function() {
            let data = [];
            $('#2020').val() === '1' ? data.push('2020') : true;
            $('#2021').val() === '1' ? data.push('2021') : true;
            applyChanges('years', data);
        });

        $('input[name="years[]"]').on('change', function() {
            $(this).val($(this).prop('checked') ? 1 : 0);
        });

        $('#limitlessTicket').on('change', function() {
            $(this).val($(this).prop('checked') ? 1 : 0);
        });

        $('#informationSaveButton').on('click', function() {
            let avName = $('#avName').val();
            let isLimitless = $('#limitlessTicket').val();
            let ticketType = $('#ticketType').val();

            let object = {
                'avName': avName,
                'isLimitless': isLimitless,
                'ticketType': ticketType // ticket type operation will be added after date ranges
            };
            applyChanges('saveInformation', object);
        });

        function applyChanges(type, data) {
            globalApplyChanges(type, data);
        }

        setTimeout(function() {
            getAvdatesToEdit();
        }, 500);

        function getAvdatesToEdit() {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '/av/getAvdatesToEditWithoutHour',
                data: {
                    availabilityId: $('#availabilityId').val()
                },
                success: function (data) {
                    var size = data.length;
                    data.forEach(function (d, index) {
                        addOldDates(index, d, size);
                    });
                }
            });
        }

        function addOldDates(index, d, size) {
            let date = index + 1;
            let block = '';
            block += '<div class="col-md-12';
            block += index == (size - 1) ? ' lastDate' : ' allDates hide';
            block += '">\n';
            block += '<div class="col-md-6">\n';
            block += '<input data-id="' + date + '" data-avdate-id="' + d.id + '" type="text" class="dateRange' + date + '" name="daterange[]" value="" />\n';
            block += '</div>\n';
            block += '<div class="col-md-3">\n';
            block += '<input name="radioSelectedAvdate" class="radioSelectedAvdate" type="radio" id="radioSelectedAvdate' + date + '" ';
            if (date === 1) {
                block += 'checked="checked"';
            }
            block += '/>\n';
            block += '<label for="radioSelectedAvdate' + date + '">Select</label>\n';
            block += '</div>\n';
            block += '<div class="col-md-3">\n';
            block += '<div class="help-tip hidden-xs hidden-sm">\n';
            block += '<p style="width: 125px;">Add New Date</p>\n';
            block += '</div>\n';
            block += '<button onclick="addDates(' + date + ');" data-avdate-id="' + d.id + '" data-id="' + date + '" class="addNewDateButton waves-effect waves-light btn btn-primary btn-small pull-right" style="background: #26a69a;">+</button>\n';
            if (date !== 1) {
                block += '<div class="help-tip hidden-xs hidden-sm">\n';
                block += '<p style="width: 125px;">Delete Date</p>\n';
                block += '</div>\n';
                block += '<button data-avdate-id="' + d.id + '" data-id="' + date + '" class="removeDateButton btn btn-primary pull-right" style="margin-right: 20px; background: #f4364f;">x</button>\n';
                if (index == (size - 1)) {
                    block += '<button onclick="showToggle()" class="waves-effect waves-light btn btn-primary btn-small pull-right showToggleButton" style="background: #1c72e5;margin-right: 20px">Show Dates</button>\n'
                }
            }
            block += '</div>\n';
            block += '</div>';
            $('.dateRangeWrapper').append(block);
            initDateRanges(date, d);
            $('button.addNewDateButton[data-id="' + index + '"]').attr('disabled', true);
            $('button.removeDateButton[data-id="' + index + '"]').attr('disabled', true);
            $('input.dateRange' + index).attr('disabled', true);
        }

        function initDateRanges(date, d) {
            let validFromTo = d['valid_from_to'];
            let validFrom = validFromTo.split(' - ')[0];
            let validTo = validFromTo.split(' - ')[1];
            let dateRangeIterator = parseInt(date);
            $('.dateRange' + dateRangeIterator).daterangepicker({
                'opens': 'center',
                'drops': 'down',
                'minDate': validFrom,
                'startDate': validFrom,
                'endDate': validTo,
                "locale": {
                    "format": "DD/MM/YYYY",
                }
            });
        }

        $('.dateRangeWrapper').on('click', '.removeDateButton', function() {
            let avdateID = $(this).attr('data-avdate-id');
            if (avdateID !== '') {
                applyChanges('removeAvdate', avdateID);
            }
            $(this).parent().parent().remove();
            $('button.addNewDateButton[data-id="'+(globalItObject.it - 1)+'"]').attr('disabled', false);
            $('button.removeDateButton[data-id="'+(globalItObject.it - 1)+'"]').attr('disabled', false);
            $('input.dateRange'+(globalItObject.it - 1)).attr('disabled', false);
            globalItObject.removeButtonClicked = 1;
            globalItObject.it = globalItObject.it - 1;
            arrangeCalendar();
        });

        // Made a workaround for first remove minute hour function
        let weekDaysArr = ['.mondayDiv', '.tuesdayDiv', '.wednesdayDiv', '.thursdayDiv', '.fridayDiv', '.saturdayDiv', '.sundayDiv'];
        for (let b=0; b<weekDaysArr.length; b++) {
            weekDaysArr[b] = weekDaysArr[b] + '11';
            $('.weekDayHoursWrapper').on('click', weekDaysArr[b] + ' .removeMinHourButton', function() {
                $(this).parent().parent().remove();
            });
        }

        $('.dateRangeWrapper').on('change', '.radioSelectedAvdate', function() {
            $('.radioSelectedAvdate').removeAttr('checked');
            $(this).attr('checked', 'checked');
        });

        $('.dateRangeWrapper').on('apply.daterangepicker', 'input[name="daterange[]"]', function(ev, picker) {
            let startDateYmd = picker.startDate.format('YYYY-MM-DD');
            let endDateYmd = picker.endDate.format('YYYY-MM-DD');
            let startDateDmy = picker.startDate.format('DD/MM/YYYY');
            let endDateDmy = picker.endDate.format('DD/MM/YYYY');
            let validFromTo = startDateDmy + ' - ' + endDateDmy;
            let it = $(this).attr('data-id');
            let avdateID = $(this).attr('data-avdate-id');
            let a = true;
            let b = true;
            if (it !== '1') {
                let endDateOld = moment($('.dateRange' + (it - 1)).val().split(' - ')[1], "DD/MM/YYYY").add(1, "d");
                let startDateNew = moment($('.dateRange' + it).val().split(' - ')[0], "DD/MM/YYYY");
                a = endDateOld.format("DD/MM/YYYY");
                b = startDateNew.format("DD/MM/YYYY");
            }
            if (a !== b) {
                Materialize.toast('End date of old date picker and start date of new date picker must be consecutive!', 4000, 'toast-alert');
                return;
            } else {
                let object = {
                    'validFromTo': validFromTo,
                    'validFrom': startDateYmd,
                    'validTo': endDateYmd,
                    'avdateID': avdateID
                };
                applyChanges('extendAvdate', object);
                arrangeCalendar();
            }
        });

        function arrangeCalendar() {
            globalArrangeCalendar();
        }

        $('#saveWeekdayTimeButton').on('click', function() {

            let avdateID = 0;
            let type = $('#availabilityType').val();
            let boolArr = [];

            $('.radioSelectedAvdate').each(function(index, item) {
                if ($(this).is(':checked')) {
                    avdateID = $(this).parent().parent().find('.addNewDateButton').attr('data-avdate-id');
                }
            });

            if (avdateID !== 0) {
                let object = {
                    'avdateID': avdateID,
                    'monday': [],
                    'tuesday': [],
                    'wednesday': [],
                    'thursday': [],
                    'friday': [],
                    'saturday': [],
                    'sunday': []
                };
                let weekDaysArr = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                weekDaysArr.forEach(function(dayName, i) {
                    $('input[name="'+dayName+'Hour1[]"]').each(function(index, item) {
                        if ($(this).val() !== '') {
                            object[dayName].push($(this).val());
                        }
                    });
                    if (type === 'Operating Hours') {
                        $('input[name="'+dayName+'HourTo1[]"]').each(function(index, item) {
                            if ($(this).val() !== '') {
                                object[dayName].push($(this).val());
                            }
                        });
                        if (object[dayName].length !== 2 && object[dayName].length !== 0) {
                            boolArr.push(false);
                        }
                    }
                });
                applyChanges('addNewHoursToAvdate', object);
            } else {
                Materialize.toast('A problem is occurred! Please try again later!', 4000, 'toast-alert');
            }
        });

    });

    function copyToAllBelow(day, date) {
        let type = $('#availabilityType').val();
        let weekDaysArr = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        let weekDayIndex = weekDaysArr.indexOf(day) + 1;
        let changableWeekDays = weekDaysArr.slice(weekDayIndex);

        changableWeekDays.forEach(function(changableWeekDay) {
            let block = '';
            $('.'+changableWeekDay+'Div'+date+'1 .removeMinHourButton').click();
            let toBeCopied = $('input[name="'+day+'Hour'+date+'[]"');
            $('input[name="'+changableWeekDay+'Hour'+date+'[]"').first().val(toBeCopied.first().val());
            let toBeCopiedTo;
            if (type === 'Operating Hours') {
                toBeCopiedTo = $('input[name="'+day+'HourTo'+date+'[]"');
                $('input[name="'+changableWeekDay+'HourTo'+date+'[]"').first().val(toBeCopiedTo.first().val());
            }
            if (toBeCopied.length > 1) {
                for(let x=1; x<toBeCopied.length; x++) {
                    block += '<div class="col-md-12 input-field s12 dynamicDiv">\n';
                    block += '<div class="hourDivFrom col-md-3">\n' +
                        '         <input style="margin: 0 30px 20px 0; width: 110%;" type="time" class="validate form-control col-md-12 s12" id="'+changableWeekDay+'Hour" name="'+changableWeekDay+'Hour'+date+'[]" value="'+toBeCopied.eq(x).val()+'">\n' +
                        '     </div>';
                    block += '<div class="removeDiv col-md-2"><a class="removeMinHourButton btn btn-danger" style="background: #f4364f;">x</a></div></div>';
                }
            }
            $('.'+changableWeekDay+'Div'+date+'1').append(block);
        });
    }

    function validateDates(it) {
        let endDateOld = moment($('.dateRange' + (it - 1)).val().split(' - ')[1], "DD/MM/YYYY").add(1, "d");
        let startDateNew = moment($('.dateRange' + it).val().split(' - ')[0], "DD/MM/YYYY");
        let a = endDateOld.format("DD/MM/YYYY");
        let b = startDateNew.format("DD/MM/YYYY");
        if (a !== b) {
            Materialize.toast('End date of old date picker and start date of new date picker must be consecutive!', 4000, 'toast-alert');
            return false;
        }
        return true;
    }

    function globalApplyChanges(type, data) {
        var tempObj = data;
        if (type !== 'addNewHoursToAvdate' && $('#notValidForBlockout').val() === '0') {
            Materialize.toast('There is no hour for this availability. You need to add some hours on Date Operations first.', 4000, 'toast-alert');
            return;
        }
        $.ajax({
            async: false,
            method: 'POST',
            url : '/av/applyChanges',
            data : {
                _token: $('meta[name="csrf-token"]').attr('content'),
                type: type,
                data: data,
                availabilityId: $('#availabilityId').val()
            },
            success: function(data) {
                if (data.success) {
                    if(tempObj.isLimitless == 1){
                        isCheckedLimitless = 1;
                    }else{
                        isCheckedLimitless = 0;
                    }

                    if ($('#availabilityType').val() === 'Operating Hours' && type === 'addedUpdatedDateTime') {
                        $('#hourTicketRowCount').val(1);
                    }
                    if (parseInt($('#hourTicketRowCount').val()) !== 0) {
                        $('#disableEnableDiv').show();
                    } else {
                        if ($('#availabilityType').val() === 'Operating Hours') {
                            $('#disableEnableDiv').hide();
                        }
                    }
                    if (data.orderID) {
                        $('button.addNewDateButton[data-id="'+data.orderID+'"]').attr('data-avdate-id', data.avdateID);
                        $('button.removeDateButton[data-id="'+data.orderID+'"]').attr('data-avdate-id', data.avdateID);
                        $('input[name="daterange[]"][data-id="'+data.orderID+'"]').attr('data-avdate-id', data.avdateID);
                    }
                    Materialize.toast('Changes are applied successfully', 4000, 'toast-success');
                    if (data.notValidForBlockout === 1) {
                        $('#notValidForBlockout').val(1);
                    }
                } else if (data.bulkTicketToNoMatchException) {
                    Materialize.toast(data.bulkTicketToNoMatchException, 10000, 'toast-alert');
                } else if (data.limitlessWithTicketTypeError) {
                    Materialize.toast(data.limitlessWithTicketTypeError, 10000, 'toast-alert');
                } else if (data.dateRangeError) {
                    Materialize.toast('This date range has tickets. Please try another date range!', 10000, 'toast-alert');
                } else if (data.error) {
                    Materialize.toast('An unexpected error is occured. Your last changes might not have been applied.<br> Session might be expired. In that case you need to log in again.', 10000, 'toast-alert');
                }
            },
            statusCode: {
                419: function() {
                    Materialize.toast('Your last changes might not have been applied.<br> Session is expired, you need to log in again!', 30000, 'toast-alert');
                },
                500: function() {
                    Materialize.toast('An unexpected error is occured. Your last changes might not have been applied.<br> Please consult software development team!', 30000, 'toast-alert');
                }
            }
        });
    }

    function globalArrangeCalendar() {
        let lastDateRange = $('.dateRange' + globalItObject.it).val();
        lastDateRange = lastDateRange.split(' - ')[1];
        let maxDate = moment(lastDateRange, 'DD/MM/YYYY');
        let datepicker = $('.datepicker-here').datepicker().data('datepicker');
        datepicker.update({
            maxDate: maxDate.toDate(),
        });
    }

    // Add Only Dates Dynamically For Every Availability
    function addDates(it) {
        if (it >= 2) {
            if (!validateDates(it)) {
                return;
            }
        }
        let date = it + 1;
        let block = '';
        block += '<div class="col-md-12">\n';
        block += '<div class="col-md-6">\n';
        block += '<input data-avdate-id="" data-id="'+date+'" type="text" class="dateRange'+date+'" name="daterange[]" value="" />\n';
        block += '</div>\n';
        block += '<div class="col-md-3">\n';
        block += '<input name="radioSelectedAvdate" class="radioSelectedAvdate" type="radio" id="radioSelectedAvdate'+date+'" ';
        block += ' />\n';
        block += '<label for="radioSelectedAvdate'+date+'">Select</label>\n';
        block += '</div>\n';
        block += '<div class="col-md-3">\n';
        block += '<button onclick="addDates('+date+');" data-avdate-id="" data-id="'+date+'" class="addNewDateButton waves-effect waves-light btn btn-primary btn-small pull-right" style="background: #26a69a;">+</button>\n';
        if (date !== 1) {
            block += '<button data-avdate-id="" data-id="'+date+'" class="removeDateButton btn btn-primary pull-right" style="margin-right: 20px; background: #f4364f;">x</button>\n';
        }
        block += '</div>\n';
        block += '</div>';

        $('.dateRangeWrapper').append(block);
        globalItObject.removeButtonClicked = 0;
        globalItObject.it = it + 1;
        let validFromTo = $('.dateRange' + (it+1)).val();
        let object = {
            'validFromTo': validFromTo,
            'orderID': date
        };
        globalApplyChanges('addAvdate', object);
        $('button.addNewDateButton[data-id="'+it+'"]').attr('disabled', true);
        $('button.removeDateButton[data-id="'+it+'"]').attr('disabled', true);
        $('input.dateRange'+it).attr('disabled', true);

        let lastDateRange = $('.dateRange' + globalItObject.it).val();
        globalArrangeCalendar();
    }

    // That workaround is starting from second. Basicly it is observing the globalItObject object and it fires the function below when it changed
    globalItObject.registerListener(function() {
        if (globalItObject.it > 1 && globalItObject.removeButtonClicked === 0) {
            let minDate = $('.dateRange'+ (globalItObject.it - 1)).val().split('- ')[1];
            let minDateMoment = moment(minDate, "DD/MM/YYYY").add(1, 'd');
            let minDateString = moment(minDateMoment).format('DD/MM/YYYY');
            let maxDateMoment = moment(minDateString, "DD/MM/YYYY").add(1, 'months');
            let maxDateString = moment(maxDateMoment).format('DD/MM/YYYY');
            $('.dateRange' + globalItObject.it).daterangepicker({
                'opens': 'center',
                'drops': 'down',
                'minDate': minDateString,
                'startDate': minDateString,
                'endDate': maxDateString,
                "locale": {
                    "format": "DD/MM/YYYY",
                }
            });
        }
    });

    // Add Hour/Minute Dynamically For Every Day
    function addMinHour(day, date, time) {
        let block = '<div class="col-md-12 input-field dynamicDiv">\n';
        block += '<div class="hourDivFrom col-md-3">\n' +
            '         <input style="margin: 0 30px 20px 0; width: 110%;" type="time" class="validate form-control col-md-12 s12" id="'+day+'Hour" name="'+day+'Hour'+date+'[]" value="">\n' +
            '     </div>';
        block += '<div class="removeDiv col-md-2"><a class="removeMinHourButton btn btn-danger" style="background: #f4364f;">x</a></div></div>';
        $('.'+day+'Div'+date+time).append(block);
    }

    $('.anchorClass').on('click', function() {
        let href = $(this).attr('href');
        href = href.substring(1);
        scrollToAnchor(href);
    });

    function scrollToAnchor(aid) {
        let aTag = $("a[id='"+ aid +"']");
        $('html,body').animate({scrollTop: aTag.offset().top - 150},'slow');
    }

    //Get the button:
    mybutton = document.getElementById("scrollToTop");

    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() {scrollFunction()};

    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            mybutton.style.display = "block";
        } else {
            mybutton.style.display = "none";
        }
    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        $('html, body').animate({scrollTop: 0}, 'slow');
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    }

    $('.divideButton').on('click', function() {
        let $this = $(this);
        let $thisParent = $this.parent().parent().parent();

        let hourFrom = $thisParent.find('.hourRegularFrom').val();
        let hourTo = $thisParent.find('.hourRegularTo').val();
        let interval = $thisParent.find('#intervalRegular').val();

        let dividedHours = divideHours(hourFrom, hourTo, interval);
        console.log(dividedHours);
        for(let i=0; i<dividedHours.length; i++) {
            if($thisParent.attr('data-type') == 'single')
                addNewDateTimeAndTicket(dividedHours[i]);
            else {
                let day = ($thisParent.parent().parent().find('label').text()).toLowerCase();
                addMinHourByDivideButton(day, 1, 1, dividedHours[i]);
            }
        }

        if($thisParent.hasClass('collapse in'))
            $thisParent.collapse('toggle');

    });

    function divideHours(hourFrom, hourTo, interval) {
        let dividedHours = [];

        let hourFromInMin = hourFrom.split(':');
        hourFromInMin = parseInt(hourFromInMin[0]*60) + parseInt(hourFromInMin[1]);

        let hourToInMin = hourTo.split(':');
        hourToInMin = parseInt(hourToInMin[0]*60) + parseInt(hourToInMin[1]);

        interval = parseInt(interval);
        let hour = "";

        for(let i=hourFromInMin; i<=hourToInMin; i=i+interval) {
            dividedHours.push(convertToHourFormat(i));
        }

        return dividedHours;
    }

    function convertToHourFormat(hourInMin) {
        var hours = Math.floor(hourInMin / 60);
        if(hours < 10) hours = '0' + hours;

        var minutes = hourInMin % 60;
        if(minutes < 10) minutes = '0' + minutes;

        return hours + ":" + minutes;
    }

    function addMinHourByDivideButton(day, date, time, hour) {
        let block = '<div class="col-md-12 input-field dynamicDiv">\n';
        block += '<div class="hourDivFrom col-md-3">\n' +
            '         <input style="margin: 0 30px 20px 0; width: 110%;" type="time" class="validate form-control col-md-12 s12" id="'+day+'Hour" name="'+day+'Hour'+date+'[]" value="'+hour+'">\n' +
            '     </div>';
        block += '<div class="removeDiv col-md-2"><a class="removeMinHourButton btn btn-danger" style="background: #f4364f;">x</a></div></div>';
        $('.'+day+'Div'+date+time).append(block);
    }

    function addNewDateTimeAndTicket(regularHour = null) {

        let hourTicketRowCount = $('#addNewDateTimeAndTicket').parent().parent().parent().find('#hourTicketRowCount').val();
        $('#addNewDateTimeAndTicket').parent().parent().parent().find('#hourTicketRowCount').val(parseInt(hourTicketRowCount) + 1);

        let availabilityType = $('#availabilityType').val();
        let avTicketType = $('#avTicketType').val();
        let block = '';

        block += '<table style="display: block;overflow: auto;">';
        block += '<tbody>';
        block += '<tr>';
        block += '<td>' +
            '<div id="hourSection">\n' +
            '<div class="hourDivFrom">\n';
        if(regularHour == null)
            block += '<input style="margin: 0 30px 20px 0;" type="time" data-type="updated" class="validate form-control col-md-12 s12" id="hour" name="hour[]" value="">\n';
        else
            block += '<input style="margin: 0 30px 20px 0;" type="time" data-type="updated" class="validate form-control col-md-12 s12" id="hour" name="hour[]" value="'+regularHour+'">\n';
        block += '<input type="hidden" id="hour-copy" value="">\n' +

            '</div>' +
            '</div>\n';
        if (availabilityType === 'Operating Hours') {
            block += '<div id="hourToSection">\n' +
                '<div class="hourDivTo">\n' +
                '<label>To</label>' +
                '<input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="hourTo" name="hourTo[]" value="">\n' +
                '<input type="hidden" id="hourTo-copy" value="">\n' +
                '</div>' +
                '</div>\n';
        }
        '</td>';
        if (availabilityType === 'Starting Time' && avTicketType !== '4') {

            if(isCheckedLimitless == "0"){
                block += '<td>' +
                    '<div id="ticketSection">\n' +
                    '<div class="ticketDivFrom">\n' +
                    '<input style="margin: 0 30px 20px 0;" type="number" data-type="updated" class="validate form-control col-md-12 s12" id="ticket" name="ticket[]" value="">\n' +
                    '</div>\n' +
                    '</div>\n';
                '</td>';
            }
        }



        block += '<td>' +
            '<button class="btn btn-primary newElement" id="saveDateTimeAndTicketButton" style="background: #26a69a;padding: 0 1.5rem;"><i class="icon-cz-floppy"></i></button>\n' +
            '</td>';









        block += '<td>' +
            '<input id="hourToggle" type="checkbox" checked data-toggle="toggle" data-style="ios" data-onstyle="success" data-offstyle="danger">\n' +
            '</td>';
        if (availabilityType === 'Starting Time') {
            block += '<td>' +
                '<button class="btn btn-primary newElement" id="removeDateTimeAndTicketButton" style="padding: 0 1.5rem!important;background: #f4364f;">x</button>\n' +
                '</td>';
        }
        block += '<td>' +
            '<input type="text" id="soldTickets" readonly="" value="0">\n' +
            '</td>';
        block += '<td>' +
            '<input type="text" id="czOngoing" readonly="" value="0">\n' +
            '</td>';
        block += '<td>' +
            '<input type="text" id="gygOngoing" readonly="" value="0">\n' +
            '</td>';
        block += '</tbody>';
        block += '</table>';

        $('#hourTicketDiv').append(block);
        $('#hourTicketDiv #hourToggle').bootstrapToggle();
    }
</script>
