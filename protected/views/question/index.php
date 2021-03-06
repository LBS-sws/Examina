<?php
$this->pageTitle=Yii::app()->name . ' - Question';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'question-list',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo TestTopForm::getQuizTitleName($model->index); ?></strong>
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
                <?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
                    'submit'=>Yii::app()->createUrl('testTop/index')));
                ?>
                <?php
                //var_dump(Yii::app()->session['rw_func']);
                if (Yii::app()->user->validRWFunction('SS02'))
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('examina','add question'), array(
                        'submit'=>Yii::app()->createUrl('question/new',array('quiz_id'=>$model->index)),
                    ));
                ?>
            </div>
            <div class="btn-group pull-right" role="group">
                <?php if (Yii::app()->user->validRWFunction('SS02')){
                    //導入
                    echo TbHtml::button('<span class="fa fa-file-text-o"></span> '.Yii::t('examina','Import File'), array(
                        'data-toggle'=>'modal','data-target'=>'#importQuestion'));
                } ?>
            </div>
        </div></div>
    <?php
    $search = array(
        'title_code',
        'name',
        'type_name',
    );
    //if (!Yii::app()->user->isSingleCity()) $search[] = 'city_name';
   $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('examina','question list'),
        'model'=>$model,
        'viewhdr'=>'//question/_listhdr',
        'viewdtl'=>'//question/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search'=>$search,
        'searchlinkparam'=>array("index"=>$model->index),
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
if (Yii::app()->user->validRWFunction('SS02'))
    $this->renderPartial('//site/importQuestion',array('name'=>"UploadExcelForm","model"=>$model));
?>

<?php
$js = Script::genTableRowClick();
Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

