<form action="<?= site_url($current_module->slug . (isset($item) ? '/edit/' . $id : '/add')); ?>" id="validateSubmitForm" class="form-horizontal" role="form" enctype="multipart/form-data" method="post">
  <?php if (isset($item)) { ?>
    <input type="hidden" name="id" value="<?= $id; ?>" id="inputId" />
  <?php   } ?>
  <ul class="nav nav-tabs col-sm-12">
    <li class="active"><a href="#tab1" data-toggle="tab"><i class="fa fa-file-alt"></i> <?= T_('Dados Gerais'); ?></a></li>
    <li><a href="#tab2" data-toggle="tab"><i class="fa fa-map"></i> <?= T_('Localização'); ?></a></li>
    <li><a href="#tab3" data-toggle="tab"><i class="fa fa-user"></i> <?= T_('Redes Sociais'); ?></a></li>
    <li><a href="#tab4" data-toggle="tab"><i class="fa fa-share-alt"></i> <?= T_('SEO'); ?></a></li>
    <?php if ($this->config->item('multi_company')) { ?>
      <li><a href="#tab5" data-toggle="tab"><i class="fa fa-globe"></i> <?= T_('Site'); ?></a></li>
    <?php }
    if (isset($item) && $item->id == 2) { ?>
      <li><a href="#tab6" data-toggle="tab"><i class="fa fa-phone"></i> <?= T_('Informações'); ?></a></li>
    <?php } ?>
  </ul>
  <div class="tab-content col-sm-12">
    <!-- Primeira Tab (Empresa) -->
    <div class="tab-pane fade active in" id="tab1">
      <div class="tab-content">
        <div class="col-xs-12 col-sm-2 form-group">
          <label for="inputStatus" class="col-xs-12 control-label"><?= T_('Ativo:'); ?> </label>
          <div class="make-switch">
            <?php if (!isset($item) || $item->status == '1') { ?>
              <div class="button-switch button-on"><?= T_('Sim'); ?></div>
              <input type="checkbox" name="status" checked="checked" id="inputStatus">
            <?php } else { ?>
              <div class="button-switch button-off"><?= T_('Não'); ?></div>
              <input type="checkbox" name="status" id="inputStatus">
            <?php } ?>
          </div>
        </div>

        <div class="col-xs-12 col-sm-2 form-group">
          <label for="inputSite" class="col-xs-12 control-label"><?= T_('Site Ativo:'); ?> </label>
          <div class="make-switch">
            <?php if (!isset($item) || $item->active_site == '1') { ?>
              <div class="button-switch button-on"><?= T_('Sim'); ?></div>
              <input type="checkbox" name="active_site" checked="checked" id="inputSite">
            <?php } else { ?>
              <div class="button-switch button-off"><?= T_('Não'); ?></div>
              <input type="checkbox" name="active_site" id="inputSite">
            <?php } ?>
          </div>
        </div>

        <div class="col-xs-12 form-group">
          <label for="inputTitulo" class="control-label"><?= T_('Razão Social:'); ?> </label>
          <input type="text" class="form-control" name="company_name" id="inputTitulo" placeholder="<?= T_('Razão Social'); ?>" value="<?= (isset($item->company_name)) ?  $item->company_name : ''; ?>" required>
        </div>
        <div class="col-xs-12 col-sm-6 form-group">
          <label for="inputTitulo" class="control-label"><?= T_('Nome Fantasia:'); ?> </label>
          <input type="text" class="form-control" name="fantasy_name" id="inputTitulo" placeholder="<?= T_('Nome Fantasia'); ?>" value="<?= (isset($item->fantasy_name)) ?  $item->fantasy_name : ''; ?>" required>
        </div>
        <div class="col-xs-12 col-sm-6 form-group">
          <label for="inputPhone" class="control-label"><?= T_('Telefone:'); ?> </label>
          <input type="text" class="form-control" name="phone" id="inputPhone" placeholder="<?= T_('Telefone'); ?>" value="<?= (isset($item->phone)) ?  $item->phone : ''; ?>" required>
        </div>
        <div class="col-xs-12 col-sm-6 form-group">
          <label for="inputWpp" class="control-label"><?= T_('Whatsapp:'); ?> </label>
          <input type="text" class="form-control" name="whatsapp" id="inputWpp" placeholder="<?= T_('Whatsapp'); ?>" value="<?= (isset($item->whatsapp)) ?  $item->whatsapp : ''; ?>" required>
        </div>
        <div class="col-xs-12 col-sm-6 form-group">
          <label for="inputPartnerWpp" class="control-label"><?= T_('Whatsapp - Parceiros:'); ?> </label>
          <input type="text" class="form-control" name="partner_whatsapp" id="inputPartnerWpp" placeholder="<?= T_('Whatsapp - Parceiros'); ?>" value="<?= (isset($item->partner_whatsapp)) ?  $item->partner_whatsapp : ''; ?>">
        </div>
        <div class="col-xs-12 col-sm-6 form-group">
          <label for="inputSACWpp" class="control-label"><?= T_('Whatsapp - Dúvidas e SAC:'); ?> </label>
          <input type="text" class="form-control" name="sac_whatsapp" id="inputSACWpp" placeholder="<?= T_('Whatsapp - Dúvidas e SAC'); ?>" value="<?= (isset($item->sac_whatsapp)) ?  $item->sac_whatsapp : ''; ?>">
        </div>
        <div class="col-xs-12 col-sm-6 form-group">
          <label for="inputEmail" class="control-label"><?= T_('E-mail:'); ?> </label>
          <input type="text" class="form-control" name="email" id="inputEmail" placeholder="<?= T_('E-mail'); ?>" value="<?= (isset($item->email)) ?  $item->email : ''; ?>" required>
        </div>
        <div class="col-xs-12 col-sm-6 form-group">
          <label for="inputHREmail" class="control-label"><?= T_('E-mail do RH:'); ?> </label>
          <input type="text" class="form-control" name="hr_email" id="inputHREmail" placeholder="<?= T_('E-mail do RH'); ?>" value="<?= (isset($item->hr_email)) ?  $item->hr_email : ''; ?>">
        </div>
        <div class="col-xs-12 col-sm-6 form-group">
          <label for="inputGoogleTagManager" class="control-label"><?= T_('Google Tag Manager:'); ?> </label>
          <input type="text" class="form-control" name="google_tag_manager" id="inputGoogleTagManager" placeholder="<?= T_('Google Tag Manager'); ?>" value="<?= isset($item) ? $item->google_tag_manager : ''; ?>">
        </div>

        <div class="col-xs-12 col-sm-6 form-group">
          <label for="inputLanguage" class="control-label"><?= T_('Idioma Principal:'); ?> </label>
          <select class="form-control select2" name="language_main" id="inputLanguage" required>
            <?php foreach ($languages as $key => $value) { ?>
              <option <?= (isset($item) && $value->id == $item->language_main) ? ' selected ' : ''; ?> value="<?= $value->id; ?>"><?= $value->name; ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="col-xs-12 col-sm-6 form-group">
          <label for="inputLanguages" class="control-label"><?= T_('Idiomas Disponíveis:'); ?> </label>
          <select class="form-control select2" name="languages_site[]" id="inputLanguages" data-placeholder="<?= T_('Nenhum idioma selecionado'); ?>" multiple required>
            <option value=""></option>
            <?php foreach ($languages as $key => $value) { ?>
              <option <?= (isset($item) && in_array($value->id, explode(',', $item->languages_site))) ? ' selected ' : ''; ?> value="<?= $value->id; ?>"><?= $value->name; ?></option>
            <?php } ?>
          </select>
        </div>

        <div class="col-xs-12 col-sm-12 col-lg-12 form-group">
          <label for="inputBusinessHours" class="control-label"><?= T_('Horário de Funcionamento'); ?>: </label>
          <textarea id="inputBusinessHours" name="business_hours" class="form-control list-editor" style="height: 320px;" rows="5"><?= (isset($item)) ? $item->business_hours : ''; ?></textarea>
        </div>
      </div>

    </div>

    <!-- Segunda Tab (Localização) -->
    <div class="tab-pane fade" id="tab2">
      <?php
      $location_config = array();
      if (isset($countries))
        $location_config['countries'] = $countries;
      if (isset($states))
        $location_config['estados'] = $states;
      if (isset($cities))
        $location_config['cidades'] = $cities;
      if (isset($item)) {
        $location_config['data'] = new stdClass();
        $location_config['data']->lat = isset($item->lat) ? $item->lat : '';
        $location_config['data']->lng = isset($item->lng) ? $item->lng : '';
        $location_config['data']->state = isset($item->state) ? $item->state : '';
        $location_config['data']->id_country = isset($item->id_country) ? $item->id_country : '';
        $location_config['data']->city = isset($item->city) ? $item->city : '';
        $location_config['data']->suburb = isset($item->suburb) ? $item->suburb : '';
        $location_config['data']->zip_code = isset($item->zipcode) ? $item->zipcode : '';
        $location_config['data']->street = isset($item->address) ? $item->address : '';
        $location_config['data']->number = isset($item->number) ? $item->number : '';
        $location_config['data']->additional_info = isset($item->complement) ? $item->complement : '';
      }
      $this->load->view('endereco/endereco', $location_config);
      ?>
    </div>

    <!-- Terceira Tab (Redes Sociais) -->
    <div class="tab-pane fade" id="tab3">

      <?= $this->load->view('comum/nav-lang', array('tabname' => 'tablang', 'languages' => $languages_by_company)); ?>
      <div class="tab-content">
        <!-- Body da tab linguagem -->
        <?php foreach ($languages_by_company as $key => $language) { ?>
          <div class="tab-pane<?= ($key == 0) ? ' active in ' : ''; ?> fade" id="tablang<?= $key; ?>">
            <div class="col-xs-12 col-sm-6 form-group">
              <label for="inputInstagram<?= $key; ?>" class="control-label"><?= T_('Instagram:'); ?> </label>
              <input type="text" class="form-control" name="value[<?= $language->id; ?>][instagram]" id="inputInstagram<?= $key; ?>" placeholder="Instagram" value="<?= (isset($item->languages[$language->id])) ? $item->languages[$language->id]->instagram : ''; ?>">
            </div>
            <div class="col-xs-12 col-sm-6 form-group">
              <label for="inputTiktok<?= $key; ?>" class="control-label"><?= T_('Tiktok:'); ?> </label>
              <input type="text" class="form-control" name="value[<?= $language->id; ?>][tiktok]" id="inputTiktok<?= $key; ?>" placeholder="Tiktok" value="<?= (isset($item->languages[$language->id])) ? $item->languages[$language->id]->tiktok : ''; ?>">
            </div>
            <div class="col-xs-12 col-sm-6 form-group">
              <label for="inputFacebook<?= $key; ?>" class="control-label"><?= T_('Facebook:'); ?> </label>
              <input type="text" class="form-control" name="value[<?= $language->id; ?>][facebook]" id="inputFacebook<?= $key; ?>" placeholder="Facebook" value="<?= (isset($item->languages[$language->id])) ? $item->languages[$language->id]->facebook : ''; ?>">
            </div>
            <div class="col-xs-12 col-sm-6 form-group">
              <label for="inputLinkedIn<?= $key; ?>" class="control-label"><?= T_('LinkedIn:'); ?> </label>
              <input type="text" class="form-control" name="value[<?= $language->id; ?>][linkedin]" id="inputLinkedIn<?= $key; ?>" placeholder="LinkedIn" value="<?= (isset($item->languages[$language->id])) ? $item->languages[$language->id]->linkedin : ''; ?>">
            </div>
          </div>
        <?php } ?>
      </div>
    </div>

    <!-- Quarta Tab (SEO) -->
    <div class="tab-pane fade" id="tab4">
      <?= $this->load->view('comum/nav-lang', array('tabname' => 'seotablang', 'languages' => $languages_by_company)); ?>
      <div class="tab-content">
        <!-- Body da tab linguagem -->
        <?php foreach ($languages_by_company as $key => $language) { ?>
          <div class="tab-pane<?= ($key == 0) ? ' active in ' : ''; ?> fade" id="seotablang<?= $key; ?>">
            <div class="col-sm-12 form-group">
              <label for="inputWebmaster<?= $key; ?>" class="control-label">Meta Webmaster: </label>
              <input type="text" class="form-control" name="value[<?= $language->id; ?>][meta_webmaster]" id="inputWebmaster<?= $key; ?>" placeholder="Meta Webmaster" value="<?= (isset($item->languages[$language->id])) ? $item->languages[$language->id]->meta_webmaster : ''; ?>">
            </div>
            <div class="col-sm-12 form-group">
              <label for="inputTitle<?= $key; ?>" class="control-label">Meta Title: </label>
              <input type="text" class="form-control" name="value[<?= $language->id; ?>][meta_title]" id="inputTitle<?= $key; ?>" placeholder="Meta Title" value="<?= (isset($item->languages[$language->id])) ? $item->languages[$language->id]->meta_title : ''; ?>">
            </div>
            <div class="col-sm-12 form-group">
              <label for="inputDescription<?= $key; ?>" class="control-label">Meta Description: </label>
              <input type="text" class="form-control" name="value[<?= $language->id; ?>][meta_description]" id="inputDescription<?= $key; ?>" placeholder="Meta Description" value="<?= (isset($item->languages[$language->id])) ? $item->languages[$language->id]->meta_description : ''; ?>">
            </div>
            <div class="col-sm-12 form-group">
              <label for="inputKeywords<?= $key; ?>" class="control-label">Meta Keywords: </label>
              <input type="text" class="form-control" name="value[<?= $language->id; ?>][meta_keywords]" id="inputKeywords<?= $key; ?>" placeholder="Meta Keywords" value="<?= (isset($item->languages[$language->id])) ? $item->languages[$language->id]->meta_keywords : ''; ?>">
            </div>
          </div>
        <?php } ?>
      </div>
    </div>

    <?php if ($this->config->item('multi_company')) { ?>
      <!-- Quinta Tab (Site) -->
      <div class="tab-pane fade" id="tab5">

        <div class="col-sm-12 col-sm-6 form-group">
          <label for="inputSlug" class="control-label"><?= T_('Slug:'); ?> </label>
          <input type="text" class="form-control" name="slug" id="inputSlug" placeholder="<?= T_('Slug'); ?>" value="<?= isset($item) ? $item->slug : ''; ?>">
        </div>

        <div class="col-xs-12 col-sm-6 form-group">
          <label for="inputDomain" class="control-label"><?= T_('Domínio:'); ?> </label>
          <input type="text" class="form-control" name="domain" id="inputDomain" placeholder="<?= T_('URL'); ?>" value="<?= isset($item) ? $item->domain : ''; ?>">
        </div>
        <?php if ($this->config->item('multi_company_colors')) { ?>
          <div class="col-sm-12 col-sm-6 form-group">
            <label for="inputColor1" class="control-label"><?= T_('Cor Principal:'); ?> </label>
            <div class="input-group input-append color colorpicker2" data-color="<?= isset($item->colors) ? $item->colors->primary : $default_colors['primary']; ?>">
              <span class="input-group-addon add-on"><i></i></span>
              <input id="inputColor1" type="text" class="form-control" placeholder="<?= T_('Cor Principal'); ?>" name="colors[primary]" value="<?= isset($item->colors) ? $item->colors->primary : $default_colors['primary']; ?>">
            </div>
          </div>

          <div class="col-sm-12 col-sm-6 form-group">
            <div class="row">
              <div class="col-sm-6">
                <label for="inputColor2" class="control-label"><?= T_('Cor Degradê de:'); ?> </label>
                <div class="input-group input-append color colorpicker2" data-color="<?= isset($item->colors) ? $item->colors->gradient->from : $default_colors['gradient_from']; ?>">
                  <span class="input-group-addon add-on"><i></i></span>
                  <input id="inputColor2" type="text" class="form-control" placeholder="<?= T_('Cor Degradê de'); ?>" name="colors[gradient][from]" value="<?= isset($item->colors) ? $item->colors->gradient->from : $default_colors['gradient_from']; ?>">
                </div>
              </div>
              <div class="col-sm-6">
                <label for="inputColor3" class="control-label"><?= T_('Cor Degradê para:'); ?> </label>
                <div class="input-group input-append color colorpicker2" data-color="<?= isset($item->colors) ? $item->colors->gradient->to : $default_colors['gradient_to']; ?>">
                  <span class="input-group-addon add-on"><i></i></span>
                  <input id="inputColor3" type="text" class="form-control" placeholder="<?= T_('Cor Degradê para'); ?>" name="colors[gradient][to]" value="<?= isset($item->colors) ? $item->colors->gradient->to : $default_colors['gradient_to']; ?>">
                </div>
              </div>
            </div>
          </div>
        <?php
        }

        $this->load->view('gallery/single-file', array(
          'label' => 'Favicon',
          'module' => 'empresas',
          'file' => (isset($item->favicon) && $item->favicon) ? $item->favicon : null,
          'dimensions' => array(
            'w' => 16,
            'h' => 16
          ),
          'key' => 1,
          'id' => 'fileuploadImage-favicon',
          'name' => 'favicon',
          'upload' => site_url('gallery/upload/image')
        ));

        $this->load->view('gallery/single-file', array(
          'label' => 'Logo',
          'module' => 'empresas',
          'file' => (isset($item->image) && $item->image) ? $item->image : null,
          'dimensions' => array(
            'w' => '197',
            'h' => '36'
          ),
          'id' => 'fileuploadImage-primary',
          'key' => 1,
          'name' => 'image',
          'upload' => site_url('gallery/upload/image')
        ));

        $this->load->view('gallery/single-file', array(
          'label' => 'Imagem de Fundo',
          'module' => 'empresas',
          'file' => (isset($item->background_image) && $item->background_image) ? $item->background_image : null,
          'dimensions' => array(
            'w' => '1920',
            'h' => '1080'
          ),
          'id' => 'fileuploadImage-bg',
          'key' => 1,
          'name' => 'background_image',
          'upload' => site_url('gallery/upload/image')
        ));
        ?>
      </div>
    <?php } ?>

    <?php if (isset($item) && $item->id == 2) { ?>
      <!-- Sexta Tab (Informações) -->
      <div class="tab-pane fade" id="tab6">

        <?php for ($i = 0; $i < 2; $i++) { ?>
          <div class="col-md-12">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h5><?= $i == 0 ? T_('Peças de Reposição') : T_('Assistência Técnica'); ?></h5>
              </div>
              <div class="panel-body">
                <div class="panel panel-default address-panel">
                  <div class="panel-heading">
                    <h5><?= T_('Endereço'); ?></h5>
                  </div>
                  <div class="panel-body">
                    <div class="row">
                      <div class="col-xs-4 form-group">
                        <input type="text" class="form-control inputmask-cep" name="info[<?= $i ?>][zipcode]" value="<?= isset($item->infos[$i]) ? $item->infos[$i]->zipcode : ''; ?>" placeholder="<?= T_('CEP'); ?>">
                      </div>
                      <div class="col-xs-4 form-group">
                        <input type="text" class="form-control" name="info[<?= $i ?>][state]" value="<?= isset($item->infos[$i]) ? $item->infos[$i]->state : ''; ?>" placeholder="<?= T_('Estado/Província'); ?>">
                      </div>
                      <div class="col-xs-4 form-group">
                        <input type="text" class="form-control" name="info[<?= $i ?>][city]" value="<?= isset($item->infos[$i]) ? $item->infos[$i]->city : ''; ?>" placeholder="<?= T_('Cidade'); ?>">
                      </div>
                      <div class="col-xs-4 form-group">
                        <input type="text" class="form-control" name="info[<?= $i ?>][suburb]" value="<?= isset($item->infos[$i]) ? $item->infos[$i]->suburb : ''; ?>" placeholder="<?= T_('Bairro'); ?>">
                      </div>
                      <div class="col-xs-4 form-group">
                        <input type="text" class="form-control" name="info[<?= $i ?>][street]" value="<?= isset($item->infos[$i]) ? $item->infos[$i]->street : ''; ?>" placeholder="<?= T_('Rua'); ?>">
                      </div>
                      <div class="col-xs-4 form-group">
                        <input type="text" class="form-control inputmask-decimal" name="info[<?= $i ?>][number]" value="<?= isset($item->infos[$i]) ? $item->infos[$i]->number : ''; ?>" placeholder="<?= T_('Número'); ?>">
                      </div>
                      <div class="col-xs-12">
                        <input type="text" class="form-control" name="info[<?= $i ?>][additional_info]" value="<?= isset($item->infos[$i]) ? $item->infos[$i]->additional_info : ''; ?>" placeholder="<?= T_('Complemento'); ?>">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h5><?= T_('Dados'); ?></h5>
                  </div>
                  <div class="panel-body">
                    <div class="row">
                      <div class="col-sm-12 col-sm-6">
                        <input type="text" class="form-control" name="info[<?= $i ?>][email]" placeholder="<?= T_('Email'); ?>" value="<?= isset($item->infos[$i]) ? $item->infos[$i]->email : ''; ?>">
                      </div>
                      <div class="col-xs-12 col-sm-6">
                        <input type="text" class="form-control inputmask-phone" name="info[<?= $i ?>][phone]" placeholder="<?= T_('Telefone'); ?>" value="<?= isset($item->infos[$i]) ? $item->infos[$i]->phone : ''; ?>">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>

      </div>
    <?php } ?>
  </div>

  <div class="clearfix">
  </div>

  <div class="separator col-sm-12 text-center">
    <a href="<?= site_url($current_module->slug); ?>" class="btn btn-default"><?= T_('Cancelar'); ?></a>
    <button type="submit" class="btn btn-primary"><?= isset($item) ? T_('Salvar') : T_('Cadastrar'); ?></button>
  </div>
</form>