<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/15
 * Time: 15:06
 */
namespace App\Admin\Extensions\Form;

use Encore\Admin\Form\Field\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ExtraImage extends Image
{
    protected $isAbsolutePath = false; //  标识是否支持绝对地址

    /**
     * Upload file and delete original file.
     *
     * @param UploadedFile $file
     *
     * @return mixed
     */
    protected function uploadAndDeleteOriginal(UploadedFile $file)
    {
        $this->renameIfExists($file);

        $path = null;

        if (!is_null($this->storagePermission)) {
            $path = $this->storage->putFileAs($this->getDirectory(), $file, $this->name, $this->storagePermission);
        } else {
            $path = $this->storage->putFileAs($this->getDirectory(), $file, $this->name);
        }

        $this->destroy();
        if ( stripos($path, "http") === false ){
            return rtrim(env('OSS_URL', "/")).'/'.ltrim($path, "/");
        }
        return $path;
    }

    public function absolutePath($bool = false)
    {
        $this->isAbsolutePath = $bool;
        return $this;
    }

}
