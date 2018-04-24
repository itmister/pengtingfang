<?php
namespace Util\Admin;
use \Core\Controller;

class Admin_controller extends Controller {

    public $url_login = '';
    public $url_default = '';

    protected $_url_logout = '';                       //登出连接
    protected $_login_sec_key = 'y23334KLSlsgg';     //登录sec加密密钥
    protected $_user_info = [];

    protected $_urls = [];                              //跟后台交互的连接

    /**
     * 是否启用权限限制
     * @var bool
     */
    protected $_use_rbac       = true;

    /**q
     * @var \Dao\Dao
     */
    protected $_dao_admin_user = null;

    /**
     * @var \Dao\Dao
     */
    protected $_dao_admin_group = null;

    /**
     * @var \Dao\Dao
     */
    protected $_dao_admin_authority = null;

    /**
     * @var \Dao\Dao
     */
    protected $_dao_admin_menu = null;


    public function __construct( $cur_module = null, $cur_controller = null , $cur_action = null ) {
        parent::__construct( $cur_module, $cur_controller, $cur_action );
        $this->_assign('url_logout', $this->_url_logout);
        $this->_assign('url_login', $this->url_login );
        $this->_login_check();
        if (method_exists($this, '_init')) $this->_init();//初始化

    }

    public function login() {

        $param = $this->params();
        if ( $param->int('submit') ) {
            $user_name  = $param->string('user_name', true);
            $password   = $param->string('password', true);
            if ( $user_name != 'admin' || $password != 'z2015m88a' ) $this->_error('帐号密码不正确');
            $dateline_login = time();
            $sec_info = json_encode( [ 'user_name' => $user_name, 'time' => $dateline_login ] );
            $sec = \Util\Security::encrypt( $sec_info, \Config::get('security_key', null,  $this->_login_sec_key, 'info') );
            \io\cookie::set('sec', $sec);
            $this->_success( [ 'url' => $this->url_default ] );
        }

        \View::i()->display( 'template/login', '.php', __DIR__ );
    }

    public function logout() {
        \io\cookie::set('sec', null);
        $this->_redirect($this->url_login);
    }

    /**
     * 始始化，构造函数后执行
     */
    protected function _init() {

        //crud连接
        $this->_urls['index'] = \View::i()->url(['m' => $this->_module, 'c' => $this->_controller, 'a' => 'index']);
        $this->_urls['add'] = \View::i()->url(['m' => $this->_module, 'c' => $this->_controller, 'a' => 'add']);
        $this->_urls['edit'] = \View::i()->url(['m' => $this->_module, 'c' => $this->_controller, 'a' => 'edit']);
        $this->_urls['delete'] = \View::i()->url(['m' => $this->_module, 'c' => $this->_controller, 'a' => 'delete']);

        $this->_assign(['urls' => $this->_urls]);
    }

    protected function _login_check() {
        if ( $this->_action == 'login'|| false == $this->_use_rbac ) return false;
        if ( !$this->_is_login() ) $this->_redirect( $this->url_login );
    }

    protected function _is_login() {
        $sec    = \io\cookie::get('sec');
        $info   = json_decode( \Util\Security::decrypt( $sec, \Config::get('security_key', null, $this->_login_sec_key, 'info') ), true );
        $this->_user_info = !empty($info) ? $info : [];
        return !empty( $this->_user_info );
    }

    public function _display(  $tpl = '', $prefix = '.php', $path_template = '' ) {
        $tpl = $this->_tpl( $tpl );
        $this->_assign('tpl_content', $tpl );
        \View::i()->display('template/layout', '.php', __DIR__ );
    }
}
