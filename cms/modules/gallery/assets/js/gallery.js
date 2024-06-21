var images = [];
$(function(){
    var url_crud = (window.location.href).split('/').pop();

    if(url_crud == "cadastrar"){
        $('.sort-images-gallery').css({"display":"none"});
    }

    $('.template-upload select').select2({
        templateResult: formatSelect,
        templateSelection: formatSelect
    });

    $(document).on('click', '.highlighted', function() {
        var check = $(this).find('input[type="checkbox"]');
        $('.highlighted input[type="checkbox"]').prop('checked', false);
        $('.highlighted').removeClass('checked')
        $(this).addClass('checked');
        check.prop('checked', true);
    });

    // UPLOAD PHOTOS
    if ($('.fileUploadImage').length > 0){
        $('.fileUploadImage').each(function(){
            var $el = $(this),
                elData = $el.closest('.gallery-images').find('.imagesAll').data();

            $el.fileupload({
                autoUpload: false,
                dataType: 'json',
                maxChunkSize: (elData.gallerychunk === undefined) ? false : 1000000,
                pasteZone: null,
                previewMaxWidth: 170,
                previewMaxHeight: 170,
                previewCrop: true,
                url: site_url + 'gallery/upload',
                progress: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#'+ data.divId + ' .progress .progress-bar').css( 'width', progress + '%');

                    if(progress == 100) {
                        setTimeout(function() {
                            $('#'+ data.divId + ' .progress').removeClass('active');
                            $('#'+ data.divId + ' .progress .progress-bar').removeClass('progress-bar-info').addClass('progress-bar-success');
                        }, 1000);
                    }
                },
                processalways: function (e, data) {
                    var uploadFile = data.files[0];
                    var regex = '';
                    // Fazer a variavel receber a expressao regular e identificar se é pra deixar arquivos no upload através de um parametro no html (enviado ao chamar galeria)


                    if($('#upload_files').length > 0){
                        regex = /\.(jpg|jpeg|png|doc|docx|pdf|xls|xlsx|mp4|mov)$/i;
                        var obj = { layout: 'top', text: "O arquivo \'"+uploadFile.name+"\' não será enviado.\n\nVocê pode enviar arquivos com extensão: JPG, JPEG, PNG, DOC(X), XLS(X), PDF, MP4 ou MOV.", type: 'error' };
                    }else{
                        regex = /\.(jpg|jpeg|png)$/i;
                        var obj = { layout: 'top', text: "O arquivo \'"+uploadFile.name+"\' não será enviado.\n\nVocê pode enviar arquivos com extensão:\nImagens: JPG, JPEG ou PNG.", type: 'error' };
                    }

                    if ( !(regex).test(uploadFile.name) ){
                        openNotification(obj);
                        return false;
                    }


                    if( data.paramName.indexOf('[]') != '-1' ){
                        // Gera id único
                        var id = 'img' + Math.floor(Math.random()* 1000000);
                        // Controle para não duplicar ids
                        var aux = $('#'+id);
                        while(aux.length != 0){
                            id = 'img' + Math.floor(Math.random()* 1000000);
                            aux = $('#'+id);
                        }

                        // Salva o id
                        data.divId = id;
                        // Gera Layout
                        var img =   '<div class="template-upload fade in new" data-id="'+ data.divId +'" id="'+ data.divId +'">\
                                        <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-info" style="width:0%;"></div></div>\
                                        <span class="preview">\
                                            <div class="highlighted">\
                                                <input type="checkbox" name="'+elData.gallerytype+'['+ data.divId +'][highlighted]" />\
                                            </div>\
                                        </span>';

                                        if(Object.keys(languages).length > 1 && elData.multilang) {

                                            $.each( languages, function( key, value ) {
                                                var current = current_lang != value.code ? ' hide' : '';
                                                img += '<p class="name language'+key+current+'"><input type="text" name="'+elData.gallerytype+'['+ data.divId +'][subtitle]['+key+']" class="form-control" placeholder="Legenda" value="" /></p>';
                                            });
                                            img += '<select name="'+elData.gallerytype+'['+ data.divId +'][id_language]" class="form-control change-language" >';

                                                $.each( languages, function( key, value ) {
                                                    var selected = current_lang == value.code ? ' selected="selected" ' : '';
                                                    img += '<option '+selected+' data-image="'+value.image+'" value="'+key+'" >'+value.code.toUpperCase()+'</option>';
                                                });

                                            img += '</select>';
                                        } else {
                                            img += '<p class="name"><input type="text" name="'+elData.gallerytype+'['+ data.divId +'][subtitle]" class="form-control" placeholder="Legenda" value="" /></p>';
                                        }

                                        img += '<button class="btn btn-danger btn-stroke cancel">\
                                            <i class="glyphicon glyphicon-trash"></i>\
                                        </button>\
                                        <strong class="error text-danger"></strong>\
                                    </div>';

                        var containerAppend = "."+data.paramName.replace('[]','');
                        $(containerAppend).append(img);
                        images.push(data);
                        files.push(data);

                        var file_type = data.files[0].type.split('/');
                        if(file_type[0] == 'image'){
                            console.log(data.files[0]);
                            $(containerAppend + ' #' + data.divId).find('.preview').append(data.files[0].preview);
                        }else if(file_type[0] == 'video'){
                            $(containerAppend + ' #' + data.divId).find('.preview').append(data.files[0].preview);
                        }else{
                            $(containerAppend + ' #' + data.divId).find('.preview').append(getFilePreview(data.files[0].name)).css({"margin-bottom" : "16px"});
                        }

                        $('.radio-custom > input[type=radio]').each(function () {
                            var $this = $(this);
                            if ($this.data('radio')) return;
                       //     $this.radio($this.data());
                        });

                        $(containerAppend + ' #' + data.divId + ' select').select2({
                            templateResult: formatSelect,
                            templateSelection: formatSelect
                        });
                    }
                },
                submit: function (e, data) {
                    data.formData = elData;
                },
                done: function (e, data) {
                    $.each(files, function( index, value ) {
                        if (value.divId == data.divId){
                            value.textStatus = data.textStatus;
                            return false;
                        }
                    });
                    if (data.jqXHR.responseJSON.status){
                        var highlighted = $('#'+data.divId).find('input[name="'+elData.gallerytype+'['+ data.divId +'][highlighted]"]').is(':checked') ? 1 : 0;
                        $('.imageInputs').append('<input type="hidden" name="gallery['+ data.divId +'][image]" value="'+data.jqXHR.responseJSON.file_id+'" />');
                        $('.imageInputs').append('<input type="hidden" name="gallery['+ data.divId +'][order_by]" value="'+ ($('#' + data.divId).prevAll('.template-upload').length + 1) +'" />');
                        $('.imageInputs').append('<input type="hidden" name="gallery['+ data.divId +'][type]" value="'+ elData.gallerytype +'" />');

                        if(Object.keys(languages).length > 1 && elData.multilang) {
                            $.each( languages, function( key, value ) {
                                var subtitle = $('#'+data.divId).find('input[name="'+elData.gallerytype+'['+ data.divId +'][subtitle]['+key+']"]').val();
                                $('.imageInputs').append('<input type="hidden" name="gallery['+ data.divId +'][subtitle]['+key+']" value="'+ subtitle +'" />');
                            });
                        } else {
                            var subtitle = $('#'+data.divId).find('input[name="'+elData.gallerytype+'['+ data.divId +'][subtitle]"]').val();
                            $('.imageInputs').append('<input type="hidden" name="gallery['+ data.divId +'][subtitle]" value="'+ subtitle +'" />');
                        }

                        if(highlighted)
                            $('.imageInputs').append('<input type="hidden" name="gallery['+ data.divId +'][highlighted]" value="'+ highlighted +'" />');
                    }
                    sendForm($('#validateSubmitForm'));
                },
            });
        });
        $('.imagesAll, .filesAll').on('click','.cancel',function(e){
            e.preventDefault();
            e.stopPropagation();
            var id = $(this).closest('.template-upload').data('id');
            for (i in files){
                if (id == files[i].divId)
                    files.splice(i,1);
            }
            for (i in images){
                if (id == images[i].divId)
                    images.splice(i,1);
            }

            $(this).closest('.template-upload').remove();
            setOrderFiles();
        });
        $('.fileupload-buttonbar .cancel').click(function(e){
            e.preventDefault();
            e.stopPropagation();
            if (!$(this).hasClass('btn-default')){

                for (i in images){
                    for (j in files){
                        if (images[i].divId == files[j].divId)
                            files.splice(j, 1);
                    }
                }

                images = [];
                $(this).closest('.gallery-images').find('.template-upload:not(.old)').remove();
                setOrderFiles();
            }
        });
    }
    // DISPONIVEIS NA EDIÇÃO
    // Delete All
    $('body').on('change','.deleteAll',function(){
        var $selector = ($(this).attr('id') == 'inputFileDeleteAll') ? $('.fileOlds') : $('.imageOlds');
        if ($(this).is(':checked')){
            $(this).siblings('i').addClass('checked')
            $selector.find('.checkbox-custom :checkbox').prop('checked', true).trigger('change').siblings('i').addClass('checked');
        }else{
            $(this).siblings('i').removeClass('checked')
            $selector.find('.checkbox-custom :checkbox').prop('checked', false).trigger('change').siblings('i').removeClass('checked');
        }
        if ($(this).attr('id') == 'inputFileDeleteAll')
            updateDeleteFiles();
        else
            updateDeletePhotos();
    });
    $('body').on('change','.deleteIndividual .checkbox-custom :checkbox',function(){
        var $selector = ($(this).closest('.deleteIndividual').hasClass('imageOlds')) ? $('#inputPhotoDeleteAll') : $('#inputFileDeleteAll');
        if ($(this).is(':checked')){
            $(this).siblings('i').addClass('checked')
        }else{
            $(this).siblings('i').removeClass('checked')
        }
        if ($(this).closest('.deleteIndividual').find('.checkbox-custom :checkbox:checked').size() == $(this).closest('.deleteIndividual').find('.checkbox-custom :checkbox').size())
            $selector.prop('checked', true).siblings('i').addClass('checked');
        else
            $selector.prop('checked', false).siblings('i').removeClass('checked');
        if ($(this).closest('.deleteIndividual').hasClass('imageOlds'))
            updateDeletePhotos();
        else
            updateDeleteFiles();
    });

    $('body').on('change','select.change-language',function(){
        $(this).parent().find('.name').addClass('hide');
        $(this).parent().find('.name.language'+$(this).val()).removeClass('hide');
    });

    // Sortable
    if (typeof $.fn.sortable === 'function' ) {
        if ($('.imageOlds').length > 0 || $('.fileOlds').length > 0){
            $('.imageOlds, .fileOlds').sortable({
                update: function( event, ui ) {
                    var orderWrapper;
                    if ($(this).hasClass('imagesAll'))
                        orderWrapper = '.imageOlds';
                    else
                        orderWrapper = '.fileOlds';

                    $('.sort-info > i').animate({'opacity': '1'}, 100);
                    var order = [];
                    $(orderWrapper + ' .template-upload:not(.new)').each(function(){
                        order.push($(this).data('id'));
                    });

                    // Permite ordenação apenas se as imagens já foram salvas no registro
                    if(url_crud != 'cadastrar'){
                        $.ajax({
                            data: {
                                order: order.join(','),
                                id: $('#inputId').val(),
                                gallerytable: $(this).closest('.deleteIndividual').data('gallerytable')
                            },
                            dataType: "json",
                            url: site_url + 'gallery/sort-images',
                            type: 'POST',
                            success: function(data){
                                var obj = { layout: 'top', text: data.message, type: data.classe };
                                openNotification(obj);
                                $('.sort-info > i').animate({'opacity': '0'}, 250);
                                setOrderFiles();
                            }
                        });
                    }
                }
            });
            $('.imageOlds, .fileOlds').on('click','.delete',function(e){
                e.preventDefault();
                e.stopPropagation();

                var id = $(this).closest('.template-upload').data('id');
                for (var i in files){
                    if (id == files[i].divId)
                        files.splice(i,1);
                }
                for (var i in images){
                    if (id == images[i].divId)
                        images.splice(i,1);
                }

                var self = $(this);
                bootbox.confirm("Você tem certeza que deseja excluir esta imagem?", function(result){
                    if (result) {
                        $.ajax({
                            type: "POST",
                            url: site_url + 'gallery/delete-image',
                            data: {
                                ids: self.closest('.template-upload').data('id'),
                                gallerytable: self.closest('.deleteIndividual').data('gallerytable'),
                                gallerypath: self.closest('.deleteIndividual').data('gallerypath')
                            },
                            dataType: "json",
                            success: function(data) {
                                var obj = { layout: 'top', text: data.message, type: data.classe };
                                openNotification(obj);
                                self.closest('.template-upload').animate({opacity: 0}, 700,function(){
                                    $(this).remove();
                                    if ($('.imageOlds .template-upload').length==0){
                                        $(this).closest('.col-xs-12').find('input[type="hidden"][id^="#delete"], .delete-all, label[for$="DeleteAll"]').fadeOut(600, function(){ $(this).remove(); });
                                    }
                                    setOrderFiles();
                                });
                            }
                        });
                    }
                });
            });
            $('.gallery-images').on('click','.delete-all',function(e){
                e.preventDefault();
                e.stopPropagation();

                var ids = [],
                    imgs,
                    checkbox,
                    deleteBt,
                    wrapper,
                    $self = $(this);

                if ($self.closest('.gallery-images').find('.deleteIndividual').hasClass('imagesAll')){
                    checkbox = 'inputPhotoDeleteAll';
                    deleteBt = '#deleteImages';
                    wrapper = '.imageOlds';
                } else {
                    checkbox = 'inputFileDeleteAll';
                    deleteBt = '#deleteFiles';
                    wrapper = '.fileOlds';
                }

                if (!$(this).hasClass('btn-default')){

                    for (var i in images){
                        for (j in files){
                            if (images[i].divId == files[j].divId)
                                files.splice(j, 1);
                        }
                    }

                    images = [];
                    $(this).closest('.gallery-images').find('.template-upload:not(.old)').remove();
                    setOrderFiles();
                }


                imgs = $(wrapper + ' .checkbox-custom :checkbox:checked').parents('.template-upload');
                imgs.each(function(){
                    ids.push($(this).data('id'));
                });
                if (ids.length>0){
                    bootbox.confirm("Você tem certeza que deseja excluir estes itens?", function(result){
                        if (result) {
                            $.ajax({
                                type: "POST",
                                url: site_url + 'gallery/delete-image',
                                data: {
                                    ids: ids.join(','),
                                    gallerytable: $self.closest('.gallery-images').find('.deleteIndividual').data('gallerytable'),
                                    gallerypath: $self.closest('.gallery-images').find('.deleteIndividual').data('gallerypath')
                                },
                                dataType: "json",
                                success: function(data) {
                                    var obj = { layout: 'top', text: data.message, type: data.classe };
                                    openNotification(obj);
                                    imgs.each(function(){
                                        $(this).animate({opacity: 0}, 700,function(){
                                            $(this).remove();
                                            if ($(wrapper + ' .template-upload').length==0){
                                                $(wrapper).parents('.col-xs-12').find(deleteBt + ', .delete-all, label[for="' + checkbox + '"]').fadeOut(600, function(){ $(this).remove(); });
                                            }
                                            setOrderFiles();
                                        });
                                    });
                                    $('#' + checkbox).prop('checked', false).siblings('i').removeClass('checked');
                                }
                            });
                        }
                    });
                }
            });
        }
    }
});

function updateDeletePhotos()
{
    var v = [];
    $('.imageOlds .checkbox-custom :checkbox:checked').each(function(){
        v.push($(this).parents('.template-upload').data('id'));
    });
    $('#deleteImages').val(v.join(','));
}


function updateDeleteFiles()
{
    var v = [];
    $('.fileOlds .checkbox-custom :checkbox:checked').each(function(){
        v.push($(this).parents('.template-upload').data('id'));
    });
    $('#deleteFiles').val(v.join(','));
}

function getFilePreview (name){

    var ext = name.split('.');
    ext = ext.pop().toUpperCase();

    return "<div class='file-preview'>" + ext + "</div>";

}

function setOrderFiles ()
{
    $('.template-upload').each(function(i,el){

        $(this).find('.image-order').val(i+1);

    });
}

function formatSelect(data)
{
    if (!data.id) { return data.text; }
    return $("<span><img class='img-flag' src='" + base_img + '/' +  $(data.element).data('image')+"'/> "+data.text+'</span>');
}

Comum.cropImage();