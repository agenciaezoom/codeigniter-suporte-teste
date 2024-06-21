(function($)
{
    var imgCrop = false;

    $('.crop-image').on('click', function (e){
        var timestamp = new Date().getTime(),
            el = this,
            img = $(this).data('imagesite'),
            id = $(this).closest('.template-upload').data('id');

        $('#modal-crop').prop('data-id', id);
        $('#modal-crop .image-crop-holder').addClass('loading');
        $('<img/>', {
            src : site_url + img + '?'+ timestamp
        }).load(function () {
            var image = $(this);
            image.addClass('img-responsive');
            $('#modal-crop .image-crop-holder').append(image);
            setTimeout(function() {
                $('#modal-crop .image-crop-holder').removeClass('loading');
                setTimeout(function(){
                    var w = image.width(),
                        h = image.height();
                    $('#modal-crop').find('input[name="image"]').val(img);
                    $('#modal-crop').find('input[name="image_width"]').val(w);
                    $('#modal-crop').find('input[name="image_height"]').val(h);
                    $('#modal-crop').find('[type="submit"]').removeAttr('disabled');
                    image.Jcrop({
                        onChange: updatePreview,
                        onSelect: updatePreview,
                    },function(){
                        imgCrop = this;
                        imgCrop.setOptions({ bgFade: true });
                        imgCrop.setSelect([w/4, h/4, (w/4)+(w/2), (h/4)+(h/2)]);
                        imgCrop.ui.selection.addClass('jcrop-selection');
                    });
                },500);
            }, 50);
        });
    });

    $('#modal-crop').on('hidden.bs.modal', function(e){
        if (imgCrop != false)
            imgCrop.destroy();
        $('#modal-crop .image-crop-holder img').remove();
        $('#modal-crop').find('input[type="hidden"]').val('');
        $('#modal-crop').find('[type="submit"]').attr('disabled','disabled');
    });

    $('#modal-crop').on('click', '.crop-cancel', function(e){
        e.preventDefault();
        $('#modal-crop .close').trigger('click');
    });

    function updatePreview(c){
        $('#modal-crop').find('input[name="crop_x"]').val(c.x);
        $('#modal-crop').find('input[name="crop_y"]').val(c.y);
        $('#modal-crop').find('input[name="crop_width"]').val(c.w);
        $('#modal-crop').find('input[name="crop_height"]').val(c.h);
    }

    $("#modal-crop form buttom[type='submit']").on('click', function(e){
        e.preventDefault();
        e.stopPropagation();

        var form = $('#modal-crop form');

        $(form).find('[type="submit"]').attr('disabled','disabled').html('<img src="'+base_img+'/loading.gif">').addClass('loading');
        $.ajax({
            type: "POST",
            url: $(form).attr('action'),
            data: $(form).serialize(),
            dataType: "json",
            complete: function () {
                $(form).find('[type="submit"]').removeAttr('disabled').html('Recortar').removeClass('loading');
            },
            success: function(data){
                console.log('oi');

                var obj = {
                    layout: 'top',
                    text: data.message,
                    type: data.classe
                };
                openNotification(obj);
                console.log(data);

                if (data.status){
                    // Atualiza thumb
                    var timestamp = new Date().getTime();
                    console.log($('#modal-crop').data('id'));

                    $('.imageOlds .template-upload[data-id="' + $('#modal-crop').data('id') + '"] .preview img').attr('src', data.image + "&" + timestamp);
                    $('.imageOlds .template-upload[data-id="' + $('#modal-crop').data('id') + '"] .config-file-close').trigger('click');
                    $('#modal-crop .close').trigger('click');
                }

            }
        });
    });


})(jQuery);