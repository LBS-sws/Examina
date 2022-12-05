<?php
if($this->function_id!=$model->menu_code){
    $this->redirect(Yii::app()->createUrl('site/index'));
}
$this->pageTitle=Yii::app()->name . ' - Mutual Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'Mutual-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('study','study mutual form'); ?></strong>
	</h1>
    <ol class="breadcrumb">
        <li><?php echo $model->menu_name;?></li>
        <li><?php echo TbHtml::link(Yii::t("app","Study mutual"),Yii::app()->createUrl('mutual/index',array("index"=>$model->menu_id)));?></li>
        <li><?php echo TbHtml::link(Yii::t("study","My study mutual"),Yii::app()->createUrl('mutual/my',array("index"=>$model->menu_id)));?></li>
        <li class="active"><?php echo Yii::t("study","study mutual form");?></li>
    </ol>
</section>

<section class="content">
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php 
			if ($model->scenario!='new' && $model->scenario!='view') {
				echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add Another'), array(
					'submit'=>Yii::app()->createUrl('mutual/add',array("index"=>$model->menu_id))));
			}
		?>
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('mutual/my',array("index"=>$model->menu_id))));
		?>
<?php if ($model->scenario!='view'&&in_array($model->mutual_state,array(0,3))): ?>
			<?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('study','draft'), array(
				'submit'=>Yii::app()->createUrl('mutual/draft')));
			?>
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('study','submit'), array(
				'submit'=>Yii::app()->createUrl('mutual/audit')));
			?>
<?php endif ?>
<?php if ($model->scenario!='new' && $model->scenario!='view'&&in_array($model->mutual_state,array(0,3))): ?>
	<?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
			'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
		);
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

            <?php if ($model->mutual_state==3): ?>
                <div class="form-group has-error">
                    <?php echo $form->labelEx($model,'reject_remark',array('class'=>"col-lg-2 control-label")); ?>
                    <div class="col-lg-5">
                        <?php
                        echo $form->textArea($model, 'reject_remark',
                            array('readonly'=>(true),'rows'=>4)
                        ); ?>
                    </div>
                </div>
            <?php endif ?>
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
					array('readonly'=>(true))
				); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'mutual_body',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-5">
				<?php
                echo $form->textArea($model, 'mutual_body',
					array('readonly'=>($model->scenario=='view'||!in_array($model->mutual_state,array(0,3))),'rows'=>4)
				); ?>
				</div>
			</div>
            <?php if ($model->mutual_body!=$model->end_body): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'end_body',array('class'=>"col-lg-2 control-label")); ?>
                    <div class="col-lg-5">
                        <?php
                        echo $form->textArea($model, 'end_body',
                            array('readonly'=>(true),'rows'=>4)
                        ); ?>
                    </div>
                </div>
            <?php endif ?>
            <!--
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
			-->
		</div>
	</div>
</section>

<?php $this->renderPartial('//site/removedialog'); ?>

<?php
$js = Script::genDeleteData(Yii::app()->createUrl('mutual/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


