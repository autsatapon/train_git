<?php echo Form::open(array('method'=>'get')) ?>
	<div class="mws-form-inline" style="background-color:#A0A0A0;border:1px solid #8C8C8C;overflow:hidden;padding:10px 0px 10px 0px;">
		<div class="mws-form-row" style="float:left;margin-left:20px;">
			<?php echo Form::label('product', Product::getLabel('title')) ?>
			<div class="mws-form-item">
				<?php echo Form::text('product', Input::old('product')) ?>
			</div>
		</div>
		<div class="mws-form-row" style="float:left;margin-left:20px;">
			<?php echo Form::label('product_line', Product::getLabel('product_line')) ?>
			<div class="mws-form-item">
				<?php echo Form::text('product_line', Input::old('product_line')) ?>
			</div>
		</div>
		<div class="mws-form-row" style="float:left;margin-left:20px;">
			<?php echo Form::label('brand', 'Brand') ?>
			<div class="mws-form-item">
				<?php echo Form::brandDropdown('brand', Input::old('brand')) ?>
			</div>
		</div>
		<div class="mws-form-row" style="float:left;margin-left:20px;">
			<?php echo Form::label('vendor', 'Vendor') ?>
			<div class="mws-form-item">
				<?php echo Form::vendorDropdown('vendor', Input::old('vendor')) ?>
			</div>
		</div>

		<div class="mws-button-row"  style="float:left;margin-left:20px;padding-top:22px;">
			<?php echo Form::submit('Search', array('class'=>'btn btn-primary')) ?>
        </div>
	</div>
<?php echo Form::close() ?>