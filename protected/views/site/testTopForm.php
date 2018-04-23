
<div class="form-group">
    <?php echo $form->labelEx($model,'name',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-5">
        <?php echo $form->textField($model, 'name',
            array('readonly'=>($readonly))
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'dis_name',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-5">
        <?php echo $form->textArea($model, 'dis_name',
            array('readonly'=>($readonly),'rows'=>4)
        ); ?>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'start_time',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <?php echo $form->textField($model, 'start_time',
                array('class'=>'form-control pull-right','readonly'=>($readonly),'id'=>'start_time'));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'end_time',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <?php echo $form->textField($model, 'end_time',
                array('class'=>'form-control pull-right','readonly'=>($readonly),'id'=>'end_time'));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <?php echo $form->labelEx($model,'exa_num',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-3">
        <?php echo $form->numberField($model, 'exa_num',
            array('readonly'=>($readonly),'min'=>1)
        ); ?>
    </div>
</div>
