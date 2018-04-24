<?php
namespace Sdk\Ws;
class WcsImageView {

    /**
     * @var 缩放模式
     * mode = 1	限定缩略图的宽最少为<width>，高最少为<height>，进行等比缩放，居中裁剪。转后的缩略图通常恰好是<width>x<height>的大小（有一个边缩放的时候会因为超出矩形框而被裁剪掉多余部分）。如果只指定width参数或只指定height参数，代表限定为长宽相等的正方图。
     * mode = 2	限定缩略图的宽度最多为<width>，高度最多为<height>，进行等比缩放，不裁剪。如果只指定width参数则表示限定宽度（高度自适应），只指定height 参数则表示限定高度（宽度自适应）。
     * mode = 3	限定缩略图的宽最少为<width>，高最少为<height>，进行等比缩放，不裁剪。
     */
    public $mode;
    /**
     * @var 缩放宽度（单位px）
     */
    public $width;
    /**
     * @var 缩放高度（单位px）
     */
    public $height;
    /**
     * @var 新图的图像质量，取值范围：1-100，缺省为85；
     * 如原图质量小于指定值，则按原值输出
     */
    public $quality;
    /**
     * @var 新图的输出格式，取值范围：jpg，gif，png等，缺省为原图格式
     */
    public $format;

    function __construct($mode)
    {
        $this->mode = $mode;
    }


    public function build_url_params() {
        $params = 'op=imageView2&mode=' . $this->mode;

        if (!empty($this->width)) {
            $params .= '&width=' . $this->width;
        }

        if (!empty($this->height)) {
            $params .= '&$height=' . $this->height;
        }

        if (!empty($this->quality)) {
            $params .= '&$quality=' . $this->quality;
        }

        if (!empty($this->format)) {
            $params .= '&$format=' . $this->format;
        }

        return $params;
    }
} 