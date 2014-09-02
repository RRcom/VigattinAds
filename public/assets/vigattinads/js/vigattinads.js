/* vigattin category map v1.0 */
var catMapVigattin = {
    'homepage' : {'url' : 'vigattintourism.com'}
};

function jsTrim(str)
{
    return str.replace(/^\s+|\s+$/gm,'');
}

var vigattinAds = new (function($) {
    var iframe;

    this.init = function() {
        iframe = $('.vigattinads-frame');
        setIframe();
    }

    function setIframe() {
        var keyword = '('+urlToKeyword(window.location.href)+')';
        var showIn = iframe.attr('data-showin');
        var template = (iframe.attr('data-template')) ? iframe.attr('data-template') : '';
        var limit = (iframe.attr('data-limit')) ? iframe.attr('data-limit') : 6;
        if(window.location.hash.substr(1) == 'preview') {
            iframe.attr('src', 'http://www.service.vigattin.com/vigattinads/showads/vigattin-tiles?showin=preview');
        }
        else {

            iframe.attr('src', 'http://www.service.vigattin.com/vigattinads/showads/vigattin-tiles?showin='+encodeURIComponent(showIn)+'&template='+encodeURIComponent(template)+'&limit='+encodeURIComponent(limit)+'&keyword='+encodeURIComponent(keyword));
        }
    }

    function urlToKeyword(url) {
        if(url.match(/vigattin.com/)) return 'homepage';
    }
})(jQuery);

$(document).ready(function() {
    vigattinAds.init();
});
