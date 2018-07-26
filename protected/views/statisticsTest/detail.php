<?php
$this->pageTitle=Yii::app()->name . ' - statisticsDetail';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'statisticsDetail-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','Test results statistics'); ?></strong>
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
    <div class="box">
        <div class="box-body">
            <div class="btn-group" role="group">
                <?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
                    'submit'=>Yii::app()->createUrl('statisticsTest/view',array("index"=>$model->qui_id))));
                ?>
            </div>
        </div>
    </div>

    <?php
    $search = array(
        'employee_name',
        'city',
    );
   $this->widget('ext.layout.ListPageWidget', array(
        'title'=>$model->examinaName,
        'model'=>$model,
        'viewhdr'=>'//statisticsTest/_listhdr_detail',
        'viewdtl'=>'//statisticsTest/_listdtl_detail',
        'gridsize'=>'24',
        'height'=>'600',
       'searchlinkparam'=>array('index'=>$model->qui_id,'staff'=>$model->staff),
        'search'=>$search,
    ));
    ?>
</section>
<?php
echo $form->hiddenField($model,'pageNum');
echo $form->hiddenField($model,'totalRow');
echo $form->hiddenField($model,'orderField');
echo $form->hiddenField($model,'orderType');
?>
<?php $this->endWidget(); ?>

<?php
$js = Script::genTableRowClick();
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

