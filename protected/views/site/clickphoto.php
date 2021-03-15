<?php
	$ftrbtn = array();
	$ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('id'=>"btn_".$flow_code."_close",'data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_DEFAULT,"class"=>"pull-left"));
	$this->beginWidget('bootstrap.widgets.TbModal', array(
					'id'=>'clickphoto_'.$flow_code,
					'header'=>Yii::t('examina','look for photo'),
					'footer'=>$ftrbtn,
					'show'=>false,
					'htmlOptions'=>array('class'=>'bs-example-modal-lg'),
                    'size'=>" modal-lg",
					//'class'=>"modal-lg",
				));
?>

<div class="form-group">
    1111
</div>

<?php
	$this->endWidget(); 
?>
