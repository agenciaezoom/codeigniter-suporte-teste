<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo $title; ?></h4>
        </div>
        <div class="modal-body">
            <code>
                <?php echo $queries; ?>
            </code>
            <div class="row">
                <textarea class="sr-only" name="queries" id="queries"><?php echo str_replace("<br>", "\n", trim($queries)); ?></textarea>
                <button class="btn btn-primary copy-btn" id="copy-btn"><span>Copiar</span></button>

                <!-- <div id="response-message"><p></p></div> -->
            </div>
            <!-- <div class="row">
            </div> -->
        </div>
        <div class="modal-footer">
        </div>
    </div>
</div>