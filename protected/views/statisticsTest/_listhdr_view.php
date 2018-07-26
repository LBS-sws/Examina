<tr>
	<th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('d.name'),'#',$this->createOrderLink('statisticsView-list','d.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('d.city'),'#',$this->createOrderLink('statisticsView-list','d.city'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('join_num').$this->drawOrderArrow('join_num'),'#',$this->createOrderLink('statisticsView-list','join_num'))
        ;
        ?>
    </th>
    <th>
        <?php echo "<a href='#'>".Yii::t("examina","correct")."</a>";?>
    </th>
    <th>
        <?php echo "<a href='#'>".Yii::t("examina","correct num")."</a>";?>
    </th>
</tr>
