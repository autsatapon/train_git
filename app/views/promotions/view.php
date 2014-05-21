<?php

		Theme::asset()->writeStyle('Promotion-Management-create-version-1.0','
		.mws-form .mws-form-inline .mws-form-label {
		    float: left;
		    padding-top: 5px;
		    width: 15%;
		}
		.tarea {
		    width:55%;
			height:120px;;
		}
		.small {
		    width:55%;
			margin-left:10px;
		}
		.ssmall {
		    width:15%;
			margin-left:10px;
		}
		.sssmall {
		    width:8%;
			margin-left:10px;
		}
		.ssssmall {
		    width:3%;
			margin-left:10px;
		}
		.mws-form .mws-form-inline .mws-form-item {
			 margin-left: 180px;
			 width:70%;
		}
		.border{
			border:2px solid #000;
			-webkit-border-radius: 5px;
			-moz-border-radius: 5px;
			border-radius: 5px;
			margin-left: 180px;
			padding:15px;
			text-align:left;
		}
		.border-original{
			border:2px solid #000;
			margin-left: 180px;
			padding:15px;
			text-align:left;
		}
		.mws-form .mws-form-inline .mws-form-item-condition {
			 margin-left: 180px;
			 width:70%;
		}
		.mws-form .mws-form-inline .mws-form-item-effect {
			 margin-left: 180px;
			 width:65%;
		}
		.condition-box {
			width:100%;
			overflow:hidden;
			margin-top:10px;
			font-size: 14px;
    		line-height: 40px;
		}
		.condition-box-left {
			width:25%;
			text-align:left;
			float:left;
		}
		.condition-box-right {
			width:75%;
			text-align:left;
			float:left;
		}
		.effect-box {
			width:100%;
			overflow:hidden;
			margin-top:5px;
			font-size: 14px;
		}
		.effect-box-left {
			width:45%;
			text-align:left;
			float:left;
		}
		.effect-box-left2 {
			width:15%;
			text-align:left;
			float:left;
		}
		.effect-box-center {
			width:15%;
			text-align:center;
			float:left;
		}
		.effect-box-right {
			width:40%;
			text-align:left;
			float:left;
		}
		.effect-box-right2 {
			width:70%;
			text-align:left;
			float:left;
		}
		.right {
			width:90%;
			overflow:hidden;
			text-align:right;
			padding-right:10px;
		}
		.center {
			width:90%;
			overflow:hidden;
			text-align:center;
			padding-right:10px;
		}
		h2{
			margin-left:15px;
		}

		.mws-form .mws-button-row {
			text-align:right;
		}

		/* Base class */
		.description {
			position: relative;
			margin: 0px 0px 10px;
			padding: 10px 10px 10px;
			background-color: #fff;
			border: 1px solid #ddd;
			-webkit-border-radius: 4px;
			-moz-border-radius: 4px;
			border-radius: 4px;
			text-indent: 27px;
			line-height: 25px;
		}

		/* Echo out a label for the example */
		.description.translate:after {
			content: attr(locale);
			position: absolute;
			text-indent: 0px;
			top: -1px;
			left: -1px;
			padding: 0px 7px;
			font-size: 12px;
			line-height: 25px;
			font-weight: bold;
			background-color: #f5f5f5;
			border: 1px solid #ddd;
			color: #9da0a4;
			-webkit-border-radius: 4px 0 4px 0;
			-moz-border-radius: 4px 0 4px 0;
			border-radius: 4px 0 4px 0;
		}

		h2 {
			display: inline;
		}

		table.coupon-list {
			width: 300px;
			line-height: 15px;
			border: black 1px solid;
			padding: 2px;
			margin-top: 10px;
		}

		table.coupon-list > thead > tr > th,
		table.coupon-list > tbody > tr > td
		{
			padding: 2px;
			border-bottom: rgb(224, 224, 224) 1px solid;
		}

		table.coupon-list > thead > tr > th:first-child,
		table.coupon-list > tbody > tr > td:first-child
		{
			width: 90%;
		}

		table.coupon-list > thead > tr > th:not(:first-child),
		table.coupon-list > tbody > tr > td:not(:first-child)
		{
			text-align:center;
		}
		');

?>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span>View Promotion</span>
    </div>
    <div class="mws-panel-body no-padding">


        <?php echo HTML::message(); ?>

        <div class="mws-form">
            <fieldset class="mws-form-inline">
            	<legend>Details</legend>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="Type">Type</label>
                    <div class="mws-form-item">
                    	<?php echo $promotion->promotion_category; ?>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="name">Name</label>
                    <div class="mws-form-item">
                        <?php echo $promotion->name; ?>
                    </div>
                </div>
                 <div class="mws-form-row">
                    <label class="mws-form-label" for="period">Period</label>
                    <div class="mws-form-item">
	                   <?php echo date('d F Y', strtotime($promotion->start_date)); ?>
	                   To
	                   <?php echo date('d F Y', strtotime($promotion->end_date)); ?>
                    </div>
                </div>
				<div class="mws-form-row">
                    <label class="mws-form-label" for="code">Code</label>
                    <div class="mws-form-item">
                        <?php echo $promotion->code; ?>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="description">Description</label>
                    <div class="mws-form-item">
                    	<div class="description">
                    		<?php echo $promotion->description; ?>
                    	</div>

                    	<?php foreach($promotion->translates as $translate): ?>
                    		<div class="description translate" locale="<?php echo strtoupper(substr($translate->locale, 3, 2)); ?>">
                    			<?php echo $translate->description; ?>
                    		</div>
                    	<?php endforeach; ?>

                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="note">Note</label>
                    <div class="mws-form-item">
                    	<?php echo $promotion->note ? $promotion->note->detail : ""; ?>
                    </div>
                </div>
            </fieldset>

            <!-- start Condition -->

            <fieldset class="mws-form-inline">
            	<legend>Condition</legend>
	            <div class="mws-form-row">
	                    <label class="mws-form-label" for="Condition">&nbsp;</label>
	                    <div class="mws-form-item-condition border">
	                        <!--<a class="btn btn-default" href="#"><i class="icon-plus"></i> Add Condition (And)</a> <br>-->
	                        <?php if($promotion->conditions) foreach ($promotion->conditions as $type => $conditionSet): ?>
	                        	<?php foreach ($conditionSet as $condition): ?>


									<?php if($type == "day"): ?>
										<div class="condition-box">
				                        	<div class="condition-box-left">
				                        		Day
					                        </div>
					                        <div class="condition-box-right">
					                        	<?php echo implode(", ", $condition['day']); ?>
					                        	<br>
					                        	<?php echo $condition['start_time']; ?>
													&nbsp;To&nbsp;
												<?php echo $condition['end_time']; ?>
					                        </div>
				                        </div>
	                        		<?php endif; ?>

	                        		<?php if($type == "total_order"): ?>
	                        			<div class="condition-box">
				                        	<div class="condition-box-left">
				                        		Total Order
					                        </div>
					                        <div class="condition-box-right">
					                        	<?php echo $condition['start_order']; ?>
													&nbsp;To&nbsp;
												<?php echo $condition['end_order']; ?>
					                        </div>
				                        </div>
	                        		<?php endif; ?>

	                        		<?php if($type == "total_order"): ?>
	                        			<div class="condition-box">
				                        	<div class="condition-box-left">
				                        		Total Order
					                        </div>
					                        <div class="condition-box-right">
					                        	<?php echo $condition['start_order']; ?>
													&nbsp;To&nbsp;
												<?php echo $condition['end_order']; ?>
					                        </div>
				                        </div>
	                        		<?php endif; ?>

	                        		<?php if($type == "trueyou"): ?>
	                        			<div class="condition-box">
				                        	<div class="condition-box-left">
				                        		Card Type
					                        </div>
					                        <div class="condition-box-right">
					                        	<?php echo @$condition['type']; ?>
					                        </div>
				                        </div>
	                        		<?php endif; ?>

	                        		<?php if($type == "promotion_code"): ?>
	                        			<div class="condition-box">
									        <div class="condition-box-left">
				                        		Promotion Code
				                        		<div class="center">As :</div>
				                        		<div class="center">Type :</div>
				                        		<div class="center">Code :</div>
					                        </div>
					                        <div class="condition-box-right">
					                        	<br>
					                        	<?php echo @$condition['type'] == 'coupon' ? "Coupon Code" : "Gift Voucher"; ?>
					                        	<br>

					                        	<?php if(@$condition['format'] == "single_code"): ?>

													"Single Code" &nbsp;&nbsp;&nbsp;&nbsp; Code can be used for
													<?php echo $condition['single_code']['used_times']; ?> Times
												<?php else: ?>
													"Unique Code" has
													<?php echo $condition['multiple_code']['count']; ?> Codes (Each code can be used once)
												<?php endif; ?>

												<table class="coupon-list">
													<thead>
														<tr>
															<th>Code</th>
															<th>Avaliable</th>
															<th>Status</th>
														</tr>
													</thead>
													<tbody>
												<?php foreach($promotion->promotionCodes as $promotionCode): ?>
													<tr>
														<td><?php echo $promotionCode->code; ?></td>
														<td><?php echo $promotionCode->avaliable; ?></td>
														<td><?php echo ucfirst($promotionCode->status); ?></td>
													</tr>
												<?php endforeach; ?>
													</tbody>
												</table>
											</div>
				                        </div>
	                        		<?php endif; ?>


	                        	<?php endforeach; ?>
	                        <?php endforeach; ?>

<!--
	                    	<div class="condition-box">
	                        	<div class="condition-box-left">
	                        		<input type="checkbox" name="" > Day
		                        </div>
		                        <div class="condition-box-right">
		                        	&nbsp;<input type="checkbox" name="" > Sun
		                        	&nbsp;<input type="checkbox" name="" > Mon
		                        	&nbsp;<input type="checkbox" name="" > Tue
		                        	&nbsp;<input type="checkbox" name="" > Wen
		                        	&nbsp;<input type="checkbox" name="" > Thu
		                        	&nbsp;<input type="checkbox" name="" > Fri
		                        	&nbsp;<input type="checkbox" name="" > Sat <br>
		                        	Time Between
		                        	<select name="start_time">
									<?php for($hours=0; $hours<24; $hours++)
									    for($mins=0; $mins<60; $mins+=10)
									        echo '<option>'.str_pad($hours,2,'0',STR_PAD_LEFT).':'
									                       .str_pad($mins,2,'0',STR_PAD_LEFT).'</option>';
									?>
									</select>

										&nbsp;To&nbsp;

									<select name="end_time">
									<?php for($hours=0; $hours<24; $hours++)
									    for($mins=0; $mins<60; $mins+=10)
									        echo '<option>'.str_pad($hours,2,'0',STR_PAD_LEFT).':'
									                       .str_pad($mins,2,'0',STR_PAD_LEFT).'</option>';
									?>
									</select>

		                        </div>
	                        </div>

	                        <div class="condition-box">
	                        	<div class="condition-box-left">
	                        		<input type="checkbox" name="" > Total Order
		                        </div>
		                        <div class="condition-box-right">
		                        	<input type="text" class="ssmall" name="start_order" id="start_order" value="<?php echo Input::old('start_order') ?>">&nbsp;To&nbsp;
									<input type="text" class="ssmall" name="end_order" id="end_order" value="<?php echo Input::old('end_order') ?>">
		                        </div>
	                        </div>

	                        <div class="condition-box">
	                        	<div class="condition-box-left">
	                        		<input type="checkbox" name="" > Summary order of
		                        </div>
		                        <div class="condition-box-right">
		                        	<select name="summary_order_of">
		                        		<option>Variant , Product , Collection , Brand</option>
		                        	</select> <a href="#" title="">&nbsp;Choose</a> <br>
		                        	<input type="text" class="ssmall" name="start_summary_order" id="start_summary_order" value="<?php echo Input::old('start_summary_order') ?>">&nbsp;To&nbsp;
									<input type="text" class="ssmall" name="end_summary_order" id="end_summary_order" value="<?php echo Input::old('end_summary_order') ?>">
		                        </div>
	                        </div>

	                        <div class="condition-box">
	                        	<div class="condition-box-left">
	                        		<input type="checkbox" name="" > Product Qty
		                        </div>
		                        <div class="condition-box-right">
		                        	<input type="text" class="sssmall" name="number_product_qty" id="number_product_qty" value="<?php echo Input::old('number_product_qty','1') ?>">
		                        	&nbsp;Of&nbsp;
		                        	<select name="product_qty">
		                        		<option>Variant , Product , Collection , Brand</option>
		                        	</select> &nbsp;<a href="#" title="">Choose</a> <br>
		                        </div>
	                        </div>

	                        <div class="condition-box">
	                        	<div class="condition-box-left">
	                        		<input type="checkbox" name="" > Combination Of <br>
	                        		<div class="right"><a href="#" title=""><strong>+</strong></a> | <a href="#" title=""><strong>-</strong></a> &nbsp; And</div>
		                        </div>
		                        <div class="condition-box-right">
		                        	<input type="text" class="sssmall" name="number_product_combination" id="number_product_combination" value="<?php echo Input::old('number_product_combination','2') ?>">
		                        	&nbsp;Of&nbsp;
		                        	<select name="combination">
		                        		<option>Variant , Product , Collection , Brand</option>
		                        	</select> &nbsp;<a href="#" title="">Choose</a> <br>

		                        	<input type="text" class="sssmall" name="number_product_combination2" id="number_product_combination2" value="<?php echo Input::old('number_product_combination2','1') ?>">
		                        	&nbsp;Of&nbsp;
		                        	<select name="combination">
		                        		<option>Variant , Product , Collection , Brand</option>
		                        	</select> &nbsp;<a href="#" title="">Choose</a>
		                        </div>
	                        </div>

	                        <div class="condition-box">
	                        	<div class="condition-box-left">
	                        		<input type="checkbox" name="" > Promotion Code
	                        		<div class="center">As :</div>
	                        		<div class="center">Type :</div>
	                        		<div class="center">&nbsp;</div>
	                        		<div class="center">Code :</div>
		                        </div>
		                        <div class="condition-box-right">
		                        	  <br><input type="radio"  name="promotion_status_as" value="coupon_code"> Coupon Code &nbsp;&nbsp;<input type="radio"  name="promotion_status_as" value="cash_voucher" checked="checked"> Gift Voucher

		                        	  <br><input type="radio"  name="promotion_status_type" value="single_code"> Single Code &nbsp;&nbsp;&nbsp;&nbsp; Code can be used for
		                        	  <input type="text" class="sssmall" name="number_promotion_status_type" id="number_promotion_status_type" value="<?php echo Input::old('number_promotion_status_type','1') ?>"> Times
		                        	  <br><input type="radio"  name="promotion_status_type" value="unique_code"> Unique Code &nbsp;&nbsp;
		                        	  <input type="text" class="sssmall" name="number_promotion_status_type" id="number_promotion_status_type" value="<?php echo Input::old('number_promotion_status_type','1') ?>"> Codes (Each code can be used once)

		                        	  <br><input type="radio"  name="promotion_status_code" value="auto_generated_code"> Auto-generated Code<br>
		                        	  &nbsp;&nbsp;&nbsp;&nbsp;Start with <input type="text" class="ssmall" name="auto_gen_start" id="auto_gen_start" value="<?php echo Input::old('auto_gen_start') ?>"> And end with
		                        	  <select name="auto_gen_num">
		                        		<option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option>
		                        		<option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option>
		                              </select> Random Chars

		                               <br><input type="radio"  name="promotion_status_code" value="custom_code"> Custom Code<br>

		                               <input type="text" class="ssssmall" name="count_char_code" value="1" readonly="readonly">
		                               <input type="text" class="small" name="custom_code_text" id="custom_code_text" value="<= นับเลขของ Unique Code">
		                               <i class="icon-ok-circle"></i>

		                               <br><input type="text" class="ssssmall" name="count_char_code" value="1" readonly="readonly">
		                               <input type="text" class="small" name="custom_code_text" id="custom_code_text" value="">
		                               <i class="icon-ok-circle"></i>

		                               <br><input type="text" class="ssssmall" name="count_char_code" value="1" readonly="readonly">
		                               <input type="text" class="small" name="custom_code_text" id="custom_code_text" value="">
		                               <i class="icon-ban-circle"></i> This code is not avaliable.
		                               <br><br>

		                               <input type="text" class="small" name="custom_code_text" id="custom_code_text" value="ถ้าเป็น Single ไม่ต้องนับเลข"><i class="icon-ok-circle"></i>
		                        </div>
	                        </div>
	                        -->

	                    </div>
	            </div>
	        </fieldset>
            <!-- end Condition -->

            <?php if($promotion->effects != false): ?>

            <!-- start Effect -->

            <fieldset class="mws-form-inline">
            	<legend>Effect</legend>
<?php // s ($promotion->effects); ?>
            	<?php if(in_array("discount", $promotion->effects['type'])): ?>
	            <div class="mws-form-row">
	            	<div class="mws-form-item-effect border-original">
	                    <label class="mws-form-label" for="discount">
	                    	Discount
	                    </label>
	                    <div class="mws-form-item">
	                    	<div class="effect-box">
	                    		<div class="effect-box-left">
	                    			<?php if(@$promotion->promotion_category != 'trueyou'): ?>
		                    			<?php if(@$promotion->effects['discount']['type'] == "price"): ?>
		                    				<?php echo @$promotion->effects['discount']['baht']; ?> baht.
		                    			<?php else: ?>
		                    				<?php echo @$promotion->effects['discount']['percent']; ?>%.
		                    			<?php endif; ?>
		                    		<?php else: ?>
		                    			<?php if(@$promotion->effects['discount']['type'] == "price"): ?>
		                    				<?php echo @$promotion->effects['discount']['baht']; ?> baht.
		                    			<?php else: ?>
		                    				<?php echo @$promotion->effects['discount']['percent']; ?>%.
		                    			<?php endif; ?>
		                    		<?php endif; ?>
	                   			</div>

	                   			<div class="effect-box-center">
	                   			    <strong>ON</strong>
	                   			</div>
	                   			<div class="effect-box-right">
	                   				<?php if(@$promotion->promotion_category != 'trueyou'): ?>
		                   				<?php if(@$promotion->effects['discount']['on'] == "cart"): ?>
		                   					Cart
		                   				<?php endif; ?>

		                   				<?php if(@$promotion->effects['discount']['on'] == "same_product"): ?>
		                   					Same Product
		                   				<?php endif; ?>

		                   				<?php if(@$promotion->effects['discount']['on'] == "following"): ?>
		                   					The following Item
		                   					<ol>
		                   						<?php foreach ($promotion->effects['discount']['following_items'] as $item): ?>
		                   							<li><?php echo $item; ?></li>
		                   						<?php endforeach; ?>
		                   					</ol>
		                   				<?php endif; ?>
		                   			<?php else: ?>
		                   				<?php echo @$promotion->effects['discount']['which'];?><br>

		                   					The following <?php echo @$promotion->effects['discount']['which']; ?>
		                   					<ol>
		                   						<?php
		                   						$following_items = explodeFilter(",", @$promotion->effects['discount']['following_items']);
		                   						foreach ($following_items as $item): ?>
		                   							<li><?php echo $item; ?></li>
		                   						<?php endforeach; ?>
		                   					</ol>

		                   				<?php if(@$promotion->effects['discount']['exclude_product']['on'] == 'following'):?>
		                   					<div class="clear"></div>
		                   					Exclude Product
		                   					<ol>
		                   						<?php
		                   						$un_following_items = explodeFilter(",", $promotion->effects['discount']['exclude_product']['un_following_items']);
		                   						foreach ($un_following_items as $item): ?>
		                   							<li><?php echo $item; ?></li>
		                   						<?php endforeach; ?>
		                   					</ol>
		                   				<?php endif; ?>

		                   				<?php if(@$promotion->effects['discount']['exclude_variant']['on'] == 'following'):?>
		                   					<div class="clear"></div>
		                   					Exclude Variant
		                   					<ol>
		                   						<?php
		                   						$un_following_items = explodeFilter(",", $promotion->effects['discount']['exclude_variant']['un_following_items']);
		                   						foreach ($un_following_items as $item): ?>
		                   							<li><?php echo $item; ?></li>
		                   						<?php endforeach; ?>
		                   					</ol>
		                   				<?php endif; ?>


		                   			<?php endif; ?>

	                   			</div>
	                   		</div>
	                    </div>
	                 </div>
                </div>
            	<?php endif; ?>

            	<?php if(in_array("free", $promotion->effects['type'])): ?>

            	<div class="mws-form-row">
	            	<div class="mws-form-item-effect border-original">
	                    <label class="mws-form-label" for="Free">
	                    	Free
	                    </label>
	                    <div class="mws-form-item">
	                    	<div class="effect-box">
	                    		<div class="effect-box-left">
	                    		<?php echo $promotion->effects['free']['count']; ?>
	                    		<br>
	                   			</div>
	                   			<div class="effect-box-center">
	                   			    <strong>Of</strong>
	                   			</div>
	                   			<div class="effect-box-right">
	                   				<?php if(@$promotion->effects['free']['on'] == "same_product"): ?>
	                   					Same Product
	                   				<?php else: ?>
	                   					The following Item
	                   					<ol>
	                   						<?php foreach ($promotion->effects['free']['following_items'] as $item): ?>
	                   							<li><?php echo $item; ?></li>
	                   						<?php endforeach; ?>
	                   					</ol>
	                   				<?php endif; ?>
	                   			</div>
	                   		</div>
	                    </div>
	                    <div class="mws-form-item">
	                    	<div class="effect-box">
	                    		<div class="effect-box-left2">
	                    		Apply : <br>
	                   			</div>
	                   			<div class="effect-box-right2">
	                   				<?php if(@$promotion->effects['free']['apply'] == "combination"): ?>
	                   					To each combination
	                   					<?php if( ! empty($promotion->effects['free']['combination']['limit'])): ?>
	                   						But limit to <?php echo $promotion->effects['free']['combination']['limit_count']; ?>
	                   					<?php endif; ?>
	                   				<?php else: ?>
	                   					Only once per order
	                   				<?php endif; ?>
	                   			</div>
	                   		</div>
	                    </div>

	                 </div>
                </div>

            	<?php endif; ?>

                <?php if(in_array("free_shipping", $promotion->effects['type'])): ?>

                <div class="mws-form-row">
	            	<div class="mws-form-item-effect border-original">
	                    <label class="mws-form-label" for="Free Shipping">
	                    	Free Shipping
	                    </label>
	                    <div class="mws-form-item">
	                    	<br>
	                    </div>
	                 </div>
                </div>

                <?php endif; ?>

             </fieldset>
            <!-- end Effect -->
            <?php endif; ?>
<!--

            <hr>
            <div class="mws-form-inline">
            <h2>Limitation</h2>

                 <div class="mws-form-row">
	            	<div class="mws-form-item-effect border-original">
	                    <label class="mws-form-label" for="Limitation">&nbsp;</label>
	                    <?php if( ! empty($promotion->limitations['limit_campaign'])): ?>
	                    <div class="mws-form-item">
	                    		<input type="checkbox" name="limitation_type" value="campaign" >&nbsp;Use quota of campaign
	                    </div>
	                	<?php endif; ?>
	                    <div class="mws-form-item">
	                    		<input type="checkbox" name="limitation_type" value="limit_budget" >&nbsp;Limit budget to <input type="text" class="ssmall" name="limit_budget" value="2000" > Baht
	                    </div>
	                    <div class="mws-form-item">
	                    		<input type="checkbox" name="limitation_type" value="limit_item" >&nbsp;Limit items &nbsp;&nbsp;&nbsp;(Product,Brand ที่แถม) <input type="text" class="ssmall" name="limit_item" value="800" > Items
	                    </div>
	                    <div class="mws-form-item">
	                    		<input type="checkbox" name="limitation_type" value="quota_per" >&nbsp;limit to &nbsp;<input type="text" class="sssmall" name="limit_user_per" value="5" >
	                    		 &nbsp;users per&nbsp;<select name="limit_user_per_type"><option>Day,Week,Month</option></select>
	                    </div>
	                    <div class="mws-form-item">
	                    		<input type="checkbox" name="limitation_type" value="quota_of_user" >&nbsp;limit to &nbsp;<input type="text" class="sssmall" name="limit_user_per" value="1" >
	                    		 &nbsp;users per user
	                    </div>
	                 </div>
                </div>

             </div>
  -->

            <div class="mws-button-row clearfix">
                <a onclick="<?php echo URL::to('promotions/view/'.$promotion->campaign_id.'?campaign=true'); ?>" class="btn pull-left">Back</a>
            </div>

        </div>


    </div>

</div>