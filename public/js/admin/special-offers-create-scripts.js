$(function(){
//LEFT MOBILE MENU OPEN
    $(".atab-menu").on('click', function() {
        $(".sb2-1").css("left", "0");
        $(".btn-close-menu").css("display", "inline-block");
    });

    //LEFT MOBILE MENU CLOSE
    $(".btn-close-menu").on('click', function() {
        $(".sb2-1").css("left", "-350px");
        $(".btn-close-menu").css("display", "none");
    });
    $('#productSelect').on('change', function() {
        $('#previouslyAddedOffersDiv').hide();
        $('#commissionDiv').hide();
        let productSelect = $('#productSelect').val();
        $('#optionSelect').html("<option selected>Choose an Option</option>\n");
        $.ajax({
            method: 'POST',
            url : '/booking/optionSelect',
            data : {
                _token: $('meta[name="csrf-token"]').attr('content'),
                productSelect: productSelect
            },
            success: function(data) {
                let options = data.option;
                for (let i=0; i<options.length; i++) {
                    $('#optionSelect').append('<option data-ref-code="'+options[i].referenceCode+'" value="'+options[i].id+'">'+options[i].title+'</option>');
                }
            }
        });
    });

    $('#optionSelect').on('change', function() {
        let productSelect = $('#productSelect').val();
        let optionSelect = $('#optionSelect').val();
        $.ajax({
            method: 'POST',
            url : '/special-offers/getAvailabilityType',
            data : {
                _token: $('meta[name="csrf-token"]').attr('content'),
                optionID: optionSelect,
                productID: productSelect
            },
            success: function(data) {
                $("#max-value").val(data.max_value);
                if (data.datesAndTimesVisible) {
                    $('#dateTimesDiv').show();
                } else {
                    $('#dateTimesDiv').hide();
                }
                $('#informationPart').show();
                showOldSpecialOffers(data.specialOffers);
            }
        });
    });

    $('#dateRange, #weekDay, #randomDay, #dateTimes').on('click', function() {
        let optionId = $('#optionSelect').val();
        let ids = ['dateRange', 'weekDay', 'randomDay', 'dateTimes'];
        let $this = $(this);
        ids.forEach(function(id, index) {
            let $thisId = $this.attr('id');
            if (id === $thisId) {
                let datepicker = $('.datepicker-various').datepicker().data('datepicker');
                datepicker.clear();
                $this.attr('checked', 'checked');
                if (['dateRange', 'randomDay', 'dateTimes'].includes($thisId)) {
                    $('#inputPart').hide();
                    $('#calendarPart').show();
                    $('.datepicker-various').datepicker({
                        onSelect: function(formattedDate, inst, fd) {
                            $('#selectedDate').val(formattedDate);
                            if ($thisId === 'dateTimes') {
                                $.ajax({
                                    method: 'POST',
                                    url : '/special-offers/getDateTimes',
                                    data : {
                                        _token: $('meta[name="csrf-token"]').attr('content'),
                                        formattedDate: formattedDate,
                                        optionId: optionId,
                                        dateType: $thisId
                                    },
                                    success: function(data) {
                                        let discountDTDiv = $('#discountDTDiv');
                                        discountDTDiv.html('');
                                        let mixedAv = data.mixedAv;
                                        let block = '';
                                        if (mixedAv) {
                                            // Demanded only for S with limitless && S with normal, so first element on array won't be wrong
                                            let owner = $('#owner').val();
                                            let hours = mixedAv.only_selected_times[0];
                                            hours.forEach(function(hour, index) {
                                                block += '<div class="col-md-12">\n' +
                                                    '<div class="col-md-3">\n' +
                                                    '<input type="text" id="hourInput" class="validate form-control col-md-12 s12" readonly="readonly" value="'+hour.hourFrom+'">\n' +
                                                    '</div>\n' +
                                                    '<div class="col-md-3">\n' +
                                                    '<input type="number" id="discountInput" class="validate form-control col-md-12 s12" value="" ';
                                                if (owner === 'supplier') {
                                                    block += 'max="50"';
                                                }
                                                block += '>\n' +
                                                    '</div>\n' +
                                                    '<div class="col-md-3">\n' +
                                                    '<button class="btn btn-primary" id="saveDTButton">Save</button>\n' +
                                                    '</div>\n' +
                                                    '</div>';
                                            });
                                            discountDTDiv.append(block);
                                        } else {
                                            block += '<div class="alert alert-danger" role="alert">\n' +
                                                'This day has no availability. Please select another date.\n' +
                                                '</div>';
                                            discountDTDiv.append(block);
                                        }
                                    },
                                    errors: function() {
                                        //
                                    }
                                });
                            }
                        },
                        minDate: moment().toDate(),
                        range: $thisId === 'dateRange',
                        toggleSelected: $thisId === 'randomDay',
                        multipleDates: $thisId === 'randomDay'
                    });
                    if (['dateRange', 'randomDay'].includes($thisId)) {
                        $('#discountDRRDDiv').show();
                        $('#discountDTDiv').hide();
                    } else {
                        $('#discountDRRDDiv').hide();
                        $('#discountDTDiv').show();
                    }
                } else {
                    $('#calendarPart').hide();
                    $('#inputPart').show();
                }
            } else {
                $('#' + id).removeAttr('checked');
            }
        });
        $('#previouslyAddedOffersDiv').show();
    });

    $('#saveDRRDButton').on('click', function() {
        let discountDRRDDiv = $(this).parent().parent();
        let discountDRRD = discountDRRDDiv.find('#discountDRRD');
        let discount = discountDRRD.val();
        let selectedDate = $('#selectedDate').val();
        let minType = $('input[name="minPersonOrCartTotal"]:checked').val();
        let minimum = $('#minimum').val();
        let discountType = $('input[name="percentageMoney"]:checked').val();
        let productID = $('#productSelect').val();
        let optionID = $('#optionSelect').val();
        let dateType = $('input[name="dateType"]:checked').val();
        let datepicker = $('.datepicker-various').datepicker().data('datepicker');
        let userType = $('#forWho').val();
        let max_value = $('#max-value').val();
        let maximumUsability = $('#maximumUsability').val();

       if(discountType == "money"){
        if(discount > max_value){
            Materialize.toast('For this option Maximum discount value limit is (for money)'+max_value, 6000, 'toast-alert');
            return false;
        }
       }else{
        if(discount > 100){
           Materialize.toast('Maximum discount value limit is 100 (for percentage)', 6000, 'toast-alert');
            return false;
        }
       }
        if (selectedDate !== '') {
            $.ajax({
                method: 'POST',
                url : '/special-offers/saveChanges',
                data : {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    productID: productID,
                    optionID: optionID,
                    minType: minType,
                    minimum: minimum,
                    discountType: discountType,
                    selectedDate: selectedDate,
                    discount: discount,
                    dateType: dateType,
                    userType : userType,
                    maximumUsability: maximumUsability,
                },
                success: function(data) {
                    if (data.success) {
                        Materialize.toast(data.success, 4000, data.type);
                    }
                    Materialize.toast(data.error, 4000, data.type);
                    datepicker.clear();
                }
            });
        } else {
            Materialize.toast('Please select a date first!', 4000, 'toast-alert');
        }
    });

    $('#saveWDButton').on('click', function() {
        let minType = $('input[name="minPersonOrCartTotal"]:checked').val();
        let minimum = $('#minimum').val();
        let userType = $('#forWho').val();
        let discountType = $('input[name="percentageMoney"]:checked').val();
        let productID = $('#productSelect').val();
        let optionID = $('#optionSelect').val();
        let dateType = $('input[name="dateType"]:checked').val();
        let monday = $('#monday').val();
        let tuesday = $('#tuesday').val();
        let wednesday = $('#wednesday').val();
        let thursday = $('#thursday').val();
        let friday = $('#friday').val();
        let saturday = $('#saturday').val();
        let sunday = $('#sunday').val();
        let maximumUsability = $('#maximumUsability').val();
        $.ajax({
            method: 'POST',
            url : '/special-offers/saveChanges',
            data : {
                _token: $('meta[name="csrf-token"]').attr('content'),
                productID: productID,
                optionID: optionID,
                minType: minType,
                minimum: minimum,
                discountType: discountType,
                dateType: dateType,
                userType : userType,
                week: {'monday': monday, 'tuesday': tuesday, 'wednesday': wednesday, 'thursday': thursday, 'friday': friday, 'saturday': saturday, 'sunday': sunday},
                maximumUsability: maximumUsability
            },
            success: function(data) {
                if (data.success) {
                    Materialize.toast(data.success, 4000, data.type);
                }
                Materialize.toast(data.error, 4000, data.type);
            }
        });
    });

    $('body').on('click', '#saveDTButton', function() {
        let div = $(this).parent().parent();
        let hour = div.find('#hourInput').val();
        let discount = div.find('#discountInput').val();
        let selectedDate = $('#selectedDate').val();
        let minType = $('input[name="minPersonOrCartTotal"]:checked').val();
        let minimum = $('#minimum').val();
        let discountType = $('input[name="percentageMoney"]:checked').val();
        let productID = $('#productSelect').val();
        let optionID = $('#optionSelect').val();
        let dateType = $('input[name="dateType"]:checked').val();
        let userType = $('#forWho').val();
        let maximumUsability = $('#maximumUsability').val();
        $.ajax({
            method: 'POST',
            url : '/special-offers/saveChanges',
            data : {
                _token: $('meta[name="csrf-token"]').attr('content'),
                productID: productID,
                optionID: optionID,
                minType: minType,
                minimum: minimum,
                discountType: discountType,
                dateType: dateType,
                userType: userType,
                day: selectedDate,
                hour: hour,
                discount: discount,
                maximumUsability: maximumUsability
            },
            success: function(data) {
                if (data.success) {
                    Materialize.toast(data.success, 4000, data.type);
                }
                Materialize.toast(data.error, 4000, data.type);
            }
        });
    });

    function showOldSpecialOffers(specialOffers) {
        let previouslyAddedOffersDiv = $('#previouslyAddedOffersDiv');
        if (specialOffers) {
            previouslyAddedOffersDiv.html('<label style="font-size: 1.1rem;">Previously Added Special Offers</label>\n');
            let dateRange = specialOffers.dateRange ? JSON.parse(specialOffers.dateRange) : [];
            let weekDay = specialOffers.weekDay ? JSON.parse(specialOffers.weekDay) : [];
            let randomDay = specialOffers.randomDay ? JSON.parse(specialOffers.randomDay) : [];
            let dateTimes = specialOffers.dateTimes ? JSON.parse(specialOffers.dateTimes) : [];
            let block = '';
            dateRange.forEach(function(item) {
                block += '<div class="col-md-12" style="margin: 10px;">\n';
                block += '<button id="deleteOldSpecialOffer" class="btn btn-primary hidden-xs" data-date-type="dateRange" data-min-type="'+item.minType+'" data-minimum="'+item.minimum+'" data-discount-type="'+item.discountType+'" data-discount="'+item.discount+'" data-from="'+item.from+'" data-to="'+item.to+'">\n';
                block += 'Date Range | ';
                block += item.from + ' - ' + item.to + ' | ';
                block += 'Discount: ';
                block += item.discountType === 'percentage' ? '%' : '€';
                block += item.discount + ' | ';
                block += item.minType === 'minPerson' ? 'Min. Person: ' : 'Min. Cart Total: ';
                block += item.minimum + ' | ';
                block += 'Click To Delete\n';
                block += '</button>\n';
                block += '</div>';
                block += '<button id="deleteOldSpecialOffer" style="font-size: 9px;" class="btn btn-primary hidden-sm hidden-md hidden-lg" data-date-type="dateRange" data-min-type="'+item.minType+'" data-minimum="'+item.minimum+'" data-discount-type="'+item.discountType+'" data-discount="'+item.discount+'" data-from="'+item.from+'" data-to="'+item.to+'">\n';
                block += item.from + ' - ' + item.to + ' | ';
                block += item.discountType === 'percentage' ? '%' : '€';
                block += 'Click To Delete\n';
                block += '</button>\n';
                block += '</div>';
            });
            weekDay.forEach(function(item) {
                block += '<div class="col-md-12" style="margin: 10px;">\n';
                block += '<button id="deleteOldSpecialOffer" class="btn btn-primary" data-date-type="weekDay" data-min-type="'+item.minType+'" data-minimum="'+item.minimum+'" data-discount-type="'+item.discountType+'" data-discount="'+item.discount+'" data-day-name="'+item.dayName+'">\n';
                block += 'Week Day | ';
                block += item.dayName + ' | ';
                block += 'Discount: ';
                block += item.discountType === 'percentage' ? '%' : '€';
                block += item.discount + ' | ';
                block += item.minType === 'minPerson' ? 'Min. Person: ' : 'Min. Cart Total: ';
                block += item.minimum + ' | ';
                block += 'Click To Delete\n';
                block += '</button>\n';
                block += '</div>';
            });
            randomDay.forEach(function(item) {
                block += '<div class="col-md-12" style="margin: 10px;">\n';
                block += '<button id="deleteOldSpecialOffer" class="btn btn-primary" data-date-type="randomDay" data-min-type="'+item.minType+'" data-minimum="'+item.minimum+'" data-discount-type="'+item.discountType+'" data-discount="'+item.discount+'" data-day="'+item.day+'">\n';
                block += 'Random Day | ';
                block += item.day + ' | ';
                block += 'Discount: ';
                block += item.discountType === 'percentage' ? '%' : '€';
                block += item.discount + ' | ';
                block += item.minType === 'minPerson' ? 'Min. Person: ' : 'Min. Cart Total: ';
                block += item.minimum + ' | ';
                block += 'Click To Delete\n';
                block += '</button>\n';
                block += '</div>';
            });
            dateTimes.forEach(function(item) {
                block += '<div class="col-md-12" style="margin: 10px;">\n';
                block += '<button id="deleteOldSpecialOffer" class="btn btn-primary" data-date-type="dateTimes" data-min-type="'+item.minType+'" data-minimum="'+item.minimum+'" data-discount-type="'+item.discountType+'" data-discount="'+item.discount+'" data-day="'+item.day+'" data-hour="'+item.hour+'">\n';
                block += 'Dates & Times | ';
                block += item.day + ' ' + item.hour + ' | ';
                block += 'Discount: ';
                block += item.discountType === 'percentage' ? '%' : '€';
                block += item.discount + ' | ';
                block += item.minType === 'minPerson' ? 'Min. Person: ' : 'Min. Cart Total: ';
                block += item.minimum + ' | ';
                block += 'Click To Delete\n';
                block += '</button>\n';
                block += '</div>';
            });
            previouslyAddedOffersDiv.append(block);
        }
    }

    $('body').on('click', '#deleteOldSpecialOffer', function() {
        let productID = $('#productSelect').val();
        let optionID = $('#optionSelect').val();
        let $this = $(this);
        let dateType = $this.attr('data-date-type');
        let from = $this.attr('data-from');
        let to = $this.attr('data-to');
        let dayName = $this.attr('data-day-name');
        let day = $this.attr('data-day');
        let hour = $this.attr('data-hour');
        $.ajax({
            method: 'POST',
            url : '/special-offers/deleteOldSpecialOffer',
            data : {
                _token: $('meta[name="csrf-token"]').attr('content'),
                productID: productID,
                optionID: optionID,
                dateType: dateType,
                from: from,
                to: to,
                dayName: dayName,
                day: day,
                hour: hour,
                requestType: 'ajax'
            },
            success: function(data) {
                if (data.success) {
                    $this.parent().remove();
                    Materialize.toast(data.success, 4000, data.type);
                    return;
                }
                Materialize.toast('Something went wrong!', 4000, 'toast-alert');
            }
        });
    });

    $("#productSelect, #optionSelect").select2({
        matcher: matchCustom,
        templateResult: formatCustom
    });

    $('#minPerson').on('click', function() {
        $(this).attr('checked', 'checked');
        $('#minCartTotal').removeAttr('checked');
    });

    $('#minCartTotal').on('click', function() {
        $(this).attr('checked', 'checked');
        $('#minPerson').removeAttr('checked');
    });

    $('#percentage').on('click', function() {
        $(this).attr('checked', 'checked');
        $('#money').removeAttr('checked');
    });

    $('#money').on('click', function() {
        $(this).attr('checked', 'checked');
        $('#percentage').removeAttr('checked');
    });

});

function stringMatch(term, candidate) {
    return candidate && candidate.toLowerCase().indexOf(term.toLowerCase()) >= 0;
}

function matchCustom(params, data) {
    // If there are no search terms, return all of the data
    if ($.trim(params.term) === '') {
        return data;
    }
    // Do not display the item if there is no 'text' property
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

