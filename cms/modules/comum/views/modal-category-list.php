<?php foreach ($child as $key => $item) {
    if(isset($item->title) && $item->title != ''  ){ ?>
    <option value="<?php echo $item->id; ?>"><?php echo $indent.' '.$item->title; ?> </option>
    <?php
    } if(!empty($item->child))
        echo $this->load->view('modal-category-list', array('child' => $item->child, 'indent' => $indent.'--'));
} ?>