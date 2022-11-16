<tr>
    <?php if (Yii::app()->user->validRWFunction($this->model->menu_code)): ?>
	<th></th>
    <?php endif; ?>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('a.name'),'#',$this->createOrderLink('markedlyTest-list','a.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('join_must').$this->drawOrderArrow('a.join_must'),'#',$this->createOrderLink('markedlyTest-list','a.join_must'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('bumen_ex').$this->drawOrderArrow('a.bumen_ex'),'#',$this->createOrderLink('markedlyTest-list','a.bumen_ex'))
        ;
        ?>
    </th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('exa_num').$this->drawOrderArrow('a.exa_num'),'#',$this->createOrderLink('markedlyTest-list','a.exa_num'))
			;
		?>
	</th>
    <th></th>
</tr>
