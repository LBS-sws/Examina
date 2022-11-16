<?php
if($this->function_id!=$model->menu_code){
    $this->redirect(Yii::app()->createUrl('site/index'));
}
$this->pageTitle=Yii::app()->name . ' - MockChapter Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'MockChapter-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('study','Chapter Form'); ?></strong>
	</h1>
    <ol class="breadcrumb">
        <li><?php echo $model->menu_name;?></li>
        <li>
            <?php
            echo TbHtml::link(Yii::t("app","Mock test"),Yii::app()->createUrl('MockChapter/index',array("index"=>$model->menu_id)));
            ?>
        </li>
        <li class="active"><?php echo Yii::t("study","Chapter Form");?></li>
    </ol>
</section>

<section class="content">
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php 
			if ($model->scenario!='new' && $model->scenario!='view') {
				echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add Another'), array(
					'submit'=>Yii::app()->createUrl('MockChapter/add',array("index"=>$model->menu_id))));
			}
		?>
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('MockChapter/index',array("index"=>$model->menu_id))));
		?>
<?php if ($model->scenario!='view'): ?>
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('MockChapter/save')));
			?>
<?php endif ?>
<?php if ($model->scenario!='new' && $model->scenario!='view'): ?>
	<?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
			'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
		);
	?>
<?php endif ?>
	</div>
            <?php if ($model->scenario!='new' && $model->scenario!='view'): ?>
            <div class="btn-group pull-right" role="group">
                <?php echo TbHtml::button('<span class="fa fa-list"></span> '.Yii::t('study','chapter question list'), array(
                    'submit'=>Yii::app()->createUrl('ChapterQuestion/index',array("chapter_id"=>$model->id))));
                ?>
            </div>
            <?php endif ?>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'menu_id'); ?>

			<div class="form-group">
				<?php echo $form->labelEx($model,'chapter_name',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-3">
				<?php
                echo $form->textField($model, 'chapter_name',
					array('readonly'=>($model->scenario=='view'))
				); ?>
				</div>
			</div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'random_num',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
                    <?php
                    echo $form->numberField($model, 'random_num',
                        array('readonly'=>($model->scenario=='view'),'min'=>1)
                    ); ?>
                </div>
                <div class="col-lg-7">
                    <p class="form-control-static text-warning">随机试题数量大于试题总数时，自动随机试题总数</p>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'item_sum',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
                    <?php
                    echo $form->numberField($model, 'item_sum',
                        array('readonly'=>(true))
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

<?php $this->renderPartial('//site/removedialog'); ?>

<?php
$js = Script::genDeleteData(Yii::app()->createUrl('MockChapter/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


