<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> List Policy Assignment </span>
    </div>

        <?php /* <link media="all" type="text/css" rel="stylesheet" href="http://pcms.com/themes/admin/assets/css/form.css"> */ ?>
    <?php input::flash() ?>
    <?php
        /*
        $old = Input::old();
        d($old);
        */
    ?>
    <?php echo Form::open(array('method'=>'get')) ?>
        <div class="mws-form-inline" style="background-color:#DDD;border:1px solid #8C8C8C;overflow:hidden;padding:10px 0px 10px 0px;">

            <?php
                if (Input::has('display_filter') && ! Input::has('shop'))
                {
                    echo '<div style="margin-left:20px; margin-right:20px;" class="alert alert-error">';
                    echo __("Please select shop");
                    echo '</div>';
                }
            ?>

            <div class="mws-form-row" style="float:left;margin-left:20px;">
                <?php echo Form::label('shop', 'Shop') ?>
                <div class="mws-form-item">
                    <?php echo Form::comboBox('shop', $shops, Input::old('shop')); ?>
                </div>
            </div>
            <div class="mws-form-row" style="float:left;margin-left:20px;">
                <?php echo Form::label('vendor', 'Vendor') ?>
                <div class="mws-form-item">
                    <?php echo Form::comboBox('vendor', $vendors, Input::old('vendor')) ?>
                </div>
            </div>
            <div class="mws-form-row" style="float:left;margin-left:20px;">
                <?php echo Form::label('brand', 'Brand') ?>
                <div class="mws-form-item">
                    <?php echo Form::comboBox('brand', $brands, Input::old('brand')) ?>
                </div>
            </div>

            <div class="clear">&nbsp;</div>

            <p style="padding:0 20px;"><a href="#" id="toggleAdvanceOptions">Advance Options</a></p>

            <?php
                if ( (Input::has('display_filter') && Input::get('display_filter') != 'all' )

                ) {
                    $optionsDisplay = '';
                }
                else
                {
                    $optionsDisplay = ' style="display:none;"';
                }
            ?>

            <div id="advance_search_options"<?php echo $optionsDisplay ?>>

                <div class="mws-form-row">
                    <label class="mws-form-label" style="padding:0 10px;">Display filter</label>
                    <div class="mws-form-item clearfix">
                        <ul class="search-condition mws-form-list inline">
                            <li><?php echo Form::radio('display_filter', 'all', true, array('id' => 'display_filter_all')); ?> <label for="display_filter_all">All</label></li>
                            <li><?php echo Form::radio('display_filter', 'non_assign', null, array('id' => 'display_filter_assigned')); ?> <label for="display_filter_assigned">Show element that don't have policy.</label></li>
                            <li><?php echo Form::radio('display_filter', 'assigned', null, array('id' => 'display_filter_non_assign')); ?> <label for="display_filter_non_assign">Show element that policy assigned.</label></li>
                        </ul>
                    </div>
                </div>

            </div>

            <div class="mws-button-row"  style="float:left;margin-left:20px;padding-top:22px;">
                <?php echo Form::submit('Search', array('class'=>'btn btn-primary')) ?>
                <?php echo Form::reset('Reset', array('class'=>'btn btn-warning', 'onClick'=>'window.location="'.Request::url().'"')) ?>
            </div>
        </div>
    <?php echo Form::close() ?>
    <div class="clear"></div>

<style type="text/css">
ul.search-condition { list-style-type:none; padding:0 10px; }
ul.search-condition li { display:inline; margin-right:15px; }
</style>
<?php

Theme::asset()->container('macro')->writeScript('product-search-form-script','
$(function(){
    $("#toggleAdvanceOptions").click(function(e){
        e.preventDefault();
        $("#advance_search_options").slideToggle();
    });
    // $("input[name=product_allow_installment]").change(function(e){
    //     $("#including-allow-variant").toggle($("#product_allow_installment-yes").is(":checked"));
    // });
})
');

?>

    <div class="mws-panel-body no-padding dataTables_wrapper">




        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
                    <th class="span1" style="width:150px;"><?php echo __('Shop ID'); ?></th>
                    <th class="span2" style="width:28%;"><?php echo __('Shop'); ?></th>
                    <th class="span2" style="width:28%;"><?php echo __('Vendor'); ?></th>
                    <th class="span2" style="width:28%;"><?php echo __('Brand'); ?></th>
                </tr>
            </thead>
            <tbody>


            <?php if ( isset($records) && $records ) { ?>
                <?php foreach ($records as $record) { ?>
                    <?php if (! is_null($record[0])): ?>
                    <tr style="border-top:3px solid #4D4D4D; border-bottom:1px solid #B8B8B8; margin-top:10px;">
                    <?php else: ?>
                    <tr style="border-bottom:1px solid #B8B8B8;">
                    <?php endif; ?>

                        <td class="table-center" style="background-color:#D7D7D7;width:100px"><?php echo $record[0]; ?></td>
                        <?php for ($col=1; $col <= 3; $col++): ?>
                        <td class="table-center">

                            <?php
                                $name = array_get($record[$col], 'name');
                                if ($name)
                                {
                                    echo $name.'<br>';

                                    $id = array_get($record[$col], 'id');
                                    $type = array_get($record[$col], 'type');

                                    // PolicyAssignmentsController::POLICIES_PER_MODEL

                                    $policyRelates = array_get($record[$col], 'policies');
                                    // d($policies);

                                    // for ($rowRelate=1; $rowRelate <= 1; $rowRelate++) {
                                    for ($rowRelate=1; $rowRelate <= $policyPerModel; $rowRelate++) {
                                        $selectName = "assign[{$type}][{$id}][{$rowRelate}]";

                                        $policyRelate = $policyRelates->get($rowRelate-1);

                                        if ($policyRelate)
                                        {
                                            if ($policyRelate->use_type == 'yes')
                                            {
                                                $value = $policyRelate->policy_id;
                                            }
                                            if ($policyRelate->use_type == 'no')
                                            {
                                                $value = null;
                                            }

                                            $policyRelateID = $policyRelate->id;
                                        }
                                        else
                                        {
                                            $value = 'no';
                                            $policyRelateID = '';
                                        }

                                        echo Form::select($selectName, $policyOptions, $value, array('class' => 'policy_relate_update', 'data-type' => $type, 'data-id' => $id , 'data-policy-relate-id' => $policyRelateID , 'style' => 'margin-top: 5px;'));
                                    }
                                }


                            ?>
                        </td>
                        <?php endfor; ?>
                    </tr>
                 <?php } ?>

            <?php }else{ ?>

                <td class="table-center" colspan="4">ไม่พบข้อมูล</td>

            <?php } ?>
            </tbody>
        </table>

        <?php
            if (
                isset($navigator)
                && is_object($navigator)
                && method_exists($navigator, 'appends')
            ) {
                $query = array(
                    'shop' => Input::get('shop'),
                    'vendor' => Input::get('vendor'),
                    'brand' => Input::get('brand'),
                    'display_filter' => Input::get('display_filter'),
                    );
                echo $navigator->appends($query)->links();
            }
        ?>

    </div>
</div>

<div id="dialog-confirm" title="คุณแน่ใจหรือไม่ที่จะเปลี่ยนค่านี้?" style="display:none;">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>กระบวนการนี้ไม่สามารถทำย้อนกลับได้ คุณแน่ใจหรือไม่?</p>
</div>

