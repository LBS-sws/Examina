<tr>
    <th>
        <?php echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('m.job_staff'),'#',$this->createOrderLink('statisticsQuiz-list','m.job_staff'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('m.city'),'#',$this->createOrderLink('statisticsQuiz-list','m.city'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('entry_time').$this->drawOrderArrow('m.entry_time'),'#',$this->createOrderLink('statisticsQuiz-list','m.entry_time'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('question').$this->drawOrderArrow('m.qc_date'),'#',$this->createOrderLink('statisticsQuiz-list','m.qc_date'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('endDate').$this->drawOrderArrow('m.order_end'),'#',$this->createOrderLink('statisticsQuiz-list','m.order_end'))
        ;
        ?>
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
<!--遺漏文件-->