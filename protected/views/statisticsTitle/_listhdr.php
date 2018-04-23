<tr>
    <th>
        <?php echo TbHtml::link($this->getLabelName('title_code').$this->drawOrderArrow('d.title_code'),'#',$this->createOrderLink('statisticsTitle-list','d.title_code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('d.name'),'#',$this->createOrderLink('statisticsTitle-list','d.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo "<a href='#'>".Yii::t("examina","occurrences")."</a>";?>
    </th>
    <th>
        <?php echo "<a href='#'>".Yii::t("examina","correct num")."</a>";?>
    </th>
    <th>
        <?php echo "<a href='#'>".Yii::t("examina","correct")."</a>";?>
    </th>
</tr>
