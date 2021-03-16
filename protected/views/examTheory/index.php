<?php
$this->pageTitle=Yii::app()->name . ' - Question';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'question-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    .link-red{ color: red;margin-left: 15px;}
    .form-control-static{ font-size: 16px;}
</style>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','exam(Theory + practice)'); ?></strong>
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
            &nbsp;
            <?php if ($flowTitleModel->scenario=='edit'): ?>
                <div class="btn-group pull-right" role="group">
                    <?php echo TbHtml::button('<span class="fa fa-superpowers"></span> '.Yii::t('examina','Browse mode'), array(
                        'submit'=>Yii::app()->createUrl('examTheory/view')));
                    ?>
                </div>
            <?php endif ?>
        </div></div>

    <div class="box box-info">
        <div class="box-body">
            <div class="form-group">
                <div class="col-lg-8">
                    <div class="form-control-static">
                        <?php
                        echo $flowTitleModel->getFlowTitle("code5_1");

                        echo $flowTitleModel->getUpdateLink("code5_1",$flowTitleModel->scenario=='edit');
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-8">
                    <div class="form-control-static">

                        <?php
                        $counter = ($flowTitleModel->no_of_attm['flowth'] > 0) ? ' <span id="docflowth" class="label label-info">'.$flowTitleModel->no_of_attm['flowth'].'</span>' : ' <span id="docflowth"></span>';
                        echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('examina','down excel').$counter, array(
                                'name'=>'btnFile','id'=>'btnFile','data-toggle'=>'modal','data-target'=>'#fileuploadflowth',)
                        );
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
$flowTitleModel->id = 1;
$this->renderPartial('//site/fileupload',array('model'=>$flowTitleModel,
    'form'=>$form,
    'doctype'=>'FLOWTH',
    //'maxSize'=>1024*500,
    'header'=>Yii::t('examina','down excel'),
    'ronly'=>(!Yii::app()->user->validRWFunction('TP05')),
));
?>
<?php

Script::genFileUpload($flowTitleModel,$form->id,'FLOWTH');
?>
<?php $this->endWidget(); ?>


