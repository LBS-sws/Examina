<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0008
 * Time: 上午 11:30
 */
class ConcludeStaffController extends Controller
{
    public $function_id='TE19';

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
        $this->function_id = "{$code}08";
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
                'actions'=>array('index'),
                'expression'=>array('ConcludeStaffController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        $session = Yii::app()->session;
        $code = isset($session['menu_code'])?$session['menu_code']:"dd";
        return Yii::app()->user->validRWFunction("{$code}08");
    }

    public static function allowReadOnly() {
        $session = Yii::app()->session;
        $code = isset($session['menu_code'])?$session['menu_code']:"dd";
        return Yii::app()->user->validFunction("{$code}08");
    }

    public static function allowRead() {
        return true;
    }

    public function actionIndex($index,$pageNum=0){
        $model = new ConcludeStaffList;
        if (isset($_POST['ConcludeStaffList'])) {
            $model->attributes = $_POST['ConcludeStaffList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['concludeStaff_'.$this->function_id]) && !empty($session['concludeStaff_'.$this->function_id])) {
                $criteria = $session['concludeStaff_'.$this->function_id];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveAll($index,$model->pageNum);
        $this->render('index',array('model'=>$model));
    }
}