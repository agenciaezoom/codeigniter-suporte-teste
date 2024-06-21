<?php foreach ($associations as $key => $association){ ?>
    <option <?php echo (!$association->active_site || !$association->permission_asc) ? ' disabled ' : ''; ?>
        value="<?php echo $association->id; ?>"
        <?php
        echo isset($selected) && $association->id == $selected->id_company ? 'selected="selected"' : '';
        echo isset($selected_by_key) && in_array($association->id, array_keys($selected_by_key) ) ? 'selected="selected"' : '';
        echo isset($import) && $association->id == $import ? ' selected ': '';
        echo !isset($import) && !isset($selected_by_key) && !isset($selected) && $association->id == $company->id ? ' selected ': '';
        ?>
        data-language="<?php echo isset($association->language_main) ? $association->language_main : ''; ?>"
    >
        <?php echo  $indent.$association->title; ?>
    </option>
<?php
    if( isset($association->children) && !empty($association->children) )
        echo $this->load->view('administracao/associacoes/list_association', array('associations' => $association->children, 'indent' => $indent.'-- ', 'selected' => (isset($selected) ? $selected : null) ) );
} ?>