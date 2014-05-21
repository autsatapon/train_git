<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> <?php echo Theme::place('title') ?></span>
    </div>

    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group btn-collection-nav">
                <div class="btn btn-info">
                    <a href="<?php echo URL::to('collections') ?>">Root</a>
                    <?php if ($parentId > 0) { ?>
                        <?php foreach ($parentCollection->all_node as $node) { ?>
                            <i class="icon-chevron-right"></i> <a href="<?php echo URL::to("collections?parent_id={$node['id']}") ?>"><?php echo $node['name'] ?></a>
                        <?php } ?>
                    <?php } ?>
                </div>
                <?php if ($parentId > 0) { ?>
                    <a class="btn" href="<?php echo URL::to("collections/create?parent_id={$parentId}") ?>"><i class="icol-add"></i> Create Collection (under <?php echo $parentCollection->name ?>)</a>
                <?php } else { ?>
                    <a class="btn" href="<?php echo URL::to('collections/create') ?>"><i class="icol-add"></i> Create Collection</a>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="mws-panel-body no-padding dataTables_wrapper">
        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
                    <?php /* <th style="width:30px;">No</th> */ ?>
                    <th style="width:30%;"><?php echo Collection::getLabel('name') ?></th>
                    <th style="width:25%;" class="no_sort"><?php echo Collection::getLabel('publish_for') ?></th>
                    <th style="width:15%;" class="no_sort"><?php echo Collection::getLabel('pkey') ?></th>
                    <th style="width:15%;" class="no_sort">&nbsp;</th>
                    <th style="width:15%;" class="no_sort">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php /* if ($parentId > 0) { ?>
                    <tr>
                        <?php /* <td><?php echo $key+1 ?></td> *//* ?>
                        <td><?php echo $parentCollection->name ?></td>
                        <td valign="top">
                            <?php if ( ! $parentCollection->apps->isEmpty() ) { ?>
                                <?php foreach ($parentCollection->apps as $key2 => $app) { ?>
                                    <?php echo $app->name ?>,
                                <?php } ?>
                            <?php } else { echo '-'; } ?>
                        </td>
                        <td><?php echo $parentCollection->pkey ?></td>
                        <td>
                            <a href="<?php echo URL::to("collections/edit/{$parentCollection->id}") ?>">Edit</a>
                        </td>
                        <td>
                            &nbsp;
                        </td>
                    </tr>
                <?php } */ ?>

                <?php foreach ($collections as $key => $collection) { ?>
                    <tr>
                        <?php /* <td><?php echo $key+1 ?></td> */ ?>
                        <td class="table-center"><?php echo $collection->name ?></td>
                        <td class="table-center" valign="top">

                            <?php echo implode('<br/>', $collection->apps->fetch('name')->toArray()) ?: '-'; ?>


                            <?php /*
                            <?php if ( ! $collection->apps->isEmpty()) : ?>
                                <?php foreach ($collection->apps as $key2 => $app) : ?>
                                    <?php echo $app->name; ?>,
                                <?php endforeach; ?>
                            <?php else : ?>
                            <?php echo '-'; ?>
                            <?php endif; ?>
                            */ ?>
                        </td>
                        <td class="table-center"><?php echo $collection->pkey ?></td>
                        <td class="table-center">
                            <a href="<?php echo URL::to("collections/products/{$collection->id}") ?>">Products</a> |
                            <?php if ($parentId > 0) { ?>
                                <a href="<?php echo URL::to("collections/edit/{$collection->id}?parent_id={$parentId}") ?>">Edit</a>
                            <?php } else { ?>
                                <a href="<?php echo URL::to("collections/edit/{$collection->id}") ?>">Edit</a>
                            <?php } ?>
                        </td>
                        <td class="table-right">
                            <a href="<?php echo URL::to("collections?parent_id={$collection->id}") ?>">
                                Sub-collections <i class="icon-chevron-right"></i><i class="icon-chevron-right"></i>
                            </a>
                        </td>
                    </tr>
                <?php } ?>


            </tbody>
        </table>
    </div>
</div>

<?php /*
<script>
(function( $, window, document, undefined ) {
    $(document).ready(function() {
        // Data Tables
        if( $.fn.dataTable ) {
            $(".mws-datatable-fn").dataTable({
                bSort: false,
                sPaginationType: "full_numbers",
                 "aoColumns": [
                    { "bVisible": true, "bSearchable": true, "bSortable": false , sClass: "alignLeft" },
                    { "bVisible": true, "bSearchable": true, "bSortable": false , sClass: "alignCenter" },
                    { "bVisible": true, "bSearchable": true, "bSortable": false , sClass: "alignCenter" },
                    { "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "alignCenter" },
                    { "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "alignCenter" }
                ]
            });
        }
        $('.sorting_1').removeClass('sorting_1');
    });
}) (jQuery, window, document);
</script>
*/ ?>