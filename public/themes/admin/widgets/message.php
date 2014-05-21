<?php if (count($messages) && is_object($messages)) : ?>
    <div class="alert error">
        This is an error message.
        <ul>
            <?php foreach ($messages->all('<li>:message</li>') as $message) : ?>
            <?php echo $message; ?>
            <?php endforeach; ?>
        </ul>
    </div>
<?php elseif (!empty($messages) && !is_object($messages)): ?>
    <div class="alert <?php echo $type; ?>">
        <?php echo $messages; ?>
    </div>
<?php endif; ?>