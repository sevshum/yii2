<?php
namespace app\modules\user\models;

use app\modules\user\models\components\VerifyEmailInterface;
use Yii;
use yii\base\Object;
use yii\helpers\Url;


class SocialService extends Object
{
    /**
     * @var User 
     */
    public $user;
    
    /**
     * @var \yii\authclient\ClientInterface 
     */
    public $client;
    
    /**
     * @var \yii\authclient\AuthAction
     */
    public $context;
    /**
     * @var array 
     */
    private $socialData;
        
    public function run()
    {
        $this->socialData = $this->client->getUserAttributes();
        $mapData = $this->getMappedData();
        if ($this->user === null) {
            return $this->loginOrCreate($mapData);
        }
        return $this->assignProvider($mapData);
    }
    
    /**
     * @param array $data
     */
    protected function loginOrCreate($data)
    {
        if (!isset($data['id'])) { //process incorrect data
            Yii::$app->getSession()->set('social_data', $data);
            return $this->context->redirect(Url::toRoute(['/user/users/create']));
        }
        $provider = Provider::findOne(['provider_id' => $data['id'], 'provider' => $this->client->getId()]);
        if ($provider === null) { //create user or add provider
            $user = User::create($data, 'social');
            if ($user === null && ($user = User::findOne(['email' => $data['email']])) === null) {
                Yii::$app->getSession()->set('social_data', $data);
                return $this->context->redirect(Url::toRoute(['/user/users/create']));
            }
            $userExists = true;
            $this->createProvider($user, $data);
        } else { //The provider has user
            $user = $provider->user;
        }
        $this->setAvatar($user, $data);        
        if (!isset($userExists)) {
            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'You successfully registred.'));
        }
        if ($user instanceof VerifyEmailInterface && !$user->is_email_verified) {
            $user->verifyEmail();
        }
        $webUser = Yii::$app->getUser();
        $webUser->login($user);
        return $this->context->redirect($webUser->getReturnUrl(['/user/users/profile']));
    }
    
    protected function assignProvider($data)
    {
        if (!isset($data['id'])) { //process incorrect data
            return $this->context->redirectCancel();
        }
        $provider = Provider::findOne(['provider_id' => $data['id'], 'provider' => $this->client->getId()]);
        $user = $this->user;
        if ($provider === null) { //create user or add provider            
            if ($user->status !== User::STATUS_ACTIVE) {
                Yii::$app->getSession()->setFlash('error', Yii::t('app', 'The account is not active.'));
                return $this->context->redirect(Url::toRoute(['/user/users/create']));
            }
            $this->createProvider($user, $data);
        } else { //The provider has user
            Yii::$app->getSession()->setFlash('error', Yii::t('app', 'The social provider already connected.'));
            return $this->context->redirect(Url::toRoute(['/user/users/profile', 'tab' => 'social']));
        }
        $this->setAvatar($user, $data);
        Yii::$app->getSession()->setFlash('success', Yii::t('app', 'The social provider successfully connected.'));
        return $this->context->redirect(Url::toRoute(['/user/users/profile', 'tab' => 'social']));
    }
    
    /**
     * @param User $user
     * @param array $data
     */
    private function setAvatar($user, $data)
    {
        if (isset($data['photo']) && !$user->profile->avatar) { // assign new avatar to user
            $user->profile->setAvatar($data['photo']);
            $user->profile->save(false);
        }
    }
    
    /**
     * @param User $user
     * @param array $data
     * @return \app\modules\user\models\Provider
     */
    private function createProvider($user, $data)
    {
        $provider = new Provider([
            'data' => json_encode($this->socialData),
            'username' => $data['username'],
            'provider_id' => $data['id'], 
            'provider' => $this->client->getId()
        ]);
        $user->link('providers', $provider);
        return $provider;
    }

    /**
     * Map social data
     * @param type $data
     * @return string
     */
    protected function getMappedData()
    {
        $provider = $this->client->getId();
        $data = $this->socialData;
        
        $user = ['id' => null, 'email' => null, 'username' => null, 'photo' => null];
        if ($provider === 'google') {
            if (isset($data['emails'])) {
                foreach ($data['emails'] as $e) {
                    if ($e['type'] === 'account') {
                        $user['email'] = $e['value'];
                        break;
                    }
                }
                if ($user['email'] === null) {
                    $user['email'] = $data['emails'][0]['value'];
                }
            }
            $user['id'] = isset($data['id']) ? $data['id'] : null;
            $user['username'] = isset($data['displayName']) ? $data['displayName'] : null;
            $user['photo'] = isset($data['image']['url']) ? $data['image']['url'] : null;
            if ($user['photo'] !== null) {
                $user['photo'] = strtr($user['photo'], ['sz=50' => 'sz=500']);
            }
        } elseif ($provider === 'vkontakte') {
            $user['id'] = isset($data['uid']) ? $data['uid'] : null; 
            $user['email'] = isset($data['email']) ? $data['email'] : null; 
            $user['username'] = isset($data['first_name'], $data['last_name']) ? 
                ($data['first_name'] . ' ' . $data['last_name']) : null;
            $user['photo'] = isset($data['photo_max_orig']) ? $data['photo_max_orig'] : null;
        } elseif ($provider === 'facebook') {
            $user['id'] = isset($data['id']) ? $data['id'] : null; 
            $user['username'] = isset($data['first_name'], $data['last_name']) ? 
                ($data['first_name'] . ' ' . $data['last_name']) : null;
            $user['email'] = isset($data['email']) ? $data['email'] : null;
            $user['photo'] = 'https://graph.facebook.com/' . $user['id'] . '/picture?width=500&height=500';
        } else {
            print_r($data);die;
        }
        return $user;
    }
}
