<?php
/**
 * @desc 文件
 */
namespace Util;

class File {

    public static function dir( $root, $only_file = true , $ext = '' , $pattern = '') {
        $dir = @dir( $root );//打开upload目录；“@”可屏蔽错误信息，因有时候需要显示文件的目录内并没有文件，此时可能会报出错误，用“@”隐藏掉错误
        $ext_len = -1 * strlen( $ext );
        while ( ($file = $dir->read() ) !== false ) {
            if ( $file == '.' || $file == '..' ) continue;
            if ( $only_file && is_dir( $root . DIRECTORY_SEPARATOR . $file) ) continue;
            if ( $ext_len != 0 && substr($file,  $ext_len ) != $ext ) continue;
            if ( !empty($pattern) && !preg_match($pattern, $file)) continue;
            yield $file;
        }
        $dir->close();
    }

    /**
     * 图片文件缩放,需要操作系统安装imagemagick http://www.imagemagick.org
     * @param string $src 源文件
     * @param string $des 目标文件
     * @param integer $w 宽
     * @param integer $h 高
     */
    public static function image_resize($src, $des, $w, $h) {
        $w = intval ($w);
        $h = intval( $h );
        $src = trim( $src );
        $des = trim( $des );
        shell_exec(`convert -resize {$w}x{$h} {$src} {$des}`);
    }

    public static function get_file_lines($filename, $startLine = 1, $endLine = 50, $method = 'rb'){
        $content = array();
        $count = $endLine - $startLine;
        $fp = new \SplFileObject($filename, $method);
        $fp->seek($startLine - 1); // 转到第N行, seek方法参数从0开始计数
        for ($i = 0; $i <= $count; ++$i) {
            $content[] = $fp->current(); // current()获取当前行内容
            $fp->next(); //下一行
        }
        return $content; // array_filter过滤：false,null,''
    }

    public static function get_max_line_len($filename, $method = 'rb'){
        $fp = new \SplFileObject($filename, $method);
        $maxlen = $fp->getMaxLineLen();
        return $maxlen ? $maxlen:0;
    }
}