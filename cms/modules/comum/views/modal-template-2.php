<div class="col-xs-12 col-sm-12">
    <div class="col-xs-12 col-sm-12 block-group title-modal">
        <i class="hidden-xs fa-eye fa-3x fa-4x fa fa-fw"></i>
        <h2><?php echo $title; ?></h2>
        <p><?php echo isset($subtitle) ? $subtitle : ''; ?></p>
    </div>
</div>

<div class="col-md-4 col-lg-3">
    <div class="col-xs-12 col-sm-12 block-group">
        <div class="col-sm-12 form-group">
            <div class="separator col-sm-12"></div>
            <label class="col-sm-12">
                <i class="fa fa-fw fa-calendar fa-2x"></i>
                <span><?php echo T_('Data Publicação:'); ?></span> <br />
                <span><?php echo $day.' '.$month. ' de '. $year; ?></span>
            </label>
        </div>
    </div>

    <div class="separator"></div>

    <div class="col-xs-12 col-sm-12 block-group three-lines">
        <div class="col-sm-12 form-group">
            <div class="separator col-sm-12"></div>
            <label class="col-sm-12">
                <i class="fa fa-fw fa-user fa-2x"></i>
                <span><?php echo T_('Publicado Por:'); ?></span> <br />
                <span><?php echo $user; ?></span> <br />
                <span><?php echo isset($institution) ? $institution : ''; ?></span>
            </label>
        </div>
    </div>
</div>

<div class="col-md-8 col-lg-9">
    <div class="col-sm-12 block-group">
        <div class="clearfix"></div>
        <div class="separator col-sm-12"></div>
        <div class="col-sm-12">
            <?php   if ($text){ ?>
            <div class="innerB modal-text">
                <?php
                    echo '<i class="fa fa-quote-left fa fa-3x pull-left fa fa-muted"></i>';
                    echo '<i class="fa fa-quote-right fa fa-3x pull-right fa fa-muted"></i>';
                    echo $text;
                ?>
                <div class="clearfix"></div>
            </div>
        </div>
        <?php   } if ( isset($gallery) ){ ?>
        <div id="modal-gallery" class="owl-carousel">
            <?php foreach ($gallery as $value) { ?>
            <div class="item">
                <div class="box-generic padding-none margin-none overflow-hidden">
                    <div class="relativeWrap overflow-hidden" data-height="300px">
                        <a title="<?php echo htmlentities($value->subtitle); ?>" href="<?php echo site_url('../userfiles/gerenciador-de-conteudo/conteudo/'.$value->file); ?>" data-toggle="magnific" rel="magnific[gallery]">
                            <img src="<?php echo site_url('image/resize_crop?w=800&h=600&q=90&ezfiles/gerenciador-de-conteudo/conteudo/'.$value->file) ?>" class="img-responsive padding-none border-none" />
                            <?php if ($value->subtitle || isset($value->font)){ ?>
                            <div class="fixed-bottom bg-inverse-faded">
                                <div class="media margin-none innerAll">
                                    <div class="media-body text-white">
                                        <h4 class="text-white"><?php echo $value->subtitle; ?></h4>
                                        <p class="margin-none"><small><?php echo $value->font; ?></small></p>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </a>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
        <?php   } ?>
        <div class="separator col-sm-12"></div>
    </div>
</div>
