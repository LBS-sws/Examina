<?php
$this->pageTitle=Yii::app()->name . ' - flowTitle Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'flowTitle-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
    'htmlOptions'=>array('enctype' => 'multipart/form-data')
)); ?>

<section class="content-header">
	<h1>
		<strong>图片设置 - <?php echo $model->getFlowName(); ?></strong>
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
				'submit'=>Yii::app()->createUrl($model->getFlowBackUrl())));
		?>
        <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
            'submit'=>Yii::app()->createUrl('flowTitle/photoSave')));
        ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'flow_code'); ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'flow_code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?php echo $model->getFlowName();?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-1">
                    <table class="table table-bordered" id="table_photo">
                        <thead>
                        <tr>
                            <th width="40%">图片</th>
                            <th width="40%">图片说明</th>
                            <th width="10%">图片层级</th>
                            <th width="10%">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        echo $model->getTableBody();
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="4">
                                <?php
                                echo $form->fileField($model,"flow_photo[]",array('class'=>'uploadImage'));
                                ?>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-8 col-sm-offset-2">
                    <p class="form-control-static">
                        图片层级越大，显示越靠后
                    </p>
                </div>
            </div>
		</div>
	</div>
</section>

<?php

$js = <<<EOF
num = 0;
//刪除圖片
$('#table_photo').delegate('.deleteNow','click',function(){
    var data_num = $(this).data('num');
    var data_id = $(this).data('id');
    if(!isNaN(data_num)&&data_num>=0){
        $(this).parents('tr:first').remove();
        $('.uploadImage[num="'+data_num+'"]').remove();
    }else{
        $(this).parents('tr:first').remove();
        $('#table_photo').before('<input name="test[delete][]" type="hidden" value="'+data_id+'">');
    }
});
//添加圖片
$('#table_photo').delegate('.uploadImage','change',function(){
    if($('.add_span').length >=9){
        $(this).val('');
        alert('一次最多添加9個，請分開上傳');
        return;
    }
    var files = !!this.files ? this.files : [];
    var name = $(this).attr('name');
    if (!files.length || !window.FileReader) return;
    if (/^image/.test( files[0].type)){
        var fileName = files[0].name;
        $(this).attr('num',num);
        $(this).hide();
        $(this).after('<input name="'+name+'" class="uploadImage" type="file">');
        var reader = new FileReader();
        reader.readAsDataURL(files[0]);
        reader.onloadend = function(){
            //this.result
            var html ='';
            html+='<tr>';
            html+='<td class="text-center">';
            html+='<img height="100px" src="'+this.result+'"><br/>';
            html+='<span class="add_span">'+fileName+'</span>';
            html+='</td>';
            html+='<td><textarea class="textarea form-control" row="2" name="test[add][textarea][]"></textarea></td>';
            html+='<td><input class="number form-control" type="text" value="0" name="test[add][number][]"></td>';
            html+='<td><button class="deleteNow btn btn-default" type="button" data-num="'+num+'">删除</button></td>';
            html+='</tr>';
            $('#table_photo').append(html);
            num++;
        };
    }else{
        $(this).val('');
        alert('請選擇圖片格式的文件');
    }
});
EOF;
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

</div><!-- form -->

