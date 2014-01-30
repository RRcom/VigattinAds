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

/* ads image choose preview */
$(document).ready(function() {
    var ratio = 0.66667;
    $('.ads-sidebar-generic .ads-frame .ads-content .ads-image-preview-container').css({'overflow':'hidden'}).height($('.ads-sidebar-generic .ads-frame .ads-content .ads-image-preview-container').width() * ratio);
    $(window).resize(function(e) {
        $('.ads-sidebar-generic .ads-frame .ads-content .ads-image-preview-container').height($('.ads-sidebar-generic .ads-frame .ads-content .ads-image-preview-container').width() * ratio);
    });
});