<?php
if($this->function_id!=$model->menu_code){
    $this->redirect(Yii::app()->createUrl('site/index'));
}
$this->pageTitle=Yii::app()->name . ' - TeStudy';
?>
<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/study.css?2.1");//
?>
<section class="content-header">
    <h1><?php echo Yii::t("app","Study guide");?></h1>
	<ol class="breadcrumb">
		<li><?php echo $model->menu_name;?></li>
		<li class="active"><?php echo Yii::t("app","Study guide");?></li>
	</ol>
</section>

<section class="content">
    <?php if (Yii::app()->user->validRWFunction($model->menu_code)): ?>
	<div class="box">
        <div class="box-body">
            <div class="btn-group pull-right" role="group">
                <?php
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                        'submit'=>Yii::app()->createUrl('StudyClass/add',array("index"=>$model->menu_id)),
                    ));
                ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div class="box">
        <div class="box-body">
            <?php
            echo $model->echoMedia();
            ?>
        </div>
    </div>

    <?php
    if (Yii::app()->user->validRWFunction($model->menu_code)){
        echo $model->echoNoneDiv();
    }
    ?>
</section>