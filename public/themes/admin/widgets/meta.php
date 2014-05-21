<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span class="pull-left"><?php echo $app->name; ?></span>
        <span class="pull-right">
            <a href="<?php echo URL::to('metas/create/'.$app->id.'/'.$model.'/'.$content->id); ?>" class="various fancybox.iframe">
                <i class="icon-plus"></i>
            </a>
        </span>
    </div>
    <div class="mws-panel-body no-padding">
        <form class="mws-form" role="form">
            <div class="mws-form-inline">
                <div class="mws-form-row" id="meta-app-<?php echo $app->id; ?>">
                    <?php foreach ($content->metadatas()->whereAppId($app->id)->get() as $metadata) : ?>
                    <div class="mws-form-cols node">
                        <div class="mws-form-col-2-8">
                            <strong><?php echo $metadata->key; ?></strong>
                        </div>
                        <div class="mws-form-col-2-8">
                            <span id="meta-id-<?php echo $metadata->id; ?>" style="display:inline-block; width:600px; word-wrap:break-word;">
                                <?php echo ($metadata->key == 'upload') ? HTML::image($metadata->value) : $metadata->value; ?>
                            </span>
                        </div>
                        <span class="pull-right">
                        	<a href="<?php echo URL::to('metas/edit/'.$metadata->id); ?>" class="various fancybox.iframe"><?php echo __('Edit'); ?></a> |
                        	<a href="<?php echo URL::to('metas/ajax-delete/'.$metadata->id); ?>" class="ajax-delete"><?php echo __('Delete'); ?></a>
                    	</span>
                    </div>

                    <?php endforeach; ?>
                </div>
            </div>
        </form>
    </div>
</div>