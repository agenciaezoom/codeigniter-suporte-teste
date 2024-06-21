$(function(){
    /* Ativar/Desativar*/
    $(document).on('click', '.make-switch', function(){
        var switchButton = $(this);

        if( switchButton.find('input').trigger('click').is(':checked') ){
            switchButton.find('.button-switch').html(i18n.sim).addClass('button-on').removeClass('button-off');
        } else {
            switchButton.find('.button-switch').html(i18n.nao).addClass('button-off').removeClass('button-on');
        }

    });

    /* Visivel*/
    $(document).on('click', '.make-switch-visible', function(){
        var switchButton = $(this);

        if( switchButton.find('#inputVisible').trigger('click').is(':checked') ){
            switchButton.find('.button-switch').html(i18n.sim).addClass('button-on').removeClass('button-off');
        } else {
            switchButton.find('.button-switch').html(i18n.nao).addClass('button-off').removeClass('button-on');
        }
    });

    /* 3 Fases */
    $(document).on('click', '.make-switch-3', function(){
        var switchButton = $(this);

        switchButton.find('input').trigger('click');

        if( switchButton.find('input').attr('data-status') == 'on' ){
            switchButton.find('input').attr('data-status', '');
            switchButton.find('.button-switch').html(i18n.nao).addClass('button-off').removeClass('button-on');
        } else if( switchButton.find('input').attr('data-status') == 'pen' ) {
            switchButton.find('input').attr('data-status', 'on');
            switchButton.find('.button-switch').html(i18n.sim).addClass('button-on').removeClass('button-may');
        } else{
            switchButton.find('input').attr('data-status', 'pen');
            switchButton.find('.button-switch').html(i18n.pen).addClass('button-may').removeClass('button-off');
        }
    });
});