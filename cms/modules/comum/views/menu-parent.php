<li class="hasSubmenu<?php echo $module->id == 200 ? ' collapse-in' : ''; ?>">
    <div class="subMenuFather">
        <i class="fa <?php echo $module->icon; ?>"></i>
        <span><?php echo $module->name ;?></span>
    </div>
    <ul<?php echo $module->id == 200 ? ' style="display:block;"' : ''; ?>>
        <?php echo $children; ?>
    </ul>
</li>