<div class="video-gallery">
    <ul class="row add-videos col-sm-12">
        <?php
        if(isset($videos) && !empty($videos)){
            foreach ($videos as $key => $video) {
                $this->load->view('video-item', array(
                    'key' => ($key+1),
                    'video' => $video,
                ));
            }
        } ?>
    </ul>
    <button type="button" class="btn btn-primary more-video"><i class="fa fa-plus-circle"></i> Adicionar Vídeo</button>
    <div class="clearfix"></div>
    <div class="separator"></div>
</div>