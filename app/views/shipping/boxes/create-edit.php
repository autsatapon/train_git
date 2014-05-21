<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span><?php echo Theme::place('title');?></span>
	</div>
	<div class="mws-panel-body no-padding">

		<?php if ($errors->count() > 0) { ?>
			<div class="alert alert-error">
				<?php foreach ($errors->all() as $error) { ?>
					<p><?php echo $error ?></p>
				<?php } ?>
			</div>
		<?php } ?>

		<form method="post" action="" class="mws-form">
			<div class="mws-form-inline">
				<div class="mws-form-row">
					<label class="mws-form-label" for="name"><?php echo ShippingBox::getLabel('name'); ?></label>
					<div class="mws-form-item">
						<input type="text" class="small" name="name" id="name" value="<?php echo Input::old('name', $formData['name']); ?>">
					</div>
				</div>
				<div class="mws-form-row">
					<label class="mws-form-label" for="weight"><?php echo ShippingBox::getLabel('weight'); ?></label>
					<div class="mws-form-item">
						<input type="text" class="pcms-numeric" name="weight" id="weight" value="<?php echo Input::old('weight', $formData['weight']); ?>" style="text-align: left !important;">
					</div>
				</div>
				<div class="mws-form-row">
					<label class="mws-form-label" for="price"><?php echo ShippingBox::getLabel('price'); ?></label>
					<div class="mws-form-item">
						<input type="text" class="pcms-numeric" name="price" id="price" value="<?php if( !empty($formData['price']) )echo Input::old('price',number_format($formData['price'],2)); ?>" style="text-align: left !important;">
					</div>
				</div>
			</div>

			<div class="mws-button-row">
				<input type="submit" class="btn btn-primary" value="Save">
			</div>
		</form>

	</div>
</div>
