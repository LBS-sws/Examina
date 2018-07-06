<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('a.name'),'#',$this->createOrderLink('statisticsTest-list','a.name'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('a.city'),'#',$this->createOrderLink('statisticsTest-list','a.city'))
        ;
        ?>
    </th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('start_time').$this->drawOrderArrow('a.start_time'),'#',$this->createOrderLink('statisticsTest-list','a.start_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('end_time').$this->drawOrderArrow('a.end_time'),'#',$this->createOrderLink('statisticsTest-list','a.end_time'))
			;
		?>
	</th>
    <th>
        <?php echo "<a href='#'>".Yii::t("examina","already involved")."</a>";?>
    </th>
    <th>
        <?php echo "<a href='#'>".Yii::t("examina","sum correct")."</a>";?>
    </th>
</tr>
