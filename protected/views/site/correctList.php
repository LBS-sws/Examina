<style>
    #correctdialog dd:after{font: normal normal normal 14px/1 FontAwesome;float: left;width: 18px;margin-left: -18px;text-align: center;line-height: 18px;}
    #correctdialog dd.text-danger:after{content: "\f00d"}
    #correctdialog dd.text-primary:after{content: "\f00c"}
</style>
<?php
	$ftrbtn = array();
	$ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_DEFAULT,"class"=>"pull-right"));
	//$ftrbtn[] = TbHtml::button(Yii::t('dialog','OK'), array('id'=>'btnWFSubmit','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY,'submit' => $submit));
	$this->beginWidget('bootstrap.widgets.TbModal', array(
					'id'=>'correctdialog',
					'header'=>Yii::t('examina','correct title'),
					'footer'=>$ftrbtn,
					'show'=>false,
				));
?>

<div style="max-height: 300px;overflow-y: scroll;">
    <?php
    if (!empty($correctList)&&$testBool){
        echo "<dl>";
        foreach ($correctList as $key => $correct){
            echo "<dt>".($key+1)."、".$correct["title_name"]."</dt>";
            foreach ($correct["chooseList"] as $choose){
                if($choose["judge"] == 1){
                    echo "<dd style='padding-left: 15px;' class='text-primary'>".$choose["choose_name"]."</dd>";
                }else{
                    echo "<dd style='padding-left: 15px;'>".$choose["choose_name"]."</dd>";
                }
            }
            echo "<div style='padding: 10px;border: 1px solid'>".Yii::t("examina","Interpretation")."：".$correct["remark"]."</div>";
        }
        echo "</dl>";
    }
    ?>
</div>

<?php
	$this->endWidget(); 
?>
