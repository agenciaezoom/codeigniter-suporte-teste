<?php

    $slugArray = explode('/', $module->slug);
    $mark = 0;

    for ($i = 0; $i < count($slugArray); $i++){

        if ($this->uri->segment($i + 1) && $this->uri->segment($i + 1) == $slugArray[$i])
            $mark++;

    }

?>
<li<?php echo ($mark == count($slugArray)) ? ' class="active"' : ''; ?>>
    <?php $validhttp = !preg_match("~^(?:f|ht)tps?://~i", $module->slug); ?>
    <a target="<?php echo $validhttp ? '' : '_blank' ?>" href="<?php echo $validhttp ? site_url($module->slug) : $module->slug; ?>">
        <i class="fa <?php echo $module->icon; ?>"></i>
        <span><?php echo $module->name ;?></span>
    </a>
</li>