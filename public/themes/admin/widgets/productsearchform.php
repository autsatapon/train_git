	<?php /* <link media="all" type="text/css" rel="stylesheet" href="http://pcms.com/themes/admin/assets/css/form.css"> */ ?>
	<?php input::flash() ?>
	<?php
		/*
		$old = Input::old();
		d($old);
		*/
	?>
	<?php echo Form::open(array('method'=>'get')) ?>
		<div class="mws-form-inline" style="background-color:#DDD;border:1px solid #8C8C8C;overflow:hidden;padding:10px 0px 10px 0px;">
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
			<div class="mws-form-row" style="float:left;margin-left:20px;">
				<?php echo Form::label('tag', Product::getLabel('tag')) ?>
				<div class="mws-form-item">
					<?php echo Form::text('tag', Input::old('tag')) ?>
				</div>
			</div>

			<div class="clear">&nbsp;</div>

			<p style="padding:0 20px;"><a href="#" id="toggleAdvanceOptions">Advance Options</a></p>

			<?php
				if ( (Input::has('has_product_content') && Input::get('has_product_content') != 'all' )
						or (Input::has('has_product_mediacontent') && Input::get('has_product_mediacontent') != 'all' )
						or (Input::has('has_price') && Input::get('has_price') != 'all' )
						or (Input::has('has_variant_mediacontent') && Input::get('has_variant_mediacontent') != 'all' )
						or (Input::has('has_collection') && Input::get('has_collection') != 'all' ) )

				{
					$optionsDisplay = '';
				}
				else
				{
					$optionsDisplay = ' style="display:none;"';
				}
			?>

			<div id="advance_search_options"<?php echo $optionsDisplay ?>>

				<div class="mws-form-row">
					<label class="mws-form-label" style="padding:0 10px;">แสดงสินค้าเฉพาะที่มี collections หรือไม่ ?</label>
					<div class="mws-form-item clearfix">
						<ul class="search-condition mws-form-list inline">
							<li><input type="radio" name="has_collection" id="has_collection_all" value="all" > <label for="has_collection_all">both</label></li>
							<li><input type="radio" name="has_collection" id="has_collection_yes" value="yes" <?php if (Input::old('has_collection') != 'yes' && Input::old('has_collection') != 'no') { echo 'checked="checked"'; }elseif(Input::old('has_collection') == 'yes'){ echo 'checked="checked"'; } ?>> <label for="has_collection_yes">แสดงเฉพาะที่มี collections</label></li>
							<li><input type="radio" name="has_collection" id="has_collection_no" value="no" <?php if (Input::old('has_collection') == 'no') { echo 'checked="checked"'; } ?>> <label for="has_collection_no">แสดงเฉพาะที่ไม่มี collections</label></li>
						</ul>
					</div>
				</div>

				<div class="mws-form-row">
					<label class="mws-form-label" style="padding:0 10px;">Product's Content? (key feature &amp; description)</label>
					<div class="mws-form-item clearfix">
						<ul class="search-condition mws-form-list inline">
							<li><input type="radio" name="has_product_content" id="has_product_content_all" value="all" <?php if (Input::old('has_product_content') != 'yes' && Input::old('has_product_content') != 'no') { echo 'checked="checked"'; } ?>> <label for="has_product_content_all">both</label></li>
							<li><input type="radio" name="has_product_content" id="has_product_content_yes" value="yes" <?php if (Input::old('has_product_content') == 'yes') { echo 'checked="checked"'; } ?>> <label for="has_product_content_yes">product with content</label></li>
							<li><input type="radio" name="has_product_content" id="has_product_content_no" value="no" <?php if (Input::old('has_product_content') == 'no') { echo 'checked="checked"'; } ?>> <label for="has_product_content_no">product without content</label></li>
						</ul>
					</div>
				</div>

				<div class="mws-form-row">
					<label class="mws-form-label" style="padding:0 10px;">Product's Media Content? (image, youtube, and 360&#176; image)</label>
					<div class="mws-form-item clearfix">
						<ul class="search-condition mws-form-list inline">
							<li><input type="radio" name="has_product_mediacontent" id="has_product_mediacontent_all" value="all" <?php if (Input::old('has_product_mediacontent') != 'yes' && Input::old('has_product_mediacontent') != 'no') { echo 'checked="checked"'; } ?>> <label for="has_product_mediacontent_all">both</label></li>
							<li><input type="radio" name="has_product_mediacontent" id="has_product_mediacontent_yes" value="yes" <?php if (Input::old('has_product_mediacontent') == 'yes') { echo 'checked="checked"'; } ?>> <label for="has_product_mediacontent_yes">product with media content</label></li>
							<li><input type="radio" name="has_product_mediacontent" id="has_product_mediacontent_no" value="no" <?php if (Input::old('has_product_mediacontent') == 'no') { echo 'checked="checked"'; } ?>> <label for="has_product_mediacontent_no">product without media content</label></li>
						</ul>
					</div>
				</div>

				<div class="mws-form-row">
					<label class="mws-form-label" style="padding:0 10px;">Product allows installment</label>
					<div class="mws-form-item clearfix">
						<ul class="search-condition mws-form-list inline">
							<li><?php echo Form::radio('product_allow_installment', '', Input::old('product_allow_installment')==false, array('id' => 'product_allow_installment-both')), Form::label('product_allow_installment-both', ' both') ?></li>
							<li><?php echo Form::radio('product_allow_installment', 'yes', Input::old('product_allow_installment')=='yes', array('id' => 'product_allow_installment-yes')), Form::label('product_allow_installment-yes', ' allow') ?></li>
							<li><?php echo Form::radio('product_allow_installment', 'no', Input::old('product_allow_installment')=='no', array('id' => 'product_allow_installment-no')), Form::label('product_allow_installment-no', ' not allow') ?></li>
							<div id="including-allow-variant" style="display:<?php echo Input::old('product_allow_installment')==='yes' ? 'block' : 'none' ?>"><?php echo Form::checkbox('variant_allow_installment', 'yes', Input::old('variant_allow_installment', 'yes')=='yes', array('id' => 'variant_allow_installment')), Form::label('variant_allow_installment', ' including product which any variant allow installment; if product doesn\'t allow') ?></div>
						</ul>
					</div>
				</div>

				<div class="mws-form-row">
					<label class="mws-form-label" style="padding:0 10px;">Product allows COD</label>
					<div class="mws-form-item clearfix">
						<ul class="search-condition mws-form-list inline">
							<li><?php echo Form::radio('product_allow_cod', '', Input::old('product_allow_cod')==false, array('id' => 'product_allow_cod-both')), Form::label('product_allow_cod-both', ' both') ?></li>
							<li><?php echo Form::radio('product_allow_cod', 'yes', Input::old('product_allow_cod')=='yes', array('id' => 'product_allow_cod-yes')), Form::label('product_allow_cod-yes', ' allow') ?></li>
							<li><?php echo Form::radio('product_allow_cod', 'no', Input::old('product_allow_cod')=='no', array('id' => 'product_allow_cod-no')), Form::label('product_allow_cod-no', ' not allow') ?></li>
						</ul>
					</div>
				</div>

				<div class="mws-form-row">
					<label class="mws-form-label" style="padding:0 10px;">Variants' Price?</label>
					<div class="mws-form-item clearfix">
						<ul class="search-condition mws-form-list inline">
							<li><input type="radio" name="has_price" id="has_variants_price_all" value="all" <?php if (Input::old('has_price') != 'yes' && Input::old('has_price') != 'no') { echo 'checked="checked"'; } ?>> <label for="has_variants_price_all">both</label></li>
							<li><input type="radio" name="has_price" id="has_variants_price_yes" value="yes" <?php if (Input::old('has_price') == 'yes') { echo 'checked="checked"'; } ?>> <label for="has_variants_price_yes">all variants of product have price</label></li>
							<li><input type="radio" name="has_price" id="has_variants_price_no" value="no" <?php if (Input::old('has_price') == 'no') { echo 'checked="checked"'; } ?>> <label for="has_variants_price_no">any variant of product doesn't have price</label></li>
						</ul>
					</div>
				</div>

				<?php /*
				<div class="mws-form-row">
					<label class="mws-form-label" style="padding:0 10px;">Variants' Media Content? (image, youtube, and 360&#176; image)</label>
					<div class="mws-form-item clearfix">
						<ul class="search-condition mws-form-list inline">
							<li><input type="radio" name="has_variant_mediacontent" id="has_variant_mediacontent_all" value="all" <?php if (Input::old('has_variant_mediacontent') != 'yes' && Input::old('has_variant_mediacontent') != 'no') { echo 'checked="checked"'; } ?>> <label for="has_variant_mediacontent_all">both</label></li>
							<li><input type="radio" name="has_variant_mediacontent" id="has_variant_mediacontent_yes" value="yes" <?php if (Input::old('has_variant_mediacontent') == 'yes') { echo 'checked="checked"'; } ?>> <label for="has_variant_mediacontent_yes">all variants of product have media content</label></li>
							<li><input type="radio" name="has_variant_mediacontent" id="has_variant_mediacontent_no" value="no" <?php if (Input::old('has_variant_mediacontent') == 'no') { echo 'checked="checked"'; } ?>> <label for="has_variant_mediacontent_no">any variant of product doesn't have media content</label></li>
						</ul>
					</div>
				</div>
				*/ ?>

			</div>

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
})
');

?>