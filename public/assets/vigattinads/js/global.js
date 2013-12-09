/* toggle hide show side menu */
$(document).ready(function(e) {
    $('[data-toggle=offcanvas]').click(function() {
        $('.row-offcanvas').toggleClass('active');
    });
});

/* ads image choose preview */
$(document).ready(function(e) {
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
});

/* ads edit text */
$(document).ready(function(e) {
    $('#ads-title').keyup(function(e) {
        $('.ads-frame .ads-frame-title').text($(e.currentTarget).val());
    });
    $('#ads-description').keyup(function(e) {
        $('.ads-frame .ads-frame-description').text($(e.currentTarget).val());
    });
});