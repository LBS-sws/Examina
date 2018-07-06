<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('testTop/index'));
}
$this->pageTitle=Yii::app()->name . ' - testTop Form';
?>
<style>
    #staffDiv .checkbox-inline{width: 100px;}
</style>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'testTop-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('examina','test form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('testTop/index')));
		?>

        <?php if ($model->scenario!='view'): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('testTop/save')));
            ?>
            <?php if ($model->scenario=='edit'): ?>
                <?php
                echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                    'submit'=>Yii::app()->createUrl('testTop/new'),
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

            <?php
            $this->renderPartial('//site/testTopForm',array(
                'form'=>$form,
                'model'=>$model,
                'readonly'=>($model->scenario=='view'),
            ));
            ?>

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

            <legend><?php echo Yii::t("examina","Scope of application")?></legend>
            <div class="form-group">
                <?php echo $form->labelEx($model,'city',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'city',$model->getAllCityList(),
                        array('readonly'=>($model->scenario=='view'),"id"=>"city")
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'staff_all',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'staff_all',array(1=>Yii::t("examina","all staff"),0=>Yii::t("examina","custom")),
                        array('readonly'=>($model->scenario=='view'),"id"=>"staff_all")
                    ); ?>
                </div>
            </div>
            <div class="form-group" id="staffList" style="display: none;">
                <?php echo $form->labelEx($model,'staffList',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-8" id="staffDiv">
                    <?php if (!empty($model->city)): ?>
                        <?php echo $form->inlineCheckBoxList($model, 'staffList',$model->getAllStaffList($model->city),
                            array('disabled'=>($model->scenario=='view'))
                        ); ?>
                    <?php else:;?>
                        <label class="control-label text-warning">请选择城市</label>
                    <?php endif; ?>
                </div>
            </div>
		</div>
	</div>
</section>

<?php
$this->renderPartial('//site/removedialog');
$this->renderPartial('//site/bumendialog');
?>
<?php
$js = "
    var bool = false;
    $('#city').on('change',function(){
        $('#staff_all').val(1).trigger('change');
    });
    $('#staff_all').on('change',function(){
        if($(this).val() == 1){
            $('#staffList').slideUp(100);
        }else{
            if(bool){
                $.ajax({
                    type: 'post',
                    url: '".Yii::app()->createUrl('testTop/AjaxStaff')."',
                    data: {city:$('#city').val()},
                    dataType: 'json',
                    success: function(data){
                        if(data.status == 1){
                            var list = data['data'];
                            $('#staffDiv').html('');
                            $.each(list, function(i, n){
                                $('#staffDiv').append('<label class=\"checkbox-inline\"><input value=\"'+i+'\" type=\"checkbox\" name=\"TestTopForm[staffList][]\"> '+n+'</label>');
                            });
                        }else{
                            $('#staffDiv').html('<label class=\"control-label text-warning\">请重新选择城市</label>');
                        }
                    }
                });
            }
            $('#staffList').slideDown(100);
        }
        bool = true;
    }).trigger('change');
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genDeleteData(Yii::app()->createUrl('testTop/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

if ($model->scenario!='view') {
    $js = Script::genDatePicker(array(
        'start_time',
        'end_time',
    ));
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

