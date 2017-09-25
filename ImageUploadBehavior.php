<?php

namespace common\components;

use yii\base\Behavior,
    yii\db\ActiveRecord,
    yii\web\UploadedFile;

/**
* Behavior для загрузки фото
*
* Поведение загружает, ресайзит и удаляет старое изображение, создает эскизы
*
* method upload($path, $image, $size)
*/

class ImageUploadBehavior extends Behavior
{
    public $attributes;

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
        ];
    }

    /**
    * Метод для загрузки фото
    *
    * @param string $path Полный путь к файлу
    * @param object $image аттрибут модели с инстансом загруженного файла
    * @param array $size уменьшение избражения до нужной ширины
    * @param bool $createThumb создавать эскиз
    * @param array $thumbSize размеры эскиза
    * @return string Имя загруженного файла
    */

    public function upload($path, $image, $size = null, $createThumb = false, $thumbSize = [200, 200])
    {
        if (!file_exists($path)) {
            mkdir($path, 0777);
        }

        if ($size != null && is_int($size)) {
            $size = ['width' => $size];
        }

        $baseName = md5($this->owner->id . microtime());

        $newFilename = $baseName . '.' . $image->extension;

        if ($image->saveAs($path . DIRECTORY_SEPARATOR . $newFilename)) {
            if ($size !== null) {
                $imagineObj = \yii\imagine\Image::getImagine();
                try {
                    if (file_exists($path . DIRECTORY_SEPARATOR . $newFilename)) {
                        $imageObj = $imagineObj->open($path . DIRECTORY_SEPARATOR . $newFilename);

                        if (isset($size['width']) && !isset($size['height'])) {
                            $imageObj->resize($imageObj->getSize()->widen($size['width']))->save($path
                            . DIRECTORY_SEPARATOR . $newFilename);
                        } elseif (isset($size['height'])) {
                            $imageObj->resize($imageObj->getSize()->heighten($size['height']))->save($path
                            . DIRECTORY_SEPARATOR . $newFilename);
                        }                         
                    } else {
                        throw new \Exception('Не удалось открыть файл при ресайзе фото');
                    }
                } catch (Exception $e) {
                    // print $e->getMessage();

                }
            }

            if ($createThumb) {
                \yii\imagine\Image::thumbnail($path . DIRECTORY_SEPARATOR .
                $newFilename, $thumbSize[0], $thumbSize[1])
                ->save($path . DIRECTORY_SEPARATOR . $baseName . '_thumb' .
                $image->extension , ['quality' => 100]);
            }

            return $newFilename;
        } else {
            return false;
        }
    }

    public function beforeValidate()
    {
        foreach ($this->attributes as $attribute) {
            if (!$this->owner->{$attribute['instanceAttribute']})
                $this->owner->{$attribute['instanceAttribute']} = UploadedFile::getInstance($this->owner, $attribute['instanceAttribute']);
        }
    }

    public function beforeSave($event)
    {
        foreach ($this->attributes as $attribute) {
            if ($this->owner->{$attribute['instanceAttribute']}) {
                $oldFile = $this->owner->{$attribute['attribute']};
                $uploadedFile = $this->upload($attribute['filePath'], $this->owner->{$attribute['instanceAttribute']}, isset($attribute['resize']) ? $attribute['resize'] : null);

                if ($uploadedFile) {
                    if (file_exists($attribute['filePath']) . DIRECTORY_SEPARATOR . $oldFile)
                        @unlink($attribute['filePath'] . DIRECTORY_SEPARATOR . $oldFile);

                    $this->owner->{$attribute['attribute']} = $uploadedFile;
                }
            }
        }
    }
}
