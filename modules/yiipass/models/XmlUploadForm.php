<?php
/**
 * Created by Peter Majmesku.
 * E-Mail: p.majmesku@gmail.com
 * Date: 28.07.15
 * Time: 06:52
 */

namespace app\modules\yiipass\models;

use yii\base\Model;
use yii\web\UploadedFile;

class XmlUploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $file;

    public $file_path;

    public function upload()
    {
        // $this->getAttributes()
        $mime_type = \yii\helpers\FileHelper::getMimeType($this->file->tempName);
        if ($this->validate($mime_type)) {
            $this->file_path = 'uploads/' . $this->file->baseName . '.' . $this->file->extension;
            return $this->file->saveAs($this->file_path);
        } else {
            return false;
        }
    }
}