<?php
$this->pageTitle=Yii::app()->name . ' - statisticsAll';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'statisticsAll-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo "所有答题列表"; ?></strong>
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
                if (Yii::app()->user->validRWFunction('SC04'))
                    echo TbHtml::button('返回', array(
                        'submit'=>Yii::app()->createUrl('statisticsQuiz/index'),
                    ));
                ?>
            </div>
        </div></div>
    <?php
    $search = array(
        'quiz_name',
        'employee_name',
        'city_name',
    );
    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>"所有测试人员",
        'model'=>$model,
        'viewhdr'=>'//statisticsAll/_listhdr',
        'viewdtl'=>'//statisticsAll/_listdtl',
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
//$js = Script::genTableRowClick();
//Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

