$(document).ready(function(e) {

    /* toggle hide show side menu */
    (function($) {
        $('[data-toggle=offcanvas]').click(function() {
            $('.row-offcanvas').toggleClass('active');
        });
    })(jQuery);

    /* ads image choose preview */
    (function($) {
        var ratio = 0.66667;
        $('#ads-choose-image-input').change(function(e) {
            var fileReader = new FileReader();
            var file = e.currentTarget.files;
            if(file[0].type.match('image.*')) {
                fileReader.onload = function(imageFile) {
                    var image = new Image();
                    image.src = imageFile.target.result;
                    $(image).addClass('ads-frame-image');
                    $('.ads-image-preview-container')
                        .html('')
                        .append(image)
                        .height($('.ads-image-preview-container').width() * ratio);

                    $(window).resize(function(e) {
                        $('.ads-image-preview-container').height($('.ads-image-preview-container').width() * ratio);
                    });
                }
                fileReader.readAsDataURL(file[0]);
            }
        });
        $('.ads-image-preview-container').css({'overflow':'hidden'}).height($('.ads-image-preview-container').width() * ratio);
        $(window).resize(function(e) {
            $('.ads-image-preview-container').height($('.ads-image-preview-container').width() * ratio);
        });
    })(jQuery);

    /* ads edit text */
    (function($) {
        $('#ads-title').keyup(function(e) {
            $('.ads-frame .ads-frame-title').text($(e.currentTarget).val());
        });
        $('#ads-description').keyup(function(e) {
            $('.ads-frame .ads-frame-description').text($(e.currentTarget).val());
        });
    })(jQuery);

    /* tooltip */
    (function($) {
        $('.tooltip-enable-top').tooltip({"placement":"top"});
        $('.tooltip-enable-left').tooltip({"placement":"left"});
        $('.tooltip-enable-bottom').tooltip({"placement":"bottom"});
        $('.tooltip-enable-right').tooltip({"placement":"right"});
    })(jQuery);

    /* modal add views*/
    (function($) {
        $('#addViewCredit').on('show.bs.modal', function(e) {
            $('.modal-after-view-remaining').html($('.remaining-views').text());
            var currentGold = parseFloat($('.current-gold').text());
            $('.modal-current-gold').html(currentGold.toFixed(2));
            $('.viewToGoldRate').html(viewToGoldRate);
            $('.modal-remaining-views').val(0);
        })
    })(jQuery);

    /* verify/filter */
    (function($){
        $('.allowed-number-only').keydown(function(e) {
            if ( $.inArray(e.which,[8,39,37]) !== -1) return;
            else if($(e.currentTarget).val().length > 6) return false;
            else {
                if (e.shiftKey || (e.which < 48 || e.which > 57) && (e.which < 96 || e.which > 105 )) {
                    return false;
                }
            }
        }).change(function(e) {
            var inputVal = parseInt($(e.currentTarget).val());
            if(isNaN(inputVal)) inputVal = 0;
            $('.modal-after-view-remaining').html(parseInt($('.remaining-views').text()) + inputVal);
            var goldResult = (parseFloat($('.current-gold').text()) - (inputVal*viewToGoldRate));
            $('.modal-current-gold').html(goldResult.toFixed(2));
            if((goldResult < 0) || inputVal < 1) $('.add-views-save-change').removeAttr('disabled').attr('disabled', 'disabled');
            else $('.add-views-save-change').removeAttr('disabled');
            if(goldResult < 0) $('.modal-current-gold').removeClass('text-danger').addClass('text-danger');
            else $('.modal-current-gold').removeClass('text-danger');
        }).keyup(function(e) {
            var inputVal = parseInt($(e.currentTarget).val());
            if(isNaN(inputVal)) inputVal = 0;
            $('.modal-after-view-remaining').html(parseInt($('.remaining-views').text()) + inputVal);
            var goldResult = (parseFloat($('.current-gold').text()) - (inputVal*viewToGoldRate));
            $('.modal-current-gold').html(goldResult.toFixed(2));
            if(goldResult < 0 || inputVal < 1) $('.add-views-save-change').removeAttr('disabled').attr('disabled', 'disabled');
            else $('.add-views-save-change').removeAttr('disabled');
            if(goldResult < 0) $('.modal-current-gold').removeClass('text-danger').addClass('text-danger');
            else $('.modal-current-gold').removeClass('text-danger');
        });
    })(jQuery);

    /* add views limit */
    (function($) {
        $('.ajax-result-alert').alert('close');
        $('.views-remaining-progress').hide();
        $('.current-gold-progress').hide();
        $('#addViewCreditSave').click(function(e) {
            var views = $('.modal-remaining-views').val();
            var adsId = $('#adsId').val();
            $('#addViewCredit').modal('hide');
            $('.views-remaining-progress').show();
            $('.current-gold-progress').show();
            $.ajax( {
                type: 'POST',
                data: {"requestViews":views, "adsId":adsId},
                url: '/vigattinads/json-service/add-view-credit/post',
                dataType: 'json',
                beforeSend: function(jqXHR, settings) {
                },
                complete: function(jqXHR, textStatus) {
                    $('.views-remaining-progress').hide();
                    $('.current-gold-progress').hide();
                    if(textStatus != 'success') {
                        console.log('ads view failed');
                    }
                },
                success: function(data, textStatus, jqXHR) {
                    if(data.status == 'success') {
                        $('.current-gold').html(data.gold);
                        $('.remaining-views').html(data.views);
                        $('.current-gold-success').show().fadeOut(1000);
                        $('.views-remaining-success').show().fadeOut(1000);
                        $('.ajax-result-alert .title').html('Success!');
                        $('.ajax-result-alert .message').html('');
                        $('.ajax-result-alert').alert();
                    }
                    else {

                    }
                }
            });
        });
    })(jQuery);

});
