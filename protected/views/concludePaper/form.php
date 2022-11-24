<?php
if($this->function_id!=$model->menu_code){
    $this->redirect(Yii::app()->createUrl('site/index'));
}
$this->pageTitle=Yii::app()->name . ' - concludePaper Form';
?>
<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/study.css?2.1");//
?>
<style>
    #staffDiv .checkbox-inline{width: 100px;}
</style>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'concludePaper-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('study','test form'); ?></strong>
	</h1>
</section>

<section class="content">
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('concludePaper/index',array("index"=>$model->menu_id))));
		?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
            <?php echo $form->hiddenField($model, 'menu_id',array("id"=>"menu_id")); ?>
            <?php echo $form->hiddenField($model, 'markedly_id',array("id"=>"markedly_id")); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'employee_name',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
                    <?php
                    echo TbHtml::textField("employee_name",$model->employee_name." ($model->employee_code)",array('readonly'=>(true)));
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'markedly_name',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->textField($model, 'markedly_name',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'dis_name',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-5">
                    <?php echo $form->textArea($model, 'dis_name',
                        array('readonly'=>(true),'rows'=>3)
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'join_must',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
                    <?php echo $form->textField($model,"join_must",array('readonly'=>(true)))?>
                </div>
                <?php echo $form->labelEx($model,'title_sum',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
                    <?php echo $form->numberField($model, 'title_sum',
                        array('readonly'=>(true),'min'=>1)
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'title_num',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
                    <?php echo $form->numberField($model, 'title_num',
                        array('readonly'=>(true),'min'=>1)
                    ); ?>
                </div>
                <?php echo $form->labelEx($model,'success_ratio',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-2">
                    <?php echo $form->textField($model, 'success_ratio',
                        array('readonly'=>(true))
                    ); ?>
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
            <div class="form-group">
                <div class="col-lg-9">
                    <div style="margin: 0 auto;width: 180px;">
                        <?php
                        echo TbHtml::button("<span class='fa fa-mail-reply'></span>".Yii::t("study","before title"),array("id"=>"btn-before","class"=>"pull-left"));
                        echo TbHtml::button("<span class='fa fa-mail-forward'></span>".Yii::t("study","next title"),array("id"=>"btn-next","class"=>"pull-right"));
                        ?>
                    </div>
                </div>
            </div>
		</div>
	</div>
    <!--答题卡-->
    <div class="answer-sheet">
        <div class="sheet-div">
            <?php
            echo PaperMyForm::showAnswerSheet($model);
            ?>
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

