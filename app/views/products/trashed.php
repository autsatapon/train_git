<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Deleted Product </span>
    </div>

	<?php /*echo Form::open(array('method'=>'get')) ?>
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
	<?php echo Form::close()*/ ?>
	<div class="clear"></div>
    <div class="mws-panel-body no-padding">

        <?php echo HTML::message() ?>

        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
					<th style="width:150px;">Brand</th>
					<th >Product</th>
					<th style="width:150px;">Action</th>
                </tr>
            </thead>
            <tbody>

				<?php foreach ($products as $product) : ?>
					<tr style="border-top:3px solid #4D4D4D;border-bottom:1px solid #4D4D4D;margin-top:10px;">
						 <td style="background-color:#D7D7D7;width:100px"><?php echo $product->brand->name ?></td>
						 <td ><strong><?php echo $product->title ?></strong></td>
						 <td class="table-center">
						 	<a href="<?php echo URL::to('products/restore/'.$product->id); ?>" ><input type="button" class="btn btn-info" value="Restore"></a>
						 	<!-- <a href="<?php echo URL::to('products/delete/'.$product->id); ?>"><input type="button" class="btn btn-danger" value="Delete"></a> -->
						 </td>
					</tr>
						<?php foreach ($product->variants as $variant) : ?>

								 <tr>
									 <td ></td>
									 <td >- <?php echo $variant->title ?></td>
									 <td ></td>
								</tr>

						<?php endforeach ?>
					<tr style="border-top:1px solid #999999">

					</tr>
              	 <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

