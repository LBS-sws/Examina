<tr>
    <th>
        <?php echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('statisticsQuiz-list','b.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('b.city'),'#',$this->createOrderLink('statisticsQuiz-list','b.city'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('entry_time').$this->drawOrderArrow('b.entry_time'),'#',$this->createOrderLink('statisticsQuiz-list','b.entry_time'))
        ;
        ?>
    </th>
    <th>
        <?php echo "<a href='#'>".Yii::t("examina","Quiz cause")."</a>";?>
    </th>
    <th>
        <?php echo "<a href='#'>".Yii::t("examina","Quiz end date")."</a>";?>
    </th>
    <th>
        <?php echo "<a href='#'>".Yii::t("examina","Quiz join date")."</a>";?>
    </th>
    <th>
        <?php echo "<a href='#'>".Yii::t("examina","correct")."</a>";?>
    </th>
    <th>
        <?php echo "<a href='#'>".Yii::t("examina","correct num")."</a>";?>
    </th>
</tr>
