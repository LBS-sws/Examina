<?php
if($this->function_id!=$model->menu_code){
    $this->redirect(Yii::app()->createUrl('site/index'));
}
$this->pageTitle=Yii::app()->name . ' - MutualAudit Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'MutualAudit-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
    <h1><?php echo Yii::t("app","Study mutual audit");?></h1>
</section>

<section class="content">
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
        <?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
            'submit'=>Yii::app()->createUrl('mutualAudit/index',array("index"=>$model->menu_id))));
        ?>
        <?php if ($model->scenario!='view'): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('mutualAudit/audit')));
            ?>
            <?php echo TbHtml::button('<span class="fa fa-close"></span> '.Yii::t('misc','Deny'), array(
                'data-toggle'=>'modal','data-target'=>'#jectdialog'));
            ?>
        <?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'menu_id'); ?>
			<?php echo $form->hiddenField($model, 'mutual_state'); ?>
			<?php echo $form->hiddenField($model, 'employee_id'); ?>

			<div class="form-group">
				<?php echo $form->labelEx($model,'mutual_state',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-3">
				<?php
                echo TbHtml::textField('mutual_state',MutualMyList::getStateForArr(array("mutual_state"=>$model->mutual_state,"display"=>$model->display))["state"],
					array('readonly'=>(true))
				); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'employee_name',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-3">
				<?php
                echo $form->textField($model, 'employee_name',
					array('readonly'=>(true))
				); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'mutual_date',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-3">
				<?php
                echo $form->textField($model, 'mutual_date',
					array('readonly'=>($model->scenario=='view'),'prepend'=>"<i class='fa fa-calendar'></i>")
				); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'mutual_body',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-5">
				<?php
                echo $form->textArea($model, 'mutual_body',
					array('readonly'=>(true),'rows'=>4)
				); ?>
				</div>
			</div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'end_body',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-5">
                    <?php
                    echo $form->textArea($model, 'end_body',
                        array('readonly'=>($model->scenario=='view'),'rows'=>4)
                    ); ?>
                </div>
            </div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'z_index',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-2">
				<?php
                echo $form->numberField($model, 'z_index',
					array('readonly'=>($model->scenario=='view'))
				); ?>
				</div>
                <div class="col-lg-2">
                    <p class="form-control-static text-warning">数值越小越靠前（升序）</p>
                </div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'display',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-5">
				<?php
                $list = array(Yii::t("study","none"),Yii::t("study","show"));
                echo $form->inlineRadioButtonList($model, 'display',$list,
					array('readonly'=>($model->scenario=='view'))
				); ?>
				</div>
			</div>
		</div>
	</div>
</section>
<?php $this->renderPartial('//site/ject',array("model"=>$model,"form"=>$form,"rejectName"=>"reject_remark","submit"=>Yii::app()->createUrl('mutualAudit/reject'))); ?>

<?php

if ($model->scenario!='view') {
    $js = Script::genDatePicker(array(
        'MutualAuditModel_mutual_date',
    ));
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


