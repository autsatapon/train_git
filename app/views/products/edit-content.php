<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><?php echo Theme::place('title') ?></span>
    </div>
    <div class="mws-panel-body no-padding">

        <?php if ($errors->count() > 0) { ?>
            <div class="alert alert-error">
                <?php foreach ($errors->all() as $error) { ?>
                    <p><?php echo $error ?></p>
                <?php } ?>
            </div>
        <?php } elseif (Session::has('successMsg')) { ?>
            <div class="alert alert-success">
                <p><?php echo Session::get('successMsg') ?></p>
            </div>
        <?php } ?>

        <form method="post" action="" class="mws-form">
            <div class="mws-form-inline">

                <div class="mws-form-row">
                    <label class="mws-form-label" for="name">Product Name</label>
                    <div class="mws-form-item">
                        <p style="font-weight: bold; font-size: 18px; line-height: 24px;"><?php echo $product->title ?></p>
                    </div>
                </div>

                <div class="mws-form-row">
                    <label class="mws-form-label" for="url">Key Feature</label>
                    <div class="mws-form-item">
                        <?php echo Form::ckeditor('key_feature', $product->key_feature, array('model' => $product) ) ?>
                    </div>
                </div>

                <div class="mws-form-row">
                    <label class="mws-form-label" for="description">Description</label>
                    <div class="mws-form-item">
                        <?php echo Form::ckeditor('description', $product->description, array('model' => $product) ) ?>
                    </div>
                </div>

            </div>

            <div class="mws-button-row">
                <input type="submit" class="btn btn-primary" value="Save">
            </div>
        </form>

    </div>
</div>
