<?php
if($this->function_id!=$model->menu_code){
    $this->redirect(Yii::app()->createUrl('site/index'));
}
$this->pageTitle=Yii::app()->name . ' - StudyArticle Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'StudyArticle-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
    <h1><?php echo Yii::t("study","Article Form");?></h1>
    <ol class="breadcrumb">
        <li><?php echo $model->menu_name;?></li>
        <li>
            <?php
            echo TbHtml::link(Yii::t("app","Study guide"),Yii::app()->createUrl('StudyClass/index',array("index"=>$model->menu_id)));
            ?>
        </li>
        <li>
            <?php
            echo TbHtml::link($model->class_name,Yii::app()->createUrl('StudyArticle/index',array("index"=>$model->class_id)));
            ?>
        </li>
        <li class="active"><?php echo Yii::t("study","Article Form");?></li>
    </ol>
</section>

<section class="content">
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php 
			if ($model->scenario!='new' && $model->scenario!='view') {
				echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add Another'), array(
					'submit'=>Yii::app()->createUrl('StudyArticle/add',array("index"=>$model->menu_id))));
			}
		?>
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('StudyArticle/index',array("class_id"=>$model->class_id))));
		?>
<?php if ($model->scenario!='view'): ?>
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('StudyArticle/save')));
			?>
<?php endif ?>
<?php if ($model->scenario!='new' && $model->scenario!='view'): ?>
	<?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
			'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
		);
	?>
<?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'menu_id'); ?>
			<?php echo $form->hiddenField($model, 'class_id'); ?>

			<div class="form-group">
				<?php echo $form->labelEx($model,'study_title',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-5">
				<?php
                echo $form->textField($model, 'study_title',
					array('readonly'=>($model->scenario=='view'))
				); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model,'study_date',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-2">
				<?php
                //TbHtml::textArea()
                echo $form->textField($model, 'study_date',
					array('readonly'=>($model->scenario=='view'),'prepend'=>"<i class='fa fa-calendar'></i>")
				); ?>
				</div>
			</div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'study_subtitle',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-5">
                    <?php
                    echo $form->textArea($model, 'study_subtitle',
                        array('readonly'=>($model->scenario=='view'),'rows'=>4)
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'study_body',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-10">
                    <?php
                    echo $form->textArea($model, 'study_body',
                        array('readonly'=>($model->scenario=='view'),'id'=>"study_body",'rows'=>10)
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'study_img',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-5">
                    <?php
                    if(!empty($model->study_img)){
                        echo TbHtml::fileField('study_img',"",array("class"=>"file-update form-control","style"=>"display:none"));
                        echo $form->hiddenField($model, 'study_img');
                        echo "<div class='media fileImgShow'><div class='media-left'><img width='285px' src='".Yii::app()->createUrl('studyArticle/printImage',array("id"=>$model->id))."'></div>
                        <div class='media-body media-bottom'><a>".Yii::t("contract","update")."</a></div></div>";
                    }else{
                        echo $form->fileField($model, 'study_img',
                            array('disabled'=>($model->scenario=='view'),"class"=>"file-update form-control")
                        );
                    }
                    ?>
                </div>
            </div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'z_index',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-2">
				<?php
                echo $form->numberField($model, 'z_index',
					array('readonly'=>($model->scenario=='view'))
				); ?>
				</div>
                <div class="col-lg-2">
                    <p class="form-control-static text-warning">数值越小越靠前（升序）</p>
                </div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'display',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-5">
				<?php
                $list = array(Yii::t("study","none"),Yii::t("study","show"));
                echo $form->inlineRadioButtonList($model, 'display',$list,
					array('readonly'=>($model->scenario=='view'))
				); ?>
				</div>
			</div>
		</div>
	</div>
</section>

<?php $this->renderPartial('//site/removedialog'); ?>

<?php
$uploadImage = Yii::app()->createUrl('studyArticle/uploadImgArea');
$js = "
CKEDITOR.replace('study_body',{
      toolbar: [
        {
          name: 'clipboard',
          items: ['Undo', 'Redo']
        },
        {
          name: 'styles',
          items: ['Format', 'Font', 'FontSize']
        },
        {
          name: 'colors',
          items: ['TextColor', 'BGColor']
        },
        {
          name: 'align',
          items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']
        },
        '/',
        {
          name: 'basicstyles',
          items: ['Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat', 'CopyFormatting']
        },
        {
          name: 'links',
          items: ['Link', 'Unlink']
        },
        {
          name: 'paragraph',
          items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote']
        },
        {
          name: 'insert',
          items: ['Image', 'Table']
        },
        {
          name: 'tools',
          items: ['Maximize']
        },
        {
          name: 'editing',
          items: ['Scayt']
        }
      ],
    extraAllowedContent: 'h3{clear};h2{line-height};h2 h3{margin-left,margin-top}',

    extraPlugins: 'print,format,font,colorbutton,justify,uploadimage',
// Configure your file manager integration. This example uses CKFinder 3 for PHP.
    filebrowserUploadUrl: '{$uploadImage}',
    filebrowserImageUploadUrl: '{$uploadImage}',

      // Upload dropped or pasted images to the CKFinder connector (note that the response type is set to JSON).
    uploadUrl: '{$uploadImage}',
    image_previewText: ' ',

      removeDialogTabs: 'image:advanced;link:advanced',
      removeButtons: 'PasteFromWord'
});
    $('.file-update').upload({
        uploadUrl:'".Yii::app()->createUrl('studyArticle/uploadImg')."',
        uploadData:{class_id:'{$model->class_id}',id:'{$model->id}'},
        width:'285px',
        height:'160px'
    });
    $('body').delegate('.fileImgShow a','click',function(){
        $(this).parents('.form-group:first').find('input').val('');
        $(this).parents('.fileImgShow').parents('.form-group:first').find('input[type=\"file\"]').show();
        $(this).parents('.fileImgShow').remove();
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genDeleteData(Yii::app()->createUrl('StudyArticle/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

if ($model->scenario!='view') {
    $js = Script::genDatePicker(array(
        'StudyArticleModel_study_date',
    ));
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/jquery-form.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/ajaxFile.js", CClientScript::POS_END);
?>

<?php $this->endWidget(); ?>


