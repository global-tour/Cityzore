</div>
</div>
</div>
</body>
<script src="{{asset('js/jquery-latest.min.js')}}"></script>
<script src="{{asset('js/admin/jquery.min.js')}}"></script>
<script src="{{asset('js/admin/bootstrap.min.js')}}"></script>
<script src="{{asset('js/admin/materialize.min.js')}}"></script>
<script src="{{asset('js/admin/custom.js')}}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>

    // That object is necessary for firing remove minute hour function
    let globalItObject = {
        itInternal: 1,
        itListener: function(val){},
        set it(val) {
            this.itInternal = val;
            this.itListener(val);
        },
        get it() {
            return this.itInternal;
        },
        registerListener: function(listener) {
            this.itListener = listener;
        }
    };

    // Add Hour/Minute Dynamically For Every Day
    function addMinHour(day, date, time) {
        let type = $('input[name="radioTime"]:checked').val();
        let block = '<div class="col-md-12 input-field s12 dynamicDiv">\n';
        block += '<div class="hourDivFrom col-md-2">\n' +
            '         <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="'+day+'Hour" name="'+day+'Hour'+date+'[]" value="">\n' +
            '     </div>';
        if (type === 'Operating Hours') {
            block += '<div class="hourDivToFor'+day+' col-md-2">\n' +
                '          <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="'+day+'HourTo" name="'+day+'HourTo'+date+'[]" value="">\n' +
                '     </div>';
        }
        if (type === 'Starting Time') {
            block += '<div class="removeDiv col-md-2"><a class="removeMinHourButton btn btn-danger">x</a></div></div>';
        }
        $('.'+day+'Div'+date+time).append(block);
    }

    // Add Dates Dynamically For Every Availability
    function addDates(it) {
        let type = $('input[name="radioTime"]:checked').val();
        if (it >= 1) {
            if (!validateDateTimes(type, it)) {
                return;
            }
        }
        if (it >= 2) {
            if (!validateDates(it)) {
                return;
            }
        }
        let date = it + 1;
        $('.coll-body').hide({'easing': 'swing'});
        let block = '<li class="dynamicLi">\n' +
            '            <div class="collapsible-header coll-head"><i class="icon-cz-booking"></i>Date Range | <span id="dateRangeText'+date+'" class="dateRangeText"></span></div>\n' +
            '                 <div style="border-bottom: 0!important;" class="collapsible-body coll-body">\n' +
            '                     <div class="newDateDiv" style="height: 100%!important;">\n' +
            '                         <div class="newDateWrapper">\n';
        block += ' <div class="form-group">\n' +
            '           <input type="text" class="dateRange'+date+'" name="daterange[]" value="" />\n' +
            '      </div>\n';
        block += '<div class="form-group">\n' +
            '          <label>Monday</label>\n' +
            '          <div class="mondayDiv'+date+'1">\n' +
            '               <div class="col-md-12 input-field s12">\n';
        block += '<div class="hourDivFrom col-md-2">\n' +
            '           <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="mondayHour" name="mondayHour'+date+'[]" value="">\n' +
            '     </div>';
        if (type === 'Operating Hours') {
            block += '<div class="hourDivToForMonday col-md-2">\n' +
                '         <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="mondayHourTo" name="mondayHourTo'+date+'[]" value="">\n' +
                '     </div>';
        }
        if (type === 'Starting Time') {
            block += '<div class="addMinHourButtonDiv col-md-2">\n' +
                '          <button onclick="addMinHour(\'monday\', '+date+', 1)" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></button>\n' +
                '     </div>\n';
        }
        block += '<div class="col-md-2">\n' +
            '         <button onclick="copyToAllBelow(\'monday\', '+date+')" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></button>\n' +
            '     </div>';
        block += '        </div>\n' +
            '          </div>\n' +
            '       </div>\n';
        block += '<div class="form-group">\n' +
            '              <label>Tuesday</label>\n' +
            '               <div class="tuesdayDiv'+date+'1">\n' +
            '               <div class="col-md-12 input-field s12">\n';
        block += '<div class="hourDivFrom col-md-2">\n' +
            '           <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="tuesdayHour" name="tuesdayHour'+date+'[]" value="">\n' +
            '     </div>';
        if (type === 'Operating Hours') {
            block += '<div class="hourDivToForTuesday col-md-2">\n' +
                '         <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="tuesdayHourTo" name="tuesdayHourTo'+date+'[]" value="">\n' +
                '     </div>';
        }
        if (type === 'Starting Time') {
            block += '<div class="addMinHourButtonDiv col-md-2">\n' +
                '          <button onclick="addMinHour(\'tuesday\', '+date+', 1)" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></button>\n' +
                '     </div>\n';
        }
        block += '<div class="col-md-2">\n' +
            '         <button onclick="copyToAllBelow(\'tuesday\', '+date+')" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></button>\n' +
            '     </div>';
        block += '        </div>\n' +
            '         </div>\n' +
            '     </div>\n';
        block += '<div class="form-group">\n' +
            '          <label>Wednesday</label>\n' +
            '          <div class="wednesdayDiv'+date+'1">\n' +
            '              <div class="col-md-12 input-field s12">\n';
        block += '<div class="hourDivFrom col-md-2">\n' +
            '           <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="wednesdayHour" name="wednesdayHour'+date+'[]" value="">\n' +
            '     </div>';
        if (type === 'Operating Hours') {
            block += '<div class="hourDivToForWednesday col-md-2">\n' +
                '         <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="wednesdayHourTo" name="wednesdayHourTo'+date+'[]" value="">\n' +
                '     </div>';
        }
        if (type === 'Starting Time') {
            block += '<div class="addMinHourButtonDiv col-md-2">\n' +
                '           <button onclick="addMinHour(\'wednesday\', '+date+', 1);" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></button>\n' +
                '     </div>\n';
        }
        block += '<div class="col-md-2">\n' +
            '         <button onclick="copyToAllBelow(\'wednesday\', '+date+')" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></button>\n' +
            '     </div>';
        block += '             </div>\n' +
            '             </div>\n' +
            '        </div>\n';
        block += '<div class="form-group">\n' +
            '          <label>Thursday</label>\n' +
            '          <div class="thursdayDiv'+date+'1">\n' +
            '               <div class="col-md-12 input-field s12">\n';
        block += '<div class="hourDivFrom col-md-2">\n' +
            '           <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="thursdayHour" name="thursdayHour'+date+'[]" value="">\n' +
            '     </div>';
        if (type === 'Operating Hours') {
            block += '<div class="hourDivToForThursday col-md-2">\n' +
                '         <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="thursdayHourTo" name="thursdayHourTo'+date+'[]" value="">\n' +
                '     </div>';
        }
        if (type === 'Starting Time') {
            block += '<div class="addMinHourButtonDiv col-md-2">\n' +
                '         <button onclick="addMinHour(\'thursday\', '+date+', 1);" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></button>\n' +
                '     </div>\n';
        }
        block += '<div class="col-md-2">\n' +
            '         <button onclick="copyToAllBelow(\'thursday\', '+date+')" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></button>\n' +
            '     </div>';
        block += '              </div>\n' +
            '              </div>\n' +
            '         </div>\n';
        block += '<div class="form-group">\n' +
            '          <label>Friday</label>\n' +
            '          <div class="fridayDiv'+date+'1">\n' +
            '               <div class="col-md-12 input-field s12">\n';
        block += '<div class="hourDivFrom col-md-2">\n' +
            '           <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="fridayHour" name="fridayHour'+date+'[]" value="">\n' +
            '     </div>';
        if (type === 'Operating Hours') {
            block += '<div class="hourDivToForFriday col-md-2">\n' +
                '         <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="fridayHourTo" name="fridayHourTo'+date+'[]" value="">\n' +
                '     </div>';
        }
        if (type === 'Starting Time') {
            block += '<div class="addMinHourButtonDiv col-md-2">\n' +
                '          <button onclick="addMinHour(\'friday\', '+date+', 1);" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></button>\n' +
                '     </div>\n';
        }
        block += '<div class="col-md-2">\n' +
            '         <button onclick="copyToAllBelow(\'friday\', '+date+')" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></button>\n' +
            '     </div>';
        block += '                </div>\n' +
            '               </div>\n' +
            '          </div>\n';
        block += '<div class="form-group">\n' +
            '          <label>Saturday</label>\n' +
            '          <div class="saturdayDiv'+date+'1">\n' +
            '               <div class="col-md-12 input-field s12">\n';
        block += '<div class="hourDivFrom col-md-2">\n' +
            '           <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="saturdayHour" name="saturdayHour'+date+'[]" value="">\n' +
            '     </div>';
        if (type === 'Operating Hours') {
            block += '<div class="hourDivToForSaturday col-md-2">\n' +
                '         <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="saturdayHourTo" name="saturdayHourTo'+date+'[]" value="">\n' +
                '     </div>';
        }
        if (type === 'Starting Time') {
            block += '<div class="addMinHourButtonDiv col-md-2">\n' +
                '           <button onclick="addMinHour(\'saturday\', '+date+', 1);" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></button>\n' +
                '      </div>\n';
        }
        block += '<div class="col-md-2">\n' +
            '         <button onclick="copyToAllBelow(\'saturday\', '+date+')" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></button>\n' +
            '     </div>';
        block += '                 </div>\n' +
            '                 </div>\n' +
            '            </div>\n';
        block += '<div class="form-group">\n' +
            '          <label>Sunday</label>\n' +
            '          <div class="sundayDiv'+date+'1">\n' +
            '               <div class="col-md-12 input-field s12">\n';
        block += '<div class="hourDivFrom col-md-2">\n' +
            '           <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="sundayHour" name="sundayHour'+date+'[]" value="">\n' +
            '     </div>';
        if (type === 'Operating Hours') {
            block += '<div class="hourDivToForSunday col-md-2">\n' +
                '         <input style="margin: 0 30px 20px 0;"  type="time" class="validate form-control col-md-12 s12" id="sundayHourTo" name="sundayHourTo'+date+'[]" value="">\n' +
                '     </div>';
        }
        if (type === 'Starting Time') {
            block += '<div class="addMinHourButtonDiv col-md-2">\n' +
                '          <button onclick="addMinHour(\'sunday\', '+date+', 1);" class="addMinHourButton waves-effect waves-light btn btn-primary btn-small"><i class="icon-cz-add-date"></i></button>\n' +
                '     </div>\n';
        }
        block += '<div class="col-md-2">\n' +
            '         <button onclick="copyToAllBelow(\'sunday\', '+date+')" class="copyDatesButton waves-effect waves-light btn btn-primary btn-small" style="margin-left: 10px;"><i class="icon-cz-copy"></i></button>\n' +
            '     </div>';
        block += '               </div>\n' +
            '              </div>\n' +
            '         </div>\n';
        block += '<div class="col-md-12">\n' +
            '          <button onclick="addDates('+date+');" data-id="'+date+'" class="addNewDateButton waves-effect waves-light btn btn-primary btn-small pull-right">Add New Date Range</button>\n' +
            '          <button data-id="'+date+'" class="removeDateButton btn btn-primary">Remove Date Range</button>\n' +
            '     </div>\n';
        block += '                </div>\n' +
            '               </div>\n' +
            '          </div>\n' +
            '    </li>';
        $('.collapsible.availability').append(block);
        let collBody = $('.collapsible.availability').find('.coll-body');
        collBody.eq(it).show({'easing': 'swing'});
        globalItObject.it = it + 1;
        $('button.addNewDateButton[data-id="'+it+'"]').attr('disabled', true);
        $('button.removeDateButton[data-id="'+it+'"]').attr('disabled', true);
        $('input.dateRange'+it).attr('disabled', true);
    }

    function validateSameHour(type, it) {
        let boolArr = [];
        for(let x=1; x<=it; x++) {
            let findDuplicates = arr => arr.filter((item, index) => arr.indexOf(item) !== index);
            let mondayHours = $('input[name="mondayHour'+x+'[]"]');
            let mondays = [];
            mondayHours.each(function(index, item) {
                mondays.push(item.value);
            });
            let tuesdayHours = $('input[name="tuesdayHour'+x+'[]"]');
            let tuesdays = [];
            tuesdayHours.each(function(index, item) {
                tuesdays.push(item.value);
            });
            let wednesdayHours = $('input[name="wednesdayHour'+x+'[]"]');
            let wednesdays = [];
            wednesdayHours.each(function(index, item) {
                wednesdays.push(item.value);
            });
            let thursdayHours = $('input[name="thursdayHour'+x+'[]"]');
            let thursdays = [];
            thursdayHours.each(function(index, item) {
                thursdays.push(item.value);
            });
            let fridayHours = $('input[name="fridayHour'+x+'[]"]');
            let fridays = [];
            fridayHours.each(function(index, item) {
                fridays.push(item.value);
            });
            let saturdayHours = $('input[name="saturdayHour'+x+'[]"]');
            let saturdays = [];
            saturdayHours.each(function(index, item) {
                saturdays.push(item.value);
            });
            let sundayHours = $('input[name="sundayHour'+x+'[]"]');
            let sundays = [];
            sundayHours.each(function(index, item) {
                sundays.push(item.value);
            });

            boolArr.push(!(findDuplicates(mondays).length > 0 || findDuplicates(tuesdays).length > 0 ||
                findDuplicates(wednesdays).length > 0 || findDuplicates(thursdays).length > 0 ||
                findDuplicates(fridays).length > 0 || findDuplicates(saturdays).length > 0 ||
                findDuplicates(sundays).length > 0));
        }
        return !boolArr.includes(false);
    }

    function validateDateTimes(type, it) {
        if (type === 'Starting Time') {
            if (!validateSameHour(type, it)) {
                Materialize.toast('You have same Hour & Minute! Please correct them!', 4000, 'toast-alert');
                return false;
            }
            
            let increaseIfNotEmpty = 0;
            $('input[name="mondayHour'+it+'[]"]').each(function() {
                if($(this).val() !== '')
                    increaseIfNotEmpty++;
            });
            $('input[name="tuesdayHour'+it+'[]"]').each(function() {
                if($(this).val() !== '')
                    increaseIfNotEmpty++;
            });
            $('input[name="wednesdayHour'+it+'[]"]').each(function() {
                if($(this).val() !== '')
                    increaseIfNotEmpty++;
            });
            $('input[name="thursdayHour'+it+'[]"]').each(function() {
                if($(this).val() !== '')
                    increaseIfNotEmpty++;
            });
            $('input[name="fridayHour'+it+'[]"]').each(function() {
                if($(this).val() !== '')
                    increaseIfNotEmpty++;
            });
            $('input[name="saturdayHour'+it+'[]"]').each(function() {
                if($(this).val() !== '')
                    increaseIfNotEmpty++;
            });
            $('input[name="sundayHour'+it+'[]"]').each(function() {
                if($(this).val() !== '')
                    increaseIfNotEmpty++;
            });
            if(increaseIfNotEmpty == 0) {
                Materialize.toast('You must fill at least one Hour and Minute!', 4000, 'toast-alert');
                return false;
            }

        } else {
            if (($('input[name="mondayHour'+it+'[]"]').val() === '' || $('input[name="mondayHourTo'+it+'[]"]').val() === '') &&
                ($('input[name="tuesdayHour'+it+'[]"]').val() === '' || $('input[name="tuesdayHourTo'+it+'[]"]').val() === '') &&
                ($('input[name="wednesdayHour'+it+'[]"]').val() === '' || $('input[name="wednesdayHourTo'+it+'[]"]').val() === '') &&
                ($('input[name="thursdayHour'+it+'[]"]').val() === '' || $('input[name="thursdayHourTo'+it+'[]"]').val() === '') &&
                ($('input[name="fridayHour'+it+'[]"]').val() === '' || $('input[name="fridayHourTo'+it+'[]"]').val() === '') &&
                ($('input[name="saturdayHour'+it+'[]"]').val() === '' || $('input[name="saturdayHourTo'+it+'[]"]').val() === '') &&
                ($('input[name="sundayHour'+it+'[]"]').val() === '' || $('input[name="sundayHourTo'+it+'[]"]').val() === '')) {
                Materialize.toast('You must fill at least one Hour and Minute!', 4000, 'toast-alert');
                return false;
            }
        }
        return true;
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

    function copyToAllBelow(day, date) {
        let type = $('input[name="radioTime"]:checked').val();
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
                    block += '<div class="hourDivFrom col-md-2">\n' +
                        '         <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="'+changableWeekDay+'Hour" name="'+changableWeekDay+'Hour'+date+'[]" value="'+toBeCopied.eq(x).val()+'">\n' +
                        '     </div>';
                    block += '<div class="removeDiv col-md-2"><a class="removeMinHourButton btn btn-danger">x</a></div></div>';
                }
            }
            $('.'+changableWeekDay+'Div'+date+'1').append(block);
        });
    }

    $(function() {
        $('.coll-body').show({'easing': 'swing'});

        $('input[name="radioTime"]').on('change', function() {
            let type = $('input[name="radioTime"]:checked').val();
            if (type === 'Operating Hours') {
                $('.addMinHourButtonDiv').hide();
                $('.hourDivToForMonday').show();
                $('.hourDivToForTuesday').show();
                $('.hourDivToForWednesday').show();
                $('.hourDivToForThursday').show();
                $('.hourDivToForFriday').show();
                $('.hourDivToForSaturday').show();
                $('.hourDivToForSunday').show();

                $('.regularHrefElement').hide();
                $('.regularHrefElement ~ div').each(function() {
                    if($(this).hasClass('collapse in'))
                        $(this).collapse('toggle');
                });
            }
            if (type === 'Starting Time') {
                $('.addMinHourButtonDiv').show();
                $('.hourDivToForMonday').hide();
                $('.hourDivToForTuesday').hide();
                $('.hourDivToForWednesday').hide();
                $('.hourDivToForThursday').hide();
                $('.hourDivToForFriday').hide();
                $('.hourDivToForSaturday').hide();
                $('.hourDivToForSunday').hide();

                $('.regularHrefElement').show();
            }
        });

        // Init first(static) daterangepicker
        $('.dateRange1').daterangepicker({
            'opens': 'center',
            'drops': 'down',
            'startDate': moment().format('DD/MM/YYYY'),
            'endDate': moment().add('1', 'months').format('DD/MM/YYYY'),
            "locale": {
                "format": "DD/MM/YYYY",
            }
        });

        $('.dateRangeText').html($('.dateRange1').val());

        // Made a workaround for first remove minute hour function
        let weekDaysArr = ['.mondayDiv', '.tuesdayDiv', '.wednesdayDiv', '.thursdayDiv', '.fridayDiv', '.saturdayDiv', '.sundayDiv'];
        for (let b=0; b<weekDaysArr.length; b++) {
            weekDaysArr[b] = weekDaysArr[b] + '11';
            $('.collapsible.availability').on('click', weekDaysArr[b] + ' .removeMinHourButton', function() {
                $(this).parent().parent().remove();
            });
        }

        // That workaround is starting from second. Basicly it is observing the globalItObject object and it fires the function below when it changed
        globalItObject.registerListener(function() {
            if (globalItObject.it > 1) {
                let weekDaysArr = ['.mondayDiv', '.tuesdayDiv', '.wednesdayDiv', '.thursdayDiv', '.fridayDiv', '.saturdayDiv', '.sundayDiv'];
                for (let b=0; b<weekDaysArr.length; b++) {
                    weekDaysArr[b] = weekDaysArr[b] + globalItObject.it + '1';
                    $('.collapsible.availability').on('click', weekDaysArr[b] + ' .removeMinHourButton', function() {
                        $(this).parent().parent().remove();
                    });
                }

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
                $('#dateRangeText' + globalItObject.it).html($('.dateRange' + globalItObject.it).val());
            }
            $('input[name="daterange[]"]').on('change', function() {
                let dateRangeText = $(this).parent().parent().parent().parent().parent().find('.dateRangeText');
                dateRangeText.html($(this).val());
            });
        });

        // Used for listening the first datepicker's value
        $('input[name="daterange[]"]').on('change', function() {
            let dateRangeText = $(this).parent().parent().parent().parent().parent().find('.dateRangeText');
            dateRangeText.html($(this).val());
        });

        $('.collapsible.availability').on('click', '.removeDateButton', function() {
            $(this).parent().parent().parent().parent().parent().remove();
            $('button.addNewDateButton[data-id="'+(globalItObject.it - 1)+'"]').attr('disabled', false);
            $('button.removeDateButton[data-id="'+(globalItObject.it - 1)+'"]').attr('disabled', false);
            $('input.dateRange'+(globalItObject.it - 1)).attr('disabled', false);
            globalItObject.it = globalItObject.it - 1;
            let collBody = $('.collapsible.availability').find('.coll-body');
            collBody.eq(globalItObject.it - 1).show({'easing': 'swing'});
        });

        $('#limitlessTicket').on('change', function() {
            $(this).val($(this).prop('checked') ? 1 : 0);
        });

        $('#saveAvailabilityButton').on('click', function() {
            let type = $('input[name="radioTime"]:checked').val();
            let name = $('#avName').val();
            let ticketType = $('#ticketType').val();
            let it = globalItObject.it;
            let isLimitless = $('#limitlessTicket').val();
            if (name === '') {
                Materialize.toast('Availability Name can not be blank!', 4000, 'toast-alert');
                return;
            }
            if (it >= 1) {
                if (!validateDateTimes(type, it)) {
                    return;
                }
            }
            if (it > 1) {
                if (!validateDates(it)) {
                    return;
                }
            }
            let monday = new Array(it);
            let tuesday = new Array(it);
            let wednesday = new Array(it);
            let thursday = new Array(it);
            let friday = new Array(it);
            let saturday = new Array(it);
            let sunday = new Array(it);
            let dateRanges = new Array(it);
            for (let x=0; x<it; x++) {
                monday[x] = [];
                tuesday[x] = [];
                wednesday[x] = [];
                thursday[x] = [];
                friday[x] = [];
                saturday[x] = [];
                sunday[x] = [];
                dateRanges[x] = [];
                $('input[name="mondayHour'+(x+1)+'[]"]').each(function(i) {
                    monday[x][i] = {};
                    if ($(this).val() !== '') {
                        monday[x][i]['hourFrom'] = $(this).val();
                    }
                });
                if (type === 'Operating Hours') {
                    $('input[name="mondayHourTo'+(x+1)+'[]"').each(function(i) {
                        if ($(this).val() !== '') {
                            monday[x][i]['hourTo'] = $(this).val();
                        }
                    });
                }
                $('input[name="tuesdayHour'+(x+1)+'[]"]').each(function(i) {
                    tuesday[x][i] = {};
                    if ($(this).val() !== '') {
                        tuesday[x][i]['hourFrom'] = $(this).val();
                    }
                });
                if (type === 'Operating Hours') {
                    $('input[name="tuesdayHourTo'+(x+1)+'[]"').each(function(i) {
                        if ($(this).val() !== '') {
                            tuesday[x][i]['hourTo'] = $(this).val();
                        }
                    });
                }
                $('input[name="wednesdayHour'+(x+1)+'[]"]').each(function(i) {
                    wednesday[x][i] = {};
                    if ($(this).val() !== '') {
                        wednesday[x][i]['hourFrom'] = $(this).val();
                    }
                });
                if (type === 'Operating Hours') {
                    $('input[name="wednesdayHourTo'+(x+1)+'[]"').each(function(i) {
                        if ($(this).val() !== '') {
                            wednesday[x][i]['hourTo'] = $(this).val();
                        }
                    });
                }
                $('input[name="thursdayHour'+(x+1)+'[]"]').each(function(i) {
                    thursday[x][i] = {};
                    if ($(this).val() !== '') {
                        thursday[x][i]['hourFrom'] = $(this).val();
                    }
                });
                if (type === 'Operating Hours') {
                    $('input[name="thursdayHourTo'+(x+1)+'[]"').each(function(i) {
                        if ($(this).val() !== '') {
                            thursday[x][i]['hourTo'] = $(this).val();
                        }
                    });
                }
                $('input[name="fridayHour'+(x+1)+'[]"]').each(function(i) {
                    friday[x][i] = {};
                    if ($(this).val() !== '') {
                        friday[x][i]['hourFrom'] = $(this).val();
                    }
                });
                if (type === 'Operating Hours') {
                    $('input[name="fridayHourTo'+(x+1)+'[]"').each(function(i) {
                        if ($(this).val() !== '') {
                            friday[x][i]['hourTo'] = $(this).val();
                        }
                    });
                }
                $('input[name="saturdayHour'+(x+1)+'[]"]').each(function(i) {
                    saturday[x][i] = {};
                    if ($(this).val() !== '') {
                        saturday[x][i]['hourFrom'] = $(this).val();
                    }
                });
                if (type === 'Operating Hours') {
                    $('input[name="saturdayHourTo'+(x+1)+'[]"').each(function(i) {
                        if ($(this).val() !== '') {
                            saturday[x][i]['hourTo'] = $(this).val();
                        }
                    });
                }
                $('input[name="sundayHour'+(x+1)+'[]"]').each(function(i) {
                    sunday[x][i] = {};
                    if ($(this).val() !== '') {
                        sunday[x][i]['hourFrom'] = $(this).val();
                    }
                });
                if (type === 'Operating Hours') {
                    $('input[name="sundayHourTo'+(x+1)+'[]"').each(function(i) {
                        if ($(this).val() !== '') {
                            sunday[x][i]['hourTo'] = $(this).val();
                        }
                    });
                }
                $('input[name="daterange[]"]').each(function(i) {
                    dateRanges[i] = $(this).val();
                });
            }
            $.ajax({
                method: 'POST',
                url : '/av/store',
                data : {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    type: type,
                    name: name,
                    dateRanges: dateRanges,
                    monday: monday,
                    tuesday: tuesday,
                    wednesday: wednesday,
                    thursday: thursday,
                    friday: friday,
                    saturday: saturday,
                    sunday: sunday,
                    isLimitless: isLimitless,
                    ticketType: ticketType
                },
                success: function(data) {
                    if (typeof data.availability !== 'undefined' && data.availability.availabilityType) {
                        Materialize.toast('Availability is added successfully! You will be redirected to dashboard in 3 seconds', 4000, 'toast-success');
                        window.setTimeout(function() {
                            window.location.href = '/';
                        }, 3000);
                    } else {
                        Materialize.toast(data.errors, 4000, 'toast-alert');
                    }
                },
                errors: function() {
                    Materialize.toast('An error occured while adding Availability. Please check your data and try again!', 4000, 'toast-alert');
                }
            });
        });

        $('.divideButton').on('click', function() {
            let $this = $(this);
            let $thisParent = $this.parent().parent().parent();
            
            let hourFrom = $thisParent.find('.hourRegularFrom').val();
            let hourTo = $thisParent.find('.hourRegularTo').val();
            let interval = $thisParent.find('#intervalRegular').val();

            let dividedHours = divideHours(hourFrom, hourTo, interval);
            console.log(dividedHours);
            for(let i=0; i<dividedHours.length; i++) {
                let day = ($thisParent.parent().parent().find('label').text()).toLowerCase();
                addMinHourByDivideButton(day, 1, 1, dividedHours[i]);
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
            let block = '<div class="col-md-12 input-field s12 dynamicDiv">\n';
            block += '<div class="hourDivFrom col-md-2">\n' +
                '         <input style="margin: 0 30px 20px 0;" type="time" class="validate form-control col-md-12 s12" id="'+day+'Hour" name="'+day+'Hour'+date+'[]" value="'+hour+'">\n' +
                '     </div>';
            
            block += '<div class="removeDiv col-md-2"><a class="removeMinHourButton btn btn-danger">x</a></div></div>';
            $('.'+day+'Div'+date+time).append(block);
        }

    });
</script>
