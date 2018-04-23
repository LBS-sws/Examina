<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('statisticsTest/view',array("index"=>$model->id)));
}
$this->pageTitle=Yii::app()->name . ' - statisticsTest Form';
?>
<style>
    #staffDiv .checkbox-inline{width: 100px;}
</style>
<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'statisticsTest-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('examina','test results')." - ".$model->getEmployeeNameToId($model->employee_id); ?></strong>
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
                    'submit'=>Yii::app()->createUrl('statisticsTest/view',array("index"=>$model->id))));
                ?>
            </div>
        </div></div>

    <div class="box box-info">
        <div class="box-body">
            <?php echo $form->hiddenField($model, 'scenario'); ?>
            <?php echo $form->hiddenField($model, 'id'); ?>

            <?php
            $this->renderPartial('//site/testTopForm',array(
                'form'=>$form,
                'model'=>$model,
                'readonly'=>(true),
            ));
            ?>
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
    $('#staff_all').on('change',function(){
        if($(this).val() == 1){
            $('#staffList').slideUp(100);
        }else{
            $('#staffList').slideDown(100);
        }
    }).trigger('change');
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

