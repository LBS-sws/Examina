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
		<strong>图片设置 - <?php echo $model->getFlowName(); ?></strong>
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
                <div class="col-sm-10 col-sm-offset-1">
                    <table class="table table-bordered" id="table_photo">
                        <thead>
                        <tr>
                            <th width="40%">图片</th>
                            <th width="40%">图片说明</th>
                            <th width="10%">图片层级</th>
                            <th width="10%">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        echo $model->getTableBody();
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td>
                                <?php
                                echo $form->fileField($model,"flow_photo");
                                ?>
                            </td>
                            <td>
                                <?php
                                echo $form->textArea($model,"flow_name",array('row'=>2));
                                ?>
                            </td>
                            <td>
                                <?php
                                echo $form->numberField($model,"z_index");
                                ?>
                            </td>
                            <td>
                                <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('examina','upload'), array(
                                    'submit'=>Yii::app()->createUrl("flowTitle/photoSave")));
                                ?>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-8 col-sm-offset-2">
                    <p class="form-control-static">
                        图片层级越大，显示越靠后
                    </p>
                </div>
            </div>
		</div>
	</div>
</section>

<?php

$js = "
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

