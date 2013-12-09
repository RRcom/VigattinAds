(function() {
    $(document).ready(function(e) {
        $.ajax( {
            type: 'POST',
            data: {"data":data},
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
    });
})(jQuery);

/* ads image choose preview */
$(document).ready(function(e) {
    var ratio = 0.66667;
    $('.ads-image-preview-container').css({'overflow':'hidden'}).height($('.ads-image-preview-container').width() * ratio);
    $(window).resize(function(e) {
        $('.ads-image-preview-container').height($('.ads-image-preview-container').width() * ratio);
    });
});