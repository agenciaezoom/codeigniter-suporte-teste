<h4><?php echo T_('Conteúdo'); ?> <?php echo (($item->languages[1]->area) ? ' > '.$item->languages[1]->area:'').( ($item->languages[1]->subarea) ? ' > '.$item->languages[1]->subarea:''); ?></h4>
<form action="<?php echo site_url('paginas/savePermission/'.$id)?>" id="validateSubmitForm" class="form-horizontal" role="form" enctype="multipart/form-data" method="post">
    <input type="hidden" name="id" value="<?php echo $item->id; ?>" id="inputId" />

    <div class="col-xs-12 col-sm-6">
        <div class="form-group">
            <label for="inputTitle" class="col-xs-4 col-sm-2 col-lg-2 control-label"><?php echo T_('Título:'); ?> </label>
            <div class="col-xs-8 col-sm-10 col-lg-10">
                <select class="form-control selectpicker" name="enable_title">
                    <option selected value="enabled"><?php echo T_('Ativo'); ?></option>
                    <option <?php echo (!empty($permission) && $permission->enable_title == 'disabled') ? ' selected ' : ''; ?> value="disabled"><?php echo T_('Inativo'); ?></option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6">
        <div class="form-group">
            <label for="inputTitleBold" class="col-xs-4 col-sm-2 col-lg-2 control-label"><?php echo T_('Título com bold:'); ?> </label>
            <div class="col-xs-8 col-sm-10 col-lg-10">
                <select class="form-control selectpicker" name="enable_title_bold">
                    <option selected value="enabled"><?php echo T_('Ativo'); ?></option>
                    <option <?php echo (!empty($permission) && $permission->enable_title_bold == 'disabled') ? ' selected ' : ''; ?> value="disabled"><?php echo T_('Inativo'); ?></option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6">
        <div class="form-group">
            <label for="inputSubtitle" class="col-xs-4 col-sm-2 col-lg-2 control-label"><?php echo T_('Subtítulo:'); ?> </label>
            <div class="col-xs-8 col-sm-10 col-lg-10">
                <select class="form-control selectpicker" name="enable_subtitle">
                    <option selected value="enabled"><?php echo T_('Ativo'); ?></option>
                    <option <?php echo (!empty($permission) && $permission->enable_subtitle == 'disabled') ? ' selected ' : ''; ?> value="disabled"><?php echo T_('Inativo'); ?></option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6">
        <div class="form-group">
            <label for="inputSubtitleBold" class="col-xs-4 col-sm-2 col-lg-2 control-label"><?php echo T_('Subtítulo com bold:'); ?> </label>
            <div class="col-xs-8 col-sm-10 col-lg-10">
                <select class="form-control selectpicker" name="enable_subtitle_bold" id="inputSubtitleBold">
                    <option selected value="enabled"><?php echo T_('Ativo'); ?></option>
                    <option <?php echo (!empty($permission) && $permission->enable_subtitle_bold == 'disabled') ? ' selected ' : ''; ?> value="disabled"><?php echo T_('Inativo'); ?></option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6">
        <div class="form-group">
            <label for="inputArea" class="col-xs-4 col-sm-2 col-lg-2 control-label"><?php echo T_('Área:'); ?> </label>
            <div class="col-xs-8 col-sm-10 col-lg-10">
                <select class="form-control selectpicker" name="enable_area">
                    <option selected value="enabled"><?php echo T_('Ativo'); ?></option>
                    <option <?php echo (!empty($permission) && $permission->enable_area == 'disabled') ? ' selected ' : ''; ?> value="disabled"><?php echo T_('Inativo'); ?></option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6">
        <div class="form-group">
            <label for="inputSubarea" class="col-xs-4 col-sm-2 col-lg-2 control-label"><?php echo T_('Subárea:'); ?> </label>
            <div class="col-xs-8 col-sm-10 col-lg-10">
                <select class="form-control selectpicker" name="enable_subarea">
                    <option selected value="enabled"><?php echo T_('Ativo'); ?></option>
                    <option <?php echo (!empty($permission) && $permission->enable_subarea == 'disabled') ? ' selected ' : ''; ?> value="disabled"><?php echo T_('Inativo'); ?></option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6">
        <div class="form-group">
            <label for="inputSlug" class="col-xs-4 col-sm-2 col-lg-2 control-label"><?php echo T_('Slug:'); ?> </label>
            <div class="col-xs-8 col-sm-10 col-lg-10">
                <select class="form-control selectpicker" name="enable_slug">
                    <option selected value="enabled"><?php echo T_('Ativo'); ?></option>
                    <option <?php echo (!empty($permission) && $permission->enable_slug == 'disabled') ? ' selected ' : ''; ?> value="disabled"><?php echo T_('Inativo'); ?></option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6">
        <div class="form-group">
            <label for="inputText" class="col-xs-4 col-sm-2 col-lg-2 control-label"><?php echo T_('Texto:'); ?> </label>
            <div class="col-xs-8 col-sm-10 col-lg-10">
                <select class="form-control selectpicker" name="enable_text">
                    <option selected value="enabled"><?php echo T_('Ativo'); ?></option>
                    <option <?php echo (!empty($permission) && $permission->enable_text == 'disabled') ? ' selected ' : ''; ?> value="disabled"><?php echo T_('Inativo'); ?></option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6">
        <div class="form-group">
            <label for="inputImage" class="col-xs-4 col-sm-2 col-lg-2 control-label"><?php echo T_('Imagem:'); ?> </label>
            <div class="col-xs-8 col-sm-10 col-lg-10">
                <select class="form-control selectpicker" name="enable_image">
                    <option selected value="enabled"><?php echo T_('Ativo'); ?></option>
                    <option <?php echo (!empty($permission) && $permission->enable_image == 'disabled') ? ' selected ' : ''; ?> value="disabled"><?php echo T_('Inativo'); ?></option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6">
        <div class="form-group">
            <label for="inputImage" class="col-xs-4 col-sm-2 col-lg-2 control-label"><?php echo T_('Dimensões da Imagem:'); ?> </label>
            <div class="col-xs-8 col-sm-10 col-lg-10">
                <select class="form-control selectpicker" name="enable_image_dim">
                    <option selected value="enabled"><?php echo T_('Ativo'); ?></option>
                    <option <?php echo (!empty($permission) && $permission->enable_image_dim == 'disabled') ? ' selected ' : ''; ?> value="disabled"><?php echo T_('Inativo'); ?></option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6">
        <div class="form-group">
            <label for="inputImageMobile" class="col-xs-4 col-sm-2 col-lg-2 control-label"><?php echo T_('Imagem Mobile:'); ?> </label>
            <div class="col-xs-8 col-sm-10 col-lg-10">
                <select class="form-control selectpicker" name="enable_image_mobile">
                    <option selected value="enabled"><?php echo T_('Ativo'); ?></option>
                    <option <?php echo (!empty($permission) && $permission->enable_image_mobile == 'disabled') ? ' selected ' : ''; ?> value="disabled"><?php echo T_('Inativo'); ?></option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6">
        <div class="form-group">
            <label for="inputImageMobile" class="col-xs-4 col-sm-2 col-lg-2 control-label"><?php echo T_('Dimensões da Imagem Mobile:'); ?> </label>
            <div class="col-xs-8 col-sm-10 col-lg-10">
                <select class="form-control selectpicker" name="enable_image_mobile_dim">
                    <option selected value="enabled"><?php echo T_('Ativo'); ?></option>
                    <option <?php echo (!empty($permission) && $permission->enable_image_mobile_dim == 'disabled') ? ' selected ' : ''; ?> value="disabled"><?php echo T_('Inativo'); ?></option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6">
        <div class="form-group">
            <label for="inputArchive" class="col-xs-4 col-sm-2 col-lg-2 control-label"><?php echo T_('Arquivo:'); ?> </label>
            <div class="col-xs-8 col-sm-10 col-lg-10">
                <select class="form-control selectpicker" name="enable_archive">
                    <option selected value="enabled"><?php echo T_('Ativo'); ?></option>
                    <option <?php echo (!empty($permission) && $permission->enable_archive == 'disabled') ? ' selected ' : ''; ?> value="disabled"><?php echo T_('Inativo'); ?></option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6">
        <div class="form-group">
            <label for="inputLink" class="col-xs-4 col-sm-2 col-lg-2 control-label"><?php echo T_('Link:'); ?> </label>
            <div class="col-xs-8 col-sm-10 col-lg-10">
                <select class="form-control selectpicker" name="enable_link">
                    <option selected value="enabled"><?php echo T_('Ativo'); ?></option>
                    <option <?php echo (!empty($permission) && $permission->enable_link == 'disabled') ? ' selected ' : ''; ?> value="disabled"><?php echo T_('Inativo'); ?></option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6">
        <div class="form-group">
            <label for="inputLinkLabel" class="col-xs-4 col-sm-2 col-lg-2 control-label"><?php echo T_('Link Label:'); ?> </label>
            <div class="col-xs-8 col-sm-10 col-lg-10">
                <select class="form-control selectpicker" name="enable_link_label">
                    <option selected value="enabled"><?php echo T_('Ativo'); ?></option>
                    <option <?php echo (!empty($permission) && $permission->enable_link_label == 'disabled') ? ' selected ' : ''; ?> value="disabled"><?php echo T_('Inativo'); ?></option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6">
        <div class="form-group">
            <label for="inputLink" class="col-xs-4 col-sm-2 col-lg-2 control-label"><?php echo T_('Youtube ID:'); ?> </label>
            <div class="col-xs-8 col-sm-10 col-lg-10">
                <select class="form-control selectpicker" name="enable_youtube_id">
                    <option selected value="enabled"><?php echo T_('Ativo'); ?></option>
                    <option <?php echo (!empty($permission) && $permission->enable_youtube_id == 'disabled') ? ' selected ' : ''; ?> value="disabled"><?php echo T_('Inativo'); ?></option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6">
        <div class="form-group">
            <label for="inputLink" class="col-xs-4 col-sm-2 col-lg-2 control-label"><?php echo T_('Status:'); ?> </label>
            <div class="col-xs-8 col-sm-10 col-lg-10">
                <select class="form-control selectpicker" name="enable_status">
                    <option selected value="enabled"><?php echo T_('Ativo'); ?></option>
                    <option <?php echo (!empty($permission) && $permission->enable_status == 'disabled') ? ' selected ' : ''; ?> value="disabled"><?php echo T_('Inativo'); ?></option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6">
        <div class="form-group">
            <label for="inputGallery" class="col-xs-4 col-sm-2 col-lg-2 control-label"><?php echo T_('Galeria:'); ?> </label>
            <div class="col-xs-8 col-sm-10 col-lg-10">
                <select class="form-control selectpicker" name="enable_gallery">
                    <option selected value="enabled"><?php echo T_('Ativo'); ?></option>
                    <option <?php echo (!empty($permission) && $permission->enable_gallery == 'disabled') ? ' selected ' : ''; ?> value="disabled"><?php echo T_('Inativo'); ?></option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6">
        <div class="form-group">
            <label for="inputVideos" class="col-xs-4 col-sm-2 col-lg-2 control-label"><?php echo T_('Galeria de Vídeos:'); ?> </label>
            <div class="col-xs-8 col-sm-10 col-lg-10">
                <select class="form-control selectpicker" id="inputVideos" name="enable_videos">
                    <option selected value="enabled"><?php echo T_('Ativo'); ?></option>
                    <option <?php echo (!empty($permission) && $permission->enable_videos == 'disabled') ? ' selected ' : ''; ?> value="disabled"><?php echo T_('Inativo'); ?></option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6">
        <div class="form-group">
            <label for="inputGallery" class="col-xs-4 col-sm-2 col-lg-2 control-label"><?php echo T_('Editar:'); ?> </label>
            <div class="col-xs-8 col-sm-10 col-lg-10">
                <select class="form-control selectpicker" name="enable_edit">
                    <option selected value="enabled"><?php echo T_('Ativo'); ?></option>
                    <option <?php echo (!empty($permission) && $permission->enable_edit == 'disabled') ? ' selected ' : ''; ?> value="disabled"><?php echo T_('Inativo'); ?></option>
                </select>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6">
        <div class="form-group">
            <label for="inputDelete" class="col-xs-4 col-sm-2 col-lg-2 control-label"><?php echo T_('Deletar:'); ?> </label>
            <div class="col-xs-8 col-sm-10 col-lg-10">
                <select class="form-control selectpicker" name="enable_delete">
                    <option selected value="enabled"><?php echo T_('Ativo'); ?></option>
                    <option <?php echo (!empty($permission) && $permission->enable_delete == 'disabled') ? ' selected ' : ''; ?> value="disabled"><?php echo T_('Inativo'); ?></option>
                </select>
            </div>
        </div>
    </div>
    <div class="col-sm-12 text-center">
        <button type="submit" class="btn btn-primary">Salvar</button>
    </div>
</form>
