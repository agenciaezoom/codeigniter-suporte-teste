<?php if (isset($breadcrumb_route) && $breadcrumb_route != false) {?>
<div id="breadcrumb" class="common-limiter">
    <ol itemscope itemtype="http://schema.org/BreadcrumbList" class="breadcrumb">
        <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo site_url();?>">
                <span itemprop="name"><?php echo T_('Home'); ?></span>
            </a>
            <meta itemprop="position" content="1" />
        </li> >
        <?php
        $breadCount = 1;
        $breadTotal = count($breadcrumb_route);
        foreach ($breadcrumb_route as $key => $value) {
            $breadCount++;
            ?>
            <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?php echo site_url($key ? $key : $this->uri->uri_string()); ?>">
                    <span itemprop="name"><?php echo $value; ?></span>
                </a>
                <meta itemprop="position" content="<?php echo $breadCount; ?>" />
            </li>
            <?php
            if ($breadCount <= $breadTotal){
                // echo '>';
            }
        }
        ?>
    </ol>
</div>
<?php } ?>