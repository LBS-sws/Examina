<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0005
 * Time: 上午 11:30
 */
class PaperMyController extends Controller
{
    public $function_id='TE09';

    public function init(){
        $session = Yii::app()->session;
        if(key_exists("menu_code",$_GET)){
            $code = $_GET["menu_code"];
            $session["menu_code"]=$code;
        }elseif (isset($session['menu_code']) && !empty($session['menu_code'])) {
            $code = $session['menu_code'];
        }else{
            $code = "无";
        }
        $this->function_id = "{$code}05";
        parent::init();
    }

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
                'actions'=>array('edit'),
                'expression'=>array('PaperMyController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('PaperMyController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        $session = Yii::app()->session;
        $code = isset($session['menu_code'])?$session['menu_code']:"dd";
        return Yii::app()->user->validRWFunction("{$code}05");
    }

    public static function allowReadOnly() {
        $session = Yii::app()->session;
        $code = isset($session['menu_code'])?$session['menu_code']:"dd";
        return Yii::app()->user->validFunction("{$code}05");
    }

    public static function allowRead() {
        return true;
    }

    public function actionIndex($index,$pageNum=0){
        $model = new PaperMyList;
        if (isset($_POST['PaperMyList'])) {
            $model->attributes = $_POST['PaperMyList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['paperMy_'.$this->function_id]) && !empty($session['paperMy_'.$this->function_id])) {
                $criteria = $session['paperMy_'.$this->function_id];
                $model->setCriteria($criteria);
            }
        }
        if(MarkedlyTakeModel::validateEmployee($model)){
            $model->determinePageNum($pageNum);
            $model->retrieveAll($index,$model->pageNum);
            $this->render('index',array('model'=>$model));
        }else{
            throw new CHttpException(404,'该账号未绑定员工，请与管理员联系');
        }
    }

    public function actionEdit($index)
    {
        $model = new PaperMyForm('edit');
        if(MarkedlyTakeModel::validateEmployee($model)){
            if (!$model->retrieveData($index)) {
                throw new CHttpException(404,'The requested page does not exist.');
            } else {
                $this->render('form',array('model'=>$model,));
            }
        }else{
            throw new CHttpException(404,'该账号未绑定员工，请与管理员联系');
        }
    }

    public function actionView($index)
    {
        $model = new PaperMyForm('view');
        if(MarkedlyTakeModel::validateEmployee($model)){
            if (!$model->retrieveData($index)) {
                throw new CHttpException(404,'The requested page does not exist.');
            } else {
                $this->render('form',array('model'=>$model,));
            }
        }else{
            throw new CHttpException(404,'该账号未绑定员工，请与管理员联系');
        }
    }
}