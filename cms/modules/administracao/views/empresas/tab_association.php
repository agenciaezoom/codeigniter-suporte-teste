<div class="separator"></div>
<div class="col-xs-12 form-group">
  <label for="assoc"><?php echo T_('Selecione uma associação:'); ?> </label>
  <select id="assoc" name="companies[]" multiple="multiple" style="width:100%" class="populate placeholder select2" tabindex="-1" data-url="<?php echo $newUrl; ?>">
    <option></option>
    <?php echo $this->load->view('administracao/associacoes/list_association', array('associations' => $associations, 'indent' => '', 'selected_by_key' => $rel_assoc, 'selected' => (isset($item) ? $item : null))); ?>
  </select>
</div>
<div class="col-xs-12">
  <table class="table" id="assoc-itens">
    <thead class="bg-gray">
      <tr>
        <th><?php echo T_('Associação'); ?></th>
        <th width="300"><?php echo T_('Categoria'); ?></th>
        <th class="text-right"><?php echo T_('Ação'); ?> </th>
      </tr>
    </thead>
    <tbody>
      <?php
      if (isset($rel_assoc)) {
        foreach ($rel_assoc as $key => $value) { ?>
          <tr data-id="<?php echo $key; ?>">
            <td><?php echo $value->name; ?></td>
            <td>
              <select name="custom[cat_comp][<?php echo $key; ?>]" id="inputEditorial" class="form-control select2" data-placeholder="<?php echo T_('Selecione a Categoria'); ?>">
                <option></option>
                <?php if (!empty($value->category_by_company)) {
                  foreach ($value->category_by_company as $key => $cat) { ?>
                    <option <?php echo (isset($value->id_category) && $value->id_category == $cat->id) ? 'selected' : ''; ?> value="<?php echo $cat->id; ?>"><?php echo $cat->title; ?></option>
                <?php
                  }
                } ?>
              </select>
              <input type="hidden" name="custom[langs][<?php echo $value->language_main; ?>]" />
            </td>
            <td class="text-right">
              <button class="btn btn-default remove-assoc"> <?php echo T_('Excluir'); ?></button>
            </td>
          </tr>
      <?php
        }
      }
      ?>
    </tbody>
  </table>
</div>