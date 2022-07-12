</div>
</div>
</body>
<script src="{{asset('js/jquery-latest.min.js')}}"></script>
<script src="{{asset('js/admin/jquery.min.js')}}"></script>
<script src="{{asset('js/admin/bootstrap.min.js')}}"></script>
<script src="{{asset('js/admin/materialize.min.js')}}"></script>
<script src="{{asset('js/admin/custom.js')}}"></script>
<script src="{{asset('js/lodash.js')}}"></script>

<script>
    $(function() {
        $('body').on('click', '.addTier', function() {
            let tierIterator = $('.tierIterator').val();
            if ($('.adultDiv #maxPerson' + tierIterator).val() === '') {
                Materialize.toast('You should type Max Person count!', 4000, 'toast-alert');
                return;
            }
            let existingMaxPerson = parseInt($('.adultDiv #maxPerson' + tierIterator).val()) + 1;
            let it = parseInt(tierIterator) + 1;
            let block = '';
            block +=
                '<div class="col-md-12 categoryWrapper">\n' +
                '    <div class="input-field col-md-1 s1">\n' +
                '    </div>\n' +
                '    <div class="input-field col-md-1 s1">\n' +
                '    </div>\n' +
                '    <div class="col-md-2 s2">\n' +
                '    </div>\n' +
                '    <div class="input-field col-md-1 s1">\n' +
                '        <input id="minPerson'+it+'" name="minPerson'+it+'" type="hidden" value="'+existingMaxPerson+'" class="validate form-control minPerson">\n' +
                '        <label id="minPersonLabel'+it+'">'+existingMaxPerson+' -</label>\n' +
                '    </div>\n' +
                '    <div class="input-field col-md-1 s1">\n' +
                '        <input id="maxPerson'+it+'" name="maxPerson'+it+'" type="number" min="'+existingMaxPerson+'" class="validate form-control maxPerson">\n' +
                '        <label id="maxPersonLabel'+it+'">Max. Person</label>\n' +
                '    </div>\n' +
                '    <div class="input-field col-md-1 s1">\n' +
                '        <input id="price'+it+'" name="price'+it+'" type="number" onkeyup="calculateComission(\'price'+it+'\', $(this));" step="any" class="validate form-control price">\n' +
                '        <label for="price'+it+'">Price</label>\n' +
                '    </div>\n' +
                '    <div class="col-md-2 s2">\n' +
                '        <label for="price'+it+'Com">Price You Earn</label>\n' +
                '        <div>\n' +
                '            <span class="priceCom" id="price'+it+'Com" style="color: #ff0000;"></span>\n' +
                '        </div>\n' +
                '    </div>\n' +
                '    <div class="input-field col-md-1 s1">\n' +
                '        <button id="deleteTier'+it+'" class="btn btn-primary deleteTier">X</button>\n' +
                '    </div>\n'+
                '</div>';
            $('.categoryDiv').append(block);
            $('.tierIterator').val(it);
        });

        $('body').on('click', '.deleteTier', function() {
            let it = $('.tierIterator').val();
            $('.tierIterator').val(parseInt(it) - 1);
            $('body #deleteTier'+it).parent().parent().remove();
        });

           function reSortCategoryDiv(){
           var result = $('.categoryDiv').sort(function (a, b) {

          var contentA =parseInt( $(a).data('sort'));
          var contentB =parseInt( $(b).data('sort'));
          return (contentA < contentB) ? -1 : (contentA > contentB) ? 1 : 0;
         });

         $('.perPersonDiv').html(result);
        }
        reSortCategoryDiv();

        $('#categorySelect').on('change', function() {
            let sort = $(this).find("option:selected").attr("data-sort");
            let value = $(this).val();
            let capitalized = value.charAt(0).toUpperCase() + value.slice(1);
            $('#categorySelect option[value="'+value+'"]').remove();
            $('.adultDiv').clone().appendTo('.perPersonDiv');
            $('.categoryDiv.adultDiv').eq(0).attr("data-sort",sort);
            $('.categoryDiv.adultDiv').eq(0).removeClass('adultDiv').addClass(value+'Div');
            $('.'+value+'Div #adultLabel').eq(0).removeAttr('id').attr('id', value+'Label');
            $('.'+value+'Div #adultMin').eq(0).removeAttr('id').attr('id', value+'Min');
            $('.'+value+'Div #adultMax').eq(0).removeAttr('id').attr('id', value+'Max');
            $('.'+value+'Div #ignoreadult').eq(0).removeAttr('id').attr('id', 'ignore'+value);
            $('.'+value+'Div .adultRemove').eq(0).removeClass('adultRemove').addClass(value+'Remove');
            $('.'+value+'Div .priceCategory').eq(0).val(value);
            $('#'+value+'Min').val('');
            $('#'+value+'Max').val('');
            $('.'+value+'Div #'+value+'Label').html(capitalized);
            $('.'+value+'Div .'+value+'Remove').attr('data-cat', value);
            $('.'+value+'Div .'+value+'Remove').show();
            reSortCategoryDiv();
        });

        $('body').on('keyup', '.maxPerson', function() {
            let id = $(this).attr('id');
            let value = $(this).val();
            $('.perPersonDiv #'+id).val(value);
        });

        $('body').on('click', '.infantRemove,.childRemove,.youthRemove,.adultRemove,.euCitizenRemove', function() {
            let sortArray = {"euCitizen" : 1, "infant" : 4, "child" : 3, "youth" : 2};
            let cat = $(this).attr('data-cat');
            let capitalized = cat.charAt(0).toUpperCase() + cat.slice(1);
           $('#categorySelect').append('<option value="'+cat+'" data-sort="'+sortArray[cat]+'">'+capitalized+'</option>');
            $(this).parent().parent().remove();
            reSortCategoryDiv();
        });

        $('#priceButton').on('click', function() {
            let pricingID = $('.pricingID').val();
            let title = $('#pricingTitle').val();
            if (title === '') {
                Materialize.toast('Title can\'t be blank!', 4000, 'toast-alert');
                return;
            }
            let adultMin = $('#adultMin').val();
            let adultMax = $('#adultMax').val();
            let youthMin = $('#youthMin').val();
            let youthMax = $('#youthMax').val();
            let childMin = $('#childMin').val();
            let childMax = $('#childMax').val();
            let infantMin = $('#infantMin').val();
            let infantMax = $('#infantMax').val();
            let euCitizenMin = $('#euCitizenMin').val();
            let euCitizenMax = $('#euCitizenMax').val();
            let categories = ["adult", "youth", "child", "infant", "euCitizen"];
            let ignoredCategories = [];
            $.each(categories, function(index, value) {
                if ($('input[id="ignore'+value+'"]:checked').val() === "1") {
                    ignoredCategories.push(value);
                }
            });
            let minPerson = [];
            let maxPerson = [];
            $('.adultDiv .minPerson').each(function(index, item) {
                minPerson.push(item.value);
            });
            $('.adultDiv .maxPerson').each(function(index, item) {
                maxPerson.push(item.value);
            });
            let infantPrice = [];
            $('.infantDiv .price').each(function(index, item) {
                infantPrice.push(item.value);
            });
            let infantPriceCom = [];
            $('.infantDiv .priceCom').each(function(index, item) {
                infantPriceCom.push(item.innerText);
            });
            let childPrice = [];
            $('.childDiv .price').each(function(index, item) {
                childPrice.push(item.value);
            });
            let childPriceCom = [];
            $('.childDiv .priceCom').each(function(index, item) {
                childPriceCom.push(item.innerText);
            });
            let youthPrice = [];
            $('.youthDiv .price').each(function(index, item) {
                youthPrice.push(item.value);
            });
            let youthPriceCom = [];
            $('.youthDiv .priceCom').each(function(index, item) {
                youthPriceCom.push(item.innerText);
            });
            let adultPrice = [];
            $('.adultDiv .price').each(function(index, item) {
                adultPrice.push(item.value);
            });
            let adultPriceCom = [];
            $('.adultDiv .priceCom').each(function(index, item) {
                adultPriceCom.push(item.innerText);
            });
            let euCitizenPrice = [];
            $('.euCitizenDiv .price').each(function(index, item) {
                euCitizenPrice.push(item.value);
            });
            let euCitizenPriceCom = [];
            $('.euCitizenDiv .priceCom').each(function(index, item) {
                euCitizenPriceCom.push(item.innerText);
            });
            let tierCount = $('.tierIterator').val();
            if (isMinMaxAgesValid()) {
                $.ajax({
                    method: 'POST',
                    url: '/pricing/update',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        pricingID: pricingID,
                        title: title,
                        ignoredCategories: ignoredCategories,
                        adultMin: adultMin,
                        adultMax: adultMax,
                        adultPrice: adultPrice,
                        adultPriceCom: adultPriceCom,
                        youthMin: youthMin,
                        youthMax: youthMax,
                        youthPrice: youthPrice,
                        youthPriceCom: youthPriceCom,
                        childMin: childMin,
                        childMax: childMax,
                        childPrice: childPrice,
                        childPriceCom: childPriceCom,
                        infantMin: infantMin,
                        infantMax: infantMax,
                        infantPrice: infantPrice,
                        infantPriceCom: infantPriceCom,
                        euCitizenMin: euCitizenMin,
                        euCitizenMax: euCitizenMax,
                        euCitizenPrice: euCitizenPrice,
                        euCitizenPriceCom: euCitizenPriceCom,
                        minPerson: minPerson,
                        maxPerson: maxPerson,
                        tierCount: tierCount
                    },
                    success: function(data) {
                        if (data.success) {
                            Materialize.toast('Pricing is successfully added! You will be redirected to pricing list in 3 seconds', 4000, 'toast-success');
                            window.setTimeout(function() {
                                window.location.href = '/pricings';
                            }, 3000);
                        }
                    }
                });
            } else {
                Materialize.toast('Please fill age ranges correctly!', 4000, 'toast-alert');
            }
        });
    });

    function calculateComission(which, $this) {
        let category = $this.parent().parent().parent().find('.priceCategory').val();
        let userType = $('.userType').val();
        let price = $('body .'+category+'Div #'+which).val();
        let comission = $('.comission').val();
        if (userType === 'supplier') {
            $('body .'+category+'Div #'+which+'Com').html((price - (price * (comission / 100))).toFixed(2));
        } else {
            $('body .'+category+'Div #'+which+'Com').html(price);
        }
    }

    function isMinMaxAgesValid() {
        let agesArr = [];
        let agesArr2 = [];
        let adultMax = $('#adultMax').val();
        let adultMaxInt = parseInt(adultMax);
        let adultMin = $('#adultMin').val();
        let adultMinInt = parseInt(adultMin);

        if (isNaN(adultMaxInt) || isNaN(adultMinInt)) return false;
        if ((adultMax === '' && adultMin !== '') || (adultMax !== '' && adultMin === '') || adultMinInt > adultMaxInt) return false;

        if (adultMin !== '') {
            agesArr.push(adultMinInt);
            agesArr2.push(adultMinInt);
        }
        
        let euCitizenMax = $('#euCitizenMax').val();
        let euCitizenMaxInt = parseInt(euCitizenMax);
        let euCitizenMin = $('#euCitizenMin').val();
        let euCitizenMinInt = parseInt(euCitizenMin);

        if ($('#euCitizenMax')[0]) {
            if (isNaN(euCitizenMaxInt)) return false;
        }
        if ($('#euCitizenMin')[0]) {
            if (isNaN(euCitizenMinInt)) return false;
            if ((euCitizenMax === '' && euCitizenMin !== '') || (euCitizenMax !== '' && euCitizenMin === '') || euCitizenMinInt > euCitizenMaxInt) return false;
        }

        let youthMax = $('#youthMax').val();
        let youthMaxInt = parseInt(youthMax);
        let youthMin = $('#youthMin').val();
        let youthMinInt = parseInt(youthMin);

        if ($('#youthMax')[0]) {
            if (isNaN(youthMaxInt)) return false;
            if (youthMax !== '') {
                agesArr.push(youthMaxInt);
                agesArr2.push(youthMaxInt);
            }
        }
        if ($('#youthMin')[0]) {
            if (isNaN(youthMinInt)) return false;
            if ((youthMax === '' && youthMin !== '') || (youthMax !== '' && youthMin === '') || youthMinInt > youthMaxInt) return false;
            if (youthMin !== '' && youthMinInt != youthMinInt) {
                agesArr.push(youthMinInt);
                agesArr2.push(youthMinInt);
            }
        }

        let childMax = $('#childMax').val();
        let childMaxInt = parseInt(childMax);
        let childMin = $('#childMin').val();
        let childMinInt = parseInt(childMin);

        if ($('#childMax')[0]) {
            if (isNaN(childMaxInt)) return false;
            if (childMax !== '') {
                agesArr.push(childMaxInt);
                agesArr2.push(childMaxInt);
            }
        }

        if ($('#childMin')[0]) {
            if (isNaN(childMinInt)) return false;
            if ((childMax === '' && childMin !== '') || (childMax !== '' && childMin === '') || childMinInt > childMaxInt) return false;
            if (childMin !== '' && childMaxInt != childMinInt) {
                agesArr.push(childMinInt);
                agesArr2.push(childMinInt);
            }
        }
        let infantMax = $('#infantMax').val();
        let infantMaxInt = parseInt(infantMax);
        let infantMin = $('#infantMin').val();
        let infantMinInt = parseInt(infantMin);

        if ($('#infantMax')[0]) {
            if (isNaN(infantMaxInt)) return false;
            if (infantMax !== '') {
                agesArr.push(infantMaxInt);
                agesArr2.push(infantMaxInt);
            }
        }
        if ($('#infantMin')[0]) {
            if (isNaN(infantMinInt)) return false;
            if ((infantMax === '' && infantMin !== '') || (infantMax !== '' && infantMin === '') || infantMinInt > infantMaxInt) return false;
        }
        let sortedAgesArr = agesArr2.sort((a, b) => b - a);
        let findDuplicates = arr => arr.filter((item, index) => arr.indexOf(item) !== index);
        if (findDuplicates(sortedAgesArr).length > 0) return false;
        return _.isEqual(agesArr, sortedAgesArr);
    }

</script>
