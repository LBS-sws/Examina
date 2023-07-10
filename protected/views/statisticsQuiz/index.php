<?php
$this->pageTitle=Yii::app()->name . ' - statisticsQuiz';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'statisticsQuiz-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','Quiz results statistics'); ?></strong>
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
                <?php
                //var_dump(Yii::app()->session['rw_func']);
                if (Yii::app()->user->validFunction('SS03'))
                    echo TbHtml::button('所有测试', array(
                        'submit'=>Yii::app()->createUrl('statisticsAll/index'),
                    ));
                ?>
            </div>
        </div></div>
    <?php
    $search = array(
        'employee_name',
        'city',
        //'endDate',
        //'question',
    );
   $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('app','Quiz results statistics'),
        'model'=>$model,
        'viewhdr'=>'//statisticsQuiz/_listhdr',
        'viewdtl'=>'//statisticsQuiz/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
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

