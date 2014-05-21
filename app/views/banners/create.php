<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span>Create Banner Section</span>
    </div>
    <div class="mws-panel-body no-padding">

        <?php if (Session::has('success')): ?>
            <div class="alert alert-success">
                <p><?php echo Session::get('success') ?></p>
            </div>
        <?php endif ?>
		<?php if ($errors->count() > 0) { ?>
			<div class="alert alert-error">
				<?php foreach ($errors->all() as $error) { ?>
				    <p><?php echo $error ?></p>
				<?php } ?>
			</div>
		<?php } ?>

        <form id="banner_create" method="post" action="" class="mws-form" enctype="multipart/form-data">
            <div class="mws-form-inline">
                <div class="mws-form-row">
                    <label class="mws-form-label"><?php echo BannerSection::getLabel('ตำแหน่งแบนเนอร์') ?></label>
                    <div class="mws-form-item">
						<?php echo $groups['position']['name'];?>
                    </div>
                </div>
				<div class="mws-form-row">
                    <label class="mws-form-label"><?php echo BannerSection::getLabel('กลุ่มแบนเนอร์') ?></label>
                    <div class="mws-form-item">
                        <?php echo $groups['name']?>
                    </div>
                </div>
				<div class="mws-form-row">
                    <label class="mws-form-label" for="name"><span class="red">*</span><?php echo BannerSection::getLabel('ชื่อแบนเนอร์') ?></label>
                    <div class="mws-form-item">
                        <input type="text" class="small" name="name" id="name" value="<?php echo Input::old('name') ?>">
                    </div>
                </div>
				<div class="mws-form-row" id="row_banner_type" >
                    <label class="mws-form-label" for="banner_type"><span class="red">*</span><?php echo BannerSection::getLabel('ชนิดของแบนเนอร์') ?></label>
                    <div class="mws-form-item">
						<?php
							$banner_type = 	array(
												0 =>	array(
															'label' => BannerSection::getLabel('New Page'),
															'value' => 1
														),
												1 =>	array(
															'label' => BannerSection::getLabel('Mapping Area'),
															'value' => 2
														),
												2 =>	array(
															'label' => BannerSection::getLabel('Embed Code youtube (Today special)'),
															'value' => 3
														)
											);
							$default_type = Input::old('banner_type');
							if(empty($default_type))
							{
								$default_type = 1;
							}
						?>
						
						<?php foreach($banner_type as $bkey => $brow):?>
							<?php if($brow['value'] == $default_type){ $default_option = true;}else{ $default_option = false;}?>
							<?php echo Form::radio('banner_type', $brow['value'] , $default_option, array('class' => 'banner_type'));?> <?php echo $brow['label'];?>
						<?php endforeach;?>
					</div>
                </div>
				<div class="mws-form-row" id="row_banner_image">
                    <label class="mws-form-label" for="banner_image"><span class="red">*</span><?php echo BannerSection::getLabel('ไฟล์รูป') ?></label>
                    <div class="mws-form-item">
                        <input type="file" name="banner_image" id="banner_image">
                    </div>
                </div>
				<div class="mws-form-row" id="row_banner_link">
                    <label class="mws-form-label" for="link"><span class="red">*</span><?php echo BannerSection::getLabel('Link แบนเนอร์') ?></label>
                    <div class="mws-form-item">
                         <input type="text" class="small" name="link" id="link" value="<?php echo Input::old('link') ?>">
                    </div>
                </div>
				<div class="mws-form-row" id="row_map" style="display:none;">
                    <label class="mws-form-label" for="maping_image"><?php echo BannerSection::getLabel('Mapping Image') ?></label>
                    <div class="mws-form-item">
						<input type="hidden" name="hid_img_coords" id="hid_img_coords" />
						<input type="hidden" name="hid_img_path" id="hid_img_path" />
							<div style="float:left;">เลือกรูปจากเครื่องคอมพิวเตอร์</div>
							<iframe id=""
								name="uploader" style="margin:0 10px; padding:0px; float:left"
								src="<?php echo URL::to("banners/iframe"); ?>"
								scrolling="no"
								noresize="noresize"
								frameborder="no"
								width="400"
								frameborder="1"
								height="42"></iframe>
							<a href="javascript:gui_loadImage(window.frames['uploader'].document.getElementById('src').getAttribute('rel')); 
								$('#hid_img_path').val(window.frames['uploader'].document.getElementById('src').getAttribute('data-path'));" 
								class="">เลือกไฟล์นี้
							</a>
							<div style="clear:both"></div>
							<fieldset class="button_fieldset">
								<legend>
									<a onclick="toggleFieldset(this.parentNode.parentNode)">Image map areas</a>
								</legend>
								<div id="button_container">
									<!-- buttons come here -->
									<?php /***
									<img src="<?php echo site_assets_url('imagemap/add.gif'); ?>" onclick="myimgmap.addNewArea()" alt="Add new area" title="Add new area"/>
									<img src="<?php echo site_assets_url('imagemap/delete.gif'); ?>" onclick="myimgmap.removeArea(myimgmap.currentid)" alt="Delete selected area" title="Delete selected area"/>
									<img src="<?php echo site_assets_url('imagemap/zoom.gif'); ?>" id="i_preview" onclick="myimgmap.togglePreview();" alt="Preview image map" title="Preview image map"/>
									<img src="<?php echo site_assets_url('imagemap/html.gif'); ?>" onclick="gui_htmlShow()" alt="Get image map HTML" title="Get image map HTML"/>
									***/  ?>
									<label for="dd_zoom">Zoom:</label>
									<select onchange="gui_zoom(this)" id="dd_zoom">
										<option value='0.25'>25%</option>
										<option value='0.5'>50%</option>
										<option value='1' selected="1">100%</option>
										<option value='2'>200%</option>
										<option value='3'>300%</option>
									</select>
									<label for="dd_output">Output:</label> 
									<select id="dd_output" onchange="return gui_outputChanged(this)">
										<option value='imagemap'>Standard imagemap</option>
										<option value='css'>CSS imagemap</option>
										<option value='wiki'>Wiki imagemap</option>
									</select>
									<div>
										<a class="toggler toggler_off" onclick="gui_toggleMore();return false;">More actions</a>
										<div id="more_actions" style="display: none; position: absolute;">
											<div><a href="" onclick="toggleBoundingBox(this); return false;">&nbsp; bounding box</a></div>
											<div><a href="" onclick="return false">&nbsp; background color </a><input onchange="gui_colorChanged(this)" id="color1" style="display: none;" value="#ffffff"></div>
										</div>
									</div>
								</div>
								<div style="float: right; margin: 0 5px" class="label_with_number">
									<select onchange="changelabeling(this)">
										<option value=''>No labeling</option>
										<option value='%n' selected='1'>Label with numbers</option>
										<option value='%a'>Label with alt text</option>
										<option value='%h'>Label with href</option>
										<option value='%c'>Label with coords</option>
									</select>
								</div>
								<div id="form_container" style="clear: both;">
								<!-- form elements come here -->
								</div>
							</fieldset>
							<fieldset>
								<legend>
									<a onclick="toggleFieldset(this.parentNode.parentNode)">Image</a>
								</legend>
								<div id="pic_container">	</div>			
							</fieldset>
							<fieldset>
								<legend>
									<a onclick="toggleFieldset(this.parentNode.parentNode)">Status</a>
								</legend>
								<div id="status_container"></div>
							</fieldset>
							<fieldset id="fieldset_html" class="fieldset_off">
								<legend>
									<a onclick="toggleFieldset(this.parentNode.parentNode)">Code</a>
								</legend>
								<div>
								<div id="output_help">
								</div>
								<textarea id="html_container"></textarea></div>
							</fieldset>							
                    </div>
                </div>
				<div class="mws-form-row" id="row_youtube_embed">
                    <label class="mws-form-label" for="youtube_embed"><span class="red">*</span><?php echo BannerSection::getLabel('Youtube Embed') ?></label>
                    <div class="mws-form-item">
                         <input type="text" class="small" name="youtube_embed" id="youtube_embed" value="<?php echo Input::old('youtube_embed') ?>">
                    </div>
                </div>
				<div class="mws-form-row">
                    <label class="mws-form-label" for="description"><?php echo BannerSection::getLabel('รายละเอียด') ?></label>
                    <div class="mws-form-item">
                        <textarea class="form-control" rows="3" cols="53" name="description" id="description" ><?php echo Input::old('description') ?></textarea>
                    </div>
                </div>
				<div class="mws-form-row" id="row_target">
                    <label class="mws-form-label" for="target"><?php echo BannerSection::getLabel('Target') ?></label>
                    <div class="mws-form-item">
                        <?php echo Form::select('target', array('_blank' => '_blank', '_self' => '_self'), Input::old('target'), array('id' => 'target')); ?>
                    </div>
                </div>
				<div class="mws-form-row">
                    <label class="mws-form-label" for="period_time"><?php echo BannerSection::getLabel('ช่วงเวลา'); ?></label>
                    <div class="mws-form-item">
                        <input type="checkbox" name="period_time" id="period_time" <?php echo (Input::old('period_time') == "Y") ? 'checked="checked"' : ""; ?>><?php echo BannerSection::getLabel('กำหนดช่วงเวลาแสดงผล'); ?>
                    </div>
                </div>
				<div class="mws-form-row" id="row_period_time">
                    <label class="mws-form-label" for="start_date"></label>
                    <div class="mws-form-item">
						<?php echo BannerSection::getLabel('วันเริ้มต้น'); ?>
						<input id="start_date" type="text" class="ssmall datepicker" name="start_date" value="<?php echo Input::old('start_date'); ?>" readonly >
						<?php echo BannerSection::getLabel('วันสิ้นสุด'); ?>
						<input id="end_date"  type="text" class="ssmall datepicker" name="end_date" value="<?php echo Input::old('end_date'); ?>" readonly >
                    </div>
                </div>
				<div class="mws-form-row">
                    <label class="mws-form-label" for="cstatus"><?php echo BannerSection::getLabel('สถานะการใช้งาน') ?></label>
                    <div class="mws-form-item">
						<?php
							$status = 	array(
												0 =>	array(
															'label' => BannerSection::getLabel('เปิดใช้งาน'),
															'value' => 'Y'
														),
												1 =>	array(
															'label' => BannerSection::getLabel('ปิดการใช้งาน'),
															'value' => 'N'
														)
											);
							$default_status = Input::old('cstatus');
							if(empty($default_status))
							{
								$default_status = 'Y';
							}
						?>
						<?php foreach($status as $skey => $srow):?>
							<?php if($srow['value'] == $default_status){ $status_option = true;}else{ $status_option = false;}?>
							<?php echo Form::radio('cstatus', $srow['value'] , $status_option, array('class' => 'cstatus'));?> <?php echo $srow['label'];?>
						<?php endforeach;?>
					</div>
                </div>
            </div>
            <div class="mws-button-row">
				<input type="hidden" name="banner_group_id" value="<?php echo $groups['id']?>">
                <input type="submit" class="btn btn-primary" value="Create">
            </div>
        </form>
    </div>
</div>
