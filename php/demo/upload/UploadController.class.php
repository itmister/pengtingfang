<?php
namespace Duoduoabc\Controller;
use Admin\Controller\CommonController;
/**
 * 上传文件
 * @author pengtingfang
 */
class UploadController extends DuoduoabcController {
    public function _initialize() {
        parent::_initialize();
    }
    /**
     * 普通上传文件
     */
    public function file(){
        $data = I("get.");
        $folder = $data['type'] ? $data['type']."/": "";
        $local_path  = "duoduo/".$folder;
        if (!is_dir($local_path))
            mkdir($local_path, 0777); // 使用最大权限0777创建文件
        $file = $_FILES["file"];
        if(!$file){
            $data['status'] = '0';
            $data['info'] = '上传失败';
            $this->ajaxReturn($data);
        }
        if ($file['error'] == 0) {
            $uploadfile = md5(time().rand(10000000,99999999).rand(10000000,99999999)) . basename($_FILES['file']['name']);
            $bol = move_uploaded_file($file["tmp_name"], $local_path.$uploadfile);
            if($bol){
                $data['status'] = '1';
                $data['info'] = "file/upload/".$folder.$uploadfile;
                $this->ajaxReturn($data);
            }
        }
        $data['status'] = '0';
        $data['info'] = '上传失败';
        $this->ajaxReturn($data);
    }

    /**
     * 大文件
     */
    public function  bigFile(){
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit; // finish preflight CORS requests here
        }
        $data = I("get.");
        $folder = $data['type'] ? $data['type'].DIRECTORY_SEPARATOR: "";
        @set_time_limit(5 * 60);
        $targetDir = 'duoduo'.DIRECTORY_SEPARATOR.$folder.'file_material_tmp';
        $uploadDir = 'duoduo'.DIRECTORY_SEPARATOR.$folder;
        if($data['type'] == "csv"){
            $uploadDir = 'duoduo'.DIRECTORY_SEPARATOR.$folder.'file_material';
        }
        $cleanupTargetDir = true; // Remove old files
        $maxFileAge = 5 * 3600; // Temp file age in seconds
        if (!file_exists($targetDir)) {
            @mkdir($targetDir,0777,true);
        }
        if (!file_exists($uploadDir)) {
            @mkdir($uploadDir,0777,true);
        }
        if (isset($_REQUEST["name"])) {
            $fileName = $_REQUEST["name"];
        } elseif (!empty($_FILES)) {
            $fileName = $_FILES["file"]["name"];
        } else {
            $fileName = uniqid("file_");
        }
        $oldName = $fileName;
        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;
        if ($cleanupTargetDir) {
            if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                $this->ajaxReturn(['0','Failed to open temp directory']);
            }
            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
                if ($tmpfilePath == "{$filePath}_{$chunk}.part" || $tmpfilePath == "{$filePath}_{$chunk}.parttmp") {
                    continue;
                }
                if (preg_match('/\.(part|parttmp)$/', $file) && (@filemtime($tmpfilePath) < time() - $maxFileAge)) {
                    @unlink($tmpfilePath);
                }
            }
            closedir($dir);
        }
        if (!$out = @fopen("{$filePath}_{$chunk}.parttmp", "wb")) {
            $this->ajaxReturn(['0','Failed to open output stream']);
        }
        if (!empty($_FILES)) {
            if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
                $this->ajaxReturn(['0','Failed to move uploaded file']);
            }
            if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
                $this->ajaxReturn(['0','Failed to open input stream']);
            }
        } else {
            if (!$in = @fopen("php://input", "rb")) {
                $this->ajaxReturn(['0','Failed to open input stream']);
            }
        }
        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }
        @fclose($out);
        @fclose($in);
        rename("{$filePath}_{$chunk}.parttmp", "{$filePath}_{$chunk}.part");
        $index = 0;
        $done = true;
        for( $index = 0; $index < $chunks; $index++ ) {
            if ( !file_exists("{$filePath}_{$index}.part") ) {
                $done = false;
                break;
            }
        }
        if ( $done ) {
            $pathInfo = pathinfo($fileName);
            $hashStr = substr(md5($pathInfo['basename']),8,16);
            $hashName = time() . $hashStr . '.' .$pathInfo['extension'];
            $uploadPath = $uploadDir . DIRECTORY_SEPARATOR .$hashName;

            if (!$out = @fopen($uploadPath, "wb")) {
                $this->ajaxReturn(['0','Failed to open output stream']);
            }
            if ( flock($out, LOCK_EX) ) {
                for( $index = 0; $index < $chunks; $index++ ) {
                    if (!$in = @fopen("{$filePath}_{$index}.part", "rb")) {
                        break;
                    }
                    while ($buff = fread($in, 4096)) {
                        fwrite($out, $buff);
                    }
                    @fclose($in);
                    @unlink("{$filePath}_{$index}.part");
                }
                flock($out, LOCK_UN);
            }
            @fclose($out);
            $response = [
                'status'=>1,
                'url' => "file/upload/".($data['type'] ? $data['type']."/": "") .$oldName,
                'oldName'=>$oldName,
                'filePaht'=>$uploadPath,
                'fileSuffixes'=>$pathInfo['extension']
            ];
            $this->ajaxReturn($response);
        }
        $this->ajaxReturn(['0','result!!!']);
    }
}