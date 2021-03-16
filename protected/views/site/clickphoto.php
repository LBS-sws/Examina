<?php
$photoModel = new FlowTitleForm('view');
echo $photoModel->getFlowPhoto($flow_code);
?>

<?php
$sfile = Yii::app()->baseUrl.'/js/viewer.min.js';
Yii::app()->clientScript->registerScriptFile($sfile,CClientScript::POS_END);
$sfile = Yii::app()->baseUrl.'/css/viewer.min.css';
Yii::app()->clientScript->registerCssFile($sfile);
?>
<script>
    $(function () {
        $('.viewerUl').each(function () {
            var code = $(this).data("code");
            var viewerObj = new Viewer($(this).get(0), {
                url: 'data-original'
            });
            $("#viewer_"+code).click(function () {
                viewerObj.show();
            })

        })
    })
</script>
