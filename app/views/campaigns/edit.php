<?php

		Theme::asset()->writeStyle('Campaign-Management-create-version-1.0','
		.tarea {
		    width:90%;
			height:120px;;
		}
		.ssmall {
		    width:15%;
			margin-left:10px;
		}

		');

?>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span>Edit Campaign</span>
    </div>
    <div class="mws-panel-body no-padding">


        <?php echo HTML::message(); ?>

        <form method="post" action="" class="mws-form" enctype="multipart/form-data">
            <div class="mws-form-inline">
           		 <div class="mws-form-row">
                    <label class="mws-form-label" for="type">App Name</label>
                    <div class="mws-form-item">
                    	<?php echo $apps[$formData['app_id']] ?>

                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="Campaign Name">Campaign Name</label>
                    <div class="mws-form-item">
	                    <input type="text" class="small" name="name" id="name" value="<?php echo Input::old('name', $formData['name']) ?>">
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label" for="Detail">Detail</label>
                    <div class="mws-form-item">

                        <?php echo Form::ckeditor('detail', Input::old('detail', $formData['detail']), array('id' => 'detail', 'class' => 'form-control', 'height' => '150px')); ?>

                    </div>
                </div>
                 <div class="mws-form-row">
                    <label class="mws-form-label" for="Note">Note</label>
                    <div class="mws-form-item">

                        <textarea class="tarea" name="note"><?php echo Input::old('note', $formData['note']) ?></textarea>

                    </div>
                </div>
                 <div class="mws-form-row">
                    <label class="mws-form-label" for="Period">Period</label>
                    <div class="mws-form-item">
                    	<?php
                    		  @$start_date = explode(" ", $formData['start_date']);
                           	  @$start_time = explode(":", $start_date[1]);
							  @$end_date = explode(" ", $formData['end_date']);
                           	  @$end_time = explode(":", $end_date[1]);
                    	?>
	                   Start Date <input type="text" id="start_datepicker"  readonly class="ssmall" name="start_date"  value="<?php echo Input::old('start_date',$start_date[0]) ?>">
	                   &nbsp;
	                   <br><br>
	                   End Date <input type="text" id="end_datepicker" readonly class="ssmall" name="end_date" id="end_date" value="<?php echo Input::old('end_date',$end_date[0]) ?>">&nbsp;
	                   &nbsp;
                    </div>
                </div>
                <?php /*
                <div class="mws-form-row">
                    <label class="mws-form-label" for="Budget">Budget</label>
                    <div class="mws-form-item">
	                    <input type="text" class="small" name="budget" id="budget" value="<?php echo Input::old('budget', $formData['budget']) ?>"> บาท
                    </div>
                </div>
                */ ?>
				<div class="mws-form-row">
                    <label class="mws-form-label" for="Campaign Status">Campaign Status</label>
                    <div class="mws-form-item">
	                    <input type="radio"  name="status" value="activate" <?php if($formData['status'] == 'activate'){?>checked="checked"<?php } ?>> Activate &nbsp;&nbsp;<input type="radio"  name="status" value="deactivate" <?php if($formData['status'] == 'deactivate'){?>checked="checked"<?php } ?>> Deactivate
                    </div>
                </div>

            </div>
            <div class="mws-button-row">
                <input type="submit" class="btn btn-primary" value="Save">
            </div>
        </form>


    </div>

	<script>
	$(function() {



		$('#start_datepicker').datetimepicker({
            timeFormat: 'HH:mm',
            dateFormat: 'yy-mm-dd',
            stepMinute: 10,
            stepSecond: 10
        });
		$('#end_datepicker').datetimepicker({
            timeFormat: 'HH:mm',
            dateFormat: 'yy-mm-dd',
            stepMinute: 10,
            stepSecond: 10
        });

        

    $("#start_datepicker").change(function()
    {
        $( "#end_datepicker" ).datetimepicker("destroy");

        var start_datepicker = $("#start_datepicker").datetimepicker('getDate');

        $("#end_datepicker").datetimepicker(
        {
            timeFormat: 'HH:mm',
            dateFormat: 'yy-mm-dd',
            minDateTime:start_datepicker,
        });

    });

    $("#end_datepicker").change(function()
    {
        $( "#start_datepicker" ).datetimepicker("destroy");

        var end_datepicker = $("#end_datepicker").datetimepicker('getDate');

        $("#start_datepicker").datetimepicker(
        {
            timeFormat: 'HH:mm',
            dateFormat: 'yy-mm-dd',
            maxDateTime:end_datepicker,
        });

    });

    
	});
	</script>
</div>
