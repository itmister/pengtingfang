<?php
namespace Sdk\Ws;

class WcsImageManager
{

    /**
     * 获取公开图片的基本信息
     * @param $bucketName
     * @param $fileName
     * @return string
     */
    public function info_public($bucketName, $fileName)
    {
        return $this->info($bucketName, $fileName, true);
    }

    /**
     * 获取图片的基本信息
     * @param $bucketName
     * @param $fileName
     * @param $isPublic
     * @return string
     */
    public function info($bucketName, $fileName, $isPublic)
    {
        if ($isPublic) {
            $baseUrl = build_public_url($bucketName, $fileName) . '?';
        } else {
            $baseUrl = build_private_url($bucketName, $fileName) . '&';
        }
        $url = $baseUrl . 'op=imageInfo';
        return http_get($url);
    }

    /**
     * 获取私有图片的基本信息
     * @param $bucketName
     * @param $fileName
     * @return string
     */
    public function info_private($bucketName, $fileName)
    {
        return $this->info($bucketName, $fileName, false);
    }

    /**
     * 获取公开图片的exif信息
     * @param $bucketName
     * @param $fileName
     * @return string
     */
    public function exif_public($bucketName, $fileName)
    {
        return $this->exif($bucketName, $fileName, true);
    }

    /**
     * 获取图片的exif信息
     * @param $bucketName
     * @param $fileName
     * @param $isPublic
     * @return string
     */
    public function exif($bucketName, $fileName, $isPublic)
    {
        if ($isPublic) {
            $baseUrl = build_public_url($bucketName, $fileName) . '?';
        } else {
            $baseUrl = build_private_url($bucketName, $fileName) . '&';
        }
        $url = $baseUrl . 'op=exif';
        return http_get($url);
    }

    /**
     * 获取私有图片的exif信息
     * @param $bucketName
     * @param $fileName
     * @return string
     */
    public function exif_private($bucketName, $fileName)
    {
        return $this->exif($bucketName, $fileName, false);
    }

    /**
     * 获取公开图片的缩放url
     * @param $bucketName
     * @param $fileName
     * @param $imageView
     * @return string
     */
    public function build_view_public_url($bucketName, $fileName, $imageView)
    {
        return $this->build_view_url($bucketName, $fileName, $imageView, true);
    }

    /**
     * 获取图片的缩放url
     * @param $bucketName
     * @param $fileName
     * @param $imageView
     * @param $isPublic
     * @return string
     */
    public function build_view_url($bucketName, $fileName, $imageView, $isPublic)
    {
        if ($isPublic) {
            $baseUrl = build_public_url($bucketName, $fileName) . '?';
        } else {
            $baseUrl = build_private_url($bucketName, $fileName) . '&';
        }
        $param = $imageView->build_url_params();
        return $baseUrl . $param;
    }

    /**
     * 获取私有图片的缩放url
     * @param $bucketName
     * @param $fileName
     * @param $imageView
     * @return string
     */
    public function build_view_private_url($bucketName, $fileName, $imageView)
    {
        return $this->build_view_url($bucketName, $fileName, $imageView, false);
    }

    /**
     * 获取公开图片的水印url
     * @param $bucketName
     * @param $fileName
     * @param $imageWatermark
     * @return string
     */
    public function build_watermark_public_url($bucketName, $fileName, $imageWatermark)
    {
        return $this->build_watermark_url($bucketName, $fileName, $imageWatermark, true);
    }

    /**
     * 获取图片的水印url
     * @param $bucketName
     * @param $fileName
     * @param $imageWatermark
     * @param $isPublic
     * @return string
     */
    public function build_watermark_url($bucketName, $fileName, $imageWatermark, $isPublic)
    {
        if ($isPublic) {
            $baseUrl = build_public_url($bucketName, $fileName) . '?';
        } else {
            $baseUrl = build_private_url($bucketName, $fileName) . '&';
        }
        $param = $imageWatermark->build_url_params();
        return $baseUrl . $param;
    }

    /**
     * 获取私有图片的水印url
     * @param $bucketName
     * @param $fileName
     * @param $imageWatermark
     * @return string
     */
    public function build_watermark_private_url($bucketName, $fileName, $imageWatermark)
    {
        return $this->build_watermark_url($bucketName, $fileName, $imageWatermark, false);
    }

    /**
     * 获取公开图片的高级处理url
     * @param $bucketName
     * @param $fileName
     * @param $imageMogr
     * @return string
     */
    public function build_mogr_public_url($bucketName, $fileName, $imageMogr)
    {
        return $this->build_mogr_url($bucketName, $fileName, $imageMogr, true);
    }

    /**
     * 获取图片的高级处理url
     * @param $bucketName
     * @param $fileName
     * @param $imageMogr
     * @param $isPublic
     * @return string
     */
    public function build_mogr_url($bucketName, $fileName, $imageMogr, $isPublic)
    {
        if ($isPublic) {
            $baseUrl = build_public_url($bucketName, $fileName) . '?';
        } else {
            $baseUrl = build_private_url($bucketName, $fileName) . '&';
        }
        $param = $imageMogr->build_url_params();
        return $baseUrl . $param;
    }

    /**
     * 获取私有图片的高级处理url
     * @param $bucketName
     * @param $fileName
     * @param $imageMogr
     * @return string
     */
    public function build_mogr_private_url($bucketName, $fileName, $imageMogr)
    {
        return $this->build_mogr_url($bucketName, $fileName, $imageMogr, false);
    }
} 