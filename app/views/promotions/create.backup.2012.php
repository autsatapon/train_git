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
			 line-height:35px;
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
			margin-top:10px;
			font-size: 14px;
    		line-height: 40px;
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
			padding-top:20px;
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
		');

?>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span>Create Promotion</span>
    </div>
    <div class="mws-panel-body no-padding">


        <?php echo HTML::message(); ?>

        <form method="post" action="" class="mws-form" enctype="multipart/form-data">
            <div class="mws-form-inline">
            	<h2>Create Promotion</h2>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="Type">Type</label>
                    <div class="mws-form-item">
                    	<select name="type">
						  <option value="">Flash Sale</option>
						</select>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="name">Name</label>
                    <div class="mws-form-item">
                        <input type="text" class="small" name="name" id="name" value="<?php echo Input::old('name') ?>">
                    </div>
                </div>
                 <div class="mws-form-row">
                    <label class="mws-form-label" for="period">Period</label>
                    <div class="mws-form-item">
	                   <input type="text" id="start_datepicker"  readonly class="ssmall" name="start_date"  value="<?php echo Input::old('start_date') ?>">
	                   To <input type="text" id="end_datepicker" readonly class="ssmall" name="end_date" id="end_date" value="<?php echo Input::old('end_date') ?>">
                    </div>
                </div>
				<div class="mws-form-row">
                    <label class="mws-form-label" for="code">Code</label>
                    <div class="mws-form-item">
                        <input type="text" class="small" name="code" id="code" value="<?php echo Input::old('code') ?>">
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="detail">Detail</label>
                    <div class="mws-form-item">

                        <?php echo Form::ckeditor('detail', Input::old('detail'), array('id' => 'detail', 'class' => 'form-control', 'height' => '150px')); ?>
                        <?php echo Form::transCkeditor(null, 'detail', array('class' => 'form-control', 'height' => '150px')) ?>

                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="note">Note</label>
                    <div class="mws-form-item">

                        <textarea class="tarea" name="note"><?php echo Input::old('note') ?></textarea>

                    </div>
                </div>
            </div>

            <!-- start Condition -->
            <hr>
            <div class="mws-form-inline">
            <h2>Condition</h2>
	            <div class="mws-form-row">
	                    <label class="mws-form-label" for="Condition">&nbsp;</label>
	                    <div class="mws-form-item-condition border">
	                        <a class="btn btn-default" href="#"><i class="icon-plus"></i> Add Condition (And)</a> <br>
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
		                               <!-- example1 -->
		                               <input type="text" class="ssssmall" name="count_char_code" value="1" readonly="readonly">
		                               <input type="text" class="small" name="custom_code_text" id="custom_code_text" value="<= นับเลขของ Unique Code">
		                               <i class="icon-ok-circle"></i>
		                                <!-- example2 -->
		                               <br><input type="text" class="ssssmall" name="count_char_code" value="1" readonly="readonly">
		                               <input type="text" class="small" name="custom_code_text" id="custom_code_text" value="">
		                               <i class="icon-ok-circle"></i>
		                               <!-- example3 -->
		                               <br><input type="text" class="ssssmall" name="count_char_code" value="1" readonly="readonly">
		                               <input type="text" class="small" name="custom_code_text" id="custom_code_text" value="">
		                               <i class="icon-ban-circle"></i> This code is not avaliable.
		                               <br><br>
		                               <!-- example4 -->
		                               <input type="text" class="small" name="custom_code_text" id="custom_code_text" value="ถ้าเป็น Single ไม่ต้องนับเลข"><i class="icon-ok-circle"></i>
		                        </div>
	                        </div>

	                    </div>
	            </div>
	        </div>
            <!-- end Condition -->

            <!-- start Effect -->
            <hr>
            <div class="mws-form-inline">
            <h2>Effect</h2>
	            <div class="mws-form-row">
	            	<div class="mws-form-item-effect border-original">
	                    <label class="mws-form-label" for="discount">
	                    	<input type="radio" name="effect_type" value="discount">&nbsp;Discount
	                    </label>
	                    <div class="mws-form-item">
	                    	<div class="effect-box">
	                    		<div class="effect-box-left">
	                    		<input type="radio" name="discount_type" value="price"> Price&nbsp;&nbsp;&nbsp;&nbsp; <input type="text" class="small" name="custom_code_text" id="custom_code_text" value="200"> Baht<br>
	                    		<input type="radio" name="discount_type" value="percent"> Percent <input type="text" class="small" name="custom_code_text" id="custom_code_text" value="10"> %<br>
	                   			</div>
	                   			<div class="effect-box-center">
	                   			    <strong>ON</strong>
	                   			</div>
	                   			<div class="effect-box-right">
	                   				<input type="radio" name="discount_type_extra" value="same_product"> Same Product<br>
	                   				<input type="radio" name="discount_type_extra" value="following"> The following Item &nbsp;<a href="#" title="">Choose</a><br>
	                   				<input type="radio" name="discount_type_extra" value="cart"> cart
	                   			</div>
	                   		</div>
	                    </div>
	                 </div>
                </div>

                <div class="mws-form-row">
	            	<div class="mws-form-item-effect border-original">
	                    <label class="mws-form-label" for="Free">
	                    	<input type="radio" name="effect_type" value="free">&nbsp;Free
	                    </label>
	                    <div class="mws-form-item">
	                    	<div class="effect-box">
	                    		<div class="effect-box-left">
	                    		<input type="text" class="small" name="free_number" id="free_number" value="1"> <br>
	                   			</div>
	                   			<div class="effect-box-center">
	                   			    <strong>Of</strong>
	                   			</div>
	                   			<div class="effect-box-right">
	                   				<input type="radio" name="free_type" value="same_product"> Same Product<br>
	                   				<input type="radio" name="free_type" value="following"> The following Item &nbsp;<a href="#" title="">Choose</a><br>
	                   			</div>
	                   		</div>
	                    </div>
	                    <div class="mws-form-item">
	                    	<div class="effect-box">
	                    		<div class="effect-box-left2">
	                    		Apply : <br>
	                   			</div>
	                   			<div class="effect-box-right2">
	                   				<input type="radio" name="apply_type" value="combination"> To each combination
	                   				<input type="checkbox" name="apply_type_extra" value="apply_type_extra"> But limit to
	                   				<input type="text" class="sssmall" name="apply_type_extra_number" id="apply_type_extra_number" value="3"> Times per order<br>
	                   				<input type="radio" name="apply_type" value="once"> Only once per order
	                   			</div>
	                   		</div>
	                    </div>

	                 </div>
                </div>

                 <div class="mws-form-row">
	            	<div class="mws-form-item-effect border-original">
	                    <label class="mws-form-label" for="Free Shipping">
	                    	<input type="radio" name="effect_type" value="free_shipping">&nbsp;Free Shipping
	                    </label>
	                    <div class="mws-form-item">
	                    		<br>
	                    </div>
	                 </div>
                </div>

             </div>
            <!-- end Effect -->

            <!-- start limitation -->
            <hr>
            <div class="mws-form-inline">
            <h2>Limitation</h2>

                 <div class="mws-form-row">
	            	<div class="mws-form-item-effect border-original">
	                    <label class="mws-form-label" for="Limitation">&nbsp;</label>
	                    <div class="mws-form-item">
	                    		<input type="checkbox" name="limitation_type" value="campaign" >&nbsp;Use quota of campaign
	                    </div>
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
            <!-- end limitation -->

            <div class="mws-button-row">
                <input type="submit" class="btn btn-primary btn-large" value="Save">
            </div>

        </form>


    </div>
	<script>
	$(function() {

		$('#start_datepicker').datepicker({
			dateFormat: 'yy-mm-dd',
		});
		$('#end_datepicker').datepicker({
			dateFormat: 'yy-mm-dd',
		});
	});
	</script>
</div>
