<?php if (isset($breadcrumb_route) && $breadcrumb_route != false){ ?>
<ul class="breadcrumb" class="hidden-print">
    <li><a href="<?php echo site_url()?>"><?php echo T_('Home'); ?></a></li>
    <?php
    foreach ($breadcrumb_route as $key => $value){
        if ($key){ ?>
        <li><a href="<?php echo site_url($key); ?>"><?php echo $value; ?></a></li>
        <?php } else { ?>
        <li><span><?php echo $value;?></span></li>
        <?php }
    } ?>
</ul>
<?php } ?>