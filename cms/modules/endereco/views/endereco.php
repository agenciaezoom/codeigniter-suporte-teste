<div class="address-wrapper">
  <!-- <?php if (isset($alert) && $alert || !isset($alert)) { ?>
    <div class="alert alert-info">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <div class="text"><?php echo isset($alert) && is_string($alert) ? $alert : '<p>Digite o CEP ao lado para buscar automaticamente o endereço, ou preencha manualmente e clique em "Buscar".</p><p>Você também pode arrastar o marcador do google para o local exato onde quer marcar a localização.</p>'; ?></div>
    </div>
  <?php } ?> -->
  <div id="address-sidebar">
    <input type="hidden" name="location[lat]" id="inputLat" value="<?php echo isset($data->lat) ? $data->lat : ''; ?>">
    <input type="hidden" name="location[lng]" id="inputLng" value="<?php echo isset($data->lng) ? $data->lng : ''; ?>">
    <div class="form-group">
      <select name="location[id_country]" id="inputCountry" class="form-control select2 inputCountry">
        <option value=""><?php echo T_('Selecione o país'); ?></option>
        <?php foreach ($countries as $country) { ?>
          <option value="<?php echo $country->id; ?>" data-code="<?php echo $country->code; ?>" <?php echo (isset($data->id_country) && $country->id == $data->id_country) ? ' selected="selected"' : ''; ?>><?php echo $country->name; ?></option>
        <?php } ?>
      </select>
    </div>
    <div class="form-group">
      <input type="text" class="form-control" name="location[zip_code]" id="inputCEP" value="<?php echo isset($data->zip_code) ? $data->zip_code : ''; ?>" placeholder="<?php echo T_('CEP'); ?>">
    </div>
    <div class="form-group">
      <input type="text" class="form-control" name="location[state]" id="inputState" value="<?php echo isset($data->state) ? $data->state : ''; ?>" placeholder="<?php echo T_('Estado/Província'); ?>">
    </div>
    <div class="form-group">
      <input type="text" class="form-control" name="location[city]" id="inputCity" value="<?php echo isset($data->city) ? $data->city : ''; ?>" placeholder="<?php echo T_('Cidade'); ?>">
    </div>
    <div class="form-group">
      <input type="text" class="form-control" name="location[suburb]" id="inputSuburb" value="<?php echo isset($data->suburb) ? $data->suburb : ''; ?>" placeholder="<?php echo T_('Bairro'); ?>">
    </div>
    <div class="form-group">
      <input type="text" class="form-control" name="location[street]" id="inputStreet" value="<?php echo isset($data->street) ? $data->street : ''; ?>" placeholder="<?php echo T_('Rua'); ?>">
    </div>
    <div class="form-group">
      <input type="text" class="form-control inputmask-decimal" name="location[number]" value="<?php echo isset($data->number) ? $data->number : ''; ?>" id="inputNumber" placeholder="<?php echo T_('Número'); ?>">
    </div>
    <div class="form-group">
      <input type="text" class="form-control" name="location[additional_info]" value="<?php echo isset($data->additional_info) ? $data->additional_info : ''; ?>" id="inputAdditionaInfo" placeholder="<?php echo T_('Complemento'); ?>">
    </div>
    <!-- <button class="btn btn-block btn-primary find-place" type="button"><?php echo T_('Buscar'); ?> <i class="fa fa-fw fa-search"></i></button> -->
  </div>
  <!-- <div id="address-map"></div> -->
</div>
<?php
$a_states = array();
foreach ($states as $v) {
  $a_states[$v->uf] = $v->name;
}
?>
<script type="text/javascript">
  var states = <?php echo json_encode($a_states); ?>;
</script>