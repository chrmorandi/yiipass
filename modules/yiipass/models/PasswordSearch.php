<?php

namespace app\modules\yiipass\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\yiipass\models\Password;

/**
 * PasswordSearch represents the model behind the search form about `app\models\Password`.
 */
class PasswordSearch extends Password
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['title', 'group', 'username', 'password', 'comment', 'url', 'creation', 'lastaccess', 'lastmod', 'expire'], 'safe'],
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
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Password::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'creation' => $this->creation,
            'lastaccess' => $this->lastaccess,
            'lastmod' => $this->lastmod,
            'expire' => $this->expire,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'group', $this->group])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'url', $this->url]);

        return $dataProvider;
    }
}
