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
        <strong><?php echo Yii::t('app','Communication answer'); ?></strong>
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
                        'submit'=>Yii::app()->createUrl('answer/view')));
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
                        echo $flowTitleModel->getFlowTitle("code4_1");

                        echo $flowTitleModel->getUpdateLink("code4_1",$flowTitleModel->scenario=='edit');
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-8">
                    <div class="form-control-static">

                        <?php echo TbHtml::button(Yii::t('examina','click for photo'),array(
                                'class'=>'btn btn-link link-red',"id"=>"viewer_code4_1")
                        );
                        ?>
                        <?php if ($flowTitleModel->scenario=='edit'): ?>
                            <?php
                            echo TbHtml::link(Yii::t('examina',"upload for photo"),
                                Yii::app()->createUrl('flowTitle/uploadPhoto',array('code'=>"code4_1")),
                                array('class'=>'link-red'));
                            ?>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
$this->renderPartial('//site/clickphoto',array(
    'flow_code'=>"code4_1"
));
?>
<?php $this->endWidget(); ?>


