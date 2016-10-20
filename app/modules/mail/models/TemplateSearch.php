<?php
namespace app\modules\mail\models;

use Yii,
    yii\base\Model,
    yii\data\ActiveDataProvider;

class TemplateSearch extends Template
{
    public $searchTitle;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['token', 'searchTitle'], 'safe'],
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
        $query = Template::find()->select('mail_templates.*')
            ->innerJoin('mail_template_i18ns', '`mail_template_i18ns`.`parent_id` = `mail_templates`.`id`')
            ->groupBy('`mail_templates`.`id`');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array(
                'pageSize' => 20,
            ),
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'mail_template_i18ns.subject', $this->searchTitle])
              ->andFilterWhere(['like', 'mail_templates.token', $this->token]);

        return $dataProvider;
    }
}