<?php
if (empty($model->_testNum)){
    $this->redirect(Yii::app()->createUrl('myTest/index'));
}
$this->pageTitle=Yii::app()->name . ' - myTest Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'myTest-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<style>
    #staffDiv .checkbox-inline{width: 100px;}
    .resultDiv.have-error .resultBody_t{color:red;}
    .resultDiv.have-error .remark{color:red;border-color: red;}
    .resultBody_b{padding-left: 20px;}

    .radio>label:after{font: normal normal normal 14px/1 FontAwesome;float: left;width: 18px;margin-left: -18px;text-align: center;line-height: 18px;}
    .radio>label.text-danger:after{content: "\f00d"}
    .radio>label.text-primary:after{content: "\f00c"}
</style>
<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('examina','Simulation test'); ?></strong>
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
                    'submit'=>Yii::app()->createUrl('myTest/view',array("index"=>$index))));
                ?>
            </div>
        </div></div>

    <div class="box box-info">
        <div class="box-body">

            <?php
            $resultList = $model->getResultList();
            foreach ($resultList as $key => $result){
                if($key == 0){
                    echo "<div class='resultDiv now' data-key='".($key+1)."'>";
                }else{
                    echo "<div class='resultDiv' data-key='".($key+1)."' style='display: none'>";
                }
                echo "<h4 class='resultRe text-right'>".($key+1)." / ".count($resultList)."</h4>";
                echo "<div class='resultBody'>";
                echo "<h4 class='resultBody_t'><b>".($key+1)."、".$result["name"]."</b></h4>";
                echo "<div class='resultBody_b'>";
                foreach ($result["list"] as $item){
                    echo '<div class="radio">';
                    echo TbHtml::radioButton("examina[list_choose][$key]","",array('label'=>$item["choose_name"],'data-judge'=>$item["judge"],'class'=>'radioJudge'));
                    echo '</div>';
                }
                echo "<div class='remark' style='padding: 10px;border: 1px solid;display: none;'>".Yii::t("examina","Interpretation")."：".$result["remark"]."</div>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
            ?>
            <div class="text-center" style="padding-top: 20px;">
                <?php
                echo TbHtml::button('<span class="fa fa-mail-forward"></span> '.Yii::t('examina','next title'), array(
                    'id'=>"resultChange"));
                ?>
            </div>
            <div id="simulationResult" style="display: none">
                <legend><?php echo Yii::t("examina","simulation results");?></legend>
                <div class="form-group">
                    <?php echo TbHtml::label(Yii::t("examina","correct num"),"",array('class'=>"col-sm-2 control-label")) ?>
                    <div class="col-sm-8">
                        <p class="form-control-static" id="successNum"></p>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo TbHtml::label(Yii::t("examina","correct title"),"",array('class'=>"col-sm-2 control-label")) ?>
                    <div class="col-sm-8">
                        <p class="form-control-static" id="successStr"></p>
                    </div>
                </div>
                <legend></legend>
                <div class="form-group">
                    <?php echo TbHtml::label(Yii::t("examina","wrong num"),"",array('class'=>"col-sm-2 control-label")) ?>
                    <div class="col-sm-8">
                        <p class="form-control-static" id="errorNum"></p>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo TbHtml::label(Yii::t("examina","wrong title"),"",array('class'=>"col-sm-2 control-label")) ?>
                    <div class="col-sm-8">
                        <p class="form-control-static" id="errorStr"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div tabindex="-1" class="modal fade" style="display: none" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal" type="button">×</button>
                <h4 class="modal-title">验证信息</h4></div>
            <div class="modal-body">
                <p></p>
                <div class="errorSummary">
                    <p>请更正下列输入错误:</p>
                    <ul>
                        <li>请先完成本试题再回答下一题</li>
                    </ul>
                </div>
                <p></p>
            </div>
            <div class="modal-footer"><button data-dismiss="modal" class="btn btn-primary" type="button">确定</button></div>
        </div>
    </div>
</div>
<?php
$js = "
$('#resultChange').on('click',function(){
    if($('.resultDiv.now').length == 0){
        return false;
    };
    var resultDiv = $('.resultDiv.now').next('.resultDiv');
    if($('.resultDiv.now').find('input[type=\"radio\"]:checked').length == 0){
        $('#myModal').modal('show');
        return false;
    }
    if(resultDiv.length == 0){
        resultTest();
        return false;
    };
    $('.resultDiv.now').stop().slideUp(100).removeClass('now');
    resultDiv.stop().slideDown(100).addClass('now');
    if(resultDiv.next('.resultDiv').length == 0){
        $('#resultChange').html('<span class=\"fa fa-upload\"></span>提交');
        //$('#resultChange').off('click').attr('type','submit');
        return false;
    }
});

function resultTest(){
    var sum = $('.resultDiv').length;
    var success = 0;
    var error = 0;
    var successStr = '';
    var errorStr = '';
    $('.resultDiv').show();
    $('.remark').show();
    $('.resultRe').hide();
    $('#resultChange').hide();
    $('.radioJudge').each(function(){
        if($(this).data('judge') == 1){
            if($(this).is(':checked')){
                success++;
                if(successStr!=''){
                    successStr+=',';
                }
                successStr+=$(this).parents('.resultDiv:first').data('key');
            }
            $(this).parent('label').addClass('text-primary');
        }else if($(this).is(':checked')){
            error++;
            if(errorStr!=''){
                errorStr+=',';
            }
            errorStr+=$(this).parents('.resultDiv:first').data('key');
            $(this).parents('.resultDiv:first').addClass('have-error');
            $(this).parent('label').addClass('text-danger');
        }
    }).hide();
    $('#successNum').text(success);
    $('#successStr').text(successStr);
    $('#errorNum').text(error);
    $('#errorStr').text(errorStr);
    $('#simulationResult').show();
}
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

