<div class="mws-panel grid_8">

    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Order Discount Tracker</span>
    </div>

    <div class="mws-panel-body no-padding">
        <table class="discount-tracker mws-table mws-datatable-fn">
            <thead>
                <tr>
                    <th width="100">Order ID</th>
                    <th>Customer Name</th>
                    <th>Payment Channel</th>
                    <th>Discount</th>
                    <th>Customer Pay</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order) { ?>
                <tr>
                    <td>
                        <?php echo $order->id ?>
                    </td>
                    <td>
                        <?php echo $order->customer_name ?>
                    </td>
                    <td>
                        <?php echo $order->payment_channel ?>
                    </td>
                    <td>
                        <?php echo $order->discount ?>
                    </td>
                    <td>
                        <?php echo $order->all_customer_pay ?>
                    </td>
                    <td>
                        <a href="<?php echo URL::to('orders/discount-tracker/'.$order->id) ?>">Detail</a>
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
                    { "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "alignCenter" },
                    { "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "" },
                    { "bVisible": true, "bSearchable": false, "bSortable": true , sClass: "alignCenter" },
                    { "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "alignCenter" },
                    { "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "alignCenter" },
                    { "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "alignCenter" }
                ]
            });
        }
    });
}) (jQuery, window, document);
</script>