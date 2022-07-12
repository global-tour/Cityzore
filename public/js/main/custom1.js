function myFunction() {
    var e, n, t, l;
    for (e = document.getElementById("myInput").value.toUpperCase(), n = document.getElementById("myTable").getElementsByTagName("tr"), l = 0; l < n.length; l++) (t = n[l].getElementsByTagName("td")[1]) && (t.innerHTML.toUpperCase().indexOf(e) > -1 ? n[l].style.display = "" : n[l].style.display = "none")
}

$(document).ready(function () {
    "use strict";

    $(".about-menu").hover(function () {
        $(".about-mm").stop().fadeIn()
    }), $(".about-menu").mouseleave(function () {
        $(".about-mm").stop().fadeOut()
    }), $(".admi-menu").hover(function () {
        $(".admi-mm").fadeIn()
    }), $(".admi-menu").mouseleave(function () {
        $(".admi-mm").fadeOut()
    }), $(".cour-menu").hover(function () {
        $(".cour-mm").fadeIn()
    }), $(".cour-menu").mouseleave(function () {
        $(".cour-mm").fadeOut()
    }), $(".top-drop-menu").on("click", function () {
        $(".man-drop").fadeIn()
    }), $(".man-drop").mouseleave(function () {
        $(".man-drop").fadeOut()
    }), $(".wed-top").mouseleave(function () {
        $(".man-drop").fadeOut()
    }), $("#sf-box").on("click", function () {
        $(".sf-list").fadeIn()
    }), $(".sf-list").mouseleave(function () {
        $(".sf-list").fadeOut()
    }), $(".search-top").mouseleave(function () {
        $(".sf-list").fadeOut()
    }), $(".sdb-btn-edit").hover(function () {
        $(this).text("Click to edit my profile")
    }), $(".sdb-btn-edit").mouseleave(function () {
        $(this).text("edit my profile")
    }), $(".ed-micon").on("click", function () {
        $(".ed-mm-inn").addClass("ed-mm-act")
    }), $(".ed-mi-close").on("click", function () {
        $(".ed-mm-inn").removeClass("ed-mm-act")
    }), $(".map-container").on("click", function () {
        $(this).find("iframe").addClass("clicked")
    }).on("mouseleave", function () {
        $(this).find("iframe").removeClass("clicked")
    }), $("#status").fadeOut(), $("#preloader").delay(350).fadeOut("slow"), $("body").delay(350).css({overflow: "visible"}), $(".slider").slider(), $("#select-city,#select-city-1,#select-city-2,#select-city-3,#select-city-4,#select-city-5.autocomplete").autocomplete({
        data: {
            "New York": null,
            California: null,
            Illinois: null,
            Texas: null,
            Pennsylvania: null,
            "San Diego": null,
            "Los Angeles": null,
            Dallas: null,
            Austin: null,
            Columbus: null,
            Charlotte: null,
            "El Paso": null,
            Portland: null,
            "Las Vegas": null,
            "Oklahoma City": null,
            Milwaukee: null,
            Tucson: null,
            Sacramento: null,
            "Long Beach": null,
            Oakland: null,
            Arlington: null,
            Tampa: null,
            "Corpus Christi": null,
            Greensboro: null,
            "Jersey City": null
        }, limit: 8, onAutocomplete: function (e) {
        }, minLength: 1
    })
}), $(function () {
    var e = "mm/dd/yy", n = $("#from,#from-1,#from-2,#from-3,#from-4,#from-5").datepicker({
        defaultDate: "+1w",
        changeMonth: !1,
        numberOfMonths: 1
    }).on("change", function () {
        t.datepicker("option", "minDate", l(this))
    }), t = $("#to,#to-1,#to-2,#to-3,#to-4,#to-5").datepicker({
        defaultDate: "+1w",
        changeMonth: !1,
        numberOfMonths: 1
    }).on("change", function () {
        n.datepicker("option", "maxDate", l(this))
    });

    function l(n) {
        var t;
        try {
            t = $.datepicker.parseDate(e, n.value)
        } catch (e) {
            t = null
        }
        return t
    }
});
