<div class="listing-header">
    <?php if (in_array('cadastrar', $session_permissions[$current_module->id]) ){ ?>
    <div class="no-padleft innerB col-xs-5 col-sm-4 col-md-2 col-lg-1">
        <a href="<?php echo site_url($current_module->slug.'/cadastrar'); ?>">
            <button class="btn btn-primary btn-cadastro"><i class="fa fa-plus"></i> <?php echo T_('Cadastrar'); ?></button>
        </a>
    </div>
    <?php } if (in_array('exportar', $session_permissions[$current_module->id]) ){ ?>
    <div class="no-padleft innerB col-xs-5 col-sm-4 col-md-2 col-lg-1">
        <a href="<?php echo site_url($current_module->slug.'/exportar'); ?>">
            <button class="btn btn-primary btn-cadastro"><i class="fa fa-file-excel"></i> <?php echo T_('Exportar'); ?></button>
        </a>
    </div>
    <?php   }  ?>
    <div class="clearfix"></div>
    <div class="clearfix"></div>
    <div class="panel panel-default">
        <div class="panel-heading"><?php echo T_('Buscar/Filtrar Resultados'); ?></div>
        <div class="panel-body">
            <form id="form_filter" action="<?php echo site_url($current_module->slug); ?>" class="filter" method="POST">
                <div class="innerB no-padleft no-padright col-xs-12 visible-xs-block" style="z-index:99; position: relative;">
                    <select id="filter-show" name="show" class="selectpicker visualizeItens"  data-style="btn-primary" data-width="100%">
                        <option title="<?php echo T_('Visualizar: 10'); ?>" value="10" <?php echo ($show == 10) ? 'selected="selected"' : ''; ?>>10</option>
                        <option title="<?php echo T_('Visualizar: 25'); ?>" value="25" <?php echo ($show == 25) ? 'selected="selected"' : ''; ?>>25</option>
                        <option title="<?php echo T_('Visualizar: 50'); ?>" value="50" <?php echo ($show == 50) ? 'selected="selected"' : ''; ?>>50</option>
                        <option title="<?php echo T_('Visualizar: 100'); ?>" value="100" <?php echo ($show == 100) ? 'selected="selected"' : ''; ?>>100</option>
                    </select>
                </div>
                <div class="innerB no-padleft no-padright col-xs-12<?php echo ($search) ? ' box-searched' : ''; ?>"<?php echo isset($view_search) ? ' style="z-index:99; position: relative;"' : ''; ?>>
                    <div class="input-group">
                        <div class="input-group-btn hidden-sm" style="z-index:99; position: relative;">
                            <select id="filter-show" name="show" class="selectpicker visualizeItens"  data-style="btn-primary" data-width="100%">
                                <option title="<?php echo T_('Visualizar: 10'); ?>" value="10" <?php echo ($show == 10) ? 'selected="selected"' : ''; ?>>10</option>
                                <option title="<?php echo T_('Visualizar: 25'); ?>" value="25" <?php echo ($show == 25) ? 'selected="selected"' : ''; ?>>25</option>
                                <option title="<?php echo T_('Visualizar: 50'); ?>" value="50" <?php echo ($show == 50) ? 'selected="selected"' : ''; ?>>50</option>
                                <option title="<?php echo T_('Visualizar: 100'); ?>" value="100" <?php echo ($show == 100) ? 'selected="selected"' : ''; ?>>100</option>
                            </select>
                        </div>
                        <input name="search[title]" type="text" class="form-control" placeholder="<?php echo T_('Buscar por...'); ?>" value="<?php echo (isset($search['title']) && $search['title']) ? $search['title'] : ''; ?>" id="filter-search">
                        <?php if (!isset($view_search)) { ?>
                        <div class="input-group-btn">
                            <button class="btn btn-default rounded-right" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                            <?php if ($search) { ?>
                            <a class="btn btn-danger" href="<?php echo site_url('comum/limpar-busca'); ?>">
                                <i class="fa fa-times"></i> <span><?php echo T_('Limpar Busca'); ?></span>
                            </a>
                            <?php } ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>


                <?php if (isset($view_search)) { ?>
                    <div class="clearfix"></div>
                    <?php $this->load->view($view_search); ?>
                    <div class="col-sm-12 text-right no-padright">
                        <?php if ($search) { ?>
                        <a class="btn btn-danger" href="<?php echo site_url('comum/limpar-busca'); ?>">
                            <i class="fa fa-times"></i> <span><?php echo T_('Limpar Busca'); ?></span>
                        </a>
                        <?php } ?>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search"></i> <span><?php echo T_('Pesquisar'); ?></span>
                        </button>
                    </div>
                <?php } ?>
            </form>
        </div>
        <div class="panel-footer"><?php echo $showing; ?></div>
    </div>
</div>