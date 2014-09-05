/* tourism category map v1.0 */
var catMapTourism = {
    'homepage' : {'url' : 'vigattintourism.com'},
    'destination' : {'url' : 'vigattintourism.com/tourism/destinations'},
    'articles' : {'url' : 'vigattintourism.com/tourism/articles?page=1'},
    'tourist spots' : {'url' : 'vigattintourism.com/tourism/tourist_spots'},
    'discussion' : {'url' : 'vigattintourism.com/tourism/discussion'},
    'directory' : {'url' : 'vigattintourism.com/tourism/destinations/91/directory'}
};

function jsTrim(str)
{
    return str.replace(/^\s+|\s+$/gm,'');
}

var tourismAds = new (function($) {
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
            iframe.attr('src', 'http://www.service.vigattin.com/vigattinads/showads2/tourism-article/side-large?showin=preview');
        }
        else {

            iframe.attr('src', 'http://www.service.vigattin.com/vigattinads/showads2/tourism-article/side-large?showin='+encodeURIComponent(showIn)+'&template='+encodeURIComponent(template)+'&limit='+encodeURIComponent(limit)+'&keyword='+encodeURIComponent(keyword));
        }
    }

    function urlToKeyword(url) {
        if(url.match(/vigattintourism.com\/tourism\/destinations\/91\/directory/)) return 'directory';
        if(url.match(/vigattintourism.com\/tourism\/destinations/)) return 'destinations';
        //if(url.match(/vigattintourism.com\/tourism\/destinations\/articles/)) return 'articles';
        if(url.match(/vigattintourism.com\/tourism\/articles/)) return 'articles';
        //if(url.match(/vigattintourism.com\/tourism\/destinations\/tourist_spots/)) return 'tourist spots';
        if(url.match(/vigattintourism.com\/tourism\/tourist_spots/)) return 'tourist spots';
        //if(url.match(/vigattintourism.com\/tourism\/destinations\/discussion/)) return 'discussion';
        if(url.match(/vigattintourism.com\/tourism\/discussion/)) return 'discussion';
        if(url.match(/vigattintourism.com/)) return 'homepage';
    }
})(jQuery);

$(document).ready(function() {
    tourismAds.init();
});
