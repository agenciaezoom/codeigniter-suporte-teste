<?php
if (count($languages) < 2)
  return '';
if (!isset($tabname))
  $tabname = 'tablang';
?>

<ul class="nav nav-pills">
  <!-- Header da tab linguagem -->
  <?php foreach ($languages as $key => $language) { ?>
    <li<?php echo ($key == 0) ? ' class="active"' : ''; ?>>
      <a id="<?php echo $tabname; ?>" href="#<?php echo $tabname . $key; ?>" data-toggle="tab">
        <i class="lang-flag">
          <img src="<?php echo base_img($language->image); ?>" alt="<?php echo $language->name; ?>">
        </i>
        <?php echo $language->name; ?>
      </a>
      </li>
    <?php } ?>
</ul>