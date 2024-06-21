<?php

    //PARAMETROS QUE PODEM SER ENVIADOS
    /*
    'id' => 'multiple-select-table',
    'name' => 'itens_selected',
    'label' => T_('Itens'),
    'label_small' => T_('Selecione os itens desejados'),
    'placeholder_select' => T_('Selecione um item'),
    'placeholder_no_itens' => T_('Nenhum item selecionado'),
    'items' => $items // Array com os items do select (id e title),
    'items_selected' => $items_selected // Array com os items já selecionados (id e title)
    'additional_columns' => $additional // Array com colunas adicionais
    'disabled_items' // Itens do select que estarão com disabled
    'append_disabled_title' // Texto que será adicionado ao título do item desabilitado
    'column_label' // Label da coluna padrão ("Nome" por padrão)
    */

    if (!isset($items) || !is_array($items)) {
        return false;
    }
    $additional_columns = isset($additional_columns) ? $additional_columns : array();
    $attrs = array();
    foreach ($additional_columns as $value) {
        if ($value['type'] == 'static'){
            $attrs[] = $value['name'];
        }
    }
?>
<div id="<?php echo $id ?>">
    <div class="col-xs-12 form-group">
        <label class="control-label">
            <?php echo $label; ?>
            <small><?php echo ' - '.$label_small; ?></small>
        </label>
        <select name="<?php echo 'select_'.$name; ?>" class="form-control select2">
            <option value="" selected disabled><?php echo $placeholder_select; ?></option>
            <?php foreach ($items as $each_item) {
                    $data_attr = array();
                    foreach ($attrs as $value) {
                        $data_attr[] = 'data-'.$value.'="'.$each_item->{$value}.'"';
                    }
                    $data_attr = implode(' ', $data_attr);
                if(in_array($each_item->id, $disabled_items)){ ?>
                    <option value="<?php echo $each_item->id; ?>" <?php echo $data_attr; ?> disabled><?php echo $each_item->title.$append_disabled_title;?></option>
                <?php }else{ ?>
                    <option value="<?php echo $each_item->id; ?>" <?php echo $data_attr; ?>><?php echo $each_item->title;?></option>
                <?php }
            } ?>
        </select>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-12">
        <div class="panel panel-default">
            <table class="table checkboxs">
                <thead class="bg-gray">
                    <tr>
                        <!-- <th class="text-center action-column"></th> -->
                        <th><?php   echo $column_label ? $column_label : T_('Nome'); ?></th>
                        <?php   foreach($additional_columns as $key => $value) { ?>
                        <th style="width:<?php echo isset($value['width']) ? $value['width'] : 'auto'; ?>;"><?php   echo $key; ?></th>
                        <?php   } ?>
                        <th class="text-center action-column"><?php echo T_('Ações'); ?></th>
                    </tr>
                </thead>
                <tbody id="<?php echo $id ?>-table">
                    <tr class='no-selected' <?php echo (count($items_selected) > 0) ? 'style="display:none;"' : ''; ?>>
                        <td colspan='<?php echo 2 + count($additional_columns); ?>'><?php echo $placeholder_no_itens; ?></td>
                    </tr>
                    <?php
                        // Base:
                        $each_item_selected = (object) array(
                            'id' => '{id}',
                            'title' => '{title}'
                        );
                        foreach($additional_columns as $key => $value) {
                            $each_item_selected->{$value['name']} = isset($value['value']) ? $value['value'] : ($value['type'] == 'static' ? '{'.$value['name'].'}' : '');
                        }

                        $data_attr = array();
                        foreach ($attrs as $value) {
                            $data_attr[] = 'data-'.$value.'="{'.$value.'}"';
                        }
                        $data_attr = implode(' ', $data_attr);

                        $this->load->view('multiple-select-item', array(
                            'each_item_selected'    => $each_item_selected,
                            'name'                  => $name,
                            'data_attr'             => $data_attr,
                            'additional_columns'    => $additional_columns
                        ));
                        // Items
                        foreach ($items_selected as $each_item_selected) {
                            $data_attr = array();
                            foreach ($attrs as $value) {
                                $data_attr[] = 'data-'.$value.'="'.$each_item_selected->{$value}.'"';
                            }
                            $data_attr = implode(' ', $data_attr);

                            $this->load->view('multiple-select-item', array(
                                'each_item_selected'    => $each_item_selected,
                                'name'                  => $name,
                                'data_attr'             => $data_attr,
                                'additional_columns'    => $additional_columns
                            ));
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="clearfix"></div>
</div>