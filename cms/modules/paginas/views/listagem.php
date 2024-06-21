<?php $this->load->view('comum/busca', array('view_search' => 'filtros'));
if ($items) { ?>
  <div class="no-padleft table-list col-sm-12">
    <div class="separator"></div>
    <table class="table checkboxs">
      <thead class="bg-gray">
        <tr>
          <?php if (in_array('excluir', $session_permissions[$current_module->id])) { ?>
            <th data-field="id" class="text-center">
              <div class="checkbox checkbox-single">
                <label class="checkbox-custom">
                  <i class="far fa-fw fa-square"></i>
                  <input type="checkbox">
                </label>
              </div>
            </th>
          <?php } ?>
          <th>
            <a href="<?= site_url($this->uri->uri_string()) . '?order=area'; ?>">
              Área <i class="fa<?= $order_by && $order_by['column'] == 'area' ? ' fa-caret-' . ($order_by['order']) : ''; ?>"></i>
            </a>
          </th>
          <th>
            <a href="<?= site_url($this->uri->uri_string()) . '?order=subarea'; ?>">
              Subárea <i class="fa<?= $order_by && $order_by['column'] == 'subarea' ? ' fa-caret-' . ($order_by['order']) : ''; ?>"></i>
            </a>
          </th>
          <th class="title-column">
            <a href="<?= site_url($this->uri->uri_string()) . '?order=title'; ?>">
              Título <i class="fa<?= $order_by && $order_by['column'] == 'title' ? ' fa-caret-' . ($order_by['order']) : ''; ?>"></i>
            </a>
          </th>
          <th class="text-center action-column">Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach ($items as $key => $item) { ?>
          <tr data-id="<?= $item->id_common_content; ?>" id="item-<?= $item->id_common_content; ?>">
            <?php if (in_array('excluir', $session_permissions[$current_module->id])) { ?>
              <td class="text-center text-middle" width="70px">
                <div class="checkbox checkbox-single">
                  <label class="checkbox-custom">
                    <i class="far fa-fw fa-square"></i>
                    <input type="checkbox">
                  </label>
                </div>
              </td>
            <?php } ?>
            <td class="text-middle"><?= $item->area; ?></td>
            <td class="text-middle"><?= $item->subarea; ?></td>
            <td class="text-middle"><?= $item->title; ?></td>
            <td class="text-right nowrap">
              <?php if ($this->session->userdata('user_data')->id == 1) { ?>
                <a href="<?= site_url('paginas/permissoes/' . $item->id_common_content); ?>"><button class="btn btn-default">Permissões</button></a>
              <?php }
              if (in_array('editar', $session_permissions[$current_module->id])) { ?>
                <a href="<?= site_url('paginas/editar/' . $item->id_common_content); ?>">
                  <button class="btn <?= ($item->enable_edit == 'enabled') ? ' btn-default ' : 'btn-secondary'; ?>">
                    Editar
                  </button>
                </a>
              <?php }
              if (in_array('excluir', $session_permissions[$current_module->id])) { ?>
                <a href="<?= site_url('paginas/delete/' . $item->id_common_content); ?>" class="delete-button">
                  <button class="btn <?= ($item->enable_delete == 'enabled') ? ' btn-default ' : ' btn-secondary'; ?>">
                    Excluir
                  </button>
                </a>
              <?php } ?>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
<?php } else { ?>
  <div class="no-padleft col-sm-12 text-center">
    <div class="separator-horizontal col-sm-12"></div>
    Nenhum dado cadastrado.
  </div>
<?php } ?>
<div class="col-sm-3">
  <form class="hide delete-all-form" action="<?= site_url('paginas/delete-multiple'); ?>" method="POST">
    <input type="hidden" name="id" value="">
    <button class="btn btn-primary btn-stroke"> Excluir selecionados</button>
  </form>
</div>
<div class="col-sm-9 text-right pagination-wrapper">
  <?= $paginacao; ?>
</div>