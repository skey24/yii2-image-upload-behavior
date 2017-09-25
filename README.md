yii2-image-upload-behavior 
=======================

Behavior for upload photos.

Features:

* Upload multiple images
* Resize images
* Create thumbnails

Dependencies

yii2-imagine

Using:

Put ImageUploadBehavior in common\components

Code in model

```
use common\components\ImageUploadBehavior

public function behaviors()
{
    return [
        [
            'class' => ImageUploadBehavior::className(),
            'attributes' => [
                [
                    'instanceAttribute' => 'imagePhoto', //form field attribute
                    'attribute' => 'image', //model attribute
                    'filePath' => $this->getImagesPath(), //path to image
                    'resize' => ['width' => 159], //resize image
                    'createThumb' => false,
                ],
                [
                    'instanceAttribute' => 'preview', //form field attribute
                    'attribute' => 'cover_image', //model attribute
                    'filePath' => $this->getCoverImagePath(), //path to image
                    'resize' => ['height' => 100], //resize image
                    'createThumb' => false,
                ],
            ],
        ],
    ];
}
```
