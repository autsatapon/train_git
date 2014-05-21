<div class="mws-panel grid_8">

    <?php if(Session::has('success')) { ?>
        <div class="alert alert-success">
            <p><?php echo Session::get('success') ?></p>
        </div>
    <?php } ?>

    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Delivery Area - <?php echo $province->name ?></span>
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
                    <th>City</th>
                    <th style="width:200px;" class="no_sort">Delivery Area</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cities as $key => $city) { ?>
                    <tr>
                        <td>
                            <?php echo $city->name ?>
                        </td>
                        <td>
                            <div class="btn-group">
                                <?php if (empty($city->deliveryArea)) { ?>
                                    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                                        <?php echo $province->deliveryArea->name ?> (Same as Province) &nbsp;&nbsp;&nbsp; <span class="caret"></span>
                                    </a>
                                <?php } else { ?>
                                    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                                        <?php echo $city->deliveryArea->name ?> &nbsp;&nbsp;&nbsp; <span class="caret"></span>
                                    </a>
                                <?php } ?>
                                <ul class="dropdown-menu">
                                    <?php foreach ($deliveryAreas as $key=>$val) { ?>
                                    <li>
                                        <?php if (!empty($city->deliveryArea) && $val->id == $city->deliveryArea->id) { ?>
                                            <a href="#"><i class="icon-ok"></i> &nbsp; <?php echo $val->name ?></a>
                                        <?php } else { ?>
                                            <a href="<?php echo URL::to("shipping/delivery-area/set-area/{$province->id}/{$val->id}/{$city->id}") ?>"><?php echo $val->name ?></a>
                                        <?php } ?>
                                    </li>
                                    <?php } ?>
                                    <li>
                                        <a href="<?php echo URL::to("shipping/delivery-area/set-area/{$province->id}/0/{$city->id}") ?>"><?php if ($city->deliveryArea == null) { echo '<i class="icon-ok"></i> '; } ?>(Same as Province)</a>
                                    </li>
                                </ul>
                            </div>
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
                ]
            });
        }
    });
}) (jQuery, window, document);
</script>