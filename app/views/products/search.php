<?php foreach($products as $product): ?>
	<ul>
		<strong><?php echo $product->title ?></strong>
		<?php foreach($product->variants as $variant): ?>
			<li><?php echo $variant->title ?></li>
		<?php endforeach ?>
	</ul>
<?php endforeach ?>