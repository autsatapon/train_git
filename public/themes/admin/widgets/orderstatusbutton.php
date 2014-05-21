<?php if (count($actions) > 1): ?>
<?php reset($actions); ?>
<?php $key = key($actions); ?>
<?php $value = current($actions); ?>
<div class="btn-group">
	<a href="<?php echo Url::to('orders/actions/'.$order->getKey().'/'.$key);  ?>" class="btn btn-info action <?php echo $key; ?>">
		<?php echo $value; ?>
	</a>
	<button class="btn btn-info dropdown-toggle" data-toggle="dropdown">
		<span class="caret"></span>
	</button>
	<?php unset($actions[$key]); ?>
	<ul class="dropdown-menu">
		<?php foreach ($actions as $key => $value): ?>
		<li>
			<a href="<?php echo Url::to('orders/actions/'.$order->getKey().'/'.$key); ?>" class="action <?php echo $key; ?>">
				<?php echo $value; ?>
			</a>
		</li>
		<?php endforeach; ?>
	</ul>
</div>
<?php elseif(count($actions)==0): ?>
	&nbsp;
<?php else: ?>
<?php reset($actions); ?>
<?php $key = key($actions); ?>
<?php $value = current($actions); ?>
<a class="btn btn-info" href="<?php echo Url::to('orders/actions/'.$order->getKey().'/'.$key); ?>">
	<?php echo $value; ?>
</a>
<?php endif; ?>