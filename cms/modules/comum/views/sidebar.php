<div id="menu-left" class="hidden-print hidden-xs">
  <form id="sidebar-search" autocomplete="off">
    <input type="search" name="search" placeholder="<?php echo T_('Buscar módulo'); ?>" autocomplete="off">
    <i class="fa fas fa-search"></i>
  </form>
  <ul id="menu-list">
    <?php echo $sidebar_menu; ?>
    <div id="menu-no-modules" class="hidden"><?php echo T_('Nenhum módulo encontrado'); ?></div>
  </ul>
</div>