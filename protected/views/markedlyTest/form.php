<?php
if($this->function_id!=$model->menu_code){
    $this->redirect(Yii::app()->createUrl('site/index'));
}
$this->pageTitle=Yii::app()->name . ' - markedlyTest Form';
?>
<style>
    #staffDiv .checkbox-inline{width: 100px;}
</style>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'markedlyTest-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('study','test form'); ?></strong>
	</h1>
    <ol class="breadcrumb">
        <li><?php echo $model->menu_name;?></li>
        <li class="active"><?php echo Yii::t('app','Markedly test'); ?></li>
    </ol>
</section>

<section class="content">
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('markedlyTest/index',array("index"=>$model->menu_id))));
		?>

        <?php if ($model->scenario!='view'): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('markedlyTest/save')));
            ?>
            <?php if ($model->scenario=='edit'): ?>
                <?php
                echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                    'submit'=>Yii::app()->createUrl('markedlyTest/new',array("menu_id"=>$model->menu_id)),
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
            <?php echo $form->hiddenField($model, 'menu_id',array("id"=>"menu_id")); ?>
            <?php echo $form->hiddenField($model, 'bumen',array("id"=>"bumen")); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'name',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-4">
                    <?php echo $form->textField($model, 'name',
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'dis_name',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-5">
                    <?php echo $form->textArea($model, 'dis_name',
                        array('readonly'=>($model->scenario=='view'),'rows'=>3)
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'join_must',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->dropDownList($model,"join_must",MarkedlyTestList::getTestType(),array('readonly'=>($model->scenario=='view')))?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'exa_num',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->numberField($model, 'exa_num',
                        array('readonly'=>($model->scenario=='view'),'min'=>1)
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'bumen_ex',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-5">
                    <?php echo $form->textArea($model, 'bumen_ex',
                        array('readonly'=>(true),'rows'=>4,"id"=>"bumen_ex")
                    ); ?>
                </div>
                <div class="col-lg-2">
                    <?php
                    echo TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('study','select'),
                        array('data-toggle'=>'modal','data-target'=>'#articleSelectDialog',)
                    );
                    ?>
                </div>
            </div>
		</div>
	</div>
</section>

<?php
$this->renderPartial('//site/removedialog');
$this->renderPartial('//site/articleSelectDialog');
?>
<?php
$js = "
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genDeleteData(Yii::app()->createUrl('markedlyTest/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

