<script type="text/template" id="media-content-template">
<div class="content content-<%= mode %>" data-mode="<%= mode %>" data-thumb="<%= thumb %>" data-path="<%= src %>" data-id="<%= media_id %>">
	<div class="content-tag"><%= mode %></div>
	<img src="<%= thumb %>"/>
	<div class="content-menu">
		<% if(mode==='youtube') { %>
		<a href="<%= link %>" target="_blank"><i class="icon icon-facetime-video"></i></a>
		<% } %>
		<a href="#move-to-trash" class="trash-content"><i class="icon icon-trash"></i></a>
	</div>
</div>
</script>

<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span><?php echo Theme::place('title') ?></span>
	</div>
	<div class="mws-panel-body no-padding">

		<?php if ( !$revisions->isEmpty() ) { ?>
            <?php $revision = $revisions->first() ?>
            <div class="alert alert-warning">
                <p>Warning: You have a draft of this product !<br> <a href="<?php echo URL::to("products/approve/detail/{$product->id}/{$revision->id}") ?>">View Detail</a></p>
            </div>
        <?php } ?>

		<?php if ($errors->count() > 0) { ?>
			<div class="alert alert-error">
				<?php foreach ($errors->all() as $error) { ?>
					<p><?php echo $error ?></p>
				<?php } ?>
			</div>
		<?php } elseif (Session::has('successMsg')) { ?>
			<div class="alert alert-success">
				<p><?php echo Session::get('successMsg') ?></p>
			</div>
		<?php } ?>


		<div class="mws-form-inline">
			<form id="product-content-form" method="post" class="mws-form" enctype="multipart/form-data">
				<div class="tab-container">
					<div class="mws-form-row">
						<h3><?php echo $product->title ?></h3>
						<div><?php echo Product::getLabel('brand') ?>: <?php echo $product->brand->name ?></div>
					</div>

					<ul>
						<li><a href="#information">Information</a></li>
						<li><a href="#media-content">Media Content</a></li>
					</ul>

					<div id="information">
						<div class="mws-form-row">
							<label class="mws-form-label" for="url"><?php echo Product::getLabel('key_feature') ?></label>
							<div class="mws-form-item">
								<?php echo Form::ckeditor('key_feature', $product->key_feature, array('model' => $product) ) ?>
								<?php echo Form::transCkeditor($product, 'key_feature', array('row' => '3', 'cols' => '53', 'class' => 'form-control')) ?>
							</div>
						</div>

						<div class="mws-form-row">
							<label class="mws-form-label" for="description"><?php echo Product::getLabel('description') ?></label>
							<div class="mws-form-item">
								<?php echo Form::ckeditor('description', $product->description, array('model' => $product) ) ?>
								<?php echo Form::transCkeditor($product, 'description', array('row' => '3', 'cols' => '53', 'class' => 'form-control')) ?>
							</div>
						</div>

					</div>

					<div id="media-content">

						<div class="mws-form-row media-row product-row" data-type="product" data-id="<?php echo $product->id ?>">
							<div class="row">
								<h3><?php echo $product->title ?></h3>
								<p><?php echo Product::getLabel('pkey'),': ',$product->pkey ?></p>
							</div>

							<div class="row media-list clearfix">
							<?php foreach($product->mediaContents as $mediaContent): ?>
								<div data-mode="<?php echo $mediaContent->mode ?>" data-thumb="<?php echo $mediaContent->thumbnail ?>" data-path="<?php echo $mediaContent->image ?>" data-id="<?php echo $mediaContent->id ?>" data-link="<?php if($mediaContent->meta != false) { $meta = json_decode($mediaContent->meta); echo $meta->link; } ?>"></div>
							<?php endforeach ?>
							</div>
							<div class="media-console" data-type="product" data-id="<?php echo $product->id ?>">
							</div>
						</div>

						<?php if (count($product->styleTypes)>0): ?>
						<div class="mws-form-row media-row variant-row">
						<?php if (count($product->styleTypes)==1): ?>
							<h4><?php echo $product->styleTypes->first()->name ?></h4>
						<?php else: ?>
							<?php foreach ($product->styleTypes as $styleType): ?>
							<div>
								<h4>
								<?php if ($styleType->pivot->media_set == 1): ?>
									<?php echo $styleType->name ?>
									<a href="javascript:void(0);selectMediaSet()" style="color:#006699;font-size:12px;font-weight:normal;">change</a>
								<?php endif ?>
								</h4>
							</div>
							<?php endforeach ?>
							<div id="media-set-area" style="display:none">
								<?php $k = 0; foreach ($product->styleTypes as $styleType): ?>
									<?php if ($styleType->pivot->media_set != 1): ?>
										<input type="radio" name="media-set" id="style-type-<?php echo $styleType->id?>" value="<?php echo $styleType->id ?>" <?php echo ($k++==0 ? 'checked="checked"' : null) ?>> <label for="style-type-<?php echo $styleType->id ?>"><?php echo $styleType->name ?></label>
									<?php endif ?>
								<?php endforeach ?>
								<input type="button" class="btn btn-mini btn-primary" value="Select" id="set-media-set-btn">
								<!-- <div class="label label-warning">Warning: by selecting this will delete all previous media of current style</div> -->
							</div>
						<?php endif ?>
						</div>

						<?php foreach($productStyleOptions as $productStyleOption): ?>
						<div class="mws-form-row media-row variant-row" data-type="productStyleOption" data-id="<?php echo $productStyleOption->id ?>">
							<div class="row">
								<h4><?php echo $productStyleOption->text ?></h4>
							</div>

							<div class="row media-list clearfix">
							<?php foreach($productStyleOption->mediaContents as $mediaContent): ?>
								<div data-mode="<?php echo $mediaContent->mode ?>" data-thumb="<?php echo $mediaContent->thumbnail ?>" data-path="<?php echo $mediaContent->image ?>" data-id="<?php echo $mediaContent->id ?>"></div>
							<?php endforeach ?>
							</div>
							<div class="media-console" data-type="productStyleOption" data-id="<?php echo $productStyleOption->id ?>">
							</div>
						</div>
						<?php endforeach; ?>

						<?php endif; ?>

						<?php /* foreach($product->variants as $variant): ?>
						<div class="mws-form-row media-row variant-row" data-type="variant" data-id="<?php echo $variant->id ?>">
							<div class="row">
								<h4><?php echo $variant->title ?></h4>
								<p><?php echo ProductVariant::getLabel('inventory_id'),': ',$variant->inventory_id ?></p>
							</div>

							<div class="row media-list clearfix">
							<?php foreach($variant->mediaContents as $mediaContent): ?>
								<div data-mode="<?php echo $mediaContent->mode ?>" data-thumb="<?php echo $mediaContent->thumbnail ?>" data-path="<?php echo $mediaContent->image ?>" data-id="<?php echo $mediaContent->id ?>"></div>
							<?php endforeach ?>
							</div>
							<div class="media-console" data-type="variant" data-id="<?php echo $variant->id ?>">
							</div>
						</div>
						<?php endforeach; */ ?>

					</div>
				</div>

				<div class="mws-button-row">
					<input type="submit" class="btn btn-primary" value="Save">
				</div>
			</form>
		</div>

	<form id="media-set-form" method="post" action="<?php echo URL::to('products/set-content/select-media-set/'.$product->id) ?>">
		<input type="hidden" name="media-style-type" id="selected-media-style-type">
	</form>
	</div>
</div>

<?php

$themeAsset = Theme::asset();

$themeAsset->usePath()->add('jqueryui-css', 'css/smoothness/jquery-ui-1.10.3.custom.min.css');
$themeAsset->container('footer')->usePath()->add('jui', 'jq-ui/jquery-ui-1.10.3.custom.min.js');
$themeAsset->container('footer')->writeScript('tab & sort', "
	$('.tab-container').tabs();
	$('.media-list').sortable({
		placeholder: 'content-placeholder',
		distance: 25,
		update: function(e,ui){
			var p = $(this).parents('.media-row');
			mediaConsoles[p.data('type')+'-'+p.data('id')].dirt();
		}
	});
", 'jqueryui');

$themeAsset->add('underscore', URL::to('/js/underscore-1.5.1.min.js'));
$themeAsset->add('backbone', URL::to('/js/backbone-1.0.0.min.js'));
$themeAsset->container('footer')->writeScript('backbone-script', "
var Workspace = Backbone.Router.extend({
	routes: {
		'': 'showtabInformation',
		'information': 'showtabInformation',
		'media-content': 'showtabMediaContent'
	},

	showtabInformation: function(){
		$('.tab-container').tabs({ active:0 });
	},

	showtabMediaContent: function(){
		$('.tab-container').tabs({ active:1 });
	}
});

$(function(){
  var ws = new Workspace();
  Backbone.history.start();
});

");

$themeAsset->add('dropzone.js', URL::to('/js/dropzone-2.0.min.js'), 'jquery');

$themeAsset->writeStyle('media-list-css',"
	.product-row { border:1px solid #aaa; border-radius:4px 4px 0 0; }
	.variant-row { border:1px dashed #aaa; border-top:none; }
	.variant-row:last-child { border-radius:0 0 4px 4px; }
	.media-list { border:1px dashed #bbb; min-height:154px; padding:0px; border-radius:4px 4px 0 0; }
	.media-list .content { display:block; border-style:solid; border-width:3px; border-radius:3px; float:left; margin:4px 0 4px 5px; position:relative; }
	.media-list .content .content-tag { position:absolute; top:-2px; left:-2px; font-size:0.8em; color:#FFF; position:absolute; padding:2px; border-radius:3px; }

	.content-placeholder { display:block; border:3px #FCEFA1 solid; border-radius:3px; float:left; margin:4px 0 4px 5px; position:relative; width:150px; height:150px; background-color:#FCEFA1; }
	.content-image { border-color:#4386BC; }
	.content-image .content-tag, .content-image .primary-tag, .content-image .content-menu { background-color:#4386BC; }
	.content-youtube { border-color:#CA4E4E; }
	.content-youtube .content-tag, .content-youtube .primary-tag, .content-youtube .content-menu { background-color:#CA4E4E; }
	.content-360 { border-color:#96C742; }
	.content-360 .content-tag, .content-360 .primary-tag, .content-360 .content-menu { background-color:#96C742; }
	.primary-tag { position:absolute; bottom:0; color:#FFF; width:100%; text-align:center; }
	.content-menu { display:none; position:absolute; top:0; right:0; padding:0 4px 2px; }
	.content:hover .content-menu { display:block; }

	.media-box { border:1px dashed #bbb; background-color:#EEEEEE; border-top:none; padding:4px; border-radius:0 0 4px 4px; }
	.media-box a { color:#FFF; }
	.media-input { min-height:40px; line-height:35px; }
	.media-input .dropZone {  margin:2px 2px 5px; background-color:#FFF; padding-left:4px; line-height:100px; border-radius:4px; }

	.image-input { color:#1240AB; }
	.input-360 { color:#569700; }
	.close-input-btn { margin:3px; }

	.dropzone-thumb { background-color:#FFF; line-height:60px; }
	.dropzone-data { margin-left:4px; height:60px; background-color:#FFF; padding:0 10px; border-radius:3px; }
	.dz-thumbnail { width:60px; vertical-align:middle; }
");

$themeAsset->writeScript('media-set', '
	function selectMediaSet() {
		$("#media-set-area").toggle();
	}

	function setMediaSet() {
		$("#selected-media-style-type").val($("#media-set-area input[type=radio]:checked").val());
		$("#media-set-form").submit();
	}

	$(document).on("click","#set-media-set-btn",function(e){
		confirm("All previous media of current style will be deleted. This action cannot be undone.\nAre you sure you want to continue?")
		&& setMediaSet();
	})
');

$themeAsset->add('jsxtransformer', URL::to('/js/JSXTransformer-0.4.1.js'));
$themeAsset->add('react', URL::to('/js/react-0.4.1.min.js'));
$themeAsset->usePath()->add('media-content-react', 'js/product/media-content.js', array('jquery','jui','dropzone','jsxtransformer','react','media-content-template'), array('type'=>'text/jsx'));
?>