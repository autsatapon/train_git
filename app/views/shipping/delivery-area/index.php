<div class="mws-panel grid_8">

    <?php if(Session::has('success')) { ?>
        <div class="alert alert-success">
            <p><?php echo Session::get('success') ?></p>
        </div>
    <?php } ?>

    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Delivery Area</span>
    </div>

    <?php /*
    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a class="btn" href="<?php echo URL::to('brands/create') ?>"><i class="icol-add"></i> Create Brand</a>
            </div>
        </div>
    </div>
    */ ?>

    <div class="mws-panel-body no-padding">

        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
                    <th>Province</th>
                    <th style="width:100px;" class="no_sort">Delivery Area</th>
                    <th style="width:100px;" class="no_sort">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($provinces as $key => $province) { ?>
                    <tr>
                        <td>
                            <?php echo $province->name ?>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a style="margin:auto; display:block;" class="btn dropdown-toggle" data-toggle="dropdown" href="#"><?php echo $province->deliveryArea->name ?> &nbsp;&nbsp;&nbsp; <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <?php foreach ($deliveryAreas as $key=>$val) { ?>
                                    <li>
                                        <?php if ($val->id == $province->deliveryArea->id) { ?>
                                            <a href="#"><i class="icon-ok"></i> &nbsp; <?php echo $val->name ?></a>
                                        <?php } else { ?>
                                            <a href="<?php echo URL::to("shipping/delivery-area/set-area/{$province->id}/{$val->id}") ?>"><?php echo $val->name ?></a>
                                        <?php } ?>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </td>
                        <td>
                            <a href="<?php echo URL::to("shipping/delivery-area/province/{$province->id}") ?>">View Cities</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
(function( $, window, document, undefined ) {
    $(document).ready(function() {
        // Data Tables
        if( $.fn.dataTable ) {
            $(".mws-datatable-fn").dataTable({
                bSort: true,
                sPaginationType: "full_numbers",
                 "aoColumns": [
                    { "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "" },
                    { "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "" },
                    { "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "alignCenter" }
                ]
            });
        }
    });
}) (jQuery, window, document);
</script>