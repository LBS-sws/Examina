<?php
if($this->function_id!=$model->menu_code){
    $this->redirect(Yii::app()->createUrl('site/index'));
}
$this->pageTitle=Yii::app()->name . ' - MutualMy';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'MutualMy-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1><?php echo Yii::t("study","My study mutual");?></h1>
    <ol class="breadcrumb">
        <li><?php echo $model->menu_name;?></li>
        <li><?php echo TbHtml::link(Yii::t("app","Study mutual"),Yii::app()->createUrl('mutual/index',array("index"=>$model->menu_id)));?></li>
        <li class="active"><?php echo Yii::t("study","My study mutual");?></li>
    </ol>
</section>

<section class="content">
    <div class="box">
        <div class="box-body">
            <div class="btn-group" role="group">
                <?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
                    'submit'=>Yii::app()->createUrl('mutual/index',array("index"=>$model->menu_id))));
                ?>
            </div>
            <?php if (Yii::app()->user->validRWFunction($model->menu_code)): ?>
                <div class="btn-group pull-right" role="group">
                    <?php
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('study','publish'), array(
                        'submit'=>Yii::app()->createUrl('mutual/add',array("index"=>$model->menu_id)),
                    ));
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    $search = array(
        'end_body'
    );
   $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('study','study mutual list'),
        'model'=>$model,
        'viewhdr'=>'//mutual/_listhdr',
        'viewdtl'=>'//mutual/_listdtl',
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

