<h4>Módulos</h4>
<?php foreach ($modules as $i => $parent){ ?>
    <div class="widget collapse-charac" data-toggle="collapse-widget" data-collapse-closed="true">
        <div class="widget-body">
            <?php echo $this->auth->build_permissions($parent, 12); ?>
        </div>
    </div>
<?php } ?>
