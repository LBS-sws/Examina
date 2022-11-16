<?php
if($this->function_id!=$model->menu_code){
    $this->redirect(Yii::app()->createUrl('site/index'));
}
$this->pageTitle=Yii::app()->name . ' - StudyArticle Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'StudyArticle-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
    <h1><?php echo Yii::t("study","Article View");?></h1>
    <ol class="breadcrumb">
        <li><?php echo $model->menu_name;?></li>
        <li>
            <?php
            echo TbHtml::link(Yii::t("app","Study guide"),Yii::app()->createUrl('StudyClass/index',array("index"=>$model->menu_id)));
            ?>
        </li>
        <li>
            <?php
            echo TbHtml::link($model->class_name,Yii::app()->createUrl('StudyArticle/index',array("class_id"=>$model->class_id)));
            ?>
        </li>
        <li class="active"><?php echo Yii::t("study","Article View");?></li>
    </ol>
</section>

<section class="content">
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('StudyArticle/index',array("class_id"=>$model->class_id))));
		?>
<?php if (Yii::app()->user->validRWFunction($model->menu_code)): ?>
			<?php echo TbHtml::button('<span class="glyphicon glyphicon-pencil"></span> '.Yii::t('study','Update'), array(
				'submit'=>Yii::app()->createUrl('StudyArticle/edit',array("index"=>$model->id))));
			?>
<?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'menu_id'); ?>
			<?php echo $form->hiddenField($model, 'class_id'); ?>
            <h2 class="text-center"><?php echo $model->study_title;?></h2>
            <div>
                <?php echo $model->study_body;?>
            </div>
            <p class="text-right"><small><?php echo $model->study_date;?></small></p>
		</div>
	</div>
</section>

<?php

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


