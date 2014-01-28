/* tools */
function getBase64Image(img) {
    var canvas = document.createElement("canvas");
    canvas.width = img.width;
    canvas.height = img.height;

    var ctx = canvas.getContext("2d");
    ctx.drawImage(img, 0, 0);

    return canvas.toDataURL();
}

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
                    $('#ads-image-data-url').val(getBase64Image(image));
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

    /* modal account form */
    (function($) {
        $('.option-edit-account').click(function(e) {
            var tableRow = $('tr#'+$(e.currentTarget).attr('target-id'));
            $('#accountForm .form-control').val('');
            $('#accountForm .form-error').html('');
            $('#accountForm .dynamic-title').html('Edit Account');
            $('#accountForm .dynamic-title').html('Edit Account');
            $('#accountForm .account-form-progress').hide();
            $('#accountForm .submit-action').html('Update');
            $('#accountForm #inputEmail1').val($('.data-email', tableRow).text());
            $('#accountForm #inputUsername1').val($('.data-username', tableRow).text());
            $('#accountForm #inputFirstName1').val($('.data-first-name', tableRow).text());
            $('#accountForm #inputLastName1').val($('.data-last-name', tableRow).text());
            $('#accountForm #inputGold1').val($('.data-gold', tableRow).text());
            $('#accountForm #inputId1').val($(e.currentTarget).attr('target-id'));
            if($('.data-privilege', tableRow).text().indexOf('B') === -1) $('#accountForm #inputPrivilegeB1').prop('checked', false);
            else $('#accountForm #inputPrivilegeB1').removeAttr('checked').prop('checked', true);
            console.log($('.data-privilege', tableRow).text());
            if($('.data-privilege', tableRow).text().indexOf('A') === -1) $('#accountForm #inputPrivilegeA1').prop('checked', false);
            else $('#accountForm #inputPrivilegeA1').prop('checked', true);
            if($('.data-privilege', tableRow).text().indexOf('P') === -1) $('#accountForm #inputPrivilegeP1').prop('checked', false);
            else $('#accountForm #inputPrivilegeP1').prop('checked', true);
            $('#accountForm #inputDelete1').attr('target-id', $(e.currentTarget).attr('target-id'));
            $('#accountForm #inputDelete1').show();
        });

        $('.account-create-new').click(function(e) {
            $('#accountForm .form-control').val('');
            $('#accountForm .form-error').html('');
            $('#accountForm .submit-action').html('Create');
            $('#accountForm #inputPrivilegeB1').prop('checked', true);
            $('#accountForm #inputPrivilegeA1').prop('checked', false);
            $('#accountForm #inputPrivilegeP1').prop('checked', false);
            $('#accountForm #inputDelete1').hide();
        });
    })(jQuery);

    /* account form action */
    (function($) {
        $('#accountForm #inputSubmit1').click(function(e) {
            $(e.currentTarget).removeAttr('disabled').attr('disabled', 'disabled');
            var id = $('#accountForm #inputId1').val();
            var email = $('#accountForm #inputEmail1').val();
            var username = $('#accountForm #inputUsername1').val();
            var password = $('#accountForm #inputPassword1').val();
            var repeatPassword = $('#accountForm #inputRepeatPassword1').val();
            var firstName = $('#accountForm #inputFirstName1').val();
            var lastName = $('#accountForm #inputLastName1').val();
            var privilege = function() {
                var privilege = '';
                if($('#accountForm #inputPrivilegeB1').prop('checked')) privilege += 'B';
                if($('#accountForm #inputPrivilegeP1').prop('checked')) privilege += 'P';
                if($('#accountForm #inputPrivilegeA1').prop('checked')) privilege += 'A';
                return privilege;
            };
            var gold = $('#accountForm #inputGold1').val();
            var action = $('#accountForm #inputSubmit1').html();
            var actionUrl = (action.toLowerCase() == 'update') ? '/vigattinads/json-service/update-account/post' : '/vigattinads/json-service/create-account/post'
            var tableRow = (action.toLowerCase() == 'update') ? $('tr#'+id) : '';
            $.ajax( {
                type: 'POST',
                data: {
                    "id":id,
                    "email":email,
                    "username":username,
                    "password":password,
                    "repeatPassword": repeatPassword,
                    "firstName":firstName,
                    "lastName":lastName,
                    "privilege":privilege(),
                    "gold":gold
                },
                url: actionUrl,
                dataType: 'json',
                beforeSend: function(jqXHR, settings) {
                    $('#accountForm .account-form-progress').show();
                },
                complete: function(jqXHR, textStatus) {
                    $('#accountForm .account-form-progress').hide();
                    $(e.currentTarget).removeAttr('disabled');
                    if(textStatus != 'success') {

                    }
                },
                success: function(data, textStatus, jqXHR) {
                    if(data.status == 'success') {
                        $('#accountForm').modal('hide');
                        if(action.toLowerCase() == 'update') {
                            tableRow.css({'opacity':0});
                            $('.data-email', tableRow).html(email);
                            $('.data-username', tableRow).html(username);
                            $('.data-first-name', tableRow).html(firstName);
                            $('.data-last-name', tableRow).html(lastName);
                            $('.data-gold', tableRow).html(gold);
                            $('.data-privilege', tableRow).html(privilege());
                            tableRow.animate({'opacity':1}, 1000);
                        } else {
                            window.location = '/vigattinads/dashboard/admin/manageaccount';
                        }
                    }
                    else {
                        $('#accountForm .form-error').html('');
                        if(data.email) $('#accountForm .form-error.form-error-email').html('<small>'+data.email+'</small>');
                        if(data.username) $('#accountForm .form-error.form-error-username').html('<small>'+data.username+'</small>');
                        if(data.password) $('#accountForm .form-error.form-error-password').html('<small>'+data.password+'</small>');
                        if(data.repeatPassword) $('#accountForm .form-error.form-error-repeat-password').html('<small>'+data.repeatPassword+'</small>');
                        if(data.firstName) $('#accountForm .form-error.form-error-first-name').html('<small>'+data.firstName+'</small>');
                        if(data.lastName) $('#accountForm .form-error.form-error-last-name').html('<small>'+data.lastName+'</small>');
                    }
                }
            });
        });
        $('#accountForm #inputDelete1').click(function(e) {
            var id = $(e.currentTarget).attr('target-id');
            if(confirm('are you sure you want to delete this account?')) {
                window.location = '/vigattinads/dashboard/admin/manageaccount/delete/'+id;
            }
        });
    })(jQuery);

    /* on import ads modal shown */
    (function($) {
        var actionUrl = 'http://www.service.vigattin.com/vigattinads/dashboard/ads/import/';
        var Start = 0;
        var Name = '';
        var autoloadAdsImportList = $('.ads-import-list.auto-load');

        if(autoloadAdsImportList.length) {
            $('.import-ads-list-more-button').unbind('click').click(function(e) {
                getData(autoloadAdsImportList.attr('target-page'));
            });
            $('.import-ads-list-more-button').hide();
            getData(autoloadAdsImportList.attr('target-page'));
        }

        $('#importAdsModal').on('show.bs.modal', function(E) {
            Start = 0;
            Name = $(E.relatedTarget).attr('name');
            $('.ads-import-list', E.currentTarget).html('');
            $('.import-ads-list-more-button', E.currentTarget).unbind('click').click(function(e) {
                getData(Name);
            });
            $('.import-ads-list-more-button', E.currentTarget).hide();
        });

        $('#importAdsModal').on('shown.bs.modal', function(e) {
            getData(Name);
        });

        function getData(name) {
            $.ajax( {
                type: 'POST',
                data: {},
                url: actionUrl+name+'/'+Start,
                dataType: 'json',
                beforeSend: function(jqXHR, settings) {
                    $('.import-ads-list-progress').show();
                    $('.import-ads-list-more-button').hide();
                },
                complete: function(jqXHR, textStatus) {
                    if(textStatus != 'success') {
                    }
                },
                success: function(data, textStatus, jqXHR) {
                    $('.import-ads-list-progress').hide();
                    $.each(data.list, function(key, value) {
                        Start++;
                        var list =  '<li>' +
                                        '<div id="adsPanel'+Start+'" class="row ads-list-panel">'+
                                            '<div class="col-xs-3 image-frame"><img src="'+$('<div/>').html(value.image).text()+'"></div>'+
                                            '<div class="col-xs-7">'+
                                                '<div><a class="ads-title" target="_blank" href="'+$('<div/>').html(value.url).text()+'">'+$('<div/>').html(value.title).text()+'</a></div>'+
                                                '<div class="ads-description">'+$('<div/>').html(value.description).text()+'</div>'+
                                            '</div>'+
                                            '<div class="col-xs-2"><a class="ads-import-single-button" data-target="#adsPanel'+Start+'" href="javascript:" data-dismiss="modal"><span class="glyphicon glyphicon-star"></span> Promote</a></div>'+
                                        '</div>'+
                                    '</li>';
                        $('.ads-import-list', e.currentTarget).append(list);
                    });
                    $('.ads-list-panel .ads-import-single-button').unbind('click').click(onImportSingleClick);
                    if(Start < data.total) $('.import-ads-list-more-button').show();
                }
            });
        }

        function onImportSingleClick(e) {
            var targetId = $(e.currentTarget).attr('data-target');
            var data = {
                title: $(targetId+' .ads-title').text(),
                image: $(targetId+' .image-frame img').attr('src'),
                description: $(targetId+' .ads-description').text(),
                url: $(targetId+' .ads-title').attr('href')
            };
            var ratio = 0.66667;
            var image = new Image();
            $('#ads-image-data-url').val(data.image);
            image.src = data.image;
            $(image).addClass('ads-frame-image');
            $('.ads-image-preview-container')
                .html('')
                .append(image)
                .height($('.ads-image-preview-container').width() * ratio);
            $('#ads-title').val(data.title);
            $('#ads-url').val(data.url);
            $('#ads-description').val(data.description);
            $('.ads-frame .ads-frame-title').text(data.title);
            $('.ads-frame .ads-frame-description').text(data.description);
            $(window).resize(function(e) {
                $('.ads-image-preview-container').height($('.ads-image-preview-container').width() * ratio);
            });
        }
    })(jQuery);
});
