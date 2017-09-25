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
            ],
        ],
    ];
}
```
