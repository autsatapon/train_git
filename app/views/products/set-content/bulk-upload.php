<script type="text/template" id="media-content-template">
<div class="content content-<%= mode %>" data-mode="<%= mode %>" data-thumb="<%= thumb %>" data-path="<%= src %>" data-id="<%= media_id %>">
	<div class="content-tag"><%= mode %></div>
	<img src="<%= thumb %>"/>
	<div class="content-menu">
		<a href="#move-to-trash" class="trash-content"><i class="icon icon-trash"></i></a>
	</div>
</div>
</script>

<?php

$themeAsset = Theme::asset();

$themeAsset->add('underscore', URL::to('/js/underscore-1.5.1.min.js'));
$themeAsset->add('backbone', URL::to('/js/backbone-1.0.0.min.js'), 'underscore');
$themeAsset->add('dropzone.js', URL::to('/js/dropzone-2.0.min.js'), 'jquery');

$themeAsset->writeStyle('media-list-css',"
	.media-list { border:1px solid #bbb; min-height:154px; padding:0px; border-radius:0 0 4px 4px; }
	.media-list .content { display:block; border-style:solid; border-width:3px; border-radius:3px; float:left; margin:4px 0 4px 5px; position:relative; width:150px; height:150px; }
	.media-list .content .content-tag { position:absolute; top:-2px; left:-2px; font-size:0.8em; color:#FFF; padding:2px; border-radius:3px; z-index:2; }
	.media-list .content .thumb { position:absolute; top:0; right:0; bottom:0; left:0; margin:auto; max-width:146px; max-height:146px; border:2px solid #fff; border-radius:3px; }
	.media-list .content .content-status { position:absolute; top:-2px; right:-2px; padding:2px; border-radius:3px; }

	.content-placeholder { display:block; border:3px #FCEFA1 solid; border-radius:3px; float:left; margin:4px 0 4px 5px; position:relative; width:150px; height:150px; background-color:#FCEFA1; }
	.content-image { border-color:#4386BC; }
	.content-image .content-tag, .content-image .primary-tag, .content-image .content-menu, .content-image .content-status { background-color:#4386BC; }
	.content-youtube { border-color:#CA4E4E; }
	.content-youtube .content-tag, .content-youtube .primary-tag, .content-youtube .content-menu, .content-youtube .content-status { background-color:#CA4E4E; }
	.content-360 { border-color:#96C742; }
	.content-360 .content-tag, .content-360 .primary-tag, .content-360 .content-menu, .content-360 .content-status { background-color:#96C742; }
	.primary-tag { position:absolute; bottom:0; color:#FFF; width:100%; text-align:center; }
	.content-menu { display:none; position:absolute; top:0; right:0; padding:0 4px 2px; }
	.content:hover .content-menu { display:block; }
	.content-status { position:absolute; top:0; right:0; padding:0 4px 2px; }

	.media-box { border:1px solid #bbb; background-color:#EEEEEE; border-bottom:none; padding:4px; border-radius:4px 4px 0 0; min-height:30px; }
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

$themeAsset->add('jsxtransformer', URL::to('/js/JSXTransformer-0.4.1.js'));
$themeAsset->add('react', URL::to('/js/react-0.4.1.min.js'));
?>

<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span><?php echo Theme::place('title') ?></span>
	</div>
	<div class="mws-panel-body no-padding">
		<div class="mws-form row clearfix">
			<div id="bulk-upload-component" class="mws-form-row">
			</div>
		</div>
	</div>
</div>

<?php
$themeAsset->usePath()->add('bulk-react', 'js/product/bulk-upload.js', array('jsxtransformer','react'), array('type'=>'text/jsx'));
?>