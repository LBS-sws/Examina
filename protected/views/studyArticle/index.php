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
    <h1><?php echo $model->class_name;?></h1>
    <ol class="breadcrumb">
        <li><?php echo $model->menu_name;?></li>
        <li>
            <?php
            echo TbHtml::link(Yii::t("app","Study guide"),Yii::app()->createUrl('StudyClass/index',array("index"=>$model->menu_id)));
            ?>
        </li>
        <li class="active"><?php echo $model->class_name;?></li>
    </ol>
</section>

<section class="content">
	<div class="box">
        <div class="box-body">
            <div class="btn-group" role="group">
                <?php
                echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
                    'submit'=>Yii::app()->createUrl('StudyClass/index',array("index"=>$model->menu_id)),
                ));
                ?>
            </div>
            <?php if (Yii::app()->user->validRWFunction($model->menu_code)): ?>
            <div class="btn-group pull-right" role="group">
                <?php
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                        'submit'=>Yii::app()->createUrl('StudyArticle/add',array("index"=>$model->class_id)),
                    ));
                ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="box">
        <div class="box-body">
            <!--
            <div class="col-lg-6 article-div">
                <div class="media">
                    <div class="media-left">
                        <div class="img-article"></div>
                    </div>
                    <div class="media-body">
                        <div class="article-body">
                            <h4>3333333</h4>
                            <p>dddddddddddddddddddddddddddddddddddddggggggggggggggggggggg</p>
                        </div>
                        <div class="article-footer">
                            <div class="footer-date">2022-11-7</div>
                            <div class="footer-link">update</div>
                        </div>
                    </div>
                </div>
            </div>
            -->
            <?php
            echo $model->echoMedia();
            ?>
            <div class="col-lg-12">
                <?php
                echo $model->navBar();
                ?>
            </div>
        </div>
    </div>

    <?php
    if (Yii::app()->user->validRWFunction($model->menu_code)){
        echo $model->echoNoneDiv();
    }
    ?>
</section>
<?php
$js = "
    $('.article-div').on('click',function(){
        //var link = $(this).data('href');
        window.location.href = $(this).data('href');
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

?>