<?php
    //PARAMETROS
    // array(
    //     'params' => array(
    //         'ajax'        => Busca Títulos das colunas na url informada,
    //         'description' => Permite colocar descrição em cada coluna,
    //         'multilang'   => Habilita o Multiidioma,
    //         'values'      => Valores atuais do registro
    //         'name'        => Nome do Name dos inputs, default 'table'
    //     )
    // )

    $options = array(
        'ajax'        => FALSE, //(Ainda não implementado)
        'description' => FALSE, //(Ainda não implementado)
        'multilang'   => FALSE,
        'values'      => FALSE,
        'name'        => 'table'
    );

    $params = array_merge($options, isset($params) ? $params : []);
?>

<div data-table="<?php echo $params['name']; ?>">
    <div class="form-group col-sm-12">
        <div class="btn-group">
            <button type="button" class="btn btn-primary add-column" title="<?php echo T_('Adicionar Coluna'); ?>">
                <span class="fas fa-arrows-alt-v add-line"></span>&nbsp; <?php echo T_('Adicionar Coluna'); ?>
            </button>
            <button type="button" class="btn btn-primary add-line" title="<?php echo T_('Adicionar Linha'); ?>">
                <span class="fas fa-arrows-alt-h"></span>&nbsp; <?php echo T_('Adicionar Linha'); ?>
            </button>
            <?php if ($params['multilang']) { ?>
                <div class="btn-group multilang-drop">
                    <button type="button" class="btn btn-default dropdown-toggle multilang-drop-view" data-toggle="dropdown" title="<?php echo T_('Selecione a Linguagem'); ?>">
                        <i class="lang-flag">
                            <img width="18px" src="<?php echo base_img('br.png');?>" alt="<?php echo T_('Português (BR)'); ?>">
                        </i>
                        &nbsp;<?php echo T_('Português (BR)'); ?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <?php foreach ($languages as $key => $lang_each) { ?>
                            <li>
                                <a href="javascript:" data-lang="<?php echo $lang_each->id; ?>">
                                    <i class="lang-flag">
                                        <img width="18px" src="<?php echo base_img($lang_each->image);?>" alt="<?php echo $lang_each->name; ?>">
                                    </i>
                                    &nbsp; <?php echo $lang_each->name; ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>
            <button type="button" class="btn btn-default clear-table" title="<?php echo T_('Limpar Tabela'); ?>">
                <span class="far fa-file"></span>&nbsp;
            </button>
        </div>
    </div>

    <div class="col-md-11">
        <?php foreach ($languages as $key => $lang_each) { ?>
            <table class="table table-bordered" data-lang="<?php echo $lang_each->id; ?>" style="display: <?php echo $lang_each->id == 1 ? 'table' : 'none'; ?>">
                <thead>
                    <?php if ($params['values'] && isset($params['values']['langs'][$lang_each->id])) { ?>
                        <tr>
                            <?php foreach ($params['values']['langs'][$lang_each->id]['columns'] as $column_id => $column) {
                                $name = 'table[langs]['. $lang_each->id .'][columns]['. $column_id .']';
                            ?>
                                <th data-column="<?php echo $column_id; ?>">
                                    <input type="text" name="<?php echo $name . '[title]'; ?>" value="<?php echo $column['title']; ?>">
                                    <input type="hidden" name="<?php echo $name . '[order_by]'; ?>" value="<?php echo $column['order_by']; ?>">
                                    <span></span>
                                </th>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </thead>
                <tbody>
                    <?php if ($params['values'] && isset($params['values']['langs'][$lang_each->id])) { ?>
                        <?php foreach ($params['values']['langs'][$lang_each->id]['rows'] as $row_id => $fields) { ?>
                            <tr data-line="<?php echo $row_id; ?>">
                                <?php foreach ($fields as $field_key => $field) {
                                    $name = 'table[langs]['. $lang_each->id .'][columns]['. $field->id_column .'][rows]['. $field->row .']';
                                ?>
                                    <td>
                                        <input type="text" name="<?php echo $name . '[title]'; ?>" value="<?php echo $field->value; ?>">
                                        <input type="hidden" name="<?php echo $name . '[order_by]'; ?>" value="<?php echo $field->order_by; ?>">
                                        <span></span>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>
    </div>
</div>