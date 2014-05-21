<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><?php echo Theme::place('title') ?> - <?php echo $pcmsApp->name ?> App</span>
    </div>
    <div class="mws-panel-body no-padding">

        <?php if ($errors->count() > 0) { ?>
            <div class="alert alert-error">
                <?php foreach ($errors->all() as $error) { ?>
                    <p><?php echo $error ?></p>
                <?php } ?>
            </div>
        <?php } ?>

        <form method="post" action="" class="mws-form">
            <div class="mws-form-inline">
                <div class="mws-form-row">
                    <label class="mws-form-label" for="code">Shop ID (Code)</label>
                    <div class="mws-form-item">
                        <input type="text" class="small" name="code" id="code" value="<?php echo Input::old('code', $formData['code']) ?>">
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="name">Shop Name</label>
                    <div class="mws-form-item">
                        <input type="text" class="small" name="name" id="name" value="<?php echo Input::old('name', $formData['name']) ?>">
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="note">Note</label>
                    <div class="mws-form-item">
                        <textarea name="note" class="small"><?php echo Input::old('note', $formData['note']) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="mws-button-row">
                <input type="submit" class="btn btn-primary btn-large" value="<?php echo Theme::place('title') ?>">
            </div>
        </form>

    </div>
</div>
