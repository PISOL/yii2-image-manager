<?php

namespace pisol\imagemanager\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use pisol\imagemanager\models\ImageManager;
use pisol\imagemanager\Module;

/**
 * ImageManagerSearch represents the model behind the search form about `common\modules\imagemanager\models\ImageManager`.
 */
class ImageManagerSearch extends ImageManager
{
	public $globalSearch;
    public $folder_name;
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['globalSearch', 'folder_name'], 'safe'],
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
        $query = ImageManager::find()->select(["*", "IF(type='FOLDER', 1, 0) as is_folder"]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'pagination' => [
				'pagesize' => 100,
			],
			'sort'=> ['defaultOrder' => ['is_folder' => SORT_DESC, 'ts_created'=>SORT_DESC]]
        ]);

        $dataProvider->sort->attributes['is_folder'] = [
            'asc'=>['is_folder'=>SORT_ASC],
            'desc'=>['is_folder'=>SORT_DESC]
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // Get the module instance
        $module = Module::getInstance();

        $query->andWhere(['NOT IN', 'type', ['xls', 'xlsx', 'doc', 'docx', 'pdf']]);

        if ($module->setBlameableBehavior) {
            $query->andWhere(['user_id' => Yii::$app->user->id]);
        }
        if($this->folder_name){
            $query->andWhere(['folder_name' => $this->folder_name]);
            $query->andWhere(['<>', 'type', 'FOLDER']);
        }else{
            $query->andWhere(['OR', 'folder_name IS NULL', ['type' => 'FOLDER']]);
        }

        $query->andFilterWhere(['like', 'title_upload', $this->globalSearch]);

        return $dataProvider;
    }
}
