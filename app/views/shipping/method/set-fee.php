<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span>Set Shipping Fee</span>
	</div>
	<div class="mws-panel-body no-padding">

		<?php if(Session::has('success')) { ?>
			<div class="alert alert-success">
				<p><?php echo Session::get('success') ?></p>
			</div>
		<?php } ?>

		<?php if ($errors->count() > 0) { ?>
			<div class="alert alert-error">
				<?php foreach ($errors->all() as $error) { ?>
					<p><?php echo $error ?></p>
				<?php } ?>
			</div>
		<?php } ?>

		<form method="post" action="" id="form-set-shipping-fee" class="mws-form" enctype="multipart/form-data">
			<div class="mws-form-inline">
				<div class="mws-form-row">
					<label class="mws-form-label" for="name">Shipping Method</label>
					<div class="mws-form-item">
						<p style="padding:5px 0; margin:0;"><?php echo $shippingMethod->name ?></p>
					</div>
				</div>
				<div class="mws-form-row">
					<label class="mws-form-label" for="name">Delivery Area</label>
					<div class="mws-form-item">
						<p style="padding:5px 0; margin:0;"><?php echo $deliveryArea->name ?></p>
					</div>
				</div>

				<div class="mws-form-row">
					<table id="set-shipping-fee" class="mws-table">
						<thead>
							<tr>
								<th colspan="2">Weight (g)</th>
								<th rowspan="2">Box</th>
								<?php /*
								<th colspan="2">Product Weight (g)</th>
								*/ ?>
								<th rowspan="2">Fee (THB)</th>
								<th rowspan="2">&nbsp;</th>
							</tr>
							<tr>
								<th>Min</th>
								<th>Max</th>
								<?php /*
								<th>Min</th>
								<th>Max</th>
								*/ ?>
							</tr>
						</thead>
						<tbody>
							<?php if ($shippingFees->isEmpty()) { ?>
							<tr>
								<td>
									<input type="text" class="small pcms-numeric lower-edge" name="weight_min[]" value="0" readonly="readonly">
								</td>
								<td>
									<input type="text" class="small pcms-numeric higher-edge" name="weight_max[]" value="">
									<div class="unlimit-div">
										<input type="checkbox" class="check-unlimit" name="unlimit[]">
										to no limit
									</div>
								</td>
								<td>
									<select name="shipping_box_id[]">
										<option value="0">Choose Box</option>
										<?php foreach ($shippingBoxes as $box) { ?>
										<option value="<?php echo $box->id ?>"><?php echo $box->name ?> (<?php echo $box->weight ?>g)</option>
										<?php } ?>
									</select>
								</td>
								<?php /*
								<td>
									<input type="text" class="small pcms-numeric" name="product_weight_min[]" disabled="disabled">
								</td>
								<td>
									<input type="text" class="small pcms-numeric" name="product_weight_max[]" disabled="disabled">
								</td>
								*/ ?>
								<td>
									<input type="text" class="small pcms-numeric" name="shipping_fee[]" value="">
								</td>
								<td>
									<a href="#" class="delete-row"><i class="icol-cross"></i> Delete</a>
								</td>
							</tr>
							<?php } else { ?>
								<?php foreach ($shippingFees as $key=>$fee) { ?>
								<tr>
									<td>
										<input type="text" class="small pcms-numeric lower-edge" name="weight_min[]" value="<?php echo $fee->weight_min ?>" readonly="readonly">
									</td>
									<td>
										<input type="text" class="small pcms-numeric higher-edge" name="weight_max[]" value="<?php echo ($fee->weight_max==0) ? '' : $fee->weight_max ?>" <?php if ($fee->weight_max==0) { echo ' disabled="disabled"'; } ?>>
										<div class="unlimit-div">
											<input type="checkbox" class="check-unlimit" name="unlimit[]"<?php if ($fee->weight_max==0) { echo ' checked="checked"'; } ?>>
											to no limit
										</div>
									</td>
									<td>
										<select name="shipping_box_id[]">
											<option value="0">Choose Box</option>
											<?php foreach ($shippingBoxes as $box) { ?>
												<option value="<?php echo $box->id ?>"<?php if ($box->id == $fee->shipping_box_id) { echo ' selected="selected"'; } ?>><?php echo $box->name ?> (<?php echo $box->weight ?>g)</option>
											<?php } ?>
										</select>
									</td>
									<?php /*
									<td>
										<input type="text" class="small pcms-numeric" name="product_weight_min[]" disabled="disabled">
									</td>
									<td>
										<input type="text" class="small pcms-numeric" name="product_weight_max[]" disabled="disabled">
									</td>
									*/ ?>
									<td>
										<input type="text" class="small pcms-numeric" name="shipping_fee[]" value="<?php echo $fee->shipping_fee ?>">
									</td>
									<td>
										<a href="#" class="delete-row"><i class="icol-cross"></i> Delete</a>
									</td>
								</tr>
								<?php } ?>
							<?php } ?>
						</tbody>
					</table>
					<p style="padding:5px;">
						<a id="add-row" class="btn" href="#add-row"><i class="icol-add"></i> Add Row</a>
					</p>
				</div>

				<script type="text/javascript">
					function setLowerEdge( obj ) {
						var prev = obj.prev().find('.higher-edge');
						obj.find('.lower-edge').val( parseInt( prev && prev.val().length ? prev.val() : 0, 10 )+1 );
					}
					function setHigherEdge( obj ) {
						obj.parents('td').find('.higher-edge').attr('disabled', (obj.is(':checked') ? 'disabled' : false));
					}

					$(function(){
						$('#add-row').click(function(e){
							e.preventDefault();
							var clone = $('#set-shipping-fee tbody tr:first').clone(),
								unlimitChecked = $('#set-shipping-fee').find('.check-unlimit:checked');
							clone.find('input[type="text"]').val('');
							$('#set-shipping-fee tbody').append(clone);
							setLowerEdge(clone);
							unlimitChecked.length && unlimitChecked.attr('checked', false) && setHigherEdge( unlimitChecked );
						});

						// Validate
						$('#form-set-shipping-fee').submit(function(){
							var checkTxtInput = $('#form-set-shipping-fee input:text[value=""]:not(:disabled)').length;
							var checkSelInput = $('#form-set-shipping-fee select option:selected[value="0"]').length;
							if (checkTxtInput > 0 || checkSelInput > 0) {
								alert('Please Fill All Shipping Fee Detail.');
								return false;
							}
						});

						$( document ).on('click', 'a.delete-row', function(e){
							e.preventDefault();
							if ($('#set-shipping-fee tbody tr').length == 1) {
								return;
							}
							$(this).parents('tr').remove();
						});

						$(document).on('keyup', '.higher-edge', function(e){
							var nextTr = $(this).parents('tr').next();
							nextTr.length && setLowerEdge(nextTr);
						});

						$(document).on('change', '.check-unlimit', function(e){
							setHigherEdge( $(this) );
						})
					});
				</script>

				<style type="text/css">
					#set-shipping-fee { border:1px solid #999; }
					#set-shipping-fee td { text-align:center; vertical-align:top; }
					#set-shipping-fee input[type="text"] { width:100px; }
					#set-shipping-fee tbody tr:first-child .delete-row { visibility:hidden; }
					#set-shipping-fee tbody .unlimit-div { display:none; }
					#set-shipping-fee tbody tr:last-child .unlimit-div { display:block; }
				</style>

			</div>
			<div class="mws-button-row">
				<input type="submit" class="btn btn-primary" value="Save">
			</div>
		</form>


	</div>
</div>
