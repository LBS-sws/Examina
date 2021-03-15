<?php
$this->pageTitle=Yii::app()->name . ' - flowTitle Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'flowTitle-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
		<strong>文本设置 - <?php echo $model->getFlowName(); ?></strong>
	</h1>
<!--
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="#">Layout</a></li>
		<li class="active">Top Navigation</li>
	</ol>
-->
</section>

<section class="content">
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl($model->getFlowBackUrl())));
		?>

        <?php if ($model->scenario!='view'): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('flowTitle/save')));
            ?>
        <?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'flow_code'); ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'flow_code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?php echo $model->getFlowName();?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'flow_title',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-8">
                    <?php echo $form->textArea($model, 'flow_title',
                        array('readonly'=>($model->scenario=='view'),"id"=>"flow_title",'rows'=>13)
                    ); ?>
                </div>
            </div>
		</div>
	</div>
</section>

<?php

$js = "
$('#flow_title').wysihtml5();
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

