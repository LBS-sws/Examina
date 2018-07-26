<?php
if ($model->getErrorBool()){
    $this->redirect(Yii::app()->createUrl('simTest/index'));
}
$this->pageTitle=Yii::app()->name . ' - simTest Form';
?>
<style>
    #staffDiv .checkbox-inline{width: 100px;}
    .resultBody_b{padding-left: 20px;}
</style>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'simTest-form',
'action'=>Yii::app()->createUrl('simTest/audit'),
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Simulation test')." - ".$model->getQuizList()["name"]; ?></strong>
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
/*        echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('myTest/index')));*/
		?>
        <span style="font-size: 17px;"><?php echo Yii::t('examina','test display')."：".$model->getQuizList()["dis_name"];?></span>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">

            <?php
            echo TbHtml::hiddenField("examina[quiz_id]",$model->getQuizList()["id"]);
            $resultList = $model->getResultList();
            foreach ($resultList as $key => $result){
                if($key == 0){
                    echo "<div class='resultDiv now'>";
                }else{
                    echo "<div class='resultDiv' style='display: none'>";
                }
                //echo TbHtml::hiddenField("examina[$key][titleId]",$result["id"]);
                //echo TbHtml::hiddenField("examina[$key][list]",implode(",",array_column($result["list"],"id")));
                echo "<h4 class='resultRe text-right'>".($key+1)." / ".count($resultList)."</h4>";
                echo "<div class='resultBody'>";
                echo "<h4 class='resultBody_t'><b>".($key+1)."、".$result["name"]."</b></h4>";
                echo "<div class='resultBody_b'>";
                $item = array_column($result["list"],"choose_name","id");
                echo TbHtml::radioButtonList("examina[list_choose][$key]","",$item);
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
        $('#simTest-form').submit();
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
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

