
<?php
	$ftrbtn = array();
    $ftrbtn[] = TbHtml::button(Yii::t('dialog','Upload'), array('id'=>"importUp",'submit'=>$url));
    $ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('id'=>"btnWFClose",'data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY));
	$this->beginWidget('bootstrap.widgets.TbModal', array(
					'id'=>'importQuestion',
					'header'=>Yii::t('study','Import File'),
					'footer'=>$ftrbtn,
					'show'=>false,
				));
?>

<div class="form-group">
    <label class="col-sm-2 control-label"><?php echo Yii::t("study","file");?></label>
    <div class="col-sm-6">
        <?php
        echo TbHtml::fileField("file","",array("class"=>"form-control"));
        ?>
    </div>
</div>

<?php
	$this->endWidget(); 
?>
