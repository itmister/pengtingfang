<?php
namespace Sdk\Ws;

class WcsImageWatermark {
    /**
     * @var 水印模式
     * mode = 1 图片水印
     * mode = 2 文字水印
     */
    public $mode;
    /**
     * @var 透明度，取值范围1-100，缺省值100（完全不透明）
     */
    public $dissolve;
    /**
     * @var 水印位置，取值
     * "TOP_LEFT", "TOP_CENTER", "TOP_RIGHT",
     * "CENTER_LEFT", "CENTER", "CENTER_RIGHT",
     * "BOTTOM_LEFT", "BOTTOM_CENTER", "BOTTOM_RIGHT"
     * 默认："BOTTOM_RIGHT"
     */
    public $gravity;
    /**
     * @var 横轴边距，单位:像素(px)，缺省值为10
     */
    public $dx;
    /**
     * @var 纵轴边距，单位:像素(px)，缺省值为10
     */
    public $dy;


    /**
     * @var 水印图片地址（公网可访问）
     */
    public $image;


    /**
     * @var 水印文字内容
     */
    public $text;
    /**
     * @var 水印文字字体
     * 缺省为黑体。支持宋体，楷体，微软雅黑，arial等java平台支持的字体。
     */
    public $font;
    /**
     * @var 水印文字大小，单位: 缇，等于1/20磅，缺省值30（默认大小）
     */
    public $fontsize;
    /**
     * @var 水印文字颜色，RGB格式，
     * 可以是颜色名称（比如red）或十六进制（比如#FF0000），参考RGB颜色编码表，
     * 缺省为白色
     */
    public $fill;

    function __construct($mode)
    {
        $this->mode = $mode;
    }


    public function build_url_params() {
        $mode = $this->mode;

        $params = 'op=watermark&mode=' . $mode;

        if (!empty($this->dissolve)) {
            $params .= '&dissolve=' . $this->dissolve;
        }

        if (!empty($this->gravity)) {
            $params .= '&gravity=' . $this->gravity;
        }

        if (!empty($this->dx)) {
            $params .= '&dx=' . $this->dx;
        }

        if (!empty($this->dy)) {
            $params .= '&dy=' . $this->dy;
        }

        if ($mode === 1) {
            if (!empty($this->image)) {
                $params .= '&image=' . url_safe_base64_encode($this->image);
            }
        } else if ($mode === 2) {
            if (!empty($this->text)) {
                $params .= '&text=' . url_safe_base64_encode($this->text);
            }

            if (!empty($this->font)) {
                $params .= '&font=' . url_safe_base64_encode($this->font);
            }

            if (!empty($this->fontsize)) {
                $params .= '&fontsize=' . $this->fontsize;
            }

            if (!empty($this->fill)) {
                $params .= '&fill=' . url_safe_base64_encode($this->fill);
            }
        }

        return $params;
    }
} 