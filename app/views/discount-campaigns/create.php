<div class="mws-panel grid_8">

    <div class="mws-panel-header">
        <span>
            <i class="icon-table"></i> Create discount campaigns
        </span>
    </div>

    <div class="mws-panel-body no-padding">

        <?php echo HTML::message(); ?>

        <?php echo Form::open(array('class' => 'mws-form')); ?>

        <div class="mws-form-inline">

            <div class="mws-form-row">
                <label class="mws-form-label" for="app_id">App name *</label>
                <div class="mws-form-item">
                    <?php echo Form::select('app_id', $appOptions, null, array('id' => 'name')) ?>
                </div>
            </div>

            <div class="mws-form-row">
                <label class="mws-form-label" for="type">Type *</label>
                <div class="mws-form-item">
                    <ul class="mws-form-list">
                        <?php $c=0; foreach($typeOptions as $id => $type): ?>
                        <li>
                        <?php
                            echo Form::radio('type', $id, ($c++ == 0 ? true : false), array('id' => 'type-'.$id)),
                                 Form::label('type-'.$id, $type); ?>
                        </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            </div>

            <div class="mws-form-row">
                <label class="mws-form-label" for="code">Code *</label>
                <div class="mws-form-item">
                    <?php echo Form::text('code', null, array('id' => 'code', 'class' => 'small')); ?>
                </div>
            </div>

            <div class="mws-form-row">
                <label class="mws-form-label" for="name">Name *</label>
                <div class="mws-form-item">
                    <?php echo Form::text('name', null, array('id' => 'name', 'class' => 'small')); ?>
                </div>
            </div>

            <div class="mws-form-row">
                <label class="mws-form-label" for="description">Description *</label>
                <div class="mws-form-item">
                    <?php echo Form::textarea('description', null, array('id' => 'description', 'class' => 'small')); ?>
                </div>
            </div>

            <div class="mws-form-row">
                <label class="mws-form-label" for="note">Note</label>
                <div class="mws-form-item">
                    <?php echo Form::textarea('note', null, array('id' => 'note', 'class' => 'small')); ?>
                </div>
            </div>

            <div class="mws-form-row">
                <label class="mws-form-label" for="discount">Discount *</label>
                <div class="mws-form-item">
                    <span class="pre_discount"></span>
                    <?php echo Form::text('discount', null, array('id' => 'discount', 'class' => 'discount')); ?>
                    <?php echo Form::select('discount_type', $discountOptions, null, array('class' => 'discount_type')); ?>
                </div>
            </div>

            <div class="mws-form-row">
                <label class="mws-form-label" for="">Period</label>
                <div class="mws-form-item">
                    From <?php echo Form::text('started_at', null, array('id' => 'started_at')); ?>
                    To <?php echo Form::text('ended_at', null, array('id' => 'ended_at')); ?>
                </div>
            </div>

<!--            <div class="mws-form-row">
                <label class="mws-form-label" for="status">Status</label>
                <div class="mws-form-item">
                    <?php // echo Form::select('status', $statusOptions, null, array('id' => 'type')); ?>
                </div>
            </div>-->

        </div>

        <div class="mws-button-row">
            <?php echo Form::submit('Create', array('class' => 'btn btn-primary')); ?>
            <a href="<?php echo URL::to('discount-campaigns'); ?>" class="btn btn-default">Cancel</a>
        </div>

        <?php echo Form::close(); ?>

    </div>

</div>