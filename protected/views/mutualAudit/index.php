<?php
if($this->function_id!=$model->menu_code){
    $this->redirect(Yii::app()->createUrl('site/index'));
}
$this->pageTitle=Yii::app()->name . ' - MutualAudit';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'MutualAudit-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1><?php echo Yii::t("app","Study mutual audit");?></h1>
</section>

<section class="content">
    <?php if (Yii::app()->user->validRWFunction($model->menu_code)): ?>
    <div class="box">
        <div class="box-body">
            <div class="btn-group" role="group">
                <?php
                echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('study','audit all'), array(
                    'submit'=>Yii::app()->createUrl('mutualAudit/auditAll',array("index"=>$model->menu_id)),
                ));
                ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php
    $search = array(
        'employee_code',
        'employee_name',
        'end_body',
    );
    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('study','study mutual list'),
        'model'=>$model,
        'viewhdr'=>'//mutualAudit/_listhdr',
        'viewdtl'=>'//mutualAudit/_listdtl',
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
$('#checkAll').click(function(){
    if($(this).is(':checked')){
        $('.checkOne').prop('checked',true);
    }else{
        $('.checkOne').prop('checked',false);
    }
});

$('.end_click').click(function(e){
    e.stopPropagation();
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genTableRowClick();
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

