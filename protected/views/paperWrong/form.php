<?php
if($this->function_id!=$model->menu_code){
    $this->redirect(Yii::app()->createUrl('site/index'));
}
$this->pageTitle=Yii::app()->name . ' - paperWrong Form';
?>
<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/study.css?2.1");//
?>
<style>
    #staffDiv .checkbox-inline{width: 100px;}
</style>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'paperWrong-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('study','wrong question detail'); ?></strong>
	</h1>
</section>

<section class="content">
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('paperWrong/index',array("index"=>$model->menu_id))));
		?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
            <?php echo $form->hiddenField($model, 'menu_id',array("id"=>"menu_id")); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'employee_name',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
                    <?php
                    echo TbHtml::textField("employee_name",$model->employee_name." ($model->employee_code)",array('readonly'=>(true)));
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'wrong_date',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->textField($model, 'wrong_date',
                        array('readonly'=>(true))
                    ); ?>
                    <?php echo $form->hiddenField($model, 'wrong_num'); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'wrong_type',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <p class="form-control-static">
                        <?php

                        if($model->wrong_type==1){//综合测验
                            echo TbHtml::link(PaperWrongForm::getMarkedlyName($model->take_id),
                                Yii::app()->createUrl('paperMy/edit',array("index"=>$model->take_id,"title_id"=>$model->title_id)),
                                array("target"=>"_blank")
                            );
                        }elseif($model->wrong_type==2){
                            echo Yii::t("study","Correction of errors");//错题纠正
                        }else{
                            echo Yii::t("study","mock chapter");//章节练习
                        }
                        ?>
                    </p>
                </div>
            </div>

            <legend>&nbsp;</legend>
            <div class="form-group">
                <div class="col-lg-9">
                    <?php
                    echo PaperMyForm::showPaperTitle($model);
                    ?>
                </div>
            </div>
		</div>
	</div>
</section>

<?php
$js = "
    $('#btn-next').click(function(){
        var next = $('.resultDiv.active').next('.resultDiv');
        var title_id = next.data('id');
        if(next.length==1){
            $('.resultDiv.active').removeClass('active');
            next.addClass('active');
            $('#answerSheet>li.current').removeClass('current').next('li').addClass('current');
        }
        showBtnNextPrev();
    });
    $('#btn-before').click(function(){
        var prev = $('.resultDiv.active').prev('.resultDiv');
        if(prev.length==1){
            $('.resultDiv.active').removeClass('active');
            prev.addClass('active');
            $('#answerSheet>li.current').removeClass('current').prev('li').addClass('current');
        }
        showBtnNextPrev();
    });
    
    $('#answerSheet>li').click(function(){
        var title_id = $(this).data('id');
        $('.resultDiv').removeClass('active');
        $('#answerSheet>li').removeClass('current');
        $(this).addClass('current');
        $('.resultDiv[data-id='+title_id+']').addClass('active');
        showBtnNextPrev();
    });
    
    function showBtnNextPrev(){
        if($('.resultDiv.active').next('.resultDiv').length!=1){
            $('#btn-next').hide();//试题结束
        }else{
            $('#btn-next').show();
        }
        if($('.resultDiv.active').prev('.resultDiv').length!=1){
            $('#btn-before').hide();
        }else{
            $('#btn-before').show();
        }
    }
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

