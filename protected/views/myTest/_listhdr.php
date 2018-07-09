<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('a.name'),'#',$this->createOrderLink('myTest-list','a.name'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('bumen_ex').$this->drawOrderArrow('a.bumen_ex'),'#',$this->createOrderLink('myTest-list','a.bumen_ex'))
        ;
        ?>
    </th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('start_time').$this->drawOrderArrow('a.start_time'),'#',$this->createOrderLink('myTest-list','a.start_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('end_time').$this->drawOrderArrow('a.end_time'),'#',$this->createOrderLink('myTest-list','a.end_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('exa_num').$this->drawOrderArrow('a.exa_num'),'#',$this->createOrderLink('myTest-list','a.exa_num'))
			;
		?>
	</th>
    <th>
        <?php echo "<a href='#'>".Yii::t("examina","correct")."</a>";?>
    </th>
    <th>
        <?php echo "<a href='#'>".Yii::t("examina","correct num")."</a>";?>
    </th>
    <th>
        <?php echo "<a href='#'>".Yii::t("examina","wrong num")."</a>";?>
    </th>
    <th>
    </th>
</tr>
