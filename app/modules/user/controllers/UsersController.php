<?php

namespace app\modules\user\controllers;

use app\modules\core\components\BaseController;
use app\modules\user\models\components\VerifyEmailInterface;
use app\modules\user\models\forms\ChangePasswordForm;
use app\modules\user\models\forms\PasswordResetRequestForm;
use app\modules\user\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;


class UsersController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['create', 'forgotpassword', 'resetpassword', 'verify-email'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    
    public function actionProfile($tab = null)
    {
        $user = Yii::$app->getUser()->getIdentity();
        $user->scenario = 'public_update';
        if ($tab === null) {
            $tab = Yii::$app->getRequest()->post('tab');
        }
        if ($user->load($_POST)) {
            $user->profile->image = UploadedFile::getInstanceByName('User[profileattrs][image]');
            if ($user->validate()) {
                $user->profile->uploadImage($user->profile->image, 'avatar');
                $user->save(false);
                Yii::$app->getSession()->setFlash('user_success', Yii::t('app', 'Profile updated successfully.'));
                return $this->redirect(['profile', 'tab' => $tab]);
            }
        }
        return $this->render('profile', ['user' => $user, 'tab' => $tab]);
    }
    
    public function actionChangepassword()
    {
        $model = new ChangePasswordForm;
        $model->needActualPassword = true;
        
        $user = $this->getUser();
        if ($model->load($_POST)) {
            $model->setUser($user->getIdentity());
            if ($model->perform()) {
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Your password has been changed successfully.'));
                return $this->redirect(['settings']);
            }
        }
        return $this->render('change_password', compact('model'));
    }
    
    
    public function actionForgotpassword()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Check your email for further instructions.'));

                return $this->goHome();
            } else {
                Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Sorry, we are unable to reset password for email provided.'));
            }
        }

        return $this->render('forgot_password', [
            'model' => $model,
        ]);
    }

    public function actionResetpassword($token)
    {
        $user = User::findOne(['password_reset_token' => $token]);
        if ($user === null) {
            return $this->goHome();
        }
        $model = new ChangePasswordForm();
        if ($model->load($_POST)) {
            $model->setUser($user);
            if ($model->perform()) {
                if ($user instanceof VerifyEmailInterface && !$user->is_email_verified) {
                    $user->verifyEmail();
                }
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Your password has been changed successfully.'));
                return $this->redirect(['/site/login']);
            }
        }
        return $this->render('reset_password', ['model' => $model]);
    }
    
    public function actionVerifyEmail($token)
    {
        $implements = class_implements(User::className());
        if (!in_array('app\modules\user\models\components\VerifyEmailInterface', $implements)) {
            throw new NotFoundHttpException('Page not found');
        }
        $user = User::findByVerifyEmailToken($token);
        if ($user === null) {
            return $this->goHome();
        }
        $user->verifyEmail();
        Yii::$app->getSession()->setFlash('success', Yii::t('app', 'The email verified successfully.'));
        return $this->redirect(['/user/session/create']);
    }
    
    public function actionCreate()
    {
        $model = new User();
        $model->scenario = 'public';
        
        $session = Yii::$app->getSession();
        if ($data = $session->get('social_data')) {
            $_POST[$model->formName()] = $data;
            $session->remove('social_data');
        }
        
        if ($model->load($_POST) && $model->validate()) {
            $model->status = User::STATUS_ACTIVE;
            if ($model->save(false)) {
                if ($model instanceof VerifyEmailInterface) {
                    $model->populateVerifyEmailToken();
                    Yii::$app->getModule('mail')
                        ->createMessage()
                        ->setTemplate('verify_email', [
                            'email' => $model->email,
                            'username' => ucfirst($model->username),
                            'link' => Url::toRoute(['/user/users/verify-email', 'token' => $model->verify_email_token], true)
                        ])
                        ->setTo($model->email)
                        ->send();
                    
                    Yii::$app->getSession()->setFlash('warning', Yii::t('app', 'Please verify your email.'));
                    return $this->redirect(['/user/session/create']);
                } 
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'You have successfully registered.'));
                Yii::$app->getUser()->login($model);
                return $this->redirect(['profile']);
            }
        }
        return $this->render('create', array('model' => $model));
    }
}
