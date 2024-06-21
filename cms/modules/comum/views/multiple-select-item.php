<tr data-id="<?php echo $each_item_selected->id; ?>" <?php echo $data_attr; ?>>
  <input type="hidden" name='<?php echo $name; ?>[<?php echo $each_item_selected->id; ?>][id]' value="<?php echo $each_item_selected->id; ?>">
  <!-- <td class="text-left"><i class="fas fa fa-arrows-alt-alt handle"></i></i></td> -->
  <td class="text-left"><?php echo $each_item_selected->title; ?></td>
  <?php foreach ($additional_columns as $key => $value) { ?>
    <td>
      <?php
      switch ($value['type']) {
        case 'text':
        case 'email':
        case 'password':
        case 'tel':
          echo '<input type="' . $value['type'] . '" name="' . $name . '[' . $each_item_selected->id . '][' . $value['name'] . ']" value="' . $each_item_selected->{$value['name']} . '" class="form-control">';
          break;
        case 'number':
          echo '<input type="' . $value['type'] . '" name="' . $name . '[' . $each_item_selected->id . '][' . $value['name'] . ']" min="1" value="' . $each_item_selected->{$value['name']} . '" class="form-control">';
          break;
        case 'static':
        default:
          echo '<span>' . $each_item_selected->{$value['name']} . '</span>';
          break;
      }
      ?>
    </td>
  <?php   } ?>
  <td class="text-center">
    <a class="delete-item" data-id="<?php echo $each_item_selected->id; ?>" data-title="<?php echo $each_item_selected->title; ?>">
      <button class="btn btn-default" type="button">
        <?php echo T_('Excluir'); ?>
      </button>
    </a>
  </td>
</tr>