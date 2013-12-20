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

    /* modal edit reserve views */
    (function($) {
        $('#addViewCredit').on('show.bs.modal', function(e) {
            $('.modal-current-reserve-view').html($('.remaining-views').text());
            var currentGold = parseFloat($('.current-gold').text());
            $('.modal-current-gold').html(currentGold.toFixed(2));
            $('.viewToGoldRate').html(viewToGoldRate);
            $('.modal-remaining-views').val($('.remaining-views').text());
        })
    })(jQuery);

    /* edit reserve views */
    (function($) {
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
                        $('.alert-box').html('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><strong>Failed!</strong> Network error</div>');
                        $(".alert").alert();
                    }
                },
                success: function(data, textStatus, jqXHR) {
                    if(data.status == 'success') {
                        $('.current-gold').html(data.gold);
                        $('.remaining-views').html(data.views);
                        $('.current-gold-success').show().fadeOut(1000);
                        $('.views-remaining-success').show().fadeOut(1000);
                    }
                    else {
                        $('.alert-box').html('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><strong>'+data.status.charAt(0).toUpperCase()+data.status.slice(1)+'!</strong> '+data.reason.charAt(0).toUpperCase()+data.reason.slice(1)+'</div>');
                        $(".alert").alert();
                    }
                }
            });
        });
        setTimeout(function(){
            $(".alert.auto-hide").fadeOut(5000, function() {
                $(".alert.auto-hide").alert('close');
            })
        }, 10000);
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
            var goldResult = calculateCurrentGold(
                parseFloat($('.current-gold').text()),
                parseInt($('.modal-current-reserve-view').text()),
                inputVal,
                viewToGoldRate
            );
            $('.modal-current-gold').html(goldResult.toFixed(2));
            if((goldResult < 0) || inputVal == $('.modal-current-reserve-view').text()) $('.add-views-save-change').removeAttr('disabled').attr('disabled', 'disabled');
            else $('.add-views-save-change').removeAttr('disabled');
            if(goldResult < 0) $('.modal-current-gold').removeClass('text-danger').addClass('text-danger');
            else $('.modal-current-gold').removeClass('text-danger');
        }).keyup(function(e) {
            var inputVal = parseInt($(e.currentTarget).val());
            if(isNaN(inputVal)) inputVal = 0;
            var goldResult = calculateCurrentGold(
                parseFloat($('.current-gold').text()),
                parseInt($('.modal-current-reserve-view').text()),
                inputVal,
                viewToGoldRate
            );
            $('.modal-current-gold').html(goldResult.toFixed(2));
            if(goldResult < 0 || inputVal == $('.modal-current-reserve-view').text()) $('.add-views-save-change').removeAttr('disabled').attr('disabled', 'disabled');
            else $('.add-views-save-change').removeAttr('disabled');
            if(goldResult < 0) $('.modal-current-gold').removeClass('text-danger').addClass('text-danger');
            else $('.modal-current-gold').removeClass('text-danger');
        });

        function calculateCurrentGold(currentGold, currentReserve, newReserve, viewToGoldRate) {
            var viewChange = (currentReserve - newReserve) * viewToGoldRate;
            return currentGold + viewChange;
        }
    })(jQuery);

    /* ads form validate */
    (function($) {

        $("form button[type=submit]").click(function() {
            $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
            $(this).attr("clicked", "true");
        });

        function checkTitle() {
            var reg = /^[a-zA-Z0-9_\s.\'"/,&()-]*$/;
            var min = 6;
            var max = 48;
            var invalidCharMsg = 'Title has invalid character';
            var lengthErrorMsg = 'Title must be minimum of '+min+' and maximum of '+max+' character';
            var formGroup = $('.ads-form-title');
            var value = $('input', formGroup).val();
            if(value.length > max || value.length < min) {
                formGroup.removeClass('has-error').addClass('has-error');
                $('.help-block', formGroup).html(lengthErrorMsg);
                return false;
            }
            if(!value.match(reg)) {
                formGroup.removeClass('has-error').addClass('has-error');
                $('.help-block', formGroup).html(invalidCharMsg);
                return false;
            }
            formGroup.removeClass('has-error');
            $('.help-block', formGroup).html('');
            return true;
        }

        function checkUrl() {
            var reg = new RegExp('^(https?:\\/\\/)'+ // protocol
                '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
                '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
                '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
                '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
                '(\\#[-a-z\\d_]*)?$','i'); // fragment locator;
            var min = 0;
            var max = 255;
            var invalidCharMsg = 'The link you provided is not a valid url';
            var lengthErrorMsg = 'Url must be maximum of '+max+' character';
            var formGroup = $('.ads-form-url');
            var value = $('input', formGroup).val();
            if(value.length > max || value.length < min) {
                formGroup.removeClass('has-error').addClass('has-error');
                $('.help-block', formGroup).html(lengthErrorMsg);
                return false;
            }
            if(!value.match(reg)) {
                formGroup.removeClass('has-error').addClass('has-error');
                $('.help-block', formGroup).html(invalidCharMsg);
                return false;
            }
            formGroup.removeClass('has-error');
            $('.help-block', formGroup).html('');
            return true;
        }

        function checkKeyword() {
            var reg = /^[a-zA-Z0-9\s\,]*$/;
            var min = 0;
            var max = 48;
            var invalidCharMsg = 'Keyword has invalid character';
            var lengthErrorMsg = 'Keyword must be minimum of '+min+' and maximum of '+max+' character';
            var formGroup = $('.ads-form-keyword');
            var value = $('input', formGroup).val();
            if(value.length > max || value.length < min) {
                formGroup.removeClass('has-error').addClass('has-error');
                $('.help-block', formGroup).html(lengthErrorMsg);
                return false;
            }
            if(!value.match(reg)) {
                formGroup.removeClass('has-error').addClass('has-error');
                $('.help-block', formGroup).html(invalidCharMsg);
                return false;
            }
            formGroup.removeClass('has-error');
            $('.help-block', formGroup).html('');
            return true;
        }

        function checkDescription() {
            var reg = /^[a-zA-Z0-9_\s.\'"/,&()-]*$/;
            var min = 0;
            var max = 160;
            var invalidCharMsg = 'Description has invalid character';
            var lengthErrorMsg = 'Description must be minimum of '+min+' and maximum of '+max+' character';
            var formGroup = $('.ads-form-description');
            var value = $('textarea', formGroup).val();
            if(value.length > max || value.length < min) {
                formGroup.removeClass('has-error').addClass('has-error');
                $('.help-block', formGroup).html(lengthErrorMsg);
                return false;
            }
            if(!value.match(reg)) {
                formGroup.removeClass('has-error').addClass('has-error');
                $('.help-block', formGroup).html(invalidCharMsg);
                return false;
            }
            formGroup.removeClass('has-error');
            $('.help-block', formGroup).html('');
            return true;
        }

        $('#ads-form').submit(function(e) {
            var submitVal = $("button[type=submit][clicked=true]", e.currentTarget).val();
            var err = 0;
            switch(submitVal)
            {
                case 'next':
                    if(!checkTitle()) err++;
                    if(!checkUrl()) err++;
                    if(!checkKeyword()) err++;
                    if(!checkDescription()) err++;
                    if(err) return false
                    break;
                case 'delete':
                    return false;
                    break;
            }
        });
    })(jQuery);

    /* modal ads confirm delete */
    (function($) {

    })(jQuery);

});
