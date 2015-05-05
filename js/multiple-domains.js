jQuery(document).ready( function($) {

    $('.getCurrent').live('click', function() {
        var fullDomain = window.location.host,
            domainDiv = $(this).parent().parent(),
            rootDomain = fullDomain.split("www.")[1];
        $(domainDiv).find(".domain").val(rootDomain);
        $(domainDiv).find(".home").val('http://' + fullDomain);
        $(domainDiv).find(".siteurl").val('http://' + fullDomain);
    });

    $('.clearAll').live('click', function() {
        var domainDiv = $(this).parent().parent();
        $(domainDiv).find("input[type='text']").val('');
        $(domainDiv).find("input.siteurl, input.home").val('http://');
    });

    $('#cmt_g_analytics_enabled').live('click', function() {
        var checkbox = this,
            analyticsClass = $('.ga');
            ignoreClass = $(".ignore");

        if (checkbox.checked) {
            if (analyticsClass.hasClass("invisible")) {
                analyticsClass.removeClass("invisible");
                analyticsClass.addClass("visible");
                if ( ignoreClass.hasClass("invisible") ) {
                    ignoreClass.addClass("visible");
                    ignoreClass.removeClass("invisible");
                }
            }
        } else {
            if (analyticsClass.hasClass("visible")) {
                analyticsClass.addClass("invisible");
                analyticsClass.removeClass("visible");
                if ( ignoreClass.hasClass("visible") ) {
                    ignoreClass.addClass("invisible");
                    ignoreClass.removeClass("visible");
                }
            }
        }
    });
});