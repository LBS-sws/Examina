<?php
$this->pageTitle=Yii::app()->name . ' - statisticsTitle';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'statisticsTitle-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('app','Title results statistics'); ?></strong>
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
    <?php
    $search = array(
        'title_code',
        'name',
    );
    $modelName = get_class($model);
    $search_add_html="";
    if(!Yii::app()->user->isSingleCity()){
        $search_add_html = TbHtml::dropDownList($modelName.'[searchCity]',$model->searchCity,$model->getThisUserCityList(),
            array('size'=>15,'placeholder'=>Yii::t('misc','Start Date'),"class"=>"form-control","id"=>"searchCity"));
    }
    $search_add_html .= TbHtml::dropDownList($modelName.'[searchTitle]',$model->searchTitle,$model->getAllTestTopList(),
        array('size'=>15,'placeholder'=>Yii::t('misc','Start Date'),"class"=>"form-control","id"=>"searchTitle"));
   $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('app','Title results statistics'),
        'model'=>$model,
        'viewhdr'=>'//statisticsTitle/_listhdr',
        'viewdtl'=>'//statisticsTitle/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
       'search_add_html'=>$search_add_html,
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

