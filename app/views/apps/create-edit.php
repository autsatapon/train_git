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

        <form method="post" action="" class="mws-form">
            <div class="mws-form-inline">
                <div class="mws-form-row">
                    <label class="mws-form-label" for="name"><?php echo PApp::getLabel('name') ?></label>
                    <div class="mws-form-item">
                        <input type="text" class="small" name="name" id="name" value="<?php echo Input::old('name', $formData['name']) ?>">
                    </div>
                </div>

                <div class="mws-form-row">
                    <label class="mws-form-label" for="url"><?php echo PApp::getLabel('url') ?></label>
                    <div class="mws-form-item">
                        <input type="text" class="small" name="url" id="url" value="<?php echo Input::old('url', $formData['url']) ?>">
                    </div>
                </div>

                <div class="mws-form-row">
                    <label class="mws-form-label" for="foreground_url"><?php echo PApp::getLabel('foreground_url') ?></label>
                    <div class="mws-form-item">
                        <input type="text" class="small" name="foreground_url" id="foreground_url" value="<?php echo Input::old('foreground_url', $formData['foreground_url']) ?>">
                    </div>
                </div>

                <div class="mws-form-row">
                    <label class="mws-form-label" for="stock_code"><?php echo PApp::getLabel('stock_code') ?></label>
                    <div class="mws-form-item">
                        <input type="text" class="pcms-numeric" style="text-align:left" name="stock_code" id="stock_code" value="<?php echo Input::old('stock_code', array_get($formData, 'stock_code', 322963)) ?>">
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="nonstock_code"><?php echo PApp::getLabel('nonstock_code') ?></label>
                    <div class="mws-form-item">
                        <input type="text" class="pcms-numeric" style="text-align:left" name="nonstock_code" id="nonstock_code" value="<?php echo Input::old('nonstock_code', array_get($formData, 'nonstock_code', 320697)) ?>">
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="accessible_ips"><?php echo PApp::getLabel('accessible_ips') ?></label>
                    <div class="mws-form-item">
                        <textarea name="accessible_ips" class="small"><?php echo Input::old('accessible_ips', $formData['accessible_ips']) ?></textarea>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="note">Note</label>
                    <div class="mws-form-item">
                        <textarea name="note" class="small"><?php echo Input::old('note', $formData['note']) ?></textarea>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="free_shipping"><?php echo PApp::getLabel('free_shipping'); ?></label>
                    <div class="mws-form-item">
                        <?php echo Form::select('free_shipping', array('disabled' => 'disabled', 'enabled' => 'enabled'), Input::old('free_shipping', $formData['free_shipping'])); ?>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="max_cc_per_user"><?php echo PApp::getLabel('max_cc_per_user'); ?></label>
                        <div class="mws-form-item">
                            <input type="text" class="pcms-numeric" style="text-align:left" name="max_cc_per_user" id="max_cc_per_user" value="<?php echo $formData['max_cc_per_user'] ?>">
                        </div>
                </div>
            </div>

            <div class="mws-button-row">
                <input type="submit" class="btn btn-primary" value="Save">
            </div>
        </form>

    </div>
</div>
