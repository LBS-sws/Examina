<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('question/index'));
}
$this->pageTitle=Yii::app()->name . ' - question Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'question-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('examina','question form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('question/index')));
		?>

        <?php if ($model->scenario!='view'): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('question/save')));
            ?>
            <?php if ($model->scenario=='edit'): ?>
                <?php
                echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('examina','add question'), array(
                    'submit'=>Yii::app()->createUrl('question/new'),
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
            <?php if ($model->scenario!='new'): ?>
			<div class="form-group">
				<?php echo $form->labelEx($model,'title_code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'title_code',
                        array('readonly'=>(true))
                    ); ?>
                </div>
			</div>
            <?php endif ?>
			<div class="form-group">
				<?php echo $form->labelEx($model,'name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'name',
                        array('readonly'=>($model->scenario=='view'),'rows'=>4)
                    ); ?>
                </div>
			</div>

            <div class="form-group">
                <?php echo TbHtml::label(Yii::t('examina','correct answer').'<span class="required">*</span>',"",array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php
                        $className = get_class($model);
                        echo TbHtml::textArea($className."[answerList][0][choose]",$model->answerList[0]["choose"],array('readonly'=>($model->scenario=='view'),'rows'=>4));
                    ?>
                </div>
                <?php
                echo TbHtml::hiddenField($className."[answerList][0][id]",$model->answerList[0]["id"]);
                echo TbHtml::hiddenField($className."[answerList][1][id]",$model->answerList[1]["id"]);
                echo TbHtml::hiddenField($className."[answerList][2][id]",$model->answerList[2]["id"]);
                echo TbHtml::hiddenField($className."[answerList][3][id]",$model->answerList[3]["id"]);
                ?>
            </div>
            <div class="form-group">
                <?php echo TbHtml::label(Yii::t('examina','wrong answer A').'<span class="required">*</span>',"",array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php
                        echo TbHtml::textArea($className."[answerList][1][choose]",$model->answerList[1]["choose"],array('readonly'=>($model->scenario=='view'),'rows'=>4));
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo TbHtml::label(Yii::t('examina','wrong answer B').'<span class="required">*</span>',"",array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php
                        echo TbHtml::textArea($className."[answerList][2][choose]",$model->answerList[2]["choose"],array('readonly'=>($model->scenario=='view'),'rows'=>4));
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo TbHtml::label(Yii::t('examina','wrong answer C').'<span class="required">*</span>',"",array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php
                        echo TbHtml::textArea($className."[answerList][3][choose]",$model->answerList[3]["choose"],array('readonly'=>($model->scenario=='view'),'rows'=>4));
                    ?>
                </div>
            </div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-5">
                    <?php echo $form->textArea($model, 'remark',
                        array('readonly'=>($model->scenario=='view'),'rows'=>4)
                    ); ?>
                </div>
			</div>

		</div>
	</div>
</section>

<?php
$this->renderPartial('//site/removedialog');
?>
<?php

$js = Script::genDeleteData(Yii::app()->createUrl('question/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

