<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('category/index'));
}
$this->pageTitle=Yii::app()->name . ' - category Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'category-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('examina','Test categories form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('category/index')));
		?>

        <?php if ($model->scenario!='view'): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('category/save')));
            ?>
            <?php if ($model->scenario=='edit'): ?>
                <?php
                echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('examina','add category'), array(
                    'submit'=>Yii::app()->createUrl('category/new'),
                ));
                ?>
                <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                        'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
                );
                ?>
            <?php endif; ?>
        <?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'bumen',array("id"=>"bumen")); ?>

			<div class="form-group">
				<?php echo $form->labelEx($model,'name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'name',
                        array('readonly'=>($model->scenario=='view'))
                    );
                    ?>
                </div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'bumen_ex',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'bumen_ex',
                        array('readonly'=>(true),'rows'=>4,"id"=>"bumen_ex")
                    ); ?>
                </div>
                <div class="col-sm-2">
                    <?php
                    echo TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('examina','select'),
                        array('data-toggle'=>'modal','data-target'=>'#bumendialog',)
                    );
                    ?>
                </div>
			</div>

		</div>
	</div>
</section>

<?php
$this->renderPartial('//site/removedialog');
?>
<?php
$this->renderPartial('//site/bumendialog');
?>
<?php

$js = Script::genDeleteData(Yii::app()->createUrl('category/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

