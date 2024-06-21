/**
 * js
 *
 * @package ezoom_framework
 * @subpackage esqueci
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
            url: ($(form).is("#validateSubmitForm1")) ? site_url + 'esqueci/request-token' : site_url + 'esqueci/change-password',
            data: $(form).serialize(),
            dataType: "json",
            complete: function (data) {
                $(form).find('[type="submit"]').removeAttr('disabled').html(v).removeClass('loading');
            },
            success: function(data) {
                var obj = {
                    layout: 'top',
                    text: data.message,
                    type: data.classe
                }
                openNotification(obj);
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
            username: "required"
        },
        messages: {
            username: i18n.digite_email_nome
        }
    });
    $("#validateSubmitForm2").validate({
        rules: {
            password: {
                required: true,
                minlength: 6
            },
            password2: {
                required: true,
                minlength: 6,
                equalTo: "#password"
            }
        },
        messages: {
            password: {
                required: i18n.nova_senha,
                minlength: i18n.senha_caracteres
            },
            password2: {
                required: i18n.repita_nova_senha,
                minlength: i18n.senha_caracteres,
                equalTo: i18n.senhas_nao_iguais
            }
        }
    });
});
