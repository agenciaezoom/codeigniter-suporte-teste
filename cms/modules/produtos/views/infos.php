<div class="info-gallery">
    <ul class="row custom-gutter add-infos">
        <?php
        if(isset($infos) && !empty($infos)){
            foreach ($infos as $key => $info) {
                $this->load->view('infos-item', array('key' => $key, 'info' => $info));
            }
        } ?>
    </ul>
    <button type="button" class="btn btn-secondary more-info"><i class="fa fa-plus-circle"></i> <?= T_('Adicionar Informação') ?></button>
    <div class="separator"></div>
</div>