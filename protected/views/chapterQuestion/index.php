<?php
if($this->function_id!=$model->menu_code){
    $this->redirect(Yii::app()->createUrl('site/index'));
}
$this->pageTitle=Yii::app()->name . ' - ChapterQuestion';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'chapterQuestion-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo $model->chapter_name;?></strong>
    </h1>
    <ol class="breadcrumb">
        <li><?php echo $model->menu_name;?></li>
        <li>
            <?php
            echo TbHtml::link(Yii::t("app","Mock test"),Yii::app()->createUrl('MockChapter/index',array("index"=>$model->menu_id)));
            ?>
        </li>
        <li class="active"><?php echo $model->chapter_name;?></li>
    </ol>
</section>

<section class="content">
    <div class="box"><div class="box-body">
            <div class="btn-group" role="group">
                <?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
                    'submit'=>Yii::app()->createUrl('mockChapter/index',array("index"=>$model->menu_id))));
                ?>
                <?php
                //var_dump(Yii::app()->session['rw_func']);
                if (Yii::app()->user->validRWFunction($model->menu_code))
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('study','add question'), array(
                        'submit'=>Yii::app()->createUrl('chapterQuestion/new',array('chapter_id'=>$model->chapter_id)),
                    ));
                ?>
            </div>
            <div class="btn-group pull-right" role="group">
                <?php
                if (Yii::app()->user->validRWFunction($model->menu_code)){
                    //下载导入模板
                    echo TbHtml::link('<span class="fa fa-download"></span> '.Yii::t('study','download question model'),Yii::app()->createUrl('chapterQuestion/download'), array(
                        'class'=>"btn btn-default","target"=>"_blank"));
                    //導入
                    echo TbHtml::button('<span class="fa fa-file-text-o"></span> '.Yii::t('study','Import File'), array(
                        'data-toggle'=>'modal','data-target'=>'#importQuestion'));
                }
                ?>
            </div>
        </div></div>
    <?php
    $search = array(
        'title_code',
        'name',
        'title_type',
    );
    //if (!Yii::app()->user->isSingleCity()) $search[] = 'city_name';
   $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('study','chapter question list'),
        'model'=>$model,
        'viewhdr'=>'//chapterQuestion/_listhdr',
        'viewdtl'=>'//chapterQuestion/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search'=>$search,
        'searchlinkparam'=>array("chapter_id"=>$model->chapter_id),
    ));
    ?>
</section>
<?php
echo $form->hiddenField($model,'pageNum');
echo $form->hiddenField($model,'totalRow');
echo $form->hiddenField($model,'orderField');
echo $form->hiddenField($model,'orderType');
echo $form->hiddenField($model,'chapter_id');
?>

<?php
if (Yii::app()->user->validRWFunction($model->menu_code)){
    $this->renderPartial('//site/uploadExcel',array(
        'url'=>Yii::app()->createUrl('chapterQuestion/upload'),
        "model"=>$model
    ));
}
?>
<?php $this->endWidget(); ?>

<?php
if (Yii::app()->user->validRWFunction($model->menu_code))
    //$this->renderPartial('//site/importQuestion',array('name'=>"UploadExcelForm","model"=>$model));
?>

<?php
$js = Script::genTableRowClick();
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

