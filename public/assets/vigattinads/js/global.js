/* toggle hide show side menu */
$(document).ready(function(e) {
    $('[data-toggle=offcanvas]').click(function() {
        $('.row-offcanvas').toggleClass('active');
    });
});

/* ads image choose preview */
$(document).ready(function(e) {
    $('#ads-choose-image-input').change(function(e) {
        var fileReader = new FileReader();
        var file = e.currentTarget.files;
        var ratio = 0.66667;
        if(file[0].type.match('image.*')) {
            fileReader.onload = function(imageFile) {
                var image = new Image();
                image.src = imageFile.target.result;
                $(image).css({'width':'100%'});
                $('#ads-image-preview-container')
                    .html('')
                    .append(image)
                    .css({'overflow':'hidden'})
                    .height($('#ads-image-preview-container').width() * ratio);

                $(window).resize(function(e) {
                    $('#ads-image-preview-container').height($('#ads-image-preview-container').width() * ratio);
                });
            }
            fileReader.readAsDataURL(file[0]);
        }
    });
});