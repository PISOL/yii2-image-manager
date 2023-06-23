<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use kartik\file\FileInput;
use yii\web\JsExpression;

$this->title = Yii::t('imagemanager','Image manager');

?>
<div id="module-imagemanager" class="container-fluid <?=$selectType?>">
	<div class="row">
		<div class="col-xs-6 col-sm-10 col-image-editor">
			<div class="image-cropper">
				<div class="image-wrapper">
					<img id="image-cropper" />
				</div>
				<div class="action-buttons">
					<a href="#" class="btn btn-primary apply-crop">
						<i class="fa fa-crop"></i>
						<span class="hidden-xs"><?=Yii::t('imagemanager','Crop')?></span>
					</a>
					<?php if($viewMode === "iframe"): ?>
					<a href="#" class="btn btn-primary apply-crop-select">
						<i class="fa fa-crop"></i>
						<span class="hidden-xs"><?=Yii::t('imagemanager','Crop and select')?></span>
					</a>
					<?php endif; ?>
					<a href="#" class="btn btn-default cancel-crop">
						<i class="fa fa-undo"></i>
						<span class="hidden-xs"><?=Yii::t('imagemanager','Cancel')?></span>
					</a>
				</div>
			</div> 
		</div>
		<div class="col-xs-6 col-sm-10 col-overview">
			<?php Pjax::begin([
				'id'=>'pjax-mediamanager',
				'timeout'=>'5000'
			]);
			if($searchModel->folder_name){
				echo '<button type="button" class="btn btn-link" id="lnk-level-up"><i class="fas fa-level-up-alt"></i> BACK</button>';
			}
			  ?>    
			<?= ListView::widget([
				'dataProvider' => $dataProvider,
				'itemOptions' => ['class' => 'item img-thumbnail'],
				'layout' => "<div class='item-overview'>{items}</div> {pager}",
				'itemView' => function ($model, $key, $index, $widget) {
					return $this->render("_item", ['model' => $model]);
				},
				'pager' => ['class' => yii\bootstrap4\LinkPager::class]
			]) ?>
			<script>
				folderName = "<?php echo $searchModel->folder_name; ?>";
			</script>
			<?php Pjax::end(); ?>
		</div>
		<div class="col-xs-6 col-sm-2 col-options">
			<div class="form-group">
				<?=Html::textInput('input-mediamanager-search', null, ['id'=>'input-mediamanager-search', 'class'=>'form-control', 'placeholder'=>Yii::t('imagemanager','Search').'...'])?>
			</div>
			<hr />
			<?php
				if (Yii::$app->controller->module->canUploadImage):
			?>

			<?=FileInput::widget([
				'name' => 'imagemanagerFiles[]',
				'id' => 'imagemanager-files',
				'bsVersion' => '4.x',
				'options' => [
					'multiple' => true,
					'accept' => 'image/*'
				],
				'pluginOptions' => [
					'uploadUrl' => Url::to(['manager/upload']),
					'allowedFileExtensions' => \Yii::$app->controller->module->allowedFileExtensions, 
					'uploadAsync' => false,
					'showPreview' => false,
					'showRemove' => false,
					'showUpload' => false,
					'showCancel' => false,
					'browseClass' => 'btn btn-primary btn-block',
					'browseIcon' => '<i class="fa fa-upload"></i> ',
					'browseLabel' => Yii::t('imagemanager','Upload'),
					'uploadExtraData' => new JsExpression('function (previewId, index) {
						return { folder_name: folderName };
					}'),
				],
				'pluginEvents' => [
					"filebatchselected" => "function(event, files){  $('.msg-invalid-file-extension').addClass('d-none'); $(this).fileinput('upload'); }",
					"filebatchuploadsuccess" => "function(event, data, previewId, index) {
						imageManagerModule.uploadSuccess(data.jqXHR.responseJSON.imagemanagerFiles);
					}",
					"fileuploaderror" => "function(event, data) { $('.msg-invalid-file-extension').removeClass('d-none'); }",
				],
			]) ?>

			<?php
				endif;
			?>

			<div class="image-info d-none">
				<div class="thumbnail">
					<img src="#">
				</div>
				<div class="edit-buttons">
					<a href="#" class="btn btn-primary btn-block crop-image-item">
						<i class="fa fa-crop"></i>
						<span class="hidden-xs"><?=Yii::t('imagemanager','Crop')?></span>
					</a>
				</div>
				<div class="details">
					<div class="fileName"></div>
					<div class="created"></div>
					<div class="fileSize"></div>
					<div class="dimensions"><span class="dimension-width"></span> &times; <span class="dimension-height"></span></div>
					<?php
						if (Yii::$app->controller->module->canRemoveImage):
					?>
						<a href="#" class="btn btn-xs btn-danger delete-image-item" ><i class="fas fa-trash"></i> <?=Yii::t('imagemanager','Delete')?></a>
					<?php
						endif;
					?>
				</div>
				<?php if($viewMode === "iframe"): ?>
				<a href="#" class="btn btn-primary btn-block pick-image-item"><?=Yii::t('imagemanager','Select')?></a> 
				<?php endif; ?>
			</div>

			<?php if($viewMode === "page"): ?>
			<a href="#" class="btn btn-primary btn-block btn-create-folder mt-4"><i class="fas fa-folder-plus"></i> <?=Yii::t('imagemanager','Create folder')?></a>
			<div class="create-folder row d-none mt-4">
				<div class="col-8 pr-1">
					<?=Html::textInput('input-mediamanager-new_folder', null, ['id'=>'input-mediamanager-new_folder', 'class'=>'form-control', 'placeholder'=>Yii::t('imagemanager','Folder name').'...'])?>
				</div>
				<div class="col-2 px-1">
					<a href="#" class="btn btn-success submit-create-folder btn-block px-1" ><i class="fas fa-check"></i></a>
				</div>
				<div class="col-2 pl-0">
					<a href="#" class="btn btn-secondary cancel-create-folder btn-block px-1" ><i class="fas fa-times"></i></a>
				</div>
			</div> 
			<?php endif; ?>
		</div>  
	</div>
</div>  
<div id="loading" class="text-center position-absolute w-100 h-100" style="top:0;left:0;background-color: rgba(100, 100, 100, 0.3);z-index: 9999;display:none">
  <div class="spinner-border" role="status" style="margin-top: 20%">
    <span class="sr-only">Loading...</span>
  </div>
</div>