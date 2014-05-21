<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><?php echo Theme::place('title') ?></span>
    </div>
    <div class="mws-panel-body no-padding">

        <?php if ($errors->count() > 0) { ?>
            <div class="alert alert-error">
                <?php foreach ($errors->all() as $error) { ?>
                    <p><?php echo $error ?></p>
                <?php } ?>
            </div>
        <?php } ?>

        <form method="post" action="" class="mws-form" enctype="multipart/form-data">
            <div class="mws-form-inline">
                <div class="mws-form-row">
                    <label class="mws-form-label" for="name"><?php echo Collection::getLabel('name') ?></label>
                    <div class="mws-form-item">
                        <input type="text" class="small" name="name" id="name" value="<?php echo Input::old('name', $formData['name']) ?>">
                        <?php echo Form::transText($collection, 'name', array('class' => 'small')) ?>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="slug"><?php echo Collection::getLabel('slug') ?></label>
                    <div class="mws-form-item">
                        <input type="text" class="small" name="slug" id="slug" value="<?php echo Input::old('slug', $formData['slug']) ?>">
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label">Is Category</label>
                    <div class="mws-form-item">
                        <ul class="mws-form-list">
                            <li>
                                <label>
                                    <?php echo Form::checkbox('is_category', '1', Input::old('is_category', $formData['is_category'])) ?> Category
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label"><?php echo Collection::getLabel('publish_for') ?></label>
                    <div class="mws-form-item">
                        <ul class="mws-form-list">
                            <?php foreach ($apps as $key => $app) { ?>
                                <li>
                                    <label>
                                        <?php if ( !in_array($app->id, $disableApps) ) { ?>
                                            <input type="checkbox" name="<?php echo "app[{$app->id}]" ?>" value="<?php echo $app->id ?>"<?php if ( in_array($app->id, $inputApp) ) { echo ' checked="checked"'; } ?>> <?php echo $app->name ?>
                                        <?php } else { ?>
                                            <input type="checkbox" name="<?php echo "app[{$app->id}]" ?>" value="<?php echo $app->id ?>" disabled="disabled"> <span style="text-decoration:line-through;"><?php echo $app->name ?></span>
                                        <?php } ?>
                                    </label>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <hr>
                <?php /*
                <div class="mws-form-row">
                    <label class="mws-form-label" for="meta_title"><?php echo Collection::getLabel('meta_title') ?></label>
                    <div class="mws-form-item">
                        <input type="text" class="small" name="meta_title" id="meta_title" value="<?php echo Input::old('meta_title', $formData['meta_title']) ?>">
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="meta_keywords"><?php echo Collection::getLabel('meta_keywords') ?></label>
                    <div class="mws-form-item">
                        <input type="text" class="small" name="meta_keywords" id="meta_keywords" value="<?php echo Input::old('meta_keywords', $formData['meta_keywords']) ?>">
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="meta_description"><?php echo Collection::getLabel('meta_description') ?></label>
                    <div class="mws-form-item">
                        <textarea class="small" name="meta_description" id="meta_description"><?php echo Input::old('meta_description', $formData['meta_description']) ?></textarea>
                    </div>
                </div>
                */ ?>
                <div class="mws-form-row">
                    <label class="mws-form-label">Image</label>
                    <div class="mws-form-item">
                        <input type="file" name="image" class="small">
                    </div>
                </div>
                <?php //d($collectionImageThumb); ?>
                <?php if( isset($collectionImageThumb) ) { ?>
                    <div class="mws-form-row">
                        <label class="mws-form-label" for="brandlogoimg"></label>
                        <div class="mws-form-item small" style="width:150px;">
                           <img src="<?php echo $collectionImageThumb ?>">
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div class="mws-button-row">
                <input type="submit" class="btn btn-primary" value="Save">
            </div>
        </form>

    </div>
</div>

<?php if(isset($collection)) foreach ($apps as $app) : ?>
<?php echo Theme::widget('meta', array('app' => $app, 'content' => $collection))->render(); ?>
<?php endforeach; ?>