function openNotification(params) {
    notyfy({
        timeout: (params.timeout!=undefined) ? params.timeout : 5000,
        text: params.text,
        type: params.type,
        dismissQueue: true,
        layout: params.layout
    });
}

/**
 * Função ajax para inserir e editar registro no banco
 * @author Diogo Taparello [diogo@ezoom.com.br]
 * @date   2015-02-12
 * @formulario  {object}   recebe o formulário do submit
 */
function sendForm(formulario) {
    var send = true;
    if (files !== undefined && files.length > 0){
        $.each(files, function (index, value) {
            if (value.textStatus === undefined || value.textStatus !== 'success') {
                send = false;
            }
        });
    }

    if (send){
        var $btn = $(formulario).find('[type="submit"]'),
            btnPreSave = $btn.data('original');
        $btn.focus();
        $(':focus').trigger('blur');
        if( typeof(CKEDITOR) !== "undefined" ) {
            for (var i in CKEDITOR.instances) {
                CKEDITOR.instances[i].updateElement();
            }
        }
        $.ajax({
            type: "POST",
            url: $(formulario).prop('action'),
            data: $(formulario).serialize(),
            dataType: "json",
            success: function(data) {
                var msg = (data.redirect) ? data.message + ' '+i18n.clique+' <a href="'+site_url + data.redirectModule+'">'+i18n.aqui+'</a> '+i18n.ir_listagem : data.message,
                    obj = { layout: 'top', text: msg, type: data.classe };
                openNotification(obj);

                if (data.status) {
                    if (data.redirect){
                        $btn.html(i18n.redirecionando).removeClass('loading btn-primary').addClass('btn-success btn-stroke');
                        setTimeout(function(){
                            window.location = site_url + data.redirectModule;
                        }, 2000);
                    } else{
                        setTimeout(function(){
                            $(formulario).removeClass('sending').data('sending',false);
                            $btn.html(btnPreSave).removeClass('btn-success btn-stroke').addClass('btn-primary').removeAttr('disabled');
                        }, 2000);
                    }
                } else{
                    $.each(files, function (index) {
                        files[index].abort();
                        files[index].textStatus = '';
                    });
                    $(formulario).removeClass('sending').data('sending',false);
                    $btn.html(btnPreSave).removeClass('loading').removeAttr('disabled');
                }
            }
        });
    }
}
/**
 * Função que retorna o erro do submit dos formulários
 * @author Diogo Taparello [diogo@ezoom.com.br]
 * @elemento  {object}   formulário que fez o submit
 * $list  {}             lista de imagens
 * @date   2015-02-12
 */
function sendFormError(elemento, list) {
    elemento.currentElements.parents('label:first, div:first').find('.has-error').remove();
    elemento.currentElements.parents('.form-group:first').removeClass('has-error');

    $.each(list, function(index, error)
    {
        var ee = $(error.element);
        var eep = ee.parents('label:first').length ? ee.parents('label:first') : ee.parents('div:first');

        ee.parents('.form-group:first').addClass('has-error');
        eep.find('.has-error').remove();
        eep.append('<p class="has-error help-block">' + error.message + '</p>');
    });
}
/**
 * Função para criar efeito pregresso no upload de imagem
 * @author Diogo Taparello [diogo@ezoom.com.br]
 * $data       {}   retorno do data ajax
 * $elemento   {}   elemento progress bar
 * @date   2015-02-12
 */
function progressImage(data, elemento) {
    var progress = parseInt(data.loaded / data.total * 100, 10);
    elemento.width(progress + '%');
    if (progress == 100)
        elemento.addClass('complete').delay(2000).animate({height: 0},300, function(){
            elemento.removeClass('complete').css({width:0, height:5});
        });
}
/**
 * Função done do upload de imagem
 * @author Diogo Taparello [diogo@ezoom.com.br]
 * $data           retorno do data ajax
 * $elemento       elemento .upload-wraper
 * $formulario     formulario
 * $nameField      nome do field da tabela
 * $delFunction      caminho controller para deletar
 * @date   2015-02-12
 */
function doneImage(data, elemento, formulario, nameField, delFunction) {
    var showImage = elemento.find('.upload-image'),
        btnSend = elemento.find('label[for="fileuploadImage"]');
    if (data.autoUpload){
        if (data.jqXHR.responseJSON.status){
            showImage.find('img').attr('src', site_url + 'image/resize_crop?src=userfiles/avatar/' + data.jqXHR.responseJSON.image + '&w=200&h=200');
            $('.delete-image').removeClass('hide').hide().slideDown().attr('href', delFunction + '/' + data.jqXHR.responseJSON.image);
        }
        setTimeout(function(){
            btnSend.html(i18n.alterar_imagem).removeAttr('disabled');
        }, 400);
        openNotification({ layout: 'top', text: data.jqXHR.responseJSON.message, type: data.jqXHR.responseJSON.classe });
    }else{
        formulario.append('<input type="hidden" name="'+nameField+'" value="'+data.jqXHR.responseJSON.image+'" />');
        sendForm(formulario[0]);
        setTimeout(function(){
            btnSend.html(i18n.alterar_imagem).removeAttr('disabled');
        }, 400);
    }
}
/**
 * Função process do upload de imagem
 * @author Diogo Taparello [diogo@ezoom.com.br]
 * $data           retorno do data ajax
 * $formulario
 * @date   2015-02-12
 */
function processImage(data, formulario) {
    if (data.autoUpload){
        formulario.find('label[for="fileuploadImage"]').attr('disabled','disabled').html('<i class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></i>');
        data.submit();
    }
}

$(window).load(function(){
    updateCKEditor();
});

function updateCKEditor(editor){
    if (typeof CKEDITOR !== 'undefined'){
        if (editor === undefined){
            for (var i in CKEDITOR.instances) {
                updateCKEditorIndividual(CKEDITOR.instances[i]);
            }
        }else{
            updateCKEditorIndividual(editor);
        }
    }
}

function updateCKEditorIndividual(editor){
    editor.on('resize', function(){
        $('.layout-app .hasNiceScroll').getNiceScroll().resize();
    });
    editor.on('change', function() {
        this.updateElement();
    });
    editor.on('blur', function() {
        this.updateElement();
    });
}
