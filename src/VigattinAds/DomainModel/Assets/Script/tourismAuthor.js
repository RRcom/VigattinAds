/**
 * Created by Resty on 9/18/14.
 */
$(document).ready(function() {
    /* table tourism author list jsonP fetcher */
    (function($) {
        var actionUrl = 'http://www.vigattintourism.com/service/author';
        var tableBody = $('#authorSearchTable');
        var tableFootTr = $('#authorSearchTablePreloader');
        var SubmitButton = $('#authorSearchSubmit');
        var stringInput = $('#authorSearchString');
        var categoryInput = $('.authorSearchCategories');
        var searchString = '';
        var filter = '';
        var offset = 0;
        var limit = 10;
        var fetching = false;
        var ajax;
        var isLast = false;
        var multiSelectorBox = $('.multi-selector-box');
        var mainBox = $('.box-container');
        var mainBoxOffset
        var itemLimit = 10;

        function init() {
            if(!multiSelectorBox.length) return;
            updateMainBoxSize();
            hideItemBoxIfEmpty();
            $(document).scroll(onLogScroll);
            SubmitButton.click(onSubmit);
            $('.authors-select-all input').click(onSelectAllAuthors);
            fetchMore();
            $(window).resize(function(){
                updateMainBoxSize();
                fixedIfScrolled();
            });
        }

        function updateMainBoxSize() {
            var containerWidth = mainBox.parent().width();
            mainBox.css({'width':containerWidth});
            mainBoxOffset = mainBox.parent().offset();
        }

        function onSelectAllAuthors(e) {
            $('span[id=multiSelectorItem0]').remove();
            if($(e.currentTarget).prop('checked')) {
                $('.hide-if-all-author').hide();
                multiSelectorBox.append('<span id="multiSelectorItem0" data-name="All" data-target="0" class="multi-selector-item">All Authors</span>');
                onMultiSelectorBoxItemChange();
            } else {
                $('.hide-if-all-author').show();
                onMultiSelectorBoxItemChange();
            }
            hideItemBoxIfEmpty();
            fixedIfScrolled();
        }

        function onSubmit(e) {
            e.preventDefault();
            ajax.abort();
            fetching = false;
            searchString = stringInput.val();
            filter = getStrCat();
            offset = 0;
            tableBody.html('');
            updateMainBoxSize();
            fetchMore();
        }

        function getStrCat() {
            var strCat = '';
            $.each(categoryInput, function(key, value) {
                if($(value).is(":checked")) {
                    strCat += $(value).val()+',';
                }
            });
            return strCat;
        }

        function onLogScroll(e) {
            var scrollValue = $(document).scrollTop()+$(window).height();
            var scrollMax = $(document).height();
            if(scrollValue+200 >= scrollMax) {
                if(!isLast) fetchMore();
            }
            fixedIfScrolled();
        }

        function fixedIfScrolled() {
            if($(document).scrollTop()+50 >= mainBoxOffset.top) {
                if(mainBox.is(":visible")) {
                    fixMainBox(true);
                }
            } else {
                if(mainBox.is(":visible")) {
                    fixMainBox(false);
                }
            }
        }

        function fixMainBox(yes) {
            if(yes) {
                mainBox.css({
                    'position':'fixed',
                    'top':0,
                    'left':mainBoxOffset.left+15,
                    'margin-top':50
                });
            }
            else {
                mainBox.css({
                    'position':'relative',
                    'margin-top':0,
                    'left':'auto'
                });
            }
        }

        function hideItemBoxIfEmpty() {
            if($('.multi-selector-item', multiSelectorBox).length) {
                mainBox.css({'display':'block'});
            } else {
                mainBox.hide({'display':'none'});
            }
        }

        function onListButtonAdd(e) {
            if($('#multiSelectorItem'+$(e.currentTarget).val()).length || ($('.multi-selector-item', multiSelectorBox).length >= itemLimit)) {
            } else {
                multiSelectorBox.append(
                    $('<span id="multiSelectorItem'+$(e.currentTarget).val()+'" data-target="'+$(e.currentTarget).val()+'" data-name="'+$(e.currentTarget).attr('data-name')+'" class="hide-if-all-author multi-selector-item">'+$(e.currentTarget).attr('data-name')+'</span>')
                        .append(
                            $('<span class="small multi-selector-close" data-target="'+$(e.currentTarget).val()+'">x</span></span>').click(onItemClose)
                        )
                );
                onMultiSelectorBoxItemChange();
            }
            hideItemBoxIfEmpty();
            fixedIfScrolled();
        }

        function onItemClose(e) {
            $('span[id=multiSelectorItem'+$(e.currentTarget, multiSelectorBox).attr('data-target')+']').remove();
            onMultiSelectorBoxItemChange();
            hideItemBoxIfEmpty();
        }

        function onMultiSelectorBoxItemChange() {
            var ids = '';
            var names = '';
            $.each($('.multi-selector-item', multiSelectorBox), function(key, value) {
                ids += $(value).attr('data-target')+',';
                names += $(value).attr('data-name')+',';
            });
            $('#submitAuthorId').val(ids);
            $('#submitAuthorName').val(names);
            log( $('#submitAuthorId').val());
            log($('#submitAuthorName').val());
        }

        function fetchMore() {
            if(fetching) return;
            fetching = true;
            tableFootTr.show();
            ajax = $.ajax( {
                type: 'GET',
                data: {'string':searchString, 'filter':filter, 'offset':offset, 'limit':limit},
                url: actionUrl,
                dataType: 'jsonp',
                beforeSend: function(jqXHR, settings) {

                },
                complete: function(jqXHR, textStatus) {
                    if(textStatus != 'success') {
                    }
                },
                success: function(data, textStatus, jqXHR) {
                    offset += limit;
                    $.each(data.authors, function(key, value) {
                        tableBody.append(
                            '<tr>'+
                                '<td><div style="height: 50px; width: 50px; background-size: contain; background-image: url(\''+value.photoUrl+'\')"></div></td>'+
                                '<td>'+value.firstName+'</td>'+
                                '<td>'+value.lastName+'</td>'+
                                '<td>'+
                                '<form method="post" action="">'+
                                '<button type="button" id="authorListItem'+value.id+'" class="author-list-add-button btn btn-default" value="'+value.id+'" data-name="'+value.firstName+' '+value.lastName+'">add</button>'+
                                '<input type="hidden" name="authorFirstName" value="">'+
                                '<input type="hidden" name="authorLastName" value="">'+
                                '</form>'+
                                '</td>'+
                                '</tr>'
                        );
                    });
                    fetching = false;
                    tableFootTr.hide();
                    if(offset >= data.total) isLast = true;
                    else isLast = false;
                    $('.author-list-add-button').unbind('click').click(onListButtonAdd);
                    updateMainBoxSize();
                }
            });
        }

        init();
    })(jQuery);

    /* #articleAuthorSelectModal */
    (function($) {

        var modal = $('#articleAuthorSelectModal');
        var searchButton = $('#authorSearchSubmit', modal);
        var actionUrl = 'http://www.vigattintourism.com/service/author';
        var stringInput = $('#authorSearchString', modal);
        var resultContainer = $('#authorResultListContainer', modal);
        var categoryInput = $('.authorSearchCategories', modal);
        var selectedAuthors = $('.selected-authors', modal);
        var ajax;
        var fetching = false;
        var loader = $('#authorSearchTablePreloader', modal);
        var tableBody = $('#authorSearchTable', modal);
        var offset = 0;
        var limit = 10;
        var isLast = false;
        var maxItem = 10;
        var hasSelectAllAuthors = false;

        function onSelectAllAuthors() {
            $('div[id=author_0]').remove();
            if($('.authors-select-all input').prop('checked')) {
                $('.hide-if-all-author').hide();
                selectedAuthors.append('<div class="multi-selector-item" id="author_0" author="0"><span class="author-name">All Authors</span></div>');
            } else {
                $('.hide-if-all-author').show();
            }
            generateKeywords();
        }

        function onFetchCurrentAuthors(authors) {
            $.each(authors, function(key, value) {
                var button =  $('<button type="button" value="'+value.id+'" data-name="'+value.firstName+' '+value.lastName+'">add</button>');
                button.click(onAddAuthor);
                button.trigger('click');
            });
            $('.body-content', modal).show();
            $('.selected-category-list', modal).show();
            $('.body-loader', modal).hide();
            if(hasSelectAllAuthors) {
                $('.authors-select-all input').prop('checked', true);
                onSelectAllAuthors();
            }
        }

        function onSearchClick(e) {
            fetching = false;
            offset = 0;
            isLast = false;
            tableBody.html('');
            fetchMore();
        }

        function onTextInputKeydown(e) {
            if(e.which == 13) {
                onSearchClick(e);
                return false;
            }
        }

        function onAddAuthor(e) {
            var id = $(e.currentTarget).val();
            var name = $(e.currentTarget).attr('data-name');
            var item;
            if($('.multi-selector-item', selectedAuthors).length >= maxItem) return;
            if(!$('#author_'+id, selectedAuthors).length) {
                selectedAuthors.show();
                item = $('<div class="multi-selector-item hide-if-all-author" id="author_'+id+'" author="'+id+'"><span class="author-name">'+name+'</span> <span class="multi-selector-close" data-target="#author_'+id+'">x</span></div>');
                $('.multi-selector-close', item).click(onRemoveAuthor);
                selectedAuthors.append(item);
                generateKeywords();
            }
        }

        function onRemoveAuthor(e) {
            var dataTarget = $(e.currentTarget).attr('data-target');
            $(dataTarget, selectedAuthors).remove();
            generateKeywords();
            if(!$('.multi-selector-item', selectedAuthors).length) selectedAuthors.hide();
        }

        function onScroll(e) {
            if(e.currentTarget.scrollHeight <= ((e.currentTarget.offsetHeight + e.currentTarget.scrollTop)+50)) {
                if(!isLast) fetchMore();
            }
        }

        function init(e) {
            $('.body-content', modal).hide();
            $('.selected-category-list', modal).hide();
            $('.body-loader', modal).show();
            hasSelectAllAuthors = hasAllSelected();
            selectedAuthors.html('');
            fetchCurrentAuthors();
            $(searchButton).unbind('click').click(onSearchClick);
            $('.authors-select-all input').unbind('click').click(onSelectAllAuthors);
            tableBody.html('');
            selectedAuthors.hide();
            fetching = false;
            offset = 0;
            isLast = false;
            stringInput.val('').unbind('keydown').keydown(onTextInputKeydown);
            loader.hide();
            resultContainer.unbind('scroll').scroll(onScroll);
        }

        function fetchMore() {
            if(fetching) return;
            fetching = true;
            loader.show();
            ajax = $.ajax( {
                type: 'GET',
                data: {'string':stringInput.val(), 'filter':getStrCat(), 'offset':offset, 'limit':limit},
                url: actionUrl,
                dataType: 'jsonp',
                complete: function(jqXHR, textStatus) {
                    if(textStatus != 'success') {
                        setTimeout(function(){fetchMore()}, 3000);
                    }
                },
                success: function(data, textStatus, jqXHR) {
                    offset += limit;
                    var item;
                    $.each(data.authors, function(key, value) {
                        item =  $('<tr>'+
                            '<td><div style="height: 50px; width: 50px; background-size: contain; background-image: url(\''+value.photoUrl+'\')"></div></td>'+
                            '<td class="text-center full-name">'+value.firstName+' '+value.lastName+'</td>'+
                            '<td class="text-right">'+
                            '<button type="button" id="authorListItem'+value.id+'" class="author-list-add-button btn btn-default" value="'+value.id+'" data-name="'+value.firstName+' '+value.lastName+'">add</button>'+
                            '</td>'+
                            '</tr>');
                        $('button', item).click(onAddAuthor);
                        tableBody.append(item);
                    });
                    fetching = false;
                    loader.hide();
                    if(offset >= data.total) isLast = true;
                    else isLast = false;
                }
            });
        }

        function getStrCat() {
            var strCat = '';
            $.each(categoryInput, function(key, value) {
                if($(value).is(":checked")) {
                    strCat += $(value).val()+',';
                }
            });
            return strCat;
        }

        function generateKeywords() {
            var keywords = [];
            var keywordsRightSide = '';
            var keywordsFooter = '';
            var id;
            keywords[0] = '';
            keywords[1] = '';
            keywords[2] = '';
            $('.multi-selector-item', selectedAuthors).each(function(key, value) {
                id = $(value).attr('author');
                keywords[0] += '(tourism article header '+id+')';
                keywords[1] += '(tourism article rightside '+id+')';
                keywords[2] += '(tourism article footer '+id+')';
            });
            $('.tourism-cat-checkbox').each(function(key, value) {
                $(value).val(keywords[key]);
            });
        }

        function fetchCurrentAuthors() {
            $.ajax( {
                type: 'GET',
                data: {'ids':getIdsFromCheckBoxValue(), 'limit':10},
                url: actionUrl,
                dataType: 'jsonp',
                success: function(data, textStatus, jqXHR) {
                    onFetchCurrentAuthors(data.authors);
                }
            });
        }

        function hasAllSelected() {
            var checkBox = $('.tourism-cat-checkbox')[0];
            var hasAll = false
            $.each($(checkBox).val().split(')'), function(key, value) {
                id = value.replace( /^\D+/g, '');
                if((id !== '') && (id == 0) ) hasAll = true;
            });
            return hasAll;
        }

        function getIdsFromCheckBoxValue() {
            var checkBox = $('.tourism-cat-checkbox')[0];
            var id;
            var ids = '';
            $.each($(checkBox).val().split(')'), function(key, value) {
                id = value.replace( /^\D+/g, '');
                if(id) ids += id+',';
            });
            return ids;
        }

        $(modal).on('show.bs.modal', init);

    })(jQuery);

    /* select all category */
    (function($){

        function generateCategoryStringList() {
            var list = '';
            var count = 0;
            var categories = $('.authorSearchCategories:checked').not('.all');
            categories.each(function(key, val) {
                count++;
                list += ' '+$(val).attr('title');
                if(count < categories.length) list += ',';
            });
            if(count < 2) {
                $('.selected-category-list small').html('Selected Category:'+list);
            } else {
                $('.selected-category-list small').html('Selected Categories:'+list);
            }
        }

        generateCategoryStringList();

        $('.authorSearchCategories.all').click(function(e) {
            if($(e.currentTarget).prop('checked')) {
                $('.authorSearchCategories').prop('checked', true);
            } else {
                $('.authorSearchCategories').prop('checked', false);
            }
            generateCategoryStringList();
        });

        $('.authorSearchCategories').not('.all').click(function(e) {
            if($('.authorSearchCategories:checked').not('.all').length == $('.authorSearchCategories').not('.all').length) {
                $('.authorSearchCategories.all').prop('checked', true);
            } else {
                $('.authorSearchCategories.all').prop('checked', false);
            }
            generateCategoryStringList();
        });
    })(jQuery);
});