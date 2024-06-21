<!-- <div class="col-xs-<?php /*echo ($depth >= 3) ? '12 col-lg-6' : $colSize;*/ ?> permission-box"> -->
<div class="col-xs-12 col-lg-12 permission-box">
    <div class="row">
        <div class="col-lg-6 nome-modulo"><?php echo $module->name; ?></div>
        <div class="col-lg-6">
            <?php

            if ($module->action){

                $actions = explode(',', $module->action);
                foreach ($actions as $key => $action){
                    if(!in_array(slug($action), $permissions))
                        // continue;
                    ?>
                        <div class="checkbox">
                            <label for="modulo-<?php echo $module->id . slug($action); ?>" class="checkbox-custom">
                                <?php

                                $walkIds = explode('[', $parentsName);
                                unset($walkIds[0]);
                                $searchIn = isset($existing_permissions) ? $existing_permissions : array();
                                foreach ($walkIds as $j => $walkId){
                                    $walkId = str_replace(']', '', $walkId);
                                    if (isset($searchIn[$walkId]))
                                        $searchIn = $searchIn[$walkId];
                                    else {
                                        $searchIn = false;
                                    }
                                }
                                $searchIn = ($searchIn && isset($searchIn[$module->id])) ? $searchIn[$module->id] : false;
                                $checked = '';
                                if (isset($existing_permissions) && $searchIn && in_array(slug($action), $searchIn))
                                    $checked = ' checked';

                                ?>
                                <input <?php echo $checked; ?> type="checkbox" name="permissions<?php echo $parentsName; ?>[<?php echo $module->id; ?>][]" value="<?php echo slug($action); ?>" id="modulo-<?php echo $module->id . slug($action); ?>">
                                <i class="far fa-square"></i> <?php echo $action; ?>
                            </label>
                        </div>
                <?php
                }

            }

            ?>
        </div>
    </div>
</div>
