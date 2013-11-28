/* toggle hide show side menu */
$(document).ready(function(e) {
    $('[data-toggle=offcanvas]').click(function() {
        $('.row-offcanvas').toggleClass('active');
    });
});
