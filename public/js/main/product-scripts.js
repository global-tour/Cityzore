$(function() {
   var control = 0;
    $('body').on('click', '.-disabled-', function() {
        let translationArray = JSON.parse($('#translationArray').val());
        Materialize.toast(translationArray['noAvailableSlots'], 4000, 'toast-alert');
    });

    $('#bookNow').on('click', function() {
        let currenciesAsStr = {'1': '$', '2': '€', '3': '£', '4': '₺', '5': 'ƒ', '6': 'C$', '7': '₽', '8': 'AED', '9': '₻', '10': '¥', '11': '₹', '12': 'CSK', '13': '₼', '14': '원', '15': 'QAR', '16': '฿'};
        let adultCount = parseInt($('#adultInput').val());
        let youthCount = parseInt($('#youthInput').val());
        let childCount = parseInt($('#childInput').val());
        let infantCount = parseInt($('#infantInput').val());
        let euCitizenCount = parseInt($('#euCitizenInput').val());
        let productOption = $('.productOption:checked').val();
        let selectedDate = $('#selectedDate').val();
        let cartCount = $('#cartCount');
        let cartTable = $("#cartTable");
        let productID = $("#productID").val();
        let totalPrice = $('#totalPriceHidden').val();
        let translationArray = JSON.parse($('#translationArray').val());
        let totalPriceWOSO = $('#totalPriceWOSOHidden').val();
        let specials = $('#specials').val();
        let obj = [];
        for (let i = 0; i < $('[class^=dayTimesDiv]').length; i++) {
            let selectedHour = $(".dayTimesDiv"+i+" input[type='radio']:checked").val();
            let tootbus_availability_id = $(".dayTimesDiv"+i+" input[type='radio']:checked").attr("data-tootbus-availability-id");
            obj.push({
                id: tootbus_availability_id,
                day: selectedDate,
                hour: selectedHour,
                ticket: adultCount + youthCount + childCount + infantCount + euCitizenCount,
            });
        }

        let visitorCurrencyCode = $('#sessionCurrency').val();

        if (totalPrice > 0) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '/addToCart',
                data: {
                    productOption: productOption,
                    obj: obj,
                    productID: productID,
                    adultCount: adultCount,
                    youthCount: youthCount,
                    childCount: childCount,
                    infantCount: infantCount,
                    euCitizenCount: euCitizenCount,
                    totalPrice: totalPrice,
                    totalPriceWOSO: totalPriceWOSO,
                    specials: specials,
                    currencyID: visitorCurrencyCode
                },
                success: function(data) {
                    if(data.status == "0"){
                         var respText = data.message;
                        if(data.message.indexOf("response") !== -1){
                            var matches = data.message.match(/response.*/gms);
                            if(matches){
                                respText = matches[0];
                            }
                        }

                          Materialize.toast(respText, 10000, 'toast-alert');
                          return false;
                    }


                    cartCount.html(data.count);
                    $('#emptyCart').hide();
                    $('.cartSuccessSpan').show();
                    $('.cartErrorSpan').hide();
                    let currencySymbol = currenciesAsStr[visitorCurrencyCode];
                    cartTable.append(
                        '<tbody>' +
                        '<tr>'+
                        '<td rowspan="3" style="vertical-align: middle; padding: 3px;">' +
                        '<img id="image" src="https://cityzore.s3.eu-central-1.amazonaws.com/product-images-xs/'+data.image+'" alt="" style=" width:80px; height: 80px; border-radius: 25%; margin-right: 10px;">'+
                        '</td>' +
                        '<td id="productName" style="padding: 3px;"><span style="font-weight: bold; color: #2d5d73;">'+data.optionTitle+'</span></td>'+
                        '<td rowspan="3" style="padding: 3px; vertical-align: middle;"><a href="/deleteItemFromCart/'+data.id+'"><img src="{{asset(\'img/icon/trash.png\')}}" class="info-icon" style="width:20px;" /></a></td>'+
                        '</tr>'+
                        ' <tr>'+
                        '<td style="border: none; padding: 3px;">'+translationArray['ADULT']+' x '+data.adultCount+'</td>' +
                        '</tr>'+
                        ' <tr>'+
                        '<td style="border: none; padding: 3px;">'+translationArray['EUCITIZEN']+' x '+data.euCitizenCount+'</td>' +
                        '</tr>'+
                        ' <tr>'+
                        '<td style="border: none; padding: 3px;">'+translationArray['YOUTH']+' x '+data.youthCount+'</td>' +
                        '<td style="border: none; padding: 3px;">'+translationArray['CHILD']+' x '+data.childCount+'</td>' +
                        '<td style="border: none; padding: 3px;">'+translationArray['INFANT']+' x '+data.infantCount+'</td>' +
                        '</tr>'+
                        '<tr>' +
                        '<td style="border: none; padding: 3px;">'+currencySymbol+' '+data.totalPrice+'</td>' +
                        '</tr>'+
                        '</tbody>');
                    let langCode = $('#sessionLocale').val();
                    let langCodeForUrl = langCode === 'en' ? '' : '/' + langCode;
                    let cartRouteLocalization = $('#cartRouteLocalization').val();
                    window.location.href = langCodeForUrl + '/' + cartRouteLocalization;
                },
                error: function() {
                    $('.cartErrorSpan').show();
                    $('.cartSuccessSpan').hide();
                }
            });
        } else {
            Materialize.toast(translationArray['error'], 4000, 'toast-alert');
        }

    });

    $('body').on('click', 'input[type=\'radio\']', function() {
        let $this = $(this);
        let availabilityType = $('#availabilityType').val();
        let productOption = $(".productOption:checked").val();
        if (availabilityType === 'Starting Time') {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '/getPricingAndSpecialOffer',
                data: {
                    selectedDate: $('#selectedDate').val(),
                    productOption: productOption,
                    productID: $('#productID').val(),
                    hour: $this.val()
                },
                success: function(data) {
                    appendPricingWithSpecialOffer(data);
                }
            });
        }
    });

    function appendPricingWithSpecialOffer(data) {
        let currenciesAsStr = {'1': '$', '2': '€', '3': '£', '4': '₺', '5': 'ƒ', '6': 'C$', '7': '₽', '8': 'AED', '9': '₻', '10': '¥', '11': '₹', '12': 'CSK', '13': '₼', '14': '원', '15': 'QAR', '16': '฿'};
        let visitorCurrencyCode = $('#sessionCurrency').val();
        let commission = data.commission;
        if (data.pricing) {
            let priceInfoSection = $('#priceInfoSection');
            priceInfoSection.html('');
            let pricing = data.pricing;
            let minPersonArr = JSON.parse(pricing.minPerson);
            let maxPersonArr = JSON.parse(pricing.maxPerson);
            let adultCount = parseInt($('#adultInput').val());
            let youngCount = parseInt($('#youthInput').val());
            let childCount = parseInt($('#childInput').val());
            let infantCount = parseInt($('#infantInput').val());
            let euCitizenCount = parseInt($('#euCitizenInput').val());
            let adultPriceArr = pricing.adultPrice === null ? null : JSON.parse(pricing.adultPrice);
            let youthPriceArr = pricing.youthPrice === null ? null : JSON.parse(pricing.youthPrice);
            let childPriceArr = pricing.childPrice === null ? null : JSON.parse(pricing.childPrice);
            let infantPriceArr = pricing.infantPrice === null ? null : JSON.parse(pricing.infantPrice);
            let euCitizenPriceArr = pricing.euCitizenPrice === null ? null : JSON.parse(pricing.euCitizenPrice);
            let adultPrice = 0;
            let youthPrice = 0;
            let childPrice = 0;
            let infantPrice = 0;
            let euCitizenPrice = 0;
            maxPersonArr.forEach(function(item, index) {
                if (adultCount > 0 && adultCount <= parseInt(item) && adultCount >= parseInt(minPersonArr[index])) {
                    adultPrice = adultPriceArr[index];
                }
                if (youngCount > 0 && youthPriceArr && youngCount <= parseInt(item) && youngCount >= parseInt(minPersonArr[index])) {
                    youthPrice = youthPriceArr[index];
                }
                if (childCount > 0 && childPriceArr && childCount <= parseInt(item) && childCount >= parseInt(minPersonArr[index])) {
                    childPrice = childPriceArr[index];
                }
                if (infantCount > 0 && infantPriceArr && infantCount <= parseInt(item) && infantCount >= parseInt(minPersonArr[index])) {
                    infantPrice = infantPriceArr[index];
                }
                if (euCitizenCount > 0 && euCitizenPriceArr && euCitizenCount <= parseInt(item) && euCitizenCount >= parseInt(minPersonArr[index])) {
                    euCitizenPrice = euCitizenPriceArr[index];
                }
            });
            let totalCount = adultCount + youngCount + childCount + infantCount + euCitizenCount;
            let block = '';
            let soWillBeApplied = false;
            let totalPrice = (adultCount * parseFloat(adultPrice)) +
                (youngCount * parseFloat(youthPrice)) +
                (childCount * parseFloat(childPrice)) +
                (infantCount * parseFloat(infantPrice)) +
                (euCitizenCount * parseFloat(euCitizenPrice));

            let euroValue = parseFloat($('#euroValue').val());
            let desiredValue = parseFloat($('#desiredCurrencyValue').val());
            totalPrice = (parseFloat(totalPrice) * euroValue / desiredValue).toFixed(2);

            $('#totalPriceWOSOHidden').val(totalPrice);


            // Special Offers will be applied if there is one
            let specials = null;
            let x = null;
            if (data.specials.length !== 0) {
                for(let i=0; i<data.specials.length; i++) {
                    if(data.specials[i].isActive == 1) {
                        if (data.specials[i].dateType === 'dateTimes' && $('#availabilityType').val() === 'Starting Time') {
                            x = data.specials.filter(function(el) {
                                return el.hour === $(".choose-time input[type='radio']:checked").val();
                            });
                            specials = typeof x[0] === 'undefined' ? null : x[0];
                        }
                        else if(data.specials[i].dateType == 'randomDay')
                            specials = data.specials[i];
                        else if(data.specials[i].dateType == 'weekDay')
                            specials = data.specials[i];
                        else if(data.specials[i].dateType == 'dateRange')
                            specials = data.specials[i];
                    }
                }
                if (specials) {
                    if (specials.discountType === 'money' && parseFloat(specials.discount) >= parseFloat(totalPrice)) {
                        soWillBeApplied = false;
                    } else {
                        if (specials.minType === 'minPerson') {
                            if (totalCount >= parseInt(specials.minimum)) {
                                soWillBeApplied = true;
                                if (specials.discountType === 'percentage') {
                                    totalPrice = (totalPrice - ((totalPrice * parseInt(specials.discount)) / 100)).toFixed(2);
                                } else if (specials.discountType === 'money') {
                                    let discountForDesiredCurrency = (parseInt(specials.discount) * euroValue / desiredValue).toFixed(2);
                                    totalPrice = (totalPrice - totalCount*discountForDesiredCurrency).toFixed(2);
                                }
                            }
                        } else if (specials.minType === 'minCartTotal') {
                            if (totalPrice >= parseInt(specials.minimum)) {
                                soWillBeApplied = true;
                                if (specials.discountType === 'percentage') {
                                    totalPrice = (totalPrice - ((totalPrice * parseInt(specials.discount)) / 100)).toFixed(2);
                                } else if (specials.discountType === 'money') {
                                    let discountForDesiredCurrency = (parseInt(specials.discount) * euroValue / desiredValue).toFixed(2);
                                    totalPrice = (totalPrice - (totalCount*discountForDesiredCurrency)).toFixed(2);

                                }
                            }
                        }
                    }
                }
            }
            $('#specials').val(JSON.stringify(specials));
            ////
            $('#totalPriceHidden').val(totalPrice);
            let currencySymbol = currenciesAsStr[visitorCurrencyCode];
            block += '<span class="col-md-12">' + data.translationArray.totalPrice + ': ';

            if (typeof(commission) !== 'undefined' && commission !== null) {
                $('#commissionerEarns').val(commission.commission);
                block += currencySymbol + ' ' + totalPrice + '</span>\n'+
                    '<span id="COM" class="col-md-12">'+ currencySymbol + ' ' +((totalPrice * commission.commission) / 100).toFixed(2) + ' COM </span><span class="col-md-12">';
            } else if ($('#commissionerEarns').val()) {
                if($('#commissionerEarnsType').val() == 'percentage')
                    block += currencySymbol + ' ' + totalPrice + '</span>\n' +
                        '<span id="COM" class="col-md-12">' + currencySymbol + ' ' + (totalPrice * $('#commissionerEarns').val() / 100).toFixed(2) + ' COM </span><span class="col-md-12">';
                else if($('#commissionerEarnsType').val() == 'money')
                    block += currencySymbol + ' ' + totalPrice + '</span>\n' +
                        '<span id="COM" class="col-md-12">' + currencySymbol + ' ' + $('#commissionerEarns').val() + ' COM </span><span class="col-md-12">';
            } else {
                block +='</span>' +
                    '<span class="col-md-12">' + currencySymbol + ' ' +totalPrice+ '</span><span class="col-md-12">';
            }
            if (adultCount > 0) {
                adultPrice = (parseFloat(adultPrice) * euroValue / desiredValue).toFixed(2);
                if (soWillBeApplied) {
                    if (specials.discountType === 'percentage') {
                        adultPrice = (adultPrice * adultCount).toFixed(2);
                        block += '<span style="text-decoration: line-through;color: #8a8181">';
                        block += currencySymbol;
                        block += (parseFloat(adultPrice)).toFixed(2)+'</span> (x '+adultCount + ' ' + data.translationArray.adult + ')';
                    }
                } else {
                    block += '(';
                    block += currencySymbol;
                    block += (parseFloat(adultPrice)).toFixed(2)+' x '+adultCount + ' ' + data.translationArray.adult + ')';
                }
            }
            if (euCitizenCount > 0) {
                euCitizenPrice = (parseFloat(euCitizenPrice) * euroValue / desiredValue).toFixed(2);
                if (soWillBeApplied) {
                    if (specials.discountType === 'percentage') {
                        euCitizenPrice = (euCitizenPrice * euCitizenCount).toFixed(2);
                        block += '<span style="text-decoration: line-through;color: #8a8181">';
                        block += currencySymbol;
                        block += (parseFloat(euCitizenPrice)).toFixed(2)+'</span> (x '+euCitizenCount+' EU Citizens)';
                    }
                } else {
                    block += '(';
                    block += currencySymbol;
                    block += (parseFloat(euCitizenPrice)).toFixed(2)+' x '+euCitizenCount+' EU CITIZEN)';
                }
            }
            if (youngCount > 0) {
                youthPrice = (parseFloat(youthPrice) * euroValue / desiredValue).toFixed(2);
                if (soWillBeApplied) {
                    if (specials.discountType === 'percentage') {
                        youthPrice = (youthPrice * youngCount).toFixed(2);
                        block += '<span style="text-decoration: line-through;color: #8a8181">';
                        block += currencySymbol;
                        block += (parseFloat(youthPrice)).toFixed(2)+'</span> (x '+youngCount + ' ' + data.translationArray.youth + ')';
                    }
                } else {
                    block += ' + (';
                    block += currencySymbol;
                    block += (parseFloat(youthPrice)).toFixed(2)+' x '+youngCount+' Youths)';
                }
            }
            if (childCount > 0) {
                childPrice = (parseFloat(childPrice) * euroValue / desiredValue).toFixed(2);
                if (soWillBeApplied) {
                    if (specials.discountType === 'percentage') {
                        childPrice = (childPrice * childCount).toFixed(2);
                        block += '<span style="text-decoration: line-through;color: #8a8181">';
                        block += currencySymbol;
                        block += (parseFloat(childPrice)).toFixed(2)+'</span> (x '+childCount + ' ' + data.translationArray.child + ')';
                    }
                } else {
                    block += ' + (';
                    block += currencySymbol;
                    block += (parseFloat(childPrice)).toFixed(2)+' x '+childCount+' Childs)';
                }
            }
            if (infantCount > 0) {
                infantPrice = (parseFloat(infantPrice) * euroValue / desiredValue).toFixed(2);
                if (soWillBeApplied) {
                    if (specials.discountType === 'percentage') {
                        infantPrice = (infantPrice * infantCount).toFixed(2);
                        block += '<span style="text-decoration: line-through;color: #8a8181">';
                        block += currencySymbol;
                        block += (parseFloat(infantPrice)).toFixed(2)+'</span> (x '+infantCount + ' ' + data.translationArray.infant + ')';
                    }
                } else {
                    block += ' + (';
                    block += currencySymbol;
                    block += (parseFloat(infantPrice)).toFixed(2)+' x '+infantCount + ' ' + data.translationArray.infant + ')';
                }
            }

            block += '</span>';
            priceInfoSection.append(block);
            priceInfoSection.show();
        }
    }

    function getAvailableDates() {
        return new Promise(function(resolve, reject) {
            let errorType = '';
            $('.choose-time').hide();
            $('#priceInfoSection').hide();
            let productOption = $(".productOption:checked").val();
            let visitorCurrencyCode = $('#sessionCurrency').val();
            let ticketCount = parseInt($('#ticketCount').val());
            let productID = $('#productID').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '/getAvailableDatesNew',
                data: {
                    productOption: productOption,
                    ticket: ticketCount,
                    visitorCurrencyCode: visitorCurrencyCode,
                    productID: productID,
                },
                success: function(data) {
                    $('#getAvailableDatesIterator').val(parseInt($('#getAvailableDatesIterator').val()) + 1);
                    if (data.hasOwnProperty('errorType')) {
                        errorType = data.errorType;
                        if (errorType === 'Max. Person Count') {
                            $('#maxPersonErrorSpan').show();
                            return;
                        }
                        if (errorType === 'Min. Person Count') {
                            $('#minPersonErrorSpan').show();
                        }
                    } else {
                        errorType = '';
                        $('#minPersonErrorSpan').hide();
                        $('#maxPersonErrorSpan').hide();
                    }
                    $('.errorType').val(errorType);
                    if (data.error) {
                        $('#dateErrorSpan').show();
                    } else {
                        $('#dateErrorSpan').hide();
                    }
                    $('#desiredCurrencyValue').val(data.desiredValue);
                    let pricing = data.pricing;
                    $('#adultAgeSpan').html('(Age '+ pricing.adultMin + ' - ' + pricing.adultMax + ')');
                    if (pricing.euCitizenMin !== null && pricing.euCitizenMax !== null && pricing.euCitizenPrice !== null) {
                        $('#euCitizenAgeSpan').html('(Age '+ pricing.euCitizenMin + ' - ' + pricing.euCitizenMax + ')');
                        $('#euCitizenSpan').show();
                        $('#euCitizenDiv').show();
                    }
                    if (pricing.youthMin !== null && pricing.youthMax !== null && pricing.youthPrice !== null) {
                        $('#youthAgeSpan').html('(Age '+ pricing.youthMin + ' - ' + pricing.youthMax + ')');
                        $('#youthSpan').show();
                        $('#youthDiv').show();
                    }
                    if (pricing.childMin !== null && pricing.childMax !== null && pricing.childPrice !== null) {
                        $('#childAgeSpan').html('(Age ' + pricing.childMin + ' - ' + pricing.childMax + ')');
                        $('#childSpan').show();
                        $('#childDiv').show();
                    }
                    if (pricing.infantMin !== null && pricing.infantMax !== null && pricing.infantPrice !== null) {
                        $('#infantAgeSpan').html('(Age ' + pricing.infantMin + ' - ' + pricing.infantMax + ')');
                        $('#infantSpan').show();
                        $('#infantDiv').show();
                    }
                    $('#disabledDates').val(JSON.stringify(data.avdates.disabledDates));
                    $('.maxAvdate').val(data.avdates.max);
                    $('.specialOffer').val(data.specialDays);
                    $('.dayToPrice').val(data.dayToPrice);
                    $('.typeToPrice').val(data.typeToPrice);
                    $('.datepicker-here').datepicker({
                        onSelect: function(formattedDate, date, inst) {
                            $('#selectedDate').val(formattedDate);

                               $("#sidebar").waitMe({
                            effect : 'bounce',
                            text : '',
                            bg : 'rgba(255,255,255,0.7)',
                            color : '#f4364f',
                            maxSize : '',
                            waitTime : -1,
                            textPos : 'vertical',
                            fontSize : '',
                            source : '',
                            onClose : function() {}
                        });


                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: 'POST',
                                url: '/getAvailableTimesNew',
                                data: {
                                    selectedDate: $('#selectedDate').val(),
                                    productOption: $('.productOption:checked').val(),
                                    ticket: parseInt($('#ticketCount').val()),
                                    productID: $('#productID').val(),
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(data) {

                                    $("#sidebar").waitMe("hide");
                                    if(data.status == "0"){
                                         Materialize.toast(data.message, 6000, 'toast-alert');
                                    }
                                    $('.choose-time').html('');
                                    $('.option-name').html('');
                                    if ($('.errorType').val() === 'Min. Person Count') {
                                        return;
                                    }
                                    let block = '';
                                    if (data.mixedAv === null) {
                                        console.log('There is no available time left on this tour');
                                    } else {
                                        let mixedAv = data.mixedAv;
                                        if (mixedAv.availability_types.length === 1 && mixedAv.availability_types[0] === 'Starting Time') {
                                            $('#availabilityType').val('Starting Time');
                                        } else {
                                            $('#availabilityType').val('');
                                        }
                                        let onlySelected = mixedAv.only_selected_times;
                                        if (onlySelected[0].length > 0) {
                                            for (let i = 0; i < onlySelected.length; i++) {
                                                if (mixedAv.availability_types[i] === 'Starting Time') {
                                                    block += '<div class="dayTimesDiv'+[i]+' row" style="margin-left: 5%;">\n' +
                                                        '<div><span style="font-weight:600; font-size: 12px; color: #D35400; text-decoration:underline;">( '+mixedAv.availability_names[i]+' )</span></div>\n' +
                                                        '<span>'+data.translationArray.chooseTime+'</span><br>';
                                                } else {
                                                    block += '<div class="dayTimesDiv'+[i]+' row" style="margin-left: 5%;">\n';
                                                    block += '<div><span style="font-weight:600; font-size: 12px; color: #D35400; text-decoration:underline;">( '+mixedAv.availability_names[i]+' )</span></div>\n';
                                                }
                                                for (let y = 0; y < onlySelected[i].length; y++) {
                                                    var checked = (y==0) ? 'checked' : '';
                                                    if (mixedAv.availability_types[i] === 'Starting Time') {
                                                        block += '<label class="col-lg-4 radio" style="margin-top: 8px;color: #504c4c;">'+onlySelected[i][y].hourFrom+'\n' +
                                                            '<input value="'+onlySelected[i][y].hourFrom+'" type="radio" '+checked+' name="times'+i+'" data-tootbus-availability-id="'+onlySelected[i][y].id+'" data-id="">\n' +
                                                            '<span class="checkmark"></span>\n' +
                                                            '</label>';
                                                    } else {
                                                        block += '<label class="radio">'+onlySelected[i][y].hourFrom+' - '+onlySelected[i][y].hourTo+'' +
                                                            '<input '+checked+' value="'+onlySelected[i][y].hourFrom+' - '+onlySelected[i][y].hourTo+'" type="radio" name="times'+i+'" data-tootbus-availability-id="'+onlySelected[i][y].id+'" data-id="">' +
                                                            '<span class="checkmark"></span>' +
                                                            '</label>';
                                                    }
                                                }
                                                block+="</div><hr style='border-top: solid 1px #ccc;'>";
                                            }
                                        } else {
                                            block += "<span>'+data.error+'</span>";
                                        }



                                        $('.choose-time').append(block);
                                        $('.choose-time').show();
                                        $('.choose-person-count').show();
                                        $('.option-name').html('<span style="color: #e1253e">'+$(".productOption:checked").attr("data-title")+'</span>');
                                    }
                                    appendPricingWithSpecialOffer(data);
                                },
                                error: function(){
                                    $("#sidebar").waitMe("hide");
                                }
                            });
                        },
                        onRenderCell: function(date, cellType) {
                            let specialOffer = data.specialOffer;
                            if ((specialOffer) !== null) {
                                //console.log(specialOffer);
                                if (data.avdates.min.length !== 0 && data.avdates.max.length !== 0) {
                                    if (cellType === 'day') {
                                        let dayToPrice = JSON.parse($('.dayToPrice').val());
                                        let typeToPrice = JSON.parse($('.typeToPrice').val());
                                        let specialDatesObj = JSON.parse($('.specialOffer').val());
                                        let disabledDates = JSON.parse($('#disabledDates').val());
                                        let isDisabled = disabledDates.includes(moment(date).format('DD/MM/YYYY'));
                                        let regularPrice = $("li.dl2 span.strikeout").eq(0).text();

                                       if(regularPrice){
                                        regularPrice = parseFloat(regularPrice);
                                       }else{
                                        regularPrice = 0;
                                       }

                                        if(specialDatesObj.length > 0){
                                            //console.log(specialDatesObj);
                                            $("#alert-special-offer").html("! Special offer for this item will start on " + specialDatesObj[0]);


                                        }

                                        let specialDiss = (dayToPrice[moment(date).format('DD/MM/YYYY')]);
                                        if(!specialDiss)
                                            specialDiss = 0;

                                        let specialType = typeToPrice[moment(date).format('DD/MM/YYYY')];


                                        let targetDayPrice = 0;

                                        console.log(specialType);

                                        let percentageSymbol = "%";
                                        if(specialType == "percentage"){

                                        targetDayPrice = regularPrice - regularPrice*(specialDiss/100);
                                        }else{
                                           percentageSymbol = "";
                                        targetDayPrice = regularPrice - specialDiss;
                                        }





                                        targetDayPrice = targetDayPrice.toFixed(2);

                                        let currency_icon_for_calendar = $(".currency-icon-for-calendar").val();


                                        return {
                                            disabled: isDisabled,
                                            html: specialDatesObj.includes(moment(date).format('DD/MM/YYYY')) && !isDisabled ? moment(date).format('DD') + '<br><div style="flex-basis:100%; height:0; padding:0; margin:0;"></div><span style="clear:both; display:block; color: #f4364f; font-size:11px;" class="dp-note"> -'+percentageSymbol +" "+ specialDiss+'</span>' : moment(date).format('DD'),
                                        }
                                    }
                                } else {
                                    return {
                                        disabled: true
                                    }
                                }
                            } else {
                                if (data.avdates.min.length !== 0 && data.avdates.max.length !== 0) {
                                    if (cellType === 'day') {
                                        let disabledDates = JSON.parse($('#disabledDates').val());
                                        let isDisabled = disabledDates.includes(moment(date).format('DD/MM/YYYY'));
                                        return {
                                            disabled: isDisabled,
                                        }
                                    }
                                } else {
                                    return {
                                        disabled: true
                                    }
                                }
                            }
                        },
                        minDate: moment(data.avdates.min).isBefore(moment()) ? moment().toDate() : moment(data.avdates.min).toDate(),
                        maxDate: moment(data.avdates.max).toDate(),
                        toggleSelected: false,
                        autoClose: true
                    });
                    $('.choose-person-count').show();
                    $('#divShow').show();

                 if(control === 0){
                    $(".datepicker--cell").removeClass('-selected-');
                    $(".datepicker--cell").not(".-disabled-").first().addClass('-selected-');
                    control = 1;
                 }


                }

            });
            resolve();
        });
    }

    $('.spinnerButtons').on('click', function() {
        let btn = $(this),
            oldValue = btn.closest('.number-spinner').find('input').val().trim(),
            newVal = 0;

        if (btn.attr('data-dir') === 'up') {
            newVal = parseInt(oldValue) + 1;
        } else {
            if (oldValue > 1) {
                newVal = parseInt(oldValue) - 1;
            } else {
                newVal = 0;
            }
        }
        btn.closest('.number-spinner').find('input').val(newVal);
        let productOption = $(".productOption:checked").val();
        let ignoredCategoryCount = 0;
        let ignoredCategoriesVal = $('#ignoredCategories'+productOption).val();
        if (ignoredCategoriesVal !== 'null') {
            let ignoredCategories = JSON.parse(ignoredCategoriesVal);
            if (ignoredCategories !== 'null') {
                ignoredCategories.forEach(function(ignoredCategory) {
                    ignoredCategoryCount += parseInt($('#'+ignoredCategory+'Input').val());
                });
            }
        }
        $('#ticketCount').val(parseInt($('#adultInput').val()) + parseInt($('#youthInput').val()) + parseInt($('#childInput').val()) + parseInt($('#infantInput').val()) + parseInt($('#euCitizenInput').val()));
        $('#ticketCount').val(parseInt($('#ticketCount').val()) - parseInt(ignoredCategoryCount));
        getAvailableDates().then(selectDate);
    });

    $('.spinnerInputs').on('keyup', function() {
        let productOption = $(".productOption:checked").val();
        let ignoredCategoryCount = 0;
        let ignoredCategoriesVal = $('#ignoredCategories'+productOption).val();
        if (ignoredCategoriesVal !== 'null') {
            let ignoredCategories = JSON.parse(ignoredCategoriesVal);
            if (ignoredCategories !== 'null') {
                ignoredCategories.forEach(function(ignoredCategory) {
                    ignoredCategoryCount += parseInt($('#'+ignoredCategory+'Input').val());
                });
            }
        }
        $('#ticketCount').val(parseInt($('#adultInput').val()) + parseInt($('#youthInput').val()) + parseInt($('#childInput').val()) + parseInt($('#infantInput').val()) + parseInt($('#euCitizenInput').val()));
        $('#ticketCount').val(parseInt($('#ticketCount').val()) - parseInt(ignoredCategoryCount));
        getAvailableDates().then(selectDate);
    });

    $('.productOption').on('change', function() {
        $('#adultInput').val(1);
        $('#ticketCount').val(1);
        $('#euCitizenSpan').hide();
        $('#euCitizenDiv').hide();
        $('#euCitizenInput').val(0);
        $('#youthSpan').hide();
        $('#youthDiv').hide();
        $('#youthInput').val(0);
        $('#childSpan').hide();
        $('#childDiv').hide();
        $('#childInput').val(0);
        $('#infantSpan').hide();
        $('#infantDiv').hide();
        $('#infantInput').val(0);
        let selectedDate = $('#selectedDate').val();
        if (selectedDate !== '') {
            getAvailableDates().then(selectDate);
        } else {
            let myDatePicker = $('.datepicker-here').datepicker().data('datepicker');
            myDatePicker.destroy();
            getAvailableDates().then(function() {
                $('.-current-').click();
            });
        }
    });

    function selectDate(selectedDate) {

        if(!selectedDate){
            selectedDate = $('#selectedDate').val();
        }

        if (selectedDate !== '') {

            let myDatePicker = $('.datepicker-here').datepicker().data('datepicker');
            myDatePicker.selectDate(moment(selectedDate, 'DD/MM/YYYY').toDate());
        }
    }

});

function changeOptionCard(id) {
    $('.optionMovable').removeClass('movable-active');
    let element = document.getElementById("movable"+id);
    element.classList.toggle("movable-active");
    $('.productOption:checked').removeAttr('checked');
    $('.productOption[value='+id+']').prop('checked', 'checked');
    $('.productOption:checked').trigger('change');

    $.ajax({
        url: '/option/getOptionIncFore',
        type: 'GET',
        data: {productID: id, langID: $('#langHidden').val()}
    })
    .done(function(data) {
        let incFore = data.incFore;

        let included = incFore.withTranslation.included ?? incFore.withoutTranslation.included;
        let notIncluded = incFore.withTranslation.notIncluded ?? incFore.withoutTranslation.notIncluded;
        let incNotIncBlock = '';

        if(included) {
            included = included.split('|');
            for(let i=0; i<included.length; i++) {
                incNotIncBlock += '<p><span style="color: green">&#10004;&nbsp</span>' + included[i] + '</p>'
            }
        }

        if(notIncluded) {
            notIncluded = notIncluded.split('|');
            for(let i=0; i<notIncluded.length; i++) {
                incNotIncBlock += '<p><span style="color: red">&#10008;&nbsp</span>' + notIncluded[i] + '</p>'
            }
        }

        let knowBeforeYouGo = incFore.withTranslation.knowBeforeYouGo ?? incFore.withoutTranslation.knowBeforeYouGo;
        let knowBeforeYouGoBlock = '';
        if(knowBeforeYouGo) {
            knowBeforeYouGo = knowBeforeYouGo.split('|');
            for(let i=0; i<knowBeforeYouGo.length; i++) {
                if(i<3) {
                    knowBeforeYouGoBlock += '<p><span>&#9864;&nbsp</span>' + knowBeforeYouGo[i] + '</p>'
                } else {
                    knowBeforeYouGoBlock += '<p class="read-more-p" style="display: none;"><span>&#9864;&nbsp</span>' + knowBeforeYouGo[i] + '</p>'
                }
            }
        }

        $('#incNotIncBlock').html('');
        $('#incNotIncBlock').append(incNotIncBlock);

        $('#knowBeforeYouGoBlock').html('');
        $('#knowBeforeYouGoBlock').append(knowBeforeYouGoBlock);

        $("#know-before-you-go-read-more").remove();
        if($("#know-before-you-go-wrap p").length > 4 && $("#know-before-you-go-read-more").length == 0){
            $("#know-before-you-go-wrap").append("<b id='know-before-you-go-read-more' data-step='read' style='text-align:center; display:block; width:100px; background-color:#1d6db212; color:#000; cursor:pointer; padding:0 10px 0 10px; margin-bottom: 20px;'> Read More </b>");
        }

    })
    .fail(function() {
        console.log("error");
    });

}

window.onload = function() {
    setTimeout(function(){
      $('.productOption:checked').trigger('change');
    }, 1000);

    executeCalendarEvents();
};

function executeCalendarEvents() {
    if (!$('#dateErrorSpan').is(':visible')) {
        //$('.productOption:checked').trigger('change');
        setTimeout(function() {
            let today = new Date();
            let dd = today.getDate();
            let mm = today.getMonth();
            let yy = today.getFullYear();
            let el = getSuitableCalendarElement(dd, mm, yy);
            if (typeof el !== 'undefined') {
                el.click();
            }
        }, 100);
    }
}

function getSuitableCalendarElement(dd, mm, yy) {
    let maxAvdate = $('.maxAvdate').val();
    if (maxAvdate === '') {
        executeCalendarEvents();
    }
    let maxAvdateYear = parseInt(maxAvdate.split('-')[0]) + 1;
    if (yy !== maxAvdateYear) {
        let el = $('.datepicker--cell.datepicker--cell-day[data-date="'+dd+'"][data-month="'+mm+'"][data-year="'+yy+'"]');
        if (el.hasClass('-disabled-')) {
            let newDD = dd + 1;
            if ([0, 2, 4, 6, 7, 9, 11].includes(mm)) {
                if (dd === 31) {
                    if (mm !== 11) {
                        mm = mm + 1;
                    } else {
                        mm = 0;
                        yy = yy + 1;
                    }
                    newDD = 1;
                    $('.datepicker--nav-action[data-action="next"]').click();
                }
            } else if (mm === 1) {
                if (dd === 28) {
                    if (mm !== 11) {
                        mm = mm + 1;
                    } else {
                        mm = 0;
                        yy = yy + 1;
                    }
                    newDD = 1;
                    $('.datepicker--nav-action[data-action="next"]').click();
                }
            } else if ([1, 3, 5, 8, 10, 12].includes(mm)) {
                if (dd === 30) {
                    if (mm !== 11) {
                        mm = mm + 1;
                    } else {
                        mm = 0;
                        yy = yy + 1;
                    }
                    newDD = 1;
                    $('.datepicker--nav-action[data-action="next"]').click();
                }
            }
            el = getSuitableCalendarElement(newDD, mm, yy);
        }
        return el;
    } else {
        $('#dateErrorSpan').show();
    }
}
