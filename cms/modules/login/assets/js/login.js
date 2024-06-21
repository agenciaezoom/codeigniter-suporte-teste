/**
 * js
 *
 * @package ezoom_framework
 * @subpackage login
 * @category js
 * @author Ralf da Rocha
 * @copyright 2014 Ezoom
 */
 $.validator.setDefaults({
    submitHandler: function(form) {
        var v = $(form).find('[type="submit"]').html();
        $(form).find('[type="submit"]').attr('disabled','disabled').html('<i class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></i> Carregando').addClass('loading');
        $.ajax({
            type: "POST",
            url: 'login/auth',
            data: $(form).serialize(),
            dataType: "json",
            complete: function () {
                $(form).find('[type="submit"]').removeAttr('disabled').html(v).removeClass('loading');
            },
            success: function(data) {
                var obj = {
                    layout: 'top',
                    text: data.message,
                    type: data.classe
                };
                openNotification(obj);
                if (data.status){
                    setTimeout(function(){
                        window.location = data.redirectModule;
                    }, 2000);
                }
            }
        });
        return false;
    },
    showErrors: function(map, list)
    {
        this.currentElements.parents('label:first, div:first').find('.has-error').remove();
        this.currentElements.parents('.form-group:first').removeClass('has-error');

        $.each(list, function(index, error)
        {
            var ee = $(error.element);
            var eep = ee.parents('label:first').length ? ee.parents('label:first') : ee.parents('div:first');

            ee.parents('.form-group:first').addClass('has-error');
            eep.find('.has-error').remove();
            eep.append('<p class="has-error help-block">' + error.message + '</p>');
        });
    }
});

$(function(){
    $("#validateSubmitForm1").validate({
        rules: {
            username: "required",
            password: "required"
        },
        messages: {
            username: 'Digite seu E-mail ou seu nome de Usuário',
            password: 'Digite a sua senha'
        }
    });
});