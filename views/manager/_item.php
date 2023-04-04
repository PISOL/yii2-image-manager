<div class="thumbnail <?php echo ($model->type === "FOLDER" ? "folder" : ""); ?>" <?php echo ($model->type === "FOLDER" ? "data-folder_name='".$model->folder_name."'" : ""); ?> title="<?=$model->title_upload?>">
	<img src="<?=\Yii::$app->imagemanager->getImagePath($model->id, 150, 150)?>" alt="<?=$model->title_upload?>">
	<div class="title_upload"><?=($model->type === "FOLDER" ? $model->folder_name : $model->title_upload)?></div>
</div>
