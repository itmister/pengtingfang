<?php
namespace Activity;
class Component{
    protected $_errno = 1;
    protected $_error = '';

    /**
     * 接口返回false时，通过本接口获取详细的错误信息
     * @return array('errno'=>**, 'error'=>**)
     */
    public function getErrorInfo() {
        return array('errno'=>$this->_errno, 'error'=>$this->_error);
    }

    /**
     * 将其他接口的错误信息传递到当前组件
     * @param array $err getErrorInfo()返回的结果
     */
    public function setErrorInfo($err) {
        $this->_errno = $err['errno'];
        $this->_error = $err['error'];
    }

    /**
     *
     * @param unknown $error_code
     * @param unknown $error_message
     */
    protected function coverError($error_code,$error_message){
        $this->_errno = $error_code;
        $this->_error = $error_message;
    }
}