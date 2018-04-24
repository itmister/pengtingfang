<?php
/**
 * 收邮件类
 * Created by vl
 * Description :
 * Date: 2015/7/3
 * Time: 11:31
 */
namespace Util\Email;
class Fetch {

    protected $_cfg     = [];

    protected $_imap    = null;

    public function __construct($cfg) {

        if (!function_exists('imap_open')) throw new \Exception('need imap extensions');
        $this->_cfg = array_merge( $this->_cfg, $cfg );
        $imap      = imap_open("{{$cfg['server']}:993/imap/ssl}", $cfg['account'], $cfg['password']
            , NULL, 1, array('DISABLE_AUTHENTICATOR' => 'GSSAPI'));
        $this->_imap = $imap;

    }

    public function __destruct() {
        if ( !empty($this->_imap) ) imap_close( $this->_imap );
    }

    /**
     * @return mixed
     */
    public function get_imap_handle() {
        return !empty($this->_imap) ? $this->_imap : null;
    }

    /**
     * 迭代枚举未读邮件
     */
    public function enum_unseen() {
        $unseen_list = $this->unseen_id_list();
        if ( !empty($unseen_list)) foreach ( $unseen_list as $m ) {

            $item   = [ 'msg_no' => $m ];

            //邮件头
            $header = $this->header( $m );
            if ( empty($header) ) yield $item;
            $item['header'] = $header;

            //取邮件正文
            $item['body']   = $this->body( $m );

            //取邮件附件
            $item['attachments'] = $this->attachments( $m );
            yield $item;
        }
    }

    /**
     * 将邮件设置为已读
     * @param $msg_no
     */
    public function set_seen( $msg_no ) {
        if ( !empty($this->_imap) ) imap_setflag_full( $this->_imap, $msg_no, "\\Seen");
    }

    /**
     * 取未读邮件信息编号数组
     */
    public function unseen_id_list() {
        if (empty($this->_imap)) return [];
        $data      = imap_search( $this->_imap, 'UNSEEN');//看上去只会返回当月的
        return !empty($data) ? $data : [];
    }

    /**
     * 取邮件头信息
     * @param $msg_no
     */
    public function header( $msg_no ) {
        if ( empty($this->_imap)) return [];
        $header = imap_header($this->_imap, $msg_no);
//        \Io::output($header);

        if (empty($header)) return [];
        //取邮件头
        $item['from']            = $header->from[0]->mailbox . '@' . $header->from[0]->host;
        $item['fromaddress']    = $header->from[0]->personal;
        $item['to']              = $header->to[0]->mailbox;
        $item['subject']        = trim(imap_utf8(iconv_mime_decode($header->subject, 0, 'UTF-8')));
        $item['message_id']     = $header->message_id;
        $item['date']            = $header->udate;
        return $item;
    }

    /**
     * 取邮件正文信息
     * @param $msg_no
     * @return string
     */
    public function body( $msg_no ) {
        if (empty($this->_imap)) return '';
        $imap       = $this->_imap;
        $header     = $this->header( $msg_no );
        $body = trim(imap_fetchbody( $this->_imap, $msg_no , '1.1') );
        if (empty($body)) $body = trim(imap_fetchtext($imap, $msg_no));
        if (substr($body, 0, 1) == '=') $body = quoted_printable_decode($body);
        if ( substr($body, -1) == '=' || $header['from'] == 'tendywang@tencent.com' ) $body = base64_decode($body);
        return $body;
    }

    /**
     * 取邮件附件
     * @param $msg_no
     * @return array
     */
    public function attachments( $msg_no ) {
        if (empty($this->_imap)) return [];
        $structure = imap_fetchstructure( $this->_imap, $msg_no );
        $attachments = array();
        if (isset($structure->parts) && count($structure->parts)) {
            for ($i = 0; $i < count($structure->parts); $i++) {
                $attachments[$i] = array(
                    'is_attachment' => false,
                    'filename' => '',
                    'name' => '',
                    'attachment' => ''
                );
                if ($structure->parts[$i]->ifdparameters) {
                    foreach ($structure->parts[$i]->dparameters as $object) {
                        if (strtolower($object->attribute) == 'filename') {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['filename'] = $object->value;
                        }
                    }
                }
                if ($structure->parts[$i]->ifparameters) {
                    foreach ($structure->parts[$i]->parameters as $object) {
                        if (strtolower($object->attribute) == 'name') {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['name'] = $object->value;
                        }
                    }
                }
                if ($attachments[$i]['is_attachment']) {
                    $attachments[$i]['attachment'] = imap_fetchbody( $this->_imap, $msg_no, $i + 1);
                    if ($structure->parts[$i]->encoding == 3) { // 3 = BASE64
                        $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                    } elseif ($structure->parts[$i]->encoding == 4) { // 4 = QUOTED-PRINTABLE
                        $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                    }
                }
            }
        }
        return $attachments;
    }
}