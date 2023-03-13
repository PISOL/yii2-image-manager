<?php

namespace pisol\imagemanager\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "ImageManager".
 *
 * @property integer $id
 * @property string $title
 * @property string $created
 * @property string $modified
 * @property string $createdBy
 * @property string $modifiedBy
 */
class ImageManager extends \yii\db\ActiveRecord { 

	/**
	 * Set Created date to now
	 */
	public function behaviors() {
	    $aBehaviors = [];

	    // Add the time stamp behavior
        $aBehaviors[] = [
            'class' => TimestampBehavior::className(),
            'createdAtAttribute' => 'ts_created',
            'updatedAtAttribute' => 'ts_updated',
            'value' => new Expression('NOW()'),
        ];

        // Get the imagemanager module from the application
        $moduleImageManager = Yii::$app->getModule('imagemanager');
        /* @var $moduleImageManager Module */
        if ($moduleImageManager !== null) {
            // Module has been loaded
            if ($moduleImageManager->setBlameableBehavior) {
                // Module has blame able behavior
                $aBehaviors[] = [
                    'class' => BlameableBehavior::className(),
                    'createdByAttribute' => 'user_id',
                    'updatedByAttribute' => 'user_id',
                ];
            }
        }

		return $aBehaviors;
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%files}}';
	}

    /**
     * Get the DB component that the model uses
     * This function will throw error if object could not be found
     * The DB connection defaults to DB
     * @return null|object
     */
	public static function getDb() {
        // Get the image manager object
        $oImageManager = Yii::$app->get('imagemanager', false);

        if($oImageManager === null) {
            // The image manager object has not been set
            // The normal DB object will be returned, error will be thrown if not found
            return Yii::$app->get('db');
        }

        // The image manager component has been loaded, the DB component that has been entered will be loaded
        // By default this is the Yii::$app->db connection, the user can specify any other connection if needed
        return Yii::$app->get($oImageManager->databaseComponent);
    }

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['title'], 'required'],
			[['enabled'], 'boolean'],
			[['ts_created', 'modified'], 'safe'],
			[['type'], 'string', 'max' => 50],
			[['size_file'], 'string', 'max' => 200],
			[['title', 'title_upload'], 'string', 'max' => 250],
			[['fileMime', 'path', 'url_path', 'url_path_cache', 'folder_name','cropped_sting'], 'string', 'max' => 100],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('imagemanager', 'ID'),
			'title' => Yii::t('imagemanager', 'File Name'),
			'ts_created' => Yii::t('imagemanager', 'Created'),
			'ts_updated' => Yii::t('imagemanager', 'Modified'),
			'user_id' => Yii::t('imagemanager', 'Modified by'),
		];
	}

	public function afterDelete()
    {
        parent::afterDelete();

        // Check if file exists
        if (file_exists($this->path)) {
            unlink($this->path);
        }
    }

    /**
	 * Get image path private
	 * @return string|null If image file exists the path to the image, if file does not exists null
	 */
	public function getImagePathPrivate() {
		//set default return
		$return = null;
		//set media path
		$sMediaPath = \Yii::$app->imagemanager->mediaPath;
		$sFileExtension = pathinfo($this->title, PATHINFO_EXTENSION);
		//get image file path
		$sImageFilePath = $sMediaPath . '/' . $this->id . '.' . $sFileExtension;
		//check file exists
		if (file_exists($sImageFilePath)) {
			$return = $sImageFilePath;
		}
		return $return;
	}

	
    /**
	 * Set image paths and info
	 * @return null 
	 */
	public function setImageAttributes() {
		//set media path
		$sMediaPath = \Yii::$app->imagemanager->mediaPath;
		$sFileExtension = pathinfo($this->title, PATHINFO_EXTENSION);
		//get image file path
		$this->path = $sMediaPath . '/' . $this->title;
		$this->url_path =  \Yii::$app->imagemanager->publicUrl . '/' . $this->title;
		$this->fileMime = mime_content_type($this->path);
		$cacheUrl = "cacheUrl".rand(1, 2);
		$this->url_path_cache = \Yii::$app->imagemanager->$cacheUrl . '/' . $this->title;
		$this->type = $sFileExtension;
		$this->size_file = self::formatSizeUnits(filesize($this->path));
	}
	
    /**
	 * Set image paths and info
	 * @return null 
	 */
	public function setFolderAttributes() {
		//set media path
		$sMediaPath = \Yii::$app->imagemanager->mediaPath;
		//get image file path
		$this->path = $sMediaPath . '/' . $this->title;
		$this->url_path =  \Yii::$app->imagemanager->publicUrl . '/' . $this->title;
		$this->fileMime = mime_content_type($this->path);
		$cacheUrl = "cacheUrl".rand(1, 2);
		$this->url_path_cache = \Yii::$app->imagemanager->$cacheUrl . '/' . $this->title;
		$this->type = "FOLDER";
		$this->size_file = self::formatSizeUnits(filesize($this->path));
	}

	private static function formatSizeUnits($bytes): string
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }
        return $bytes;
    }


	/**
	 * Get image data dimension/size
	 * @return array The image sizes
	 */
	public function getImageDetails() {
		//set default return
		$return = ['width' => 0, 'height' => 0, 'size' => 0];
		//set media path
		$sMediaPath = \Yii::$app->imagemanager->mediaPath;
		//get image file path
		$sImageFilePath = $sMediaPath . '/' . $this->title;
		//check file exists
		if (file_exists($sImageFilePath)) {
			$aImageDimension = getimagesize($sImageFilePath);
			$return['width'] = isset($aImageDimension[0]) ? $aImageDimension[0] : 0;
			$return['height'] = isset($aImageDimension[1]) ? $aImageDimension[1] : 0;
			$return['size'] = Yii::$app->formatter->asShortSize(filesize($sImageFilePath), 2);
		}
		return $return;
	}

}
