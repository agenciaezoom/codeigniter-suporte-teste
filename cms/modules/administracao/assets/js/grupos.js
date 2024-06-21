$('document').ready(function(){
    // ATIVAR/DESATIVAR USUÁRIO
    $('.table .make-switch :checkbox').change(function(){
        $.ajax({
            type: "POST",
            url: site_url + 'administracao/grupos/active',
            data: {
                id: $(this).parents('tr').data('id'),
                actived: $(this).is(':checked')
            },
            dataType: "json",
            success: function(data)
            {
                var obj = {
                    layout: 'top',
                    text: data.message,
                    type: data.classe
                };
                openNotification(obj);
            }
        });
    });

    $('.permission-wrapper').on('change', '.widget-head input[type="checkbox"]', function(){
         var $parent = $(this).parents('.widget-head'),
             $body = $parent.next();

         if ($(this).is(':checked')) {
            $body.find('input[type="checkbox"]').checkbox('check');
         } else {
             $body.find('input[type="checkbox"]').checkbox('uncheck');
         }
    });

    if ($("#validateSubmitForm").length> 0 ){
        $.validator.setDefaults({
            submitHandler: function(form) {
                var v = $(form).find('[type="submit"]').html();
                $(form).find('[type="submit"]').attr('disabled','disabled').html('<img src="'+basePath+'modules/comum/assets/img/loading.gif">').addClass('loading');
                $.ajax({
                    type: "POST",
                    url: form.action,
                    data: $(form).serialize(),
                    dataType: "json",
                    complete: function () {
                        $(form).find('[type="submit"]').removeAttr('disabled').html(v).removeClass('loading');
                    },
                    success: function(data) {
                        var msg = (data.redirect) ? data.message + ' ' +i18n.redirecionado_10s+' <a href="'+data.redirect+'">'+i18n.aqui+'</a> '+i18n.ir_diretamente : data.message,
                            obj = { layout: 'top', text: msg, type: data.classe, timeout: 9500 };
                        openNotification(obj);
                        if (data.status  && data.redirect){
                            setTimeout(function(){
                                window.location = basePath + 'administracao/grupos';
                            },10000);
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
        $("#validateSubmitForm").validate({
            rules: { name: "required" }
        });
    }
});