<div class="mws-panel grid_8 promotion_create">

    <div class="mws-panel-header">
        <span>Create Promotion</span>
    </div>

    <div class="mws-panel-body no-padding">

        <?php echo HTML::message(); ?>

        <form method="post" action="" id="promotion-form" class="mws-form" enctype="multipart/form-data">

            <fieldset class="mws-form-inline">

                <legend>Promotion</legend>

                <div class="mws-form-row" id="promotion-category">
                    <label class="mws-form-label" for="type">Type</label>
                    <div class="mws-form-item">
                        <?php // echo Form::select('promotion_category', $categories, Input::old('promotion_category')); ?>
                        <?php  echo Form::select('promotion_category', $categories); ?>
                    </div>
                </div>

                <div class="mws-form-row">
                    <label class="mws-form-label" for="name">Name</label>
                    <div class="mws-form-item">
                        <input type="text" class="small" name="name" id="name" value="<?php echo Input::old('name'); ?>">
                    </div>
                </div>

                <div class="mws-form-row">
                    <label class="mws-form-label" for="start_date">Period</label>
                    <div class="mws-form-item">
                        <input type="text" class="ssmall datepicker" name="start_date" value="<?php echo Input::old('start_date'); ?>">
                        To
                        <input type="text" class="ssmall datepicker" name="end_date" value="<?php echo Input::old('end_date'); ?>">
                    </div>
                </div>

                <div class="mws-form-row">
                    <label class="mws-form-label" for="code">Code</label>
                    <div class="mws-form-item">
                        <input type="text" class="small" name="code" id="code" value="<?php echo Input::old('code'); ?>">
                    </div>
                </div>

                <div class="mws-form-row">
                    <label class="mws-form-label" for="description">Detail</label>
                    <div class="mws-form-item">
                        <?php echo Form::ckeditor('description', Input::old('description'), array('id' => 'description', 'class' => 'form-control', 'height' => '150px')); ?>
                        <?php echo Form::transCkeditor(null, 'description', array('class' => 'form-control', 'height' => '150px')); ?>
                    </div>
                </div>

                <div class="mws-form-row">
                    <label class="mws-form-label" for="note">Note</label>
                    <div class="mws-form-item">
                        <textarea class="tarea small" name="note"><?php echo Input::old('note'); ?></textarea>
                    </div>
                </div>

                <div class="mws-form-row">
                    <label class="mws-form-label" for="note">Status</label>
                    <div class="mws-form-item">
                        <?php
                            $statusOption = array('activate' => 'Activate', 'deactivate' => 'Deactivate');
                            echo Form::select('status', $statusOption, null);
                        ?>
                    </div>
                </div>

            </fieldset>

            <!-- start Condition -->
            <fieldset class="mws-form-inline conditions">

                <legend>Conditions</legend>
                <!--
                <div class="mws-form-row">
                    <a class="btn btn-default" href="#"><i class="icon-plus"></i> Add Condition (And)</a>
                </div>

                <div class="mws-form-row condition-day">
                    <label class="mws-form-label">Day</label>
                    <div class="mws-form-item">
                        <ul class="mws-form-list inline">
                            <li><input type="checkbox" value=""> <label>Sun</label></li>
                            <li><input type="checkbox" value=""> <label>Mon</label></li>
                            <li><input type="checkbox" value=""> <label>Tue</label></li>
                            <li><input type="checkbox" value=""> <label>Wed</label></li>
                            <li><input type="checkbox" value=""> <label>Thu</label></li>
                            <li><input type="checkbox" value=""> <label>Fri</label></li>
                            <li><input type="checkbox" value=""> <label>Sat</label></li>
                        </ul>
                    </div>
                    <div class="mws-form-item">
                        Between
                        <input type="text" class="ssmall timepicker" name="" value="">
                        To
                        <input type="text" class="ssmall timepicker" name="" value="">
                    </div>
                </div>

                <div class="mws-form-row condition-total-order">
                    <label class="mws-form-label">Total order</label>
                    <div class="mws-form-item">
                        <input type="text" class="ssmall" name="" value="">
                        To
                        <input type="text" class="ssmall" name="" value="">
                    </div>
                </div>

                <div class="mws-form-row condition-summary-order">
                    <label class="mws-form-label">Summary order of</label>
                    <div class="mws-form-item">
                        <select name="">
                            <option>Variant</option>
                            <option>Product</option>
                            <option>Collection</option>
                            <option>Brand</option>
                        </select>
                        <a href="#" title="">Choose</a>
                    </div>
                    <div class="mws-form-item">
                        <input type="text" class="ssmall" name="" value="">
                        To
                        <input type="text" class="ssmall" name="" value="">
                    </div>
                </div>

                <div class="mws-form-row condition-product-qty">
                    <label class="mws-form-label">Product Qty</label>
                    <div class="mws-form-item">
                        <input type="text" class="ssmall" name="" value="">
                        of
                        <select name="">
                            <option>Variant</option>
                            <option>Product</option>
                            <option>Collection</option>
                            <option>Brand</option>
                        </select>
                        <a href="#" title="">Choose</a>
                    </div>
                </div>

                <div class="mws-form-row condition-combination-of">
                    <label class="mws-form-label">Combination of</label>
                    <div class="mws-form-item">
                        <input type="text" class="ssmall" name="" value="">
                        of
                        <select name="">
                            <option>Variant</option>
                            <option>Product</option>
                            <option>Collection</option>
                            <option>Brand</option>
                        </select>
                        <a href="#" title="">Choose</a>
                    </div>
                    <label class="mws-form-label">+ | - and</label>
                    <div class="mws-form-item">
                        <input type="text" class="ssmall" name="" value="">
                        of
                        <select name="">
                            <option>Variant</option>
                            <option>Product</option>
                            <option>Collection</option>
                            <option>Brand</option>
                        </select>
                        <a href="#" title="">Choose</a>
                    </div>
                </div>
                -->
                <!-- Auto Condition-->
                <div class="mws-form-row condition-promotion-code" id="promotion-condition">
                    <label class="mws-form-label">
                        Promotion code
                    </label>
                    <div class="mws-form-item">
                        <!--
                        <div class="as">
                            <label>as:</label>
                            <div class="clearfix">
                                <div class="grid_2">
                                    <label>
                                        <?php echo Form::radio('conditions[promotion_code][0][type]', 'coupon_code', null, array("class" => "promotion_code-as")); ?>
                                        Coupon code
                                    </label>
                                </div>
                                <div class="grid_2">
                                    <label>
                                        <?php echo Form::radio('conditions[promotion_code][0][type]', 'cash_voucher', null, array("class" => "promotion_code-as cash_voucher")); ?>
                                        Gift voucher
                                    </label>
                                </div>
                            </div>
                        </div>
                        -->

                        <div class="type">
                            <label>type:</label>
                            <div class="clearfix">
                                <div class="grid_2">
                                    <label>
                                        <?php echo Form::radio('conditions[promotion_code][0][format]', 'single_code', null, array('class' => 'single_code')); ?>
                                        Single code
                                    </label>
                                </div>
                                <div class='grid_5'>
                                    code can be used for
                                    <?php echo Form::text('conditions[promotion_code][0][single_code][used_times]', null, array('class' => 'ssmall single_code-used_times pcms-numeric')); ?>
                                    times
                                </div>
                                <div class="clear"></div>
                                <div class="grid_2">
                                    <label>
                                        <?php echo Form::radio('conditions[promotion_code][0][format]', 'multiple_code', null, array('class' => 'multiple_code')); ?>
                                        Unique code
                                    </label>
                                </div>
                                <div class="grid_5">
                                    <?php echo Form::text('conditions[promotion_code][0][multiple_code][count]', null, array('class' => 'ssmall multiple_code-count pcms-numeric')); ?>
                                    codes (each code can be used once)
                                </div>
                            </div>
                        </div>

                        <div class="condition-promotion-code-code">
                            <label>code:</label>
                            <div class="clearfix">
                                <div class="grid_2">
                                    <label>
                                        <?php echo Form::radio('conditions[promotion_code][0][code]', 'auto', true); ?>
                                        Auto-generated code
                                    </label>
                                </div>
                                <div class="grid_6">
                                    start with
                                    <?php echo Form::text('conditions[promotion_code][0][start_with]', null, array('class' => 'ssmall')); ?>
                                    and end with
                                    <?php echo Form::text('conditions[promotion_code][0][end_with]', 1, array('class' => 'ssmall multiple_code-end_with pcms-numeric')); ?>
                                    random chars (minimun required: <span class="multiple_code-min_length">1</span>)
                                </div>
                                <!--
                                <div class="grid_8">
                                    <label>
                                        <input type="radio" name="conditions[promotion_code][0][code]" value="custom"> Custom code
                                    </label>
                                    <ol class="coupon_code_set">
                                        <li>
                                            <input type="text" class="ssmall coupon_code" name="" value="">
                                            <i class="icon-ok"></i>
                                        </li>
                                        <li>
                                            <input type="text" class="ssmall coupon_code" name="" value="">
                                            <i class="icon-ok"></i>
                                        </li>
                                        <li>
                                            <input type="text" class="ssmall coupon_code" name="" value="">
                                            <i class="icon-remove"></i> This code is not available
                                        </li>
                                    </ol>
                                </div>
                                -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- True you Condition -->

                <div class="mws-form-row condition-promotion-code" id="promotion-condition-trueyou" style="display:none;">
                    <label class="mws-form-label">
                        True Card
                    </label>
                    <div class="mws-form-item">
                        <div class="as">
                            <label>as:</label>
                            <div class="clearfix">
                                <div class="grid_2">
                                    <label>
                                        <?php echo Form::radio('conditions[trueyou][0][type]', 'red_card', "checked = 'checked'"); ?>
                                        True Red Card
                                    </label>
                                </div>
                                <div class="grid_2">
                                    <label>
                                        <?php echo Form::radio('conditions[trueyou][0][type]', 'black_card', null); ?>
                                        True Black Card
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>


            </fieldset>
            <!-- end Condition -->

            <!-- start auto Effect -->
            <fieldset class="mws-form-inline effects" id="auto-effect">

                <legend>Effects</legend>

                <div class="mws-form-row effects_discount">
                    <label class="mws-form-label">
                        <input type="checkbox" name="effects[type][]" value="discount"> Discount
                    </label>
                    <div class="mws-form-item">
                        <div class="clearfix">
                            <div class="grid_3">

                                    <?php echo Form::radio('effects[discount][type]', 'price', null, array('class' => 'effect-discount-price')); ?>
                                    Price
                                    <?php echo Form::text('effects[discount][baht]', null, array('class' => 'ssmall pcms-numeric')); ?>
                                    Baht
                                <div class="clear"></div>
                                    <?php echo Form::radio('effects[discount][type]', 'percent', null, array('class' => 'effect-discount-percent')); ?>
                                    Percent
                                    <?php echo Form::text('effects[discount][percent]', null, array('class' => 'ssmall effect-discount-percent-value pcms-numeric')); ?>
                                    %

                            </div>
                            <div class="grid_1">
                                <strong>On</strong>
                            </div>
                            <div class="grid_4">
                                <ul class="mws-form-list">
                                    <li>
                                        <label>
                                            <?php echo Form::radio('effects[discount][on]', 'cart', null, array('class' => 'effect-discount-on-cart')); ?>
                                            Cart
                                        </label>
                                    </li>
                                    <!--<li>
                                        <label>
                                            <?php // echo Form::radio('effects[discount][on]', 'same_product', null, array('class' => 'effect-discount-on-same_product')); ?>
                                            Same Product
                                        </label>
                                    </li>-->
                                    <li>
                                        <label>
                                            <?php echo Form::radio('effects[discount][on]', 'following', null, array('class' => 'effect-discount-on-following')); ?>
                                            The following Item
                                        </label>

                                        <!-- effect-discount-on-following-extra -->
                                        <div class="effect-discount-on-following-extra">
                                            <?php
                                                $which = array(
                                                    'variant' => 'Variant',
                                                    'product' => 'Product',
                                                    'brand' => 'Brand',
                                                    //'collection' => 'Collection'
                                                    );
                                                echo Form::select('effects[discount][which]', $which, null, array('id' => 'effect-discount-which'));
                                                echo Form::text('effects[discount][following_items]', null,
                                                        array(
                                                            'id' => 'effects-discount-following_items',
                                                            'style' => 'margin: 5px 0 0 25px;',
                                                            'placeholder' => 'Separate with comma'
                                                            )
                                                    );
                                            ?>

                                            <ul class="following_exclude">
                                                <li id="exclude_product" style="display:none;">
                                                    <label>
                                                        <?php echo Form::checkbox('effects[discount][exclude_product][on]', 'following', null, array('class' => 'effect-exclude_product-on-following','checke' => 'checked')); ?>
                                                        Exclude Products
                                                    </label>

                                                        <?php

                                                            echo Form::text('effects[discount][exclude_product][un_following_items]', null,
                                                                    array(
                                                                        'id' => 'effects-exclude_product-following_items',
                                                                        'style' => ' margin: 5px 0 0 10px;width:245px;',
                                                                        'placeholder' => 'Separate with comma'
                                                                        )
                                                                );
                                                        ?>
                                                </li>
                                                <li id="exclude_variant" style="display:none;">
                                                    <label>
                                                        <?php echo Form::checkbox('effects[discount][exclude_variant][on]', 'following', null, array('class' => 'effect-exclude_variant-on-following','checke' => 'checked')); ?>
                                                        Exclude Variants
                                                    </label>

                                                        <?php

                                                            echo Form::text('effects[discount][exclude_variant][un_following_items]', null,
                                                                    array(
                                                                        'id' => 'effects-exclude_variant-following_items',
                                                                        'style' => ' margin: 5px 0 0 10px;width:245px;',
                                                                        'placeholder' => 'Separate with comma'
                                                                        )
                                                                );
                                                        ?>
                                                </li>
                                            </ul>
                                        </div>
                                        <!-- effect-discount-on-following-extra -->
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mws-form-row effects_free">
                    <label class="mws-form-label">
                        <input type="checkbox" name="effects[type][]" value="free"> Free
                    </label>
                    <div class="mws-form-item">
                        <div class="clearfix">
                            <div class="grid_3">
                                <input type="text" class="ssmall" name="effects[free][count]" id="free_number" value="1">
                            </div>
                            <div class="grid_1">
                                <strong>Of</strong>
                            </div>
                            <div class="grid_4">
                                <ul class="mws-form-list">
                                    <!--<li>
                                        <label>
                                        <input type="radio" name="effects[free][on]" value="same_product"> Same Product
                                        </label>
                                    </li>-->
                                    <li>
                                        <label>
                                            <input type="radio" name="effects[free][on]" value="following" onclick="$('#effect-free-which').show(); $('#effects-free-following_items').show();"> The following Item
                                        </label>
                                            <?php
                                                $which = array(
                                                    //'brand' => 'Brand',
                                                    'product' => 'Product',
                                                    'variant' => 'Variant',
                                                    //'collection' => 'Collection'
                                                    );
                                                echo Form::select('effects[free][which]', $which, null, array('id' => 'effect-free-which', 'style' => 'display:none;'));
                                                echo Form::text('effects[free][following_items]', null,
                                                        array(
                                                            'id' => 'effects-free-following_items',
                                                            'style' => 'display:none; margin: 5px 0 0 25px;',
                                                            'placeholder' => 'Separate with comma'
                                                            )
                                                    );
                                            ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="clearfix">
                            <div class="grid_1">
                                Apply:
                            </div>
                            <div class="grid_7 clearfix">
                                <ul class="mws-form-list">
                                    <li>
                                        <label>
                                            <input type="radio" name="effects[free][apply]" value="combination"> To each combination
                                        </label>
                                        <label>
                                            <input type="checkbox" name="effects[free][combination][limit]" value="1"> But limit to
                                            <input type="text" class="ssmall pcms-numeric" name="effects[free][combination][limit_count]" value=""> Times per order
                                        </label>
                                    </li>
                                    <li>
                                        <label>
                                            <input type="radio" name="effects[free][apply]" value="once"> Only once per order
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mws-form-row effects_shipping">
                    <label class="mws-form-label">
                        <input type="checkbox" name="effects[type][]" value="free_shipping"> Free Shipping
                    </label>
                </div>

            </fieldset>
            <!-- end auto Effect -->

            <!-- start limitation -->
            <!--
            <fieldset class="mws-form-inline limitations">

                <legend>Limitations</legend>

                <div class="mws-form-row">
                    <div class="mws-form-item">
                        <ul class="mws-form-list">
                            <li>
                                <label>
                                    <input type="checkbox" name="limitations[limit_campaign]" value="1"> Use quota of campaign
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="checkbox" name="limitations[limit_budget]" value="1"> Limit budget to <input type="text" name="limitations[budget]" class="ssmall" value=""> Baht
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="checkbox" name="limitations[limit_items]" value="1"> Limit items (Product,Brand ที่แถม) <input type="text" name="limitations[items]" class="ssmall" value=""> Items
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="checkbox" name="limitations[limit_quota_per]" value="1"> Limit to <input type="text" class="ssmall" name="limitations[quota]" value="">
                                    users per
                                    <select name="limitations[quota_per]">
                                        <option>Day</option>
                                        <option>Week</option>
                                        <option>Month</option>
                                    </select>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="checkbox" name="limitations[limit_user]" value="1"> Limit to <input type="text" class="ssmall" value="1"> users per user
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>

            </fieldset>
            -->
            <!-- end limitation -->

            <div class="mws-button-row">
                <input type="submit" class="btn btn-primary" value="Save">
                <a href="<?php echo URL::previous(); ?>" class="btn ">Cancel</a>
            </div>

        </form>


    </div>
</div>
