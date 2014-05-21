<div class="mws-panel grid_8" style="box-shadow:none;">
    <div class="mws-panel-header">
        <span>Edit Product</span>
    </div>
    <div class="mws-panel-body no-padding">

        <?php if ( !$revisions->isEmpty() ) { ?>
            <?php $revision = $revisions->first() ?>
		<div class="alert alert-warning">
			<p>Warning: You have a draft of this product !<br> <a href="<?php echo URL::to("products/approve/detail/{$product->id}/{$revision->id}") ?>">View Detail</a></p>
		</div>
        <?php } ?>
		<?php echo HTML::message() ?>

        <form method="post" action="" class="mws-form" enctype="multipart/form-data">
            <div class="mws-form-inline">
                <div class="mws-form-row">
                    <label class="mws-form-label" for="name"><strong>Product Title</strong></label>
                    <div class="mws-form-item">
                        <input type="text" class="small" name="title" id="title" value="<?php echo Input::old('title', $formData['title']) ?>">
                        <?php echo Form::transText($product, 'title', array('class' => 'small')) ?>
                    </div>
                </div>
                <div class="clear"><br></div>

				<div class="mws-form-row">
					<label class="mws-form-label" for="name">Brands Name</label>
					<div class="mws-form-item">
	                        <?php echo Form::comboBox('brand_id', Brand::orderBy('name')->lists('name','id'), Input::old('brand_id', $product->brand_id) ); ?>
					</div>
				</div>

                <div class="mws-form-row">
                    <label class="mws-form-label" for="name">Active</label>
                    <div class="mws-form-item">
                        <?php echo Form::select('active', array('0'=>'Disable','1'=>'Active'), $product->active); ?>
                    </div>
                </div>

            </div>
            <div class="mws-button-row">
                <input type="submit" class="btn btn-primary" value="Save">
            </div>
        </form>
    </div>
    <form method="post" action="<?php echo URL::to('/products/remove/'.$product->id) ?>" id="move-2-trash">
        <div class="pull-right">
            <i class="icon-trash"></i>
            <a href="#remove" onclick="confirm('Are you sure to remove &quot;<?php echo $product->title ?>&quot; and its <?php echo count($product->variants),' ',str_plural('variant') ?>?\nThis action will effect all applications.') && $('#move-2-trash').submit()">Move to trash</a>
        </div>
    </form>
</div>

<?php if (isset($product)) foreach ($apps as $app) : ?>
<?php echo Theme::widget('meta', array('app' => $app, 'content' => $product))->render(); ?>
<?php endforeach; ?>