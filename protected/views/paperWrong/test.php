<?php
if($this->function_id!=$model->menu_code){
    $this->redirect(Yii::app()->createUrl('site/index'));
}
$this->pageTitle=Yii::app()->name . ' - PaperWrong Form';
?>
<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/study.css?2.1");//
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'paperWrong-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('study','Correction of errors');?></strong>
    </h1>
</section>

<section class="content" style="min-height: 500px;">
    <div class="box"><div class="box-body">
            <div class="btn-group" role="group">
                <?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
                    'submit'=>Yii::app()->createUrl('PaperWrong/index',array("index"=>$model->menu_id))));
                ?>
                <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save'), array(
                    'data-href'=>Yii::app()->createUrl('PaperWrong/save'),'id'=>"btn-save"));
                ?>
            </div>
        </div></div>

    <div class="box box-info">
        <div class="box-body">
            <?php echo $form->hiddenField($model, 'scenario'); ?>
            <?php echo $form->hiddenField($model, 'id'); ?>
            <?php echo $form->hiddenField($model, 'menu_id'); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'employee_code',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php
                    echo TbHtml::textField("employee_name",$model->employee_name." ($model->employee_code)",array('readonly'=>(true)));
                    ?>
                </div>
            </div>

            <legend>&nbsp;</legend>
            <div class="form-group">
                <div class="col-lg-9">
                    <?php
                    echo ChapterArticleModel::showPaperTitle($model,$model->paper_list,true,false);
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
            echo ChapterArticleModel::showAnswerSheet($model->paper_list,false);
            ?>
        </div>
    </div>
</section>

<?php
$js = "
    $('.btn-res').click(function(){
        var parentDiv = $(this).parents('.resultDiv');
        var judge = true;
        var title_id = $(this).data('id');
        if(parentDiv.find('.btn-checkbox:checked').length<1){
            alert('请选择您的答案');
            return false;
        }
        $(this).data('id','-9').hide();
        parentDiv.find('.btn-checkbox').each(function(){
            $(this).parents('div').eq(0).addClass('disabled');
        });
        $('#answerSheet>li[data-id='+title_id+']').addClass('success');
        $('#btn-next').trigger('click');
        changeSpanText();
    });
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
    
    function changeSpanText(){
        var span_success = $('#answerSheet>li.success').length;
        var sum = $('.resultDiv').length;
        if(sum>1){
            $('#span_ok').text(span_success);
        }
    }
    
    $('#btn-save').click(function(){
        if($('#answerSheet>li').not('.success').length>0){
            alert('您还有试题未做，请继续答题');
            $('#answerSheet>li').not('.success').eq(0).trigger('click');
        }else{
            var href = $(this).data('href');
            $(this).css('pointer-events','none');
            jQuery.yii.submitForm(this,href,{});
            return false;
        }
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>
<?php $this->endWidget(); ?>


