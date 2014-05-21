<div class="tabbable" style="margin: 10px;"> <!-- Only required for left/right tabs -->
  <ul class="nav nav-tabs" style="width: 100%;">
    <li class="<?php echo $mode=='choose' ? 'active' : ''; ?>"><a href="#choose" data-toggle="tab">Choose Product</a></li>
    <li class="<?php echo $mode!='choose' ? 'active' : ''; ?>"><a href="#create" data-toggle="tab">Create New Product</a></li>
  </ul>
  <div class="tab-content" style="overflow: visible;">
    <div class="tab-pane <?php echo $mode=='choose' ? 'active' : ''; ?>" id="choose">
      <?php echo Form::open(array('class' => 'mws-form wzd-default', 'files' => true, 'name' => 'choose')); ?>
      <?php echo Form::hidden('choose', 'choose'); ?>
      <!-- tab choose -->
      <div class="row-fluid well">
        <div class="grid_8">

          <label for="line_sheet">Line sheet:</label>
          <?php echo Form::select('line_sheet', $selectLineSheet, null, array('class' => 'large selectbox')); ?>
        </div>
      </div>
      <hr>
      <div class="row-fluid">
        <div class="grid_2">
          <label for="product_name">
            <span>Product Name:</span><br>
            <?php echo Form::text('product_name', null, array('class' => 'small', 'id' => 'product_name')); ?>
          </label>
          <br><br>
          <input id="search" type="button" class="btn btn-primary" name="choose" value="Search" />
        </div>
        <div class="grid_6 mws-panel-body no-padding" style="border-top: 1px solid #bcbcbc;">
          <table class="mws-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Product Line</th>
                <th>Brand</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="search_result">
              <tr>
                <td colspan="4" style="text-align: center;"> Use left form to search product </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <?php echo Form::close(); ?>
    </div>
    <div class="tab-pane <?php echo $mode!='choose' ? 'active' : ''; ?>" id="create">
      <?php echo Form::open(array('class' => 'mws-form wzd-default', 'files' => true, 'name' => 'create')); ?>
      <?php echo Form::hidden('create', 'create'); ?>
      <!-- tab create -->
      <div class="row-fluid well">
        <div class="grid_8">

          <label for="line_sheet">Line sheet:</label>
          <?php echo Form::select('line_sheet', $selectLineSheet, null, array('class' => 'small selectbox')); ?>

        </div>
      </div>
      <hr>
      <div class="row-fluid">
        <div class="grid_4">
          <label for="product_name">
            <span>Product Name:</span><br/>
            <?php echo Form::text('product_name', null, array('class' => 'small')); ?>
            <?php echo Form::transText(null, 'title', array('class' => 'small'), array("en_US")) ?>
          </label>

          <br><br>
          <input type="submit" class="btn btn-primary" name="action" value="Create" />
        </div>
        <div class="grid_2">
          <label for="brand">
            <span>Brand: </span><br/>
            <?php $brandId = $brandLastCreated ? $brandLastCreated->getKey() : null; ?>
            <?php echo Form::comboBox('brand', $brand, $brandId, array('class' => 'small', 'style' => 'width: 100px;'), true); ?>
            <div>or <a href="<?php echo URL::action('BrandsController@getCreate'); ?>">Create new Brand</a> if not in list</div>
          </label>
        </div>
      </div>
      <?php echo Form::close(); ?>
    </div>
  </div>
</div>

<?php

Theme::asset()->writeStyle('custom-li', '
.nav-tabs > li {
  width: 50%;
  font-weight: bold;
  text-align: center;
}
.well {
  background-color: lightgray;
  padding: 5px;
  height: 30px;
  /* margin-bottom: 10px; */
  border-radius: 7px;
}

.well label {
  margin-right: 20px;
}

.custom-combobox-input {
  width: 200px;
}

  ', array('main', 'bootstrap'));

// ajax search
Theme::asset()->container('footer')->writeScript('wizard-form-code', '
    ;(function( $, window, document, undefined ) {
        $(document).ready(function() {
            var search_active = false;
            var ajaxCall = function(event){
                if(search_active == true) {
                    return;
                }
                search_active = true;
                $("#search").addClass("disabled");
                $("#search_result").html("<tr><td colspan=\"4\" style=\"text-align: center;\"> Searching... </td></tr>");
                var product_name = $("#product_name");
                // var product_line = $("#product_line");
                $.post("'.URL::action('ProductNewMaterialController@postProductSearch').'",
                  {
                    product_name: product_name.val()
                    // , product_line: product_line.val()
                  }
                )
                .done(function(data) {
                    $("#search_result").html(data);
                })
                .fail(function(data) {
                    $("#search_result").html("<tr><td colspan=\"4\" style=\"text-align: center;\">An error occurred, please contact programmer... </td></tr>");
                })
                .then(function(data){
                    search_active = false;
                    $("#search").removeClass("disabled");
                });
            };
            // search ajax
            $("#search").click(ajaxCall);
            // protect enter keyboard for submit
            $(window).keydown(function(event){
                if ($(event.target).prop("id") != "product_name")
                {
                  if(event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                  }
                }
            });
            $("#product_name").on("keydown", function(e){
              if(event.keyCode == 13) {
                ajaxCall();
                return false;
              }
            });


        });
    }) (jQuery, window, document);
    ', array('jquery-form'));
?>