(function($)
{
    /* Table select / checkboxes utility */
    $('.checkboxs thead :checkbox').change(function(){
        var $table = $(this).parents('table');
        if ($(this).is(':checked'))
        {
            $(this).siblings('i').addClass('checked')
            $table.find('tbody .checkbox :checkbox').prop('checked', true).trigger('change').siblings('i').addClass('checked');
            $table.find('tbody tr.selectable').addClass('selected');
            $('.delete-all-form').removeClass('hide').show();
        }
        else
        {
            $(this).siblings('i').removeClass('checked')
            $table.find('tbody .checkbox :checkbox').prop('checked', false).trigger('change').siblings('i').removeClass('checked');
            $table.find('tbody tr.selectable').removeClass('selected');
            $('.delete-all-form').hide();
        }
        updateDeleteForm($table);
    });

    $('.checkboxs tbody tr').addClass('selectable');
    $('.checkboxs tbody :checked').each(function(){
        $(this).parent('tr').addClass('selected');
    });

    $('.checkboxs tbody').on('click', 'tr.selectable', function(e){
        var $table = $(this).parents('table');
        var src = e.target || e.srcElement;
        var c = $(this).find('.checkbox :checkbox');
        var s = $(src);
        if (src.nodeName == 'INPUT')
        {
            if (c.is(':checked')){
                $(this).addClass('selected');
                c.siblings('i').addClass('checked');
            }
            else{
                $(this).removeClass('selected');
                c.siblings('i').removeClass('checked');
            }
        }
        else if (src.nodeName != 'TD' && src.nodeName != 'TR' && src.nodeName != 'DIV')
        {
            return true;
        }
        else
        {
            if (c.is(':checked'))
            {
                c.prop('checked', false).trigger('change').siblings('i').removeClass('checked');
                $(this).removeClass('selected');
            }
            else
            {
                c.prop('checked', true).trigger('change').siblings('i').addClass('checked');
                $(this).addClass('selected');
            }
        }
        if ($table.find('tr.selectable :checkbox:checked').size() == $table.find('tr.selectable :checkbox').size())
            $table.find('thead :checkbox').prop('checked', true).siblings('i').addClass('checked');
        else
            $table.find('thead :checkbox').prop('checked', false).siblings('i').removeClass('checked');


        if ($table.find('tr.selectable .checkbox :checked').length > 0)
            $('.delete-all-form').removeClass('hide').show();
        else
            $('.delete-all-form').hide();
        updateDeleteForm($table);
    });

    $('.checkboxs').each(function(){
        if ($(this).find('tbody .checkbox :checked').size() == $(this).find('tbody .checkbox :checkbox').size() && $(this).find('tbody .checkbox :checked').length)
            $('thead :checkbox').prop('checked', true).siblings('i').addClass('checked');

        if ($(this).find('tbody .checkbox :checked').length)
            $('.delete-all-form').removeClass('hide').show();
    });


    $('.radioboxs tbody tr').addClass('selectable');
    $('.radioboxs tbody :checked').each(function(){
        $(this).parent('tr').addClass('selected');
    });
    $('.radioboxs tbody tr.selectable').click(function(e){
        var $table = $(this).parents('table');
        var src = e.target || e.srcElement;
        var c = $(this).find(':radio');
        if (src.nodeName == 'INPUT')
        {
            if (c.is(':checked'))
                $(this).addClass('selected');
            else
                $(this).removeClass('selected');
        }
        else if (src.nodeName != 'TD' && src.nodeName != 'TR')
        {
            return true;
        }
        else
        {
            if (c.is(':checked'))
            {
                c.attr('checked', false);
                $(this).removeClass('selected');
            }
            else
            {
                c.attr('checked', true);
                $('.radioboxs tbody tr.selectable').removeClass('selected');
                $(this).addClass('selected');
            }
        }
    });

    // sortable tables
    if ($( ".js-table-sortable" ).length)
    {
        $( ".js-table-sortable" ).sortable(
        {
            placeholder: "ui-state-highlight",
            items: "tbody tr",
            handle: ".js-sortable-handle",
            forcePlaceholderSize: true,
            helper: function(e, ui)
            {
                ui.children().each(function() {
                    $(this).width($(this).width());
                });
                return ui;
            },
            start: function(event, ui)
            {
                ui.placeholder.html('<td colspan="' + $(this).find('tbody tr:first td').size() + '">&nbsp;</td>');
            }
        });
    }
})(jQuery);

function updateDeleteForm($table)
{
    var v = [];
    $table.find('tbody .checkbox :checkbox:checked').each(function(){
        v.push($(this).parents('tr').data('id'));
    });
    $('.delete-all-form').children('[name="id"]').val(v.join(','));
}