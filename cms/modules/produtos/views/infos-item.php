<?php $hasLanguages = count($languages) > 1; ?>
<li class="col-xs-12 group-remove-infos" data-seq="<?php echo (isset($key)) ? $key : '{key}'; ?>">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h5>Informação</h5>
      <button type="button" class="btn btn-danger btn-sm remove-infos"><span><?= T_('Remover') ?></span></button>
    </div>

    <div class="panel-body">
      <div class="row">
        <div class="col-xs-12 col-md-6">
          <input class="form-control" placeholder="<?= T_('Campo') ?>" type="text" name="fields[<?php echo (isset($key)) ? $key : '{key}'; ?>][field]" value="<?php echo isset($info) ? $info->field : ''; ?>" required />
        </div>
        <div class="col-xs-12 col-md-6 no-padleft">
          <input class="form-control" placeholder="<?= T_('Valor') ?>" type="text" name="fields[<?php echo (isset($key)) ? $key : '{key}'; ?>][value]" value="<?php echo isset($info) ? $info->value : ''; ?>" />
        </div>
      </div>
    </div>
  </div>
</li>