<?php
    reset($all_step); $first = key($all_step);
    end($all_step); $last = key($all_step);
    $prev = $step > $first ? $all_step[$step-1] : null;
    $next = $step < $last ? $all_step[$step+1] : null;
?>
<div class="container">
    <div class="mws-panel grid_8">

        <?php echo HTML::message(); ?>

    	<div class="mws-panel-header">
        	<span><i class="icon-magic"></i> New material</span>
        </div>
        <div class="mws-panel-body no-padding">
        	<div class="wizard-nav wizard-nav-horizontal">
            	<ul>
                    <?php /* Step summary cannot be back to any step except create new */ ?>
                    <?php foreach ($all_step as $key => $sub_step): ?>
                    <?php if ($disableStack == true and $key > 1) : ?>
                    <li<?php echo $key==$step ? ' class="current"' : ''; ?> style="cursor:default;">
                        <span><i class="<?php echo $sub_step['icon']; ?>"></i> <?php echo $sub_step['label']; ?></span>
                    </li>
                    <?php else : ?>
                    <li<?php echo $key==$step ? ' class="current"' : ''; ?>>
                        <a <?php echo $key>=$step ? '#' : 'href="'.$sub_step['url'].'"'; ?>><span><i class="<?php echo $sub_step['icon']; ?>"></i> <?php echo $sub_step['label']; ?></span></a>
                    </li>
                    <?php endif; ?>
            		</li>
                    <?php endforeach; ?>

            	</ul>
            	<a class="btn responsive-prev-btn" <?php echo $prev ? 'href="'.$prev['url'].'"' : 'disabled="disabled"'; ?>><i class="icon-caret-left"></i></a>
            	<button type="submit" class="btn responsive-next-btn"><i class="icon-caret-right"></i></button>
            </div>

            <?php echo $content; ?>

        </div>
    </div>
</div>
<?php
Theme::asset()->writeStyle('form-wizard-new-material-customize', '
.wizard-nav ul li a, .wizard-nav ul li span {
    padding: 0 5px;
}
', array('wizard'));