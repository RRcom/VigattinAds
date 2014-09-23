$(document).ready(function(){
    /* Views inline update v2.0 */
    (function($){
        function init() {
            $('.ads-view-limit-inline-edit').click(onInlineEditClick);
            $('.ads-view-limit-inline-edit').on('show.bs.popover', function (e){onPopupShow(e);});
            $('.ads-view-limit-inline-edit').on('shown.bs.popover', function (e){onPopupShown(e);});
            iniPopover();
        }

        function iniPopover() {
            $('.ads-view-limit-inline-edit').popover({
                'html':true
            });
        }

        function onInlineEditClick(e) {
            $('.ads-view-limit-inline-edit').popover('hide');
        }

        function onPopupShow(e) {
        }

        function onPopupShown(e) {
            var targetId = $(e.currentTarget).attr('data-target-id');
            var popOver = $('#adsViewInlineEdit'+targetId);
            var currentGold = $('.current-gold').text();
            var currentReserve = $('#reserve'+targetId+' .reserve-value').text();
            $('.inline-edit-gold', popOver).html(currentGold);
            $('.inline-estimated-view', popOver).text(Math.floor(currentReserve / viewToGoldRate));
            $('.inline-edit-input', popOver).val(currentReserve);
            $('.inline-edit-cancel', popOver).unbind('click').click(function(e){onCancel(e, popOver)});
            $('.inline-edit-apply', popOver).unbind('click').click(function(e){onApply(e, popOver)});
            $('.inline-edit-input', popOver).unbind('change').change(function(e){onChange(e, popOver)});
            $('.inline-edit-input', popOver).unbind('keyup').keyup(function(e){onKeyup(e, popOver)});
        }

        function onCancel(e, popOver) {
            $('.ads-view-limit-inline-edit').popover('hide');
        }

        function onApply(e, popOver) {
            apiChangeReserve($('.inline-edit-input', popOver).val(), popOver.attr('data-target-id'), $('#reserve'+popOver.attr('data-target-id')));
            $('.ads-view-limit-inline-edit').popover('hide');
        }

        function onChange(e, popOver) {
            var targetId = popOver.attr('data-target-id');
            var currentReserve = $('#reserve'+targetId+' .reserve-value').text();
            var inputValue = $(e.currentTarget).val();
            var currentGold = parseFloat($('.current-gold').text());
            var newGold = parseFloat(currentGold - (inputValue - currentReserve));
            $('.inline-edit-gold', popOver).text(newGold.toFixed(2));
            $('.inline-estimated-view', popOver).text(Math.floor(inputValue / viewToGoldRate));
        }

        function onKeyup(e, popOver) {
            onChange(e, popOver);
        }

        function apiChangeReserve(newReserve, adsId, currentViewElement) {
            var oldReserve = parseFloat($('.reserve-value', currentViewElement).text());
            newReserve = (newReserve <= 0) ? 0 : (newReserve / viewToGoldRate);

            $.ajax( {
                type: 'POST',
                data: {"requestViews":newReserve, "adsId":adsId},
                url: '/vigattinads/json-service/add-view-credit/post',
                dataType: 'json',
                beforeSend: function(jqXHR, settings) {
                    $('.reserve-value', currentViewElement).text(parseFloat(newReserve * viewToGoldRate).toFixed(2));
                },
                complete: function(jqXHR, textStatus) {
                    if(textStatus != 'success') {
                    }
                },
                success: function(data, textStatus, jqXHR) {
                    if(data.status == 'success') {
                        $('.current-gold').html(data.gold);
                        $('.current-gold-success').show().fadeOut(1000);
                        $('.reserve-value', currentViewElement).text(parseFloat(data.views * viewToGoldRate).toFixed(2));
                    }
                    else {
                        $('.reserve-value', currentViewElement).text(oldReserve);
                        $('.alert-box').html('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><strong>'+data.status.charAt(0).toUpperCase()+data.status.slice(1)+'!</strong> '+data.reason.charAt(0).toUpperCase()+data.reason.slice(1)+'</div>');
                        $(".alert").alert();
                    }
                }
            });
        }

        init();
    })(jQuery);
});
