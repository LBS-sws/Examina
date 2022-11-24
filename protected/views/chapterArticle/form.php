<?php
if($this->function_id!=$model->menu_code){
    $this->redirect(Yii::app()->createUrl('site/index'));
}
$this->pageTitle=Yii::app()->name . ' - ChapterArticle Form';
?>
<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/study.css?2.1");//
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'ChapterArticle-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo isset($title_name)?$title_name:Yii::t("study","mock chapter");?></strong>
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

<section class="content" style="min-height: 500px;">
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('mockChapter/index',array("index"=>$model->menu_id))));
		?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'menu_id',array("id"=>"menu_id")); ?>
			<?php echo $form->hiddenField($model, 'chapter_id'); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'chapter_name',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->textField($model, 'chapter_name',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-9">
                    <?php
                    echo ChapterArticleModel::showPaperTitle($model,$model->paper_list,true,true);
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
            echo ChapterArticleModel::showAnswerSheet($model->paper_list,true);
            ?>
        </div>
    </div>
</section>

<?php
$ajaxUrl = Yii::app()->createUrl('chapterArticle/ajaxAddWrong');
$js = "
    $('.btn-res').click(function(){
        var parentDiv = $(this).parents('.resultDiv');
        var judge = true;
        var choose_id = [];
        var list_choose = [];
        var title_id = $(this).data('id');
        if(parentDiv.find('.btn-checkbox:checked').length<1){
            alert('请选择您的答案');
            return false;
        }
        $(this).data('id','-9').hide();
        parentDiv.find('.btn-checkbox').each(function(){
            list_choose.push($(this).attr('value'));
            if($(this).is(':checked')){
                choose_id.push($(this).attr('value'));
            }
            $(this).parents('div').eq(0).addClass('disabled');
            if($(this).data('judge')==1){
                if(!$(this).is(':checked')){
                    judge = false;//用户做错题了
                }
            }else{
                if($(this).is(':checked')){
                    judge = false;//用户做错题了
                }
            }
        });
        if(!judge){
            parentDiv.find('.btn-checkbox').each(function(){
                var text = $(this).parent('label').text();
                if($(this).data('judge')==1){
                    $(this).parent('label').addClass('text-primary').html(text);
                }else if($(this).is(':checked')){
                    $(this).parent('label').addClass('text-danger').html(text);
                }else{
                    $(this).parent('label').html(text);
                }
            });
            parentDiv.find('.resultRemark').show();
            $('#answerSheet>li[data-id='+title_id+']').addClass('error');
            
            $.ajax({
                type: 'POST',
                url: '{$ajaxUrl}',
                dataType: 'json',
                data: {
                    'menu_id':$('#menu_id').val(),
                    'title_id':title_id,
                    'choose_id':title_id,
                    'list_choose':list_choose
                },
                success: function(msg){
                },
                error:function () {
                }
            });
        }else{
            $('#answerSheet>li[data-id='+title_id+']').addClass('success');
            $('#btn-next').trigger('click');
        }
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
        var span_error = $('#answerSheet>li.error').length;
        var sum = $('.resultDiv').length;
        if(sum>1){
            var span_ratio = Math.round(span_success/sum*100)+'%';
            $('#span_success').text(span_success);
            $('#span_error').text(span_error);
            $('#span_ratio').text(span_ratio);
        }
    }
    
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>
<?php $this->endWidget(); ?>


