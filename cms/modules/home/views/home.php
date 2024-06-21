<br />
<div class="col-xs-12 well">
    <p><?php echo T_('Olá!'); ?></p>
    <p><?php echo T_('Este sistema foi desenvolvido para que seja uma ferramenta útil e de fácil utilização, por ter uma interface simples e intuitiva.'); ?></p>
    <p><?php echo T_('Utilize o menu ao lado para acessar as sessões disponíveis. Ao clicar em algum item, será exibida a página de visualização de conteúdo respectivo à sessão selecionada. Nesta página, você poderá realizar a inclusão de novas informações, fazer a edição dos itens disponíveis e até mesmo excluir algo que você não queira mais.'); ?></p>
    <p><?php echo T_('Qualquer dúvida, sugestão ou problema que você possa ter, não deixe de enviar-nos uma mensagem através do email:'); ?> <a href="mailto:suporte@ezoom.com.br">suporte@ezoom.com.br</a>. <?php echo T_('Em breve estaremos respondendo ao seu contato.'); ?></p>
    <p class="bemVindo"><?php echo T_('Seja bem vindo!'); ?></p>
</div>
<?php foreach ($showPreview as $key => $itens) { ?>
    <div class="col-xs-6">
        <h2 class="col-xs-12 title-type"><?php echo $key; ?></h2>
        <div class="gallery well">
            <?php foreach ($itens as $k => $item) {
                ?>
                <div class="item">
                    <?php if( isset($item->image) ){ ?>
                    <div class="image"><img src="<?php echo site_url('image/resize_crop?w=150&h=244&ezfiles/'.$tables[$key]['pathImage'].$item->image); ?>" /></div>
                    <?php } ?>
                    <div class="content-home">
                        <div class="title"> <?php echo isset($item->title) ? $item->title : (isset($item->name) ? $item->name : ''); ?> </div>
                        <div class="text"> <?php echo isset($item->text) ? strip_tags($item->text) : (isset($item->description) ? $item->description : ''); ?> </div>
                        <a class="edit" href="<?php echo site_url( $tables[$key]['pathImage'].'editar/'.(isset($item->id) ? $item->id : '') ); ?>">Editar</a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
<?php } ?>