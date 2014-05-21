	<?php /* <link media="all" type="text/css" rel="stylesheet" href="http://pcms.com/themes/admin/assets/css/form.css"> */ ?>
	<?php input::flash() ?>
	<?php
		/*
		$old = Input::old();
		d($old);
		*/
	?>
	<?php echo Form::open(array('url'=>'orders/search', 'method'=>'get')) ?>
		<div class="mws-form-inline" style="background-color:#DDD;border:1px solid #8C8C8C;overflow:hidden;padding:10px 0px 10px 0px;">
			<div class="mws-form-row" style="float:left;margin-left:20px;">
				<?php echo Form::label('Search by Order Date') ?>
				<div class="mws-form-item">
					<?php echo Form::text('date_start', Input::get('date_start'), array('class'=>'calendar','id'=>'date_start')) ?>
				</div>
			</div>
			<div class="mws-form-row" style="float:left;margin-left:20px;">
				&nbsp;&nbsp;
				<div class="mws-form-item">
					<?php echo Form::text('date_end', Input::get('date_end'), array('class'=>'calendar','id'=>'date_end')) ?>
				</div>
			</div>
			<div class="clear">&nbsp;</div>
			<div class="mws-form-row" style="float:left;margin-left:20px;">
				Order Status
				<div class="mws-form-item">
					<select name="order_status">
						<option value="">เลือก</option>
						<option value="<?php echo Order::STATUS_WAITING ?>" <?php if( Input::get('order_status') == 'draft'){?>selected<?php } ?>>รอลูกค้าชำระเงิน</option>
						<option value="<?php echo Order::STATUS_NEW ?>" <?php if( Input::get('order_status') == 'new'){?>selected<?php } ?>>ลูกค้าชำระเงินแล้ว</option>						
						<option value="<?php echo Order::STATUS_CANCEL ?>" <?php if( Input::get('order_status') == 'cancel'){?>selected<?php } ?>>Cancel</option>
						<option value="<?php echo Order::STATUS_EXPIRE ?>" <?php if( Input::get('order_status') == 'expire'){?>selected<?php } ?>>Expire</option>
						<option value="<?php echo Order::STATUS_PREPARING ?>" <?php if( Input::get('order_status') == 'preparing'){?>selected<?php } ?>>Preparing</option>
						<option value="<?php echo Order::STATUS_PROCESSING ?>" <?php if( Input::get('order_status') == 'processing'){?>selected<?php } ?>>Processing</option>
						<option value="<?php echo Order::STATUS_COMPLETE ?>" <?php if( Input::get('order_status') == 'complete'){?>selected<?php } ?>>Complete</option>

					</select>
				</div>
			</div>
			<div class="clear">&nbsp;</div>
			<div class="mws-form-row" style="float:left;margin-left:20px;">
				Payment Status
				<div class="mws-form-item">
					<select name="payment_status">
						<option value="">เลือก</option>

						<option value="<?php echo Order::PAYMENT_WAITING ?>" <?php if(Input::get('payment_status') == Order::PAYMENT_WAITING){?>selected<?php } ?>><?php echo Order::PAYMENT_WAITING ?></option>

						<option value="<?php echo Order::PAYMENT_SUCCESS ?>" <?php if(Input::get('payment_status') == Order::PAYMENT_SUCCESS){?>selected<?php } ?>><?php echo Order::PAYMENT_SUCCESS ?></option>

						<option value="<?php echo Order::PAYMENT_FAILED ?>" <?php if(Input::get('payment_status') == Order::PAYMENT_FAILED){?>selected<?php } ?>><?php echo Order::PAYMENT_FAILED ?></option>

						<option value="<?php echo Order::PAYMENT_RECONCILE ?>" <?php if(Input::get('payment_status') == Order::PAYMENT_RECONCILE){?>selected<?php } ?>><?php echo Order::PAYMENT_RECONCILE ?></option>

						<option value="<?php echo Order::PAYMENT_EXPIRE ?>" <?php if(Input::get('payment_status') == Order::PAYMENT_EXPIRE){?>selected<?php } ?>><?php echo Order::PAYMENT_EXPIRE ?></option>

					</select>
				</div>
			</div>

			<div class="clear">&nbsp;</div>

			<div class="mws-button-row"  style="float:left;margin-left:20px;padding-top:22px;">
				<?php echo Form::submit('Search', array('class'=>'btn btn-primary')) ?>
				<?php echo Form::reset('Reset', array('class'=>'btn btn-warning', 'onClick'=>'window.location="'.Request::url().'"')) ?>
	        </div>
		</div>
	<?php echo Form::close() ?>
	<div class="clear"></div>

<style type="text/css">
ul.search-condition { list-style-type:none; padding:0 10px; }
ul.search-condition li { display:inline; margin-right:15px; }
</style>
<?php

Theme::asset()->container('macro')->writeScript('product-search-form-script','
$(function(){

	$("#toggleAdvanceOptions").click(function(e){
		e.preventDefault();
		$("#advance_search_options").slideToggle();
	});

	$("input[name=product_allow_installment]").change(function(e){
		$("#including-allow-variant").toggle($("#product_allow_installment-yes").is(":checked"));
	});

	$("#date_start").datepicker(
	{
		dateFormat:"yy-mm-dd",
		maxDate:0
	});

	$("#date_start").change(function()
	{
		$( "#date_end" ).datepicker("destroy");

		var date_start = $("#date_start").val();

		$("#date_end").datepicker(
		{
			dateFormat:"yy-mm-dd",
			minDate:new Date(date_start),
			maxDate:0
		});

	});


})
');

?>