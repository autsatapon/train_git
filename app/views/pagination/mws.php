<?php
    $presenter = new MWSPresenter($paginator);
?>

<?php if ($paginator->getLastPage() > 1): ?>
    <div class="dataTables_paginate paging_full_numbers" id="DataTables_Table_1_paginate">
        <span>
            <?php echo $presenter->render(); ?>
        </span>
    </div>
<?php endif; ?>