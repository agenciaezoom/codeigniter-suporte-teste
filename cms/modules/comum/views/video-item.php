<?php $hasLanguages = count($languages) > 1; ?>
<li class="col-xs-12 no-padding group-remove-videos" data-seq="<?php echo (isset($key)) ? $key : '{key}'; ?>">
    <div class="col-xs-12 no-padding">
        <div class="col-sm-12">
            <label class="control-label">Vídeo</label>
            <small>(https://www.youtube.com/watch?v=_P0NaG5q4Iw)</small>
        </div>
        <div class="col-xs-12 col-sm-12 <?php echo $hasLanguages ? 'cold-md-5' : 'col-md-6'; ?> form-group">
            <input class="form-control" placeholder="Link" type="text" name="video[<?php echo (isset($key)) ? $key : '{key}'; ?>][link]" value="<?php echo isset($video) ? $video->link : ''; ?>" required />
        </div>
        <div class="col-xs-12 col-sm-7 <?php echo $hasLanguages ? 'cold-md-4' : 'col-md-5'; ?> form-group">
            <input class="form-control" placeholder="Título" type="text" name="video[<?php echo (isset($key)) ? $key : '{key}'; ?>][title]" value="<?php echo isset($video) ? $video->title : ''; ?>" />
        </div>
        
        <?php if(isset($module) && $module == 'paginas'){ ?>
        <div class="col-xs-12 col-sm-5 col-md-2 col-lg-2 form-group">
            <input class="form-control order-input" placeholder="Ordem" type="number" name="video[<?php echo (isset($key)) ? $key : '{key}'; ?>][order_by]" value="<?php echo isset($video) ? $video->order_by : '{key}'; ?>" />
        </div>
        <?php } ?>
        
        <?php   if ($hasLanguages){ ?>
        <div class="col-xs-8 col-sm-4 col-md-2 form-group">
            <select name="video[<?php echo (isset($key)) ? $key : '{key}'; ?>][id_language]" class="form-control video-language" >
                <?php foreach($languages as $language){
                    $selected = isset($video) && $language->id == $video->id_language ? 'selected="selected"' : null;
                    ?>
                    <option data-image="<?php echo $language->image; ?>" value="<?php echo $language->id ?>" <?php echo $selected ?> ><?php echo strtoupper($language->code); ?></option>
                    <?php
                }
                ?>
            </select>
        </div>
        <?php   }else{ ?>
        <input type="hidden" name="video[<?php echo (isset($key)) ? $key : '{key}'; ?>][id_language]" value="<?php echo isset($video) ? $video->id_language : $languages[0]->id; ?>" >
        <?php   } ?>
        <div class="col-xs-4 col-sm-1 form-group col-remove">
            <button type="button" class="btn btn-danger remove-videos"><i class="fa fa-times"></i></button>
        </div>
    </div>
</li>