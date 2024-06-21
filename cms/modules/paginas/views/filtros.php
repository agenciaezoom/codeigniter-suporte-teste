<div class="row" style="margin-bottom: 15px">
    <?php if (isset($areas) && !empty($areas)) { ?>
        <div class="col-md-6">
            <label for="selectArea"><?php echo T_('Filtrar por Área'); ?></label>
            <select name="search[area]" id="selectArea" class="form-control select2" data-placeholder="<?php echo T_('Selecione a Área'); ?>">
                <?php foreach ($areas as $key => $area) { ?>
                    <option value=""></option>
                    <option value="<?php echo $area; ?>" <?php echo (isset($search['area']) && $search['area'] == $area) ? 'selected' : ''; ?>><?php echo ucfirst(mb_strtolower($area)); ?></option>
                <?php } ?>
            </select>
        </div>
    <?php } ?>
    <?php if (isset($subareas) && !empty($subareas)) { ?>
        <div class="col-md-6">
            <label for="selectSubarea"><?php echo T_('Filtrar por Subárea'); ?></label>
            <select name="search[subarea]" id="selectSubarea" class="form-control select2" data-placeholder="<?php echo T_('Selecione a Subárea'); ?>">
                <?php foreach ($subareas as $key => $subarea) { ?>
                    <option value=""></option>
                    <option value="<?php echo $subarea; ?>" <?php echo (isset($search['subarea']) && $search['subarea'] == $subarea) ? 'selected' : ''; ?>><?php echo ucfirst(mb_strtolower($subarea)); ?></option>
                <?php } ?>
            </select>
        </div>
    <?php } ?>
</div>