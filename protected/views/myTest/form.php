<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('myTest/index'));
}
$this->pageTitle=Yii::app()->name . ' - myTest Form';
?>
<style>
    #staffDiv .checkbox-inline{width: 100px;}
</style>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'myTest-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','My test'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('myTest/index')));
		?>
		<?php echo TbHtml::button('<span class="fa fa-asterisk"></span> '.Yii::t('examina','Simulation test'), array(
				'submit'=>Yii::app()->createUrl('myTest/Again',array("index"=>$model->join_id))));
		?>
	</div>
	<div class="btn-group pull-right" role="group">
		<?php echo TbHtml::button('<span class="fa fa-superpowers"></span> '.Yii::t('examina','test again'), array(
				'submit'=>Yii::app()->createUrl('simTest/save')));
		?>
	</div>

	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo TbHtml::hiddenField("SimTestForm[quiz_id]",$model->id); ?>
            <div class="form-group">
                <div class="col-sm-8 col-sm-offset-2 text-warning">
                    <div class="form-control-static ">
                        <p><?php echo Yii::t('examina','Simulation test');?>：<?php echo Yii::t('examina','Not counted in statistics');?></p>
                        <p><?php echo Yii::t('examina','test again');?>：<?php echo Yii::t('examina','Participate in the statistics');?></p>
                    </div>
                </div>
            </div>

            <?php
            $this->renderPartial('//site/testTopForm',array(
                'form'=>$form,
                'model'=>$model,
                'readonly'=>($model->scenario=='view'),
            ));
            ?>
            <?php
            $testBool = $model->getCorrectNum();
            if ($testBool): ?>
            <legend><?php echo Yii::t("examina","test results")?></legend>
            <div class="form-group">
                <?php echo $form->labelEx($model,'lcd',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-8">
                    <p class="form-control-static"><?php echo $model->lcd;?></p>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'correct_num',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-8">
                    <label class="form-control-static text-primary" style="margin-right: 15px;"><?php echo count($model->correctList);?></label>
                    <?php
                    echo TbHtml::button(Yii::t('examina','Detail'), array('data-toggle'=>'modal','data-target'=>'#correctdialog',));
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'wrong_num',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-8">
                    <label class="form-control-static text-warning" style="margin-right: 15px;"><?php echo count($model->wrongList)?></label>
                    <?php
                    echo TbHtml::button(Yii::t('examina','Detail'), array('data-toggle'=>'modal','data-target'=>'#wrongdialog',));
                    ?>
                </div>
            </div>
            <?php endif; ?>
		</div>
	</div>
</section>

<?php
$this->renderPartial('//site/correctList',array(
    'testBool'=>$testBool,
    'correctList'=>$model->correctList,
));
?>

<?php
$this->renderPartial('//site/wrongList',array(
    'testBool'=>$testBool,
    'wrongList'=>$model->wrongList,
));
?>
<?php
$js = "

";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

