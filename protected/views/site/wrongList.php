<style>
    #wrongdialog dd:after{font: normal normal normal 14px/1 FontAwesome;float: left;width: 18px;margin-left: -18px;text-align: center;line-height: 18px;}
    #wrongdialog dd.text-danger:after{content: "\f00d"}
    #wrongdialog dd.text-primary:after{content: "\f00c"}
</style>

<?php
	$ftrbtn = array();
	$ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_DEFAULT,"class"=>"pull-right"));
	//$ftrbtn[] = TbHtml::button(Yii::t('dialog','OK'), array('id'=>'btnWFSubmit','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY,'submit' => $submit));
	$this->beginWidget('bootstrap.widgets.TbModal', array(
					'id'=>'wrongdialog',
					'header'=>Yii::t('examina','wrong title'),
					'footer'=>$ftrbtn,
					'show'=>false,
				));
?>

<div style="max-height: 300px;overflow-y: scroll;">
    <?php
    if (!empty($wrongList)&&$testBool){
        echo "<dl>";
        foreach ($wrongList as $key => $wrong){
            echo "<dt>".($key+1)."、".$wrong["title_name"]."</dt>";
            foreach ($wrong["chooseList"] as $choose){
                if($choose["judge"] == 1){
                    echo "<dd style='padding-left: 15px;' class='text-primary'>".$choose["choose_name"]."</dd>";
                }else{
                    if($choose["id"] == $wrong["choose_id"]){
                        echo "<dd style='padding-left: 15px;' class='text-danger'>".$choose["choose_name"]."</dd>";
                    }else{
                        echo "<dd style='padding-left: 15px;'>".$choose["choose_name"]."</dd>";
                    }
                }
            }
            echo "<div style='padding: 10px;border: 1px solid'>".Yii::t("examina","Interpretation")."：".$wrong["remark"]."</div>";
        }
        echo "</dl>";
    }
    ?>
</div>
<?php
	$this->endWidget();
?>
