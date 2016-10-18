<?php
namespace app\modules\user\models;

use app\modules\user\models\User,
    Yii,
    yii\base\Model,
    yii\data\ActiveDataProvider;

/**
 * UserSearch represents the model behind the search form about `\app\modules\user\models\User`.
 */
class UserSearch extends User
{
    public $role;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['username', 'email', 'role'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = User::find()->orderBy('`status` DESC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email]);
        if ($this->role) {
            $query->innerJoin('auth_assignment', 'users.id = auth_assignment.user_id')
                  ->andFilterWhere(['item_name' => $this->role]);
        }

        return $dataProvider;
    }
}