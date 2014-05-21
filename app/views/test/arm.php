<?php /*echo Theme::widget('WidgetPopupManageItems', array(
    'selectId' => 'effect-discount-which',
    'selectName' => 'effects[discount][which]',
    'inputId' => 'effects-discount-following_items',
    'inputName' => 'effects[discount][following_items]',
    'type' => 'brand',
    'datas' => array(array('pkey' => '61601376971792', 'title' => 'Yoobao'))
    ))->render();*/ ?>

<br />
<br />

<input type="text" id="type1" value="variant"><br />
<input type="button" class="popup-manage-items" value="Browse"
       data-popup-id="x1"
       data-popup-type="$('#type1').val()"
       data-popup-target-id="target1"
       data-popup-datas='[{"pkey":"18513845828498","title":"A Bird : \u0e41\u0e2b\u0e27\u0e19Twig ring+black rhodium plated +amethyst","img":"http:\/\/pcms.igetapp.com\/uploads\/13-12-19\/90b862a4764608fd609da084bba5c6a9_m.jpg"},{"pkey":"17013845828494","title":"A Bird : \u0e41\u0e2b\u0e27\u0e19Twig ring+black rhodium plated +aquamarine","img":"http:\/\/pcms.igetapp.com\/uploads\/13-12-19\/90b862a4764608fd609da084bba5c6a9_m.jpg"},{"pkey":"17813845828498","title":"A Bird : \u0e41\u0e2b\u0e27\u0e19Twig ring+black rhodium plated +rainbow moonstone","img":"http:\/\/pcms.igetapp.com\/uploads\/13-12-19\/90b862a4764608fd609da084bba5c6a9_m.jpg"}]'
/><br />
<input type="text" id="target1" placeholder="pkeys" style="width: 500px;" />

<hr />

<input type="text" id="type2" value="product"><br />
<input type="button" class="popup-manage-items" value="Browse"
       data-popup-id="x2"
       data-popup-type="$('#type2').val()"
       data-popup-target-id="target2"
       data-popup-datas='<?php echo json_encode(array()); ?>'
/><br />
<input type="text" id="target2" placeholder="pkeys" style="width: 500px;" />

<hr />

<input type="text" id="type22" value="exclude-variant"><br />
<input type="button" class="popup-manage-items" value="Browse"
       data-popup-id="x22"
       data-popup-type="$('#type22').val()"
       data-popup-target-id="target22"
       data-popup-product-pkeys='[24213845828492]'
       data-popup-datas='<?php echo json_encode(array(array('pkey' => '18513845828498', 'title' => 'x'))); ?>'
/><br />
<input type="text" id="target22" placeholder="pkeys" style="width: 500px;" />

<hr />

<input type="text" id="type3" value="brand"><br />
<input type="button" class="popup-manage-items" value="Browse"
       data-popup-id="x3"
       data-popup-type="$('#type3').val()"
       data-popup-target-id="target3"
       data-popup-datas='[{"title":"Adidas","pkey":"61201376033911"}]'
       data-popup-options='<?php echo json_encode(Brand::all()->lists('name', 'pkey'), JSON_HEX_APOS); ?>'
/><br />
<input type="text" id="target3" placeholder="pkeys" style="width: 500px;" />