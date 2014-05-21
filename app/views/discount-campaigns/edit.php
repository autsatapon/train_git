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
                    <?php /* echo Form::select('app_id', $appOptions, $discountCampaign->pApp['id'], array('id' => 'app_id')); */ ?>
                    <?php echo $discountCampaign->pApp->name ?>
                </div>
            </div>

            <div class="mws-form-row">
                <label class="mws-form-label" for="type">Type *</label>
                <div class="mws-form-item">
                    <ul class="mws-form-list">
                        <?php foreach($typeOptions as $id => $type): ?>
                        <li>
                        <?php
                            echo Form::radio('type', $id, ($id === $discountCampaign->type), array('id' => 'type-'.$id, 'disabled' => 'disabled')),
                                 Form::label('type-'.$id, $type); ?>
                        </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            </div>

            <div class="mws-form-row">
                <label class="mws-form-label" for="code">Code *</label>
                <div class="mws-form-item">
                    <?php echo Form::text('code', $discountCampaign->code, array('id' => 'code', 'class' => 'small')); ?>
                </div>
            </div>

            <div class="mws-form-row">
                <label class="mws-form-label" for="name">Name *</label>
                <div class="mws-form-item">
                    <?php echo Form::text('name', $discountCampaign->name, array('id' => 'name', 'class' => 'small')); ?>
                </div>
            </div>

            <div class="mws-form-row">
                <label class="mws-form-label" for="description">Description *</label>
                <div class="mws-form-item">
                    <?php echo Form::textarea('description', $discountCampaign->description, array('id' => 'description', 'class' => 'small')); ?>
                </div>
            </div>

            <div class="mws-form-row">
                <label class="mws-form-label" for="note">Note</label>
                <div class="mws-form-item">
                    <?php echo Form::textarea('note', $discountCampaign->note, array('id' => 'note', 'class' => 'small')); ?>
                </div>
            </div>

            <div class="mws-form-row">
                <label class="mws-form-label" for="discount">Discount *</label>
                <div class="mws-form-item">
                    <?php echo Form::text('discount', $discountCampaign->discount, array('id' => 'discount', 'class' => 'discount')); ?>
                    <?php echo Form::select('discount_type', $discountOptions, $discountCampaign->discount_type, array('class' => 'discount_type')); ?>
                </div>
            </div>

            <div class="mws-form-row">
                <label class="mws-form-label" for="">Period</label>
                <div class="mws-form-item">
                    Form <?php echo Form::text('started_at', $discountCampaign->started_at->format('Y-m-d'), array('id' => 'started_at')); ?>
                    To <?php echo Form::text('ended_at', $discountCampaign->ended_at->format('Y-m-d'), array('id' => 'ended_at')); ?>
                </div>
            </div>

<!--            <div class="mws-form-row">
                <label class="mws-form-label" for="status">Status</label>
                <div class="mws-form-item">
                    <?php // echo Form::select('status', $statusOptions, $discountCampaign->status, array('id' => 'type')); ?>
                </div>
            </div>-->

        </div>

        <div class="mws-button-row">
            <?php echo Form::submit('Save', array('class' => 'btn btn-primary')); ?>
            <a href="<?php echo URL::to('discount-campaigns'); ?>" class="btn btn-default">Cancel</a>
        </div>

        <?php echo Form::close(); ?>

    </div>

</div>