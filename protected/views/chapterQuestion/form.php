<?php
if($this->function_id!=$model->menu_code){
    $this->redirect(Yii::app()->createUrl('site/index'));
}
$this->pageTitle=Yii::app()->name . ' - ChapterQuestion Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'chapterQuestion-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>
<style>
    select[readonly="readonly"]{ pointer-events: none;}
</style>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t("study","chapter question form");?></strong>
    </h1>
    <ol class="breadcrumb">
        <li><?php echo $model->menu_name;?></li>
        <li>
            <?php
            echo TbHtml::link(Yii::t("app","Mock test"),Yii::app()->createUrl('MockChapter/index',array("index"=>$model->menu_id)));
            ?>
        </li>
        <li>
            <?php
            echo TbHtml::link($model->chapter_name,Yii::app()->createUrl('ChapterQuestion/index',array("chapter_id"=>$model->chapter_id)));
            ?>
        </li>
        <li class="active"><?php echo Yii::t("study","chapter question form");?></li>
    </ol>
</section>

<section class="content">
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('chapterQuestion/index',array('chapter_id'=>$model->chapter_id))));
		?>

        <?php if ($model->scenario!='view'): ?>
            <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
                'submit'=>Yii::app()->createUrl('chapterQuestion/save')));
            ?>
            <?php if ($model->scenario=='edit'): ?>
                <?php
                echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('study','add question'), array(
                    'submit'=>Yii::app()->createUrl('chapterQuestion/new',array('chapter_id'=>$model->chapter_id)),
                ));
                ?>
                <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                        'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
                );
                ?>
            <?php endif; ?>
        <?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'chapter_id'); ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'chapter_name',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->textField($model, 'chapter_name',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <?php if ($model->scenario!='new'): ?>
			<div class="form-group">
				<?php echo $form->labelEx($model,'title_code',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->textField($model, 'title_code',
                        array('readonly'=>(true))
                    ); ?>
                </div>
			</div>
            <?php endif ?>
			<div class="form-group">
				<?php echo $form->labelEx($model,'title_type',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->dropDownList($model, 'title_type',ChapterQuestionList::choiceList(),
                        array('readonly'=>($model->scenario=='view'||!empty($model->show_num)),"id"=>"title_type")
                    ); ?>
                </div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'name',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-5">
                    <?php echo $form->textArea($model, 'name',
                        array('readonly'=>($model->scenario=='view'),'rows'=>4)
                    ); ?>
                </div>
			</div>

            <div class="form-group">
                <?php echo TbHtml::label(Yii::t('study','choose').'<span class="required">*</span>',"",array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-9">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th width="70%">选项内容</th>
                            <th width="30%">是否正确选项</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $className = get_class($model);
                        $html = "";
                        if (!empty($model->answerList)){
                            foreach ($model->answerList as $key=>$row){
                                $color = $model->answerList[$key]["judge"]==1?"success":"warning";
                                $html.="<tr class='tr-choose {$color}'>";
                                $html.="<td>";
                                $html.=TbHtml::textArea($className."[answerList][{$key}][choose]",$model->answerList[$key]["choose"],array('readonly'=>($model->scenario=='view'),'rows'=>2));
                                $html.=TbHtml::hiddenField($className."[answerList][{$key}][id]",$model->answerList[$key]["id"]);
                                $html.=TbHtml::hiddenField($className."[answerList][{$key}][display]",$model->answerList[$key]["display"]);
                                $html.="</td>";
                                $html.="<td>";
                                $html.=TbHtml::dropDownList($className."[answerList][{$key}][judge]",$model->answerList[$key]["judge"],
                                    array(Yii::t("study","wrong answer"),Yii::t("study","correct answer")),array('readonly'=>($model->scenario=='view'||!empty($model->show_num)),'class'=>'judge'));
                                $html.="</td>";
                                $html.="</tr>";
                            }
                        }
                        echo $html;
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'remark',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-5">
                    <?php echo $form->textArea($model, 'remark',
                        array('readonly'=>($model->scenario=='view'),'rows'=>4)
                    ); ?>
                </div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'display',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-5">
                    <?php echo $form->radioButtonList($model, 'display',array(0=>Yii::t("study","none"),1=>Yii::t("study","show"),),
                        array('readonly'=>($model->scenario=='view'),'inline'=>true)
                    ); ?>
                </div>
			</div>

		</div>
	</div>
</section>

<?php
$this->renderPartial('//site/removedialog');
?>
<?php
$js="
$('.judge').change(function(){
    if($(this).val()==1){
        $(this).parents('.tr-choose').eq(0).removeClass('warning').addClass('success');
    }else{
        $(this).parents('.tr-choose').eq(0).removeClass('success').addClass('warning');
    }
});

$('#title_type').change(function(){
    var title_type = $(this).val();
    if(title_type==1||title_type==0){
        $('.tr-choose').show();
    }else if(title_type==2){
        $('.tr-choose').hide();
        $('.tr-choose').slice(0,2).show();
    }
}).trigger('change');
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genDeleteData(Yii::app()->createUrl('chapterQuestion/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

