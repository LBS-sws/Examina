<?php
if($this->function_id!=$model->menu_code){
    $this->redirect(Yii::app()->createUrl('site/index'));
}
$this->pageTitle=Yii::app()->name . ' - markedlyTest';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'markedlyTest-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','Markedly test'); ?></strong>

        <?php if (Yii::app()->user->validRWFunction($model->menu_code)): ?>
            <small>“综合测验”的唯读权限，会根据员工的正确率来限制员工使用其它系统</small>
        <?php endif; ?>
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
                if (Yii::app()->user->validRWFunction($model->menu_code))
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                        'submit'=>Yii::app()->createUrl('markedlyTest/new',array("menu_id"=>$model->menu_id)),
                    ));
                ?>
            </div>
        </div></div>
    <?php
    $search = array(
        'name',
        'exa_num',
        'bumen_ex',
    );
   $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('app','Test list'),
        'model'=>$model,
        'viewhdr'=>'//markedlyTest/_listhdr',
        'viewdtl'=>'//markedlyTest/_listdtl',
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
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
if (Yii::app()->user->validRWFunction($model->menu_code)){
    $js = Script::genTableRowClick();
    Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
}
?>

