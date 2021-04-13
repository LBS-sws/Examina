<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class FlowTitleController extends Controller
{
	public $function_id='TP01';

    public function filters()
    {
        return array(
            'enforceSessionExpiration',
            'enforceNoConcurrentLogin',
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'actions'=>array('save','edit','uploadPhoto','photoSave','photoEdit','photoDel'),
                'expression'=>array('FlowTitleController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('printImage'),
                'expression'=>array('FlowTitleController','allowWrite'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowWrite() {
        return !empty(Yii::app()->user->id);
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('TP01')||
            Yii::app()->user->validRWFunction('TP02')||
            Yii::app()->user->validRWFunction('TP03')||
            Yii::app()->user->validRWFunction('TP04')||
            Yii::app()->user->validRWFunction('TP05')||
            Yii::app()->user->validRWFunction('TP06');
    }

    public function actionEdit($code="")
    {
        $flowTitleModel = new FlowTitleForm('edit');
        $flowTitleModel->setFlowCode($code);
        $this->function_id = $flowTitleModel->getFunctionId();
        Yii::app()->session['active_func'] = $this->function_id;
        $flowTitleModel->flow_title = $flowTitleModel->getFlowTitle($code,false);
        $this->render('form',array('model'=>$flowTitleModel));
    }

    public function actionUploadPhoto($code="")
    {
        $flowTitleModel = new FlowTitleForm('new');
        $flowTitleModel->setFlowCode($code);
        $this->function_id = $flowTitleModel->getFunctionId();
        Yii::app()->session['active_func'] = $this->function_id;
        $this->render('form_photo',array('model'=>$flowTitleModel));
    }

    public function actionSave()
    {
        $flowTitleModel = new FlowTitleForm('edit');
        if (isset($_POST['FlowTitleForm'])) {
            $flowTitleModel->attributes = $_POST['FlowTitleForm'];
            $this->function_id = $flowTitleModel->getFunctionId();
            Yii::app()->session['active_func'] = $this->function_id;
            if ($flowTitleModel->validate()) {
                $flowTitleModel->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->render('form',array('model'=>$flowTitleModel));
            } else {
                $message = CHtml::errorSummary($flowTitleModel);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('flowTitle/edit',array('code'=>$flowTitleModel->flow_code)));
            }
        } else {
            throw new CHttpException(404,'The requested page does not exist.');
        }
    }

    public function actionPhotoSave()
    {
        $flowTitleModel = new FlowTitleForm('photo');
        if (isset($_POST['FlowTitleForm'])) {
            $flowTitleModel->attributes = $_POST['FlowTitleForm'];
            $this->function_id = $flowTitleModel->getFunctionId();
            Yii::app()->session['active_func'] = $this->function_id;
            if ($flowTitleModel->validate()) {
                $flowTitleModel->savePhoto($_POST['test']);
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('flowTitle/uploadPhoto',array('code'=>$flowTitleModel->flow_code)));
            } else {
                $message = CHtml::errorSummary($flowTitleModel);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form_photo',array('model'=>$flowTitleModel));
            }
        } else {
            throw new CHttpException(404,'The requested page does not exist.');
        }
    }

    public function actionPhotoEdit($id)
    {
        $flowTitleModel = new FlowTitleForm('test');
        if (isset($_POST['test'])) {
            $flowTitleModel->attributes = $_POST['FlowTitleForm'];
            $flowTitleModel->photoEdit($_POST['test'],$id);
            Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
            $this->redirect(Yii::app()->createUrl('flowTitle/uploadPhoto',array('code'=>$flowTitleModel->flow_code)));
        } else {
            throw new CHttpException(404,'The requested page does not exist.');
        }
    }

    public function actionPhotoDel($id)
    {
        $flowTitleModel = new FlowTitleForm('test');
        if (isset($_POST['test'])) {
            $flowTitleModel->attributes = $_POST['FlowTitleForm'];
            $flowTitleModel->photoDel($id);
            Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
            $this->redirect(Yii::app()->createUrl('flowTitle/uploadPhoto',array('code'=>$flowTitleModel->flow_code)));
        } else {
            throw new CHttpException(404,'The requested page does not exist.');
        }
    }


    public function actionPrintImage($id = 0) {
        $rows = Yii::app()->db->createCommand()->select("flow_photo")
            ->from("exa_flow_photo")->where("id=:id",array(":id"=>$id))->queryRow();
        if($rows&&!empty($rows["flow_photo"])){
            $n = new imgdata;
            $path = Yii::app()->basePath."/../".$rows["flow_photo"];
            if (file_exists($path)) {
                $n -> getdir($path);
                $n -> img2data();
            } else {
                echo "地址不存在";
                return false;
            }
        }else{
            echo "沒找到圖片";
            return false;
        }
    }
}