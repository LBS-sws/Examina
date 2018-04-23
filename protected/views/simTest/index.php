<?php
$this->pageTitle=Yii::app()->name . ' - simTest Form';
?>
<style>
    #staffDiv .checkbox-inline{width: 100px;}
</style>
<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'simTest-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','Simulation test'); ?></strong>
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
                <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('examina','start test'), array(
                    'submit'=>Yii::app()->createUrl('simTest/save')));
                ?>
            </div>
        </div></div>

    <div class="box box-info">
        <div class="box-body">

            <div class="form-group">
                <div class="col-sm-8 col-sm-offset-2 text-primary">
                    <p class="form-control-static">
                        <?php echo Yii::t("examina","The results of the simulation test are not included in the test results, only for personal reference."); ?>
                    </p>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'exa_num',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->numberField($model, 'exa_num',
                        array('readonly'=>(false),'min'=>1)
                    ); ?>
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

