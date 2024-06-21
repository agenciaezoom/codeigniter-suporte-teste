<?php if (count($parent->children) > 0) { ?>
    <div class="widget-head">
        <label class="checkbox-custom" for="all-<?php echo $module->id; ?>">
            <i class="far fa-fw fa-square"></i>
            <input type="checkbox" id="all-<?php echo $module->id; ?>">
            <?php echo $parent->name; ?>
        </label>
    </div>
    <div class="widget-body">
        <div class="row" style="background-color: rgba(0, 0, 0, .15));">
            <?php echo $modules; ?>
        </div>
    </div>
<?php } else echo $modules; ?>