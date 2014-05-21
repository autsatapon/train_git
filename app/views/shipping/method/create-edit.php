<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><?php echo Theme::place('title'); ?></span>
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
                    <label class="mws-form-label" for="name"><?php echo ShippingMethod::getLabel('name'); ?></label>
                    <div class="mws-form-item">
                        <input type="text" class="small" name="name" id="name" value="<?php echo Input::old('name', $formData['name']); ?>">
                    </div>
                </div>
                
                <div class="mws-form-row">
                    <label class="mws-form-label">Shipping To</label>
                    <div class="mws-form-item">
                        <ul class="mws-form-list">
							<?php foreach ($deliveryAreas as $area) { ?>
                            <li><input type="checkbox" name="delivery_area_id[]" id="method_area_<?php echo $area->id ?>" value="<?php echo $area->id ?>" <?php if (isset($formData['delivery_area']) && in_array($area->id, $formData['delivery_area'])) { echo ' checked="checked"'; } ?>> <label for="method_area_<?php echo $area->id ?>"><?php echo $area->name ?></label></li>
							<?php } ?>
                        </ul>
                    </div>
                </div>
                
                <div class="mws-form-row">
					<?php echo Form::label('allow_nonstock', ShippingMethod::getLabel('allow_nonstock'), array('class'=>'mws-form-label')) ?>
                    <div class="mws-form-item">
						<?php echo Form::select('allow_nonstock', array('1' => 'Yes', '0' => 'No'), Input::old('allow_nonstock', $formData['allow_nonstock'])) ?>
                    </div>
                </div>
                
                <div class="mws-form-row bordered">
					<?php echo Form::label('always_with', ShippingMethod::getLabel('always_with'), array('class'=>'mws-form-label')) ?>
                    <div class="mws-form-item">
                    <?php foreach ($all_methods as $method): ?>
                        <div>
                            <label>
                                <?php echo Form::checkbox('always_with[]', $method->getKey(), in_array($method->getKey(), $formData['always_with'])); ?>
                                <?php echo $method->name; ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="mws-form-row">
                    <label class="mws-form-label" for="max_weight"><?php echo ShippingMethod::getLabel('max_weight'); ?></label>
                    <div class="mws-form-item">
                        <input type="text" class="pcms-numeric" name="max_weight" id="max_weight" value="<?php echo Input::old('max_weight', $formData['max_weight']/1000);?>" style="text-align: left !important;" size="4"> kg
                    </div>
                </div>
                
                <div class="mws-form-row">
                    <label class="mws-form-label" for="dimension">Max Dimension</label>
                    <div class="mws-form-item">
                        <input type="text" class="pcms-numeric appendedInput" name="dimension_max" id="dimension_max" value="<?php echo Input::old('dimension_max', $formData['dimension_max']); ?>" style="text-align: left !important;  margin-right: 10px;" size="4"><span class="add-on">cm</span> x&nbsp; 
                        <input type="text" class="pcms-numeric appendedInput" name="dimension_mid" id="dimension_mid" value="<?php echo Input::old('dimension_mid', $formData['dimension_mid']); ?>" style="text-align: left !important;  margin-right: 10px;" size="4"><span class="add-on">cm</span> x&nbsp;
                        <input type="text" class="pcms-numeric appendedInput" name="dimension_min" id="dimension_min" value="<?php echo Input::old('dimension_min', $formData['dimension_min']); ?>" style="text-align: left !important;  margin-right: 10px;" size="4"><span class="add-on">cm</span> 
                    </div>
                </div>
                
                <div class="mws-form-row">
                    <label class="mws-form-label" for="description"><?php echo ShippingMethod::getLabel('description'); ?></label>
                    <div class="mws-form-item">
                        <textarea name="description" id="description" class="small"><?php echo Input::old('description', $formData['description']); ?></textarea>
                    </div>
                </div>
                
                <div class="mws-form-row">
                    <label class="mws-form-label" for="tracking_url"><?php echo ShippingMethod::getLabel('tracking_url'); ?></label>
                    <div class="mws-form-item">
                        <input type="text" class="small" name="tracking_url" id="tracking_url" value="<?php echo Input::old('tracking_url', $formData['tracking_url']); ?>">
                    </div>
                </div>
                
            </div>
            
            <div class="mws-button-row">
                <input type="submit" class="btn btn-primary" value="Save">
            </div>
        </form>
        
    </div>
</div>
