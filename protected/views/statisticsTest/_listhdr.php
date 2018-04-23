<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('name'),'#',$this->createOrderLink('statisticsTest-list','name'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('city'),'#',$this->createOrderLink('statisticsTest-list','city'))
        ;
        ?>
    </th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('start_time').$this->drawOrderArrow('start_time'),'#',$this->createOrderLink('statisticsTest-list','start_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('end_time').$this->drawOrderArrow('end_time'),'#',$this->createOrderLink('statisticsTest-list','end_time'))
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
