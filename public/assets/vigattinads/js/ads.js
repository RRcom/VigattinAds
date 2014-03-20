/* count views */
(function() {
    $(document).ready(function() {
        setTimeout(function() {
            var ids = [];
            $('.ads-frame').each(function(key, value) {
                ids.push($(value).attr('id'));
            });
            $.ajax( {
                type: 'POST',
                data: {"ids":ids},
                url: '/vigattinads/showads/validate',
                dataType: 'json',
                beforeSend: function(jqXHR, settings) {
                },
                complete: function(jqXHR, textStatus) {
                    if(textStatus != 'success') {
                        console.log('ads view failed');
                    }
                },
                success: function(data, textStatus, jqXHR) {
                    console.log(data);
                }
            });
        }, 1000);
    });
})(jQuery);

/* Add comma to a number */
function addCommas(nStr) {
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

/* ads image choose preview */
$(document).ready(function() {
    var ratio = 0.66667;
    $('.ads-sidebar-generic .ads-frame .ads-content .ads-image-preview-container').css({'overflow':'hidden'}).height($('.ads-sidebar-generic .ads-frame .ads-content .ads-image-preview-container').width() * ratio);
    $(window).resize(function(e) {
        $('.ads-sidebar-generic .ads-frame .ads-content .ads-image-preview-container').height($('.ads-sidebar-generic .ads-frame .ads-content .ads-image-preview-container').width() * ratio);
    });
});

/* ads auto refresh */
$(document).ready(function() {
    var refreshTime = 300; // in seconds
    var activeTime = refreshTime;
    var isFocus = true;
    var timerObject = setInterval(function() {
        if(isFocus) activeTime--;
        if(activeTime < 0) {
            activeTime = refreshTime;
            window.location.reload();
        }
    }, 1000);
    $(window).focus(function() {
        isFocus = true;
    });
    $(window).blur(function(){
        isFocus = false;
    });
});

/* Convert price to number format with comma */
$(document).ready(function() {
    $('.ads-frame .price-value').each(function(key, value) {
        $(value).text(addCommas($(value).text()));
    });
});