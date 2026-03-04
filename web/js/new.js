/*
Template Name: Color Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 4
Version: 4.2.0
Author: Sean Ngu
Website: http://www.seantheme.com/color-admin-v4.2/admin/
*/
var floatSubMenuTimeout, targetFloatMenu, handleSlimScroll = handleToggleNavProfile = function() {
        var i = $(".sidebar").attr("data-disable-slide-animation") ? 0 : 250;
        $('[data-toggle="nav-profile"]').click(function(e) { e.preventDefault(); var a = $(this).closest("li"),
                t = $(".sidebar .nav.nav-profile"),
                n = "expanding",
                o = "closing";
            $(t).is(":visible") ? ($(a).removeClass("active"), $(t).removeClass(o)) : ($(a).addClass("active"), $(t).addClass(n)), $(t).slideToggle(i, function() { $(t).is(":visible") ? ($(t).addClass("expand"), $(t).removeClass("closed")) : ($(t).addClass("closed"), $(t).removeClass("expand")), $(t).removeClass(n + " " + o) }) }) };