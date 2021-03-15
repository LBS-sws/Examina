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
        <strong><?php echo Yii::t('app','System exam(In previous)'); ?></strong>
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
                        'submit'=>Yii::app()->createUrl('examPrevious/view')));
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
                        echo $flowTitleModel->getFlowTitle("code6_1");

                        echo $flowTitleModel->getUpdateLink("code6_1",$flowTitleModel->scenario=='edit');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $this->endWidget(); ?>


