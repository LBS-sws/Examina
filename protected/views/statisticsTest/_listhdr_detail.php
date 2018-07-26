<tr>
	<th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('d.name'),'#',$this->createOrderLink('statisticsDetail-list','d.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('d.city'),'#',$this->createOrderLink('statisticsDetail-list','d.city'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('lcd').$this->drawOrderArrow('lcd'),'#',$this->createOrderLink('statisticsDetail-list','lcd'))
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
