<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span><?php echo Theme::place('title');?></span>
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
					<label class="mws-form-label" for="name"><?php echo PaymentMethod::getLabel('name'); ?></label>
					<div class="mws-form-item">
						<input type="text" class="small" name="name" id="name" value="<?php echo Input::old('name', $formData['name']); ?>">
					</div>
				</div>
				<div class="mws-form-row">
					<label class="mws-form-label" for="code"><?php echo PaymentMethod::getLabel('code'); ?></label>
					<div class="mws-form-item">
						<input type="text" class="small" name="code" id="code" value="<?php echo Input::old('code',$formData['code']); ?>">
					</div>
				</div>
				<div class="mws-form-row">
					<label class="mws-form-label" for="channel"><?php echo PaymentMethod::getLabel('channel'); ?></label>
					<div class="mws-form-item">
						<select name="channel" style="width:150px;">
							<option value="<?php echo Order::ONLINE ?>" <?php if( $formData['channel'] === Order::ONLINE ): ?>selected<?php endif; ?>>Online</option>
							<option value="<?php echo Order::OFFLINE ?>" <?php if( $formData['channel'] === Order::OFFLINE ): ?>selected<?php endif; ?>>Offline</option>
						</select>
					</div>
				</div>
				<div class="mws-form-row">
					<label class="mws-form-label" for="transaction_fee"><?php echo PaymentMethod::getLabel('transaction_fee'); ?></label>
					<div class="mws-form-item" style="width:150px;">
						<input type="text" class="pcms-numeric" name="transaction_fee" id="transaction_fee" value="<?php echo Input::old('transaction_fee',$formData['transaction_fee']); ?>">
					</div>
				</div>
				<div class="mws-form-row">
					<label class="mws-form-label" for="transaction_apply"><?php echo PaymentMethod::getLabel('transaction_apply'); ?></label>
					<div class="mws-form-item">
						<select name="transaction_apply" style="width:150px;">
							<option value="1" <?php if( $formData['transaction_apply'] == 'Once' ): ?>selected<?php endif; ?>>Once</option>
							<option value="2" <?php if( $formData['transaction_apply'] == 'All Items' ): ?>selected<?php endif; ?>>All Items</option>
						</select>
					</div>
				</div>
			</div>

			<div class="mws-button-row">
				<input type="submit" class="btn btn-primary" value="Save">
			</div>
		</form>

	</div>
</div>
