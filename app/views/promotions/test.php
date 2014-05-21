<div class="mws-panel grid_8">

    <div class="mws-panel-header">
        <span>Create Promotion</span>
    </div>

    <div class="mws-panel-body no-padding">

        <?php echo HTML::message(); ?>

        <form method="post" action="" class="mws-form" enctype="multipart/form-data">

            <fieldset class="mws-form-inline">

				<legend>Promotion</legend>

                <div class="mws-form-row">
                    <label class="mws-form-label" for="type">Type</label>
                    <div class="mws-form-item">
						<select id="type" name="type">
							<option value="">Flash Sale</option>
						</select>
                    </div>
                </div>

                <div class="mws-form-row">
                    <label class="mws-form-label" for="name">Name</label>
                    <div class="mws-form-item">
                        <input type="text" class="small" name="name" id="name" value="<?php echo Input::old('name'); ?>">
                    </div>
                </div>

				<div class="mws-form-row">
                    <label class="mws-form-label" for="start_date">Period</label>
                    <div class="mws-form-item">
                    	Start Date <input type="text" id="start_datepicker"  readonly class="ssmall" name="start_date"  value="<?php echo Input::old('start_date') ?>">
	                   &nbsp;Start Time 
               			<select name="start_time">
							<?php for($hours=0; $hours<24; $hours++)
							    for($mins=0; $mins<60; $mins+=10)
							        echo '<option>'.str_pad($hours,2,'0',STR_PAD_LEFT).':'
							                       .str_pad($mins,2,'0',STR_PAD_LEFT).'</option>';
							?>
						 </select>
               <br><br>
               End Date <input type="text" id="end_datepicker" readonly class="ssmall" name="end_date" id="end_date" value="<?php echo Input::old('end_date') ?>">&nbsp;
               &nbsp;End Time&nbsp; <select name="end_time">
							<?php for($hours=0; $hours<24; $hours++)
							    for($mins=0; $mins<60; $mins+=10)
							        echo '<option>'.str_pad($hours,2,'0',STR_PAD_LEFT).':'
							                       .str_pad($mins,2,'0',STR_PAD_LEFT).'</option>';
							?>
							<option>24:00</option>
						  </select>
					</div>
                </div>

				<div class="mws-form-row">
                    <label class="mws-form-label" for="code">Code</label>
                    <div class="mws-form-item">
                        <input type="text" class="small" name="code" id="code" value="<?php echo Input::old('code'); ?>">
                    </div>
                </div>

                <div class="mws-form-row">
                    <label class="mws-form-label" for="description">Detail</label>
                    <div class="mws-form-item">
                        <?php echo Form::ckeditor('description', Input::old('description'), array('id' => 'description', 'class' => 'form-control', 'height' => '150px')); ?>
                        <?php echo Form::transCkeditor(null, 'description', array('class' => 'form-control', 'height' => '150px')); ?>
                    </div>
                </div>

                <div class="mws-form-row">
                    <label class="mws-form-label" for="note">Note</label>
                    <div class="mws-form-item">
                        <textarea class="tarea small" name="note"><?php echo Input::old('note'); ?></textarea>
                    </div>
                </div>

                <div class="mws-form-row">
                    <label class="mws-form-label" for="note">Status</label>
                    <div class="mws-form-item">
                        <select name="status">
							<option value="activate">Activate</option>
							<option value="deactivate">Deactivate</option>
						</select>
                    </div>
                </div>

            </fieldset>

			<!-- start Condition -->
            <fieldset class="mws-form-inline condition">

				<legend>Condition</legend>
				
				<div class="mws-form-row">
					<a class="btn btn-default" href="#"><i class="icon-plus"></i> Add Condition</a>
					<select id="condition-choice">
						<option value="0">Day</option>
						<option value="1">Total Order</option>
						<option value="2">Summary Order of</option>
						<option value="3">Product Quantity</option>
						<option value="4">Combination</option>
						<option value="5">Promotion Code</option>
					</select>
					<input type="button" id="condition-add-ok" value="OK">
				</div>

				<div id="condition-0" class="mws-form-row condition-day" style="display:none">
                	<a href="#" class="pull-left"><i class="icon icon-trash remove-condition"></i></a>
					<label class="mws-form-label">Day</label>
					<div class="mws-form-item">
						<ul class="mws-form-list inline">
							<li><input type="checkbox" value=""> <label>Sun</label></li>
							<li><input type="checkbox" value=""> <label>Mon</label></li>
							<li><input type="checkbox" value=""> <label>Tue</label></li>
							<li><input type="checkbox" value=""> <label>Wed</label></li>
							<li><input type="checkbox" value=""> <label>Thu</label></li>
							<li><input type="checkbox" value=""> <label>Fri</label></li>
							<li><input type="checkbox" value=""> <label>Sat</label></li>
						</ul>
					</div>
					<div class="mws-form-item">
						Between
						<select name="start_time">
							<?php for($hours=0; $hours<24; $hours++)
							    for($mins=0; $mins<60; $mins+=10)
							        echo '<option>'.str_pad($hours,2,'0',STR_PAD_LEFT).':'
							                       .str_pad($mins,2,'0',STR_PAD_LEFT).'</option>';
							?>
						 </select>
						To
						<select name="start_time">
							<?php for($hours=0; $hours<24; $hours++)
							    for($mins=0; $mins<60; $mins+=10)
							        echo '<option>'.str_pad($hours,2,'0',STR_PAD_LEFT).':'
							                       .str_pad($mins,2,'0',STR_PAD_LEFT).'</option>';
							?>
							<option>24:00</option>
						 </select>
                    </div>
				</div>

				<div id="condition-1" class="mws-form-row condition-total-order" style="display:none">
                	<a href="#" class="pull-left"><i class="icon icon-trash remove-condition"></i></a>
					<label class="mws-form-label">Total order</label>
					<div class="mws-form-item">
						<input type="text" class="ssmall" name="" value="">
						To
						<input type="text" class="ssmall" name="" value="">
                    </div>
				</div>

				<div id="condition-2" class="mws-form-row condition-summary-order" style="display:none">
                	<a href="#" class="pull-left"><i class="icon icon-trash remove-condition"></i></a>
					<label class="mws-form-label">Summary order of</label>
					<div class="mws-form-item">
						<select name="">
							<option>Variant</option>
							<option>Product</option>
							<option>Collection</option>
							<option>Brand</option>
						</select>
						<input type="text">
					</div>
					<div class="mws-form-item">
						<input type="text" class="ssmall" name="" value="">
						To
						<input type="text" class="ssmall" name="" value="">
					</div>
				</div>

				<div id="condition-3" class="mws-form-row condition-product-qty" style="display:none">
                	<a href="#" class="pull-left"><i class="icon icon-trash remove-condition"></i></a>
					<label class="mws-form-label">Product Qty</label>
					<div class="mws-form-item">
						<input type="text" class="ssmall" name="" value="">
						of
						<select name="">
							<option>Variant</option>
							<option>Product</option>
							<option>Collection</option>
							<option>Brand</option>
						</select>
						<input type="text">
                    </div>
				</div>

				<div id="condition-4" class="mws-form-row condition-combination-of" style="display:none">
                	<a href="#" class="pull-left"><i class="icon icon-trash remove-condition"></i></a>
					<label class="mws-form-label">Combination of</label>
					<div class="mws-form-item">
						<input type="text" class="ssmall" name="" value="">
						of
						<select name="">
							<option>Variant</option>
							<option>Product</option>
							<option>Collection</option>
							<option>Brand</option>
						</select>
						<input type="text">
                    </div>
					<label class="mws-form-label">and</label>
					<div class="mws-form-item">
						<input type="text" class="ssmall" name="" value="">
						of
						<select name="">
							<option>Variant</option>
							<option>Product</option>
							<option>Collection</option>
							<option>Brand</option>
						</select>
						<input type="text">
                    </div><label class="mws-form-label">+ | - and</label>
					<div class="mws-form-item">
						<input type="text" class="ssmall" name="" value="">
						of
						<select name="">
							<option>Variant</option>
							<option>Product</option>
							<option>Collection</option>
							<option>Brand</option>
						</select>
						<input type="text">
                    </div>
				</div>
				
				<div id="condition-5" style="display:none">
					<div class="mws-form-row condition-promotion-code">
	                	<a href="#" class="pull-left"><i class="icon icon-trash remove-condition"></i></a>
						<label class="mws-form-label">Promotion code</label>
						<div class="mws-form-item"></div>
					</div>

					<div class="mws-form-row condition-promotion-code-as">
						<label class="mws-form-label">as:</label>
						<div class="mws-form-item">
							<ul class="mws-form-list inline">
								<li>
									<label>
										<input type="radio" name="condition[promotion_code][][type]" value="">Coupon code
									</label>
								</li>
								<li>
									<label>
										<input type="radio" name="condition[promotion_code][][type]" value=""> Gift voucher
									</label>
								</li>
							</ul>
						</div>
					</div>

					<div class="mws-form-row condition-promotion-code-type">
						<label class="mws-form-label">type:</label>
						<div class="mws-form-item">
							<ul class="mws-form-list">
								<li>
									<input type="radio" name="code-type" value=""> <label>Single code</label><br />
									code can be used for <input type="text" class="ssmall" name="" value=""> times
								</li>
								<li>
									<input type="radio" name="code-type" value=""> <label>Unique code</label><br />
									<input type="text" class="ssmall" name="" value=""> codes (each code can be used once)
								</li>
							</ul>
						</div>
					</div>

					<div class="mws-form-row condition-promotion-code-code">
						<label class="mws-form-label">type:</label>
						<div class="mws-form-item">
							<ul class="mws-form-list">
								<li>
									<input type="radio" name="code-type2" value=""> <label>Auto-generated code</label><br />
									start with <input type="text" class="ssmall" name="" value=""> and end with <input type="text" class="ssmall spinner" name="" value=""> random chars
								</li>
								<li>
									<input type="radio" name="code-type2" value=""> <label>Custom code</label>
									<ol>
										<li>
											<input type="text" class="ssmall" name="" value="">
											<i class="icon-ok"></i>
										</li>
										<li>
											<input type="text" class="ssmall" name="" value="">
											<i class="icon-ok"></i>
										</li>
										<li>
											<input type="text" class="ssmall" name="" value="">
											<i class="icon-remove"></i> This code is not available
										</li>
									</ol>
								</li>
							</ul>
						</div>
					</div>
				</div>

			</fieldset>
			<!-- end Condition -->

            <!-- start Effect -->
            <fieldset class="mws-form-inline effect">

				<legend>Effect</legend>

				<div class="mws-form-row">
					<div class="mws-form-item-effect border-original">
						<label class="mws-form-label" for="discount">
							<input type="checkbox" id="discount" name="" value="discount"> Discount
						</label>
						<div class="mws-form-item">
							<div class="row-fluid">
								<div class="grid_3">
									<input type="radio" name="discount" value="price">
									Price
									<input type="text" class="ssmall" name=""> Baht<br />
									<input type="radio" name="discount" value="">
									Percent
									<input type="text" class="small" name="" value="10"> %<br />
								</div>
								<div class="grid_1">
									<strong>ON</strong>
								</div>
								<div class="grid_3">
									<ul class="mws-form-list">
										<li>
											<label>
												<input type="radio" name="free-product" value="same_product"> Same Product
											</label>
										</li>
										<li>
											<label>
												<input type="radio" name="free-product" value="following">
												<select name="">
													<option>Variant</option>
													<option>Product</option>
													<option>Collection</option>
													<option>Brand</option>
												</select>
						<input type="text">
											</label>
										</li>
										<li>
											<label>
												<input type="radio" name="free-product" value="cart"> cart
											</label>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
                </div>

                <div class="mws-form-row">
					<div class="mws-form-item-effect border-original">
						<label class="mws-form-label" for="Free">
							<input type="checkbox" name="" value="free"> Free
						</label>
						<div class="mws-form-item">
							<div class="clearfix">
								<div class="grid_3">
									<input type="text" class="ssmall" name="" id="free_number" value="1">
								</div>
								<div class="grid_1">
									<strong>Of</strong>
								</div>
								<div class="grid_4">
									<ul class="mws-form-list">
										<li>
											<input type="radio" name="free-product" value="same_product"> Same Product
										</li>
										<li>
											<input type="radio" name="free-product" value="following">
											<select name="">
												<option>Variant</option>
												<option>Product</option>
												<option>Collection</option>
												<option>Brand</option>
											</select>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<div class="mws-form-item">
							<div class="clearfix">
								<div class="grid_1">
									Apply:
								</div>
								<div class="grid_7">
									<ul class="mws-form-list">
										<li>
											<label>
												<input type="radio" name="combination-limit" value="combination"> To each combination
											</label>
											<label>
												<input type="checkbox" name="" value="apply_type_extra"> But limit to
											</label>
												<input type="text" class="sssmall" name="" id="apply_type_extra_number" value="3"> Times per order
										</li>
										<li>
											<label>
												<input type="radio" name="combination-limit" value="once"> Only once per order
											</label>
										</li>
									</ul>
								</div>
							</div>
						</div>

					</div>
                </div>

				<div class="mws-form-row">
					<div class="mws-form-item-effect border-original">
						<label class="mws-form-label" for="Free Shipping">
							<input type="checkbox" name="" value="free_shipping">Free Shipping
						</label>
						<div class="mws-form-item"></div>
					</div>
                </div>

			</fieldset>
            <!-- end Effect -->

            <!-- start limitation -->
			<fieldset class="mws-form-inline limitation">

				<legend>Limitation</legend>

				<div class="mws-form-row">
					<div class="mws-form-item-effect border-original">
						<label class="mws-form-label" for="Limitation"></label>
						<div class="mws-form-item">
							<ul class="mws-form-list">
								<li>
									<label>
										<input type="checkbox" name="" value="campaign"> Use quota of campaign
									</label>
								</li>
								<li>
									<label>
										<input type="checkbox" name="" value="limit_budget">Limit budget to <input type="text" class="ssmall" value="2000"> Baht
									</label>
								</li>
								<li>
									<label>
										<input type="checkbox" name="" value="limit_item">Limit items (Product,Brand ที่แถม) <input type="text" class="ssmall" value="800"> Items
									</label>
								</li>
								<li>
									<label>
										<input type="checkbox" name="" value="quota_per">limit to<input type="text" class="sssmall" name="" value="5">
										usages per
										<select name="">
											<option>Day</option>
											<option>Week</option>
											<option>Month</option>
										</select>
									</label>
								</li>
								<li>
									<label>
										<input type="checkbox" name="limitation_type" value="quota_of_user"> limit to <input type="text" class="sssmall" value="1"> usages per user
									</label>
								</li>
							</ul>
						</div>
					</div>
                </div>

			</fieldset>
            <!-- end limitation -->

            <div class="mws-button-row">
                <input type="submit" class="btn btn-primary btn-large" value="Save">
                <a href="<?php echo URL::previous(); ?>" class="btn btn-large">Cancel</a>
            </div>

        </form>


    </div>
</div>
<script>
$(function(){
	$(document).on('click', '#condition-add-ok', function(e){
		e.preventDefault();
		var index = $('#condition-choice').val();
		$('#condition-'+index).show();
		$('#condition-choice').in
	}).on('click', '.remove-condition', function(e){
		e.preventDefault();
		$(this).parents('.mws-form-row').hide();
	})
})
</script>
