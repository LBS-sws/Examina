
<tr class='clickable-row' data-href='<?php echo $this->getLink($this->model->menu_code, 'paperWrong/edit', 'paperWrong/view', array('index'=>$this->record['id']));?>'>

    <td><?php echo $this->drawEditButton($this->model->menu_code, 'paperWrong/edit', 'paperWrong/view', array('index'=>$this->record['id'])); ?></td>
    <td><?php echo $this->record['employee']; ?></td>
    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['title_type']; ?></td>
    <td><?php echo $this->record['chapter_name']; ?></td>
    <td><?php echo $this->record['wrong_date']; ?></td>
    <td class="end_click">
        <?php
        if($this->record['wrong_type']==1){//综合测验
            echo TbHtml::link(PaperWrongForm::getMarkedlyName($this->record['take_id']),Yii::app()->createUrl('paperMy/edit',array("index"=>$this->record['take_id'],"title_id"=>$this->record['title_id'])),array("target"=>"_blank"));
        }elseif($this->record['wrong_type']==2){
            echo Yii::t("study","Correction of errors");//错题纠正
        }else{
            echo Yii::t("study","mock chapter");//章节练习
        }
        ?>
    </td>
</tr>
