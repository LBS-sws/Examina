<?php
if($this->function_id!=$model->menu_code){
    $this->redirect(Yii::app()->createUrl('site/index'));
}
$this->pageTitle=Yii::app()->name . ' - paperWrong';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'paperWrong-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','Paper Wrong'); ?></strong>
        <small>删除错题，需要进行“错题纠正”并且回答正确</small>
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
            <div class="btn-group pull-right" role="group">
                <?php
                echo TbHtml::link(Yii::t('study','Correction of errors'),
                    Yii::app()->createUrl('paperWrong/new',array("menu_id"=>$model->menu_id)),
                    array("class"=>"btn btn-default")
                );
                ?>
            </div>
        </div>
    </div>
    <?php
    $search = array(
        'name'
    );
   $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('study','wrong question list'),
        'model'=>$model,
        'viewhdr'=>'//paperWrong/_listhdr',
        'viewdtl'=>'//paperWrong/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search'=>$search,
       'searchlinkparam'=>array("index"=>$model->menu_id),
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
$js = "
$('.end_click').click(function(e){
    e.stopPropagation();
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genTableRowClick();
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

