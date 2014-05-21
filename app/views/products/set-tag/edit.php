<div class="mws-panel grid_8" style="box-shadow:none;">
    <div class="mws-panel-header">
        <span>Edit Product Tag</span>
    </div>
    <div class="mws-panel-body no-padding">
		<?php echo HTML::message() ?>
        <form method="post" action="" class="mws-form" enctype="multipart/form-data">
            <div class="mws-form-row">
                        <h3><?php echo $product->title ?></h3>
                        <div><?php echo Product::getLabel('brand') ?>: <?php echo $product->brand->name ?></div>
                    </div>
            <div class="mws-form">
                <div class="mws-form-row">
                    <label class="mws-form-label" for="name"><strong>Product Tag</strong></label>
                    <div class="mws-form-item">
                    <?php echo Form::tagBox('tag',Input::old('tag', $formData['tag']) ,'', $allTag) ?>
                    </div>
                </div>
                <div class="clear"><br></div>
            </div>
            <div class="mws-button-row">
                <input type="submit" class="btn btn-primary" value="Save">
            </div>
        </form>
    </div>
</div>

<?php

Theme::asset()->add('jquery', URL::to('js/jquery-1.10.2.min.js'));

