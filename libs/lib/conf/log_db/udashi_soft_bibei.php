<?php

//小铃铛 壁纸统计配置
return [
    'udashi_soft_bibei'     => [
        'software'             => 'udashibibei',
        'db_config_key'       => 'db_udashi_soft_bibei_log',//数据配置config key
        'redis_key_prefix'    => 'logs:udashibibei:', //redis队列key前缀
        'rc4_key'              => 'u893#888%32)fkt',//rc4密钥

        'action_list' => [
            'install',//安装
            'apprun',//启动
            'install_error',
            'download_error',
            'bibeiuninst',
            'noinstall',
        ],

        //记录的参数，客户端约定好顺序不能更改
        'params' => [
            //共公参数
            'common' => [
                'UID' => '', //MD5（根据CPU_Mac地址_硬盘ID）
                'CpuID' => '',  // CPU ID,
                'DiskID' => '',  // 硬盘序列号
                'MacID' => '',  // Mac地址
                'Os' => '',   //操作系统
                'ClientTime' => '',  //客户端当前时间 时间戳
            ],
            'install' => [
                'online' => '',  // 是否在线安装	在线为1 离线为0
                'appid' => '', //软件标志
                'appname' => '', //软件名称
                'appargs' => '', //运行参数
                'appversion' => '', //推广软件版本
                'version' => '',
            ],
            'apprun' => [//状态
                'online' => '',  // 是否在线安装	在线为1 离线为0
                'version' => '',
            ],
            'install_error' => [//1.3.3 装机必备软件安装失败
                'online' => '',  // 是否在线安装	在线为1 离线为0
                'appid' =>'', //软件标志
                'appversion' =>'推广软件版本',
                'version' => '',
                'errorcode' => '0', //0：未知，1：进程启动失败，2：用户取消，3：进程退出异常，4：注册表项检查失败
            ],
            'download_error' => [//1.3.4装机必备软件下载失败
                'online' => '',  // 是否在线安装	在线为1 离线为0
                'appid' =>'', //软件标志
                'appversion' =>'推广软件版本',
                'version' => '',
                'errorcode' => '0', //0：未知，1：成功，2：写入异常，3：返回码异常（不是200），4：文件不匹配（Md5）,5:用户取消
                'flag' => '000',//安全软件标识（XXX，X = 0/1，1代表存在，0代表不存在 （360Q管金山））
            ],
            'bibeiuninst' => [//卸载
                'version' => '',
            ],
            'noinstall' => [//直接关闭
                'version' => '',
                'online' => '',  // 是否在线安装	在线为1 离线为0
            ],
        ],

        'log_table_sql' => [//日志表建表sql
            'install' => "
CREATE TABLE `install{[ymd]}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `TimeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `IP` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `UID` CHAR(32) DEFAULT NULL COMMENT 'MD5:CPU_Mac地址_硬盘ID 操作系统唯一标示',
  `ClientTime` INT(10) UNSIGNED DEFAULT NULL COMMENT '客户端本地当前时间',
  `Os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `DiskID` VARCHAR(255) DEFAULT NULL COMMENT 'Diskid列号',
  `MacID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows 产品名称',
  `CpuID` VARCHAR(32) DEFAULT NULL COMMENT '软件标识',
  `online` VARCHAR(32) DEFAULT NULL COMMENT '是否在线安装	在线为1 离线为0',
  `appid` VARCHAR(255) DEFAULT NULL COMMENT '软件标志',
  `appname` VARCHAR(255) DEFAULT NULL COMMENT '软件名称',
  `appargs` VARCHAR(255) DEFAULT NULL COMMENT '运行参数',   
  `appversion` VARCHAR(255) DEFAULT NULL COMMENT '推广软件版本',
  `version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='安装接口数据';
            ",
            'apprun' => "
CREATE TABLE `apprun{[ymd]}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `TimeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `IP` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `UID` CHAR(32) DEFAULT NULL COMMENT 'MD5:CPU_Mac地址_硬盘ID 操作系统唯一标示',
  `ClientTime` INT(10) UNSIGNED DEFAULT NULL COMMENT '客户端本地当前时间',
  `Os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `DiskID` VARCHAR(255) DEFAULT NULL COMMENT 'Diskid列号',
  `MacID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows 产品名称',
  `CpuID` VARCHAR(32) DEFAULT NULL COMMENT '软件标识',
  `online` VARCHAR(32) DEFAULT NULL COMMENT '是否在线安装 在线为1 离线为0',
  `version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='启动接口数据';
            ",
            'install_error' => "
CREATE TABLE `install_error{[ymd]}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `TimeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `IP` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `UID` CHAR(32) DEFAULT NULL COMMENT 'MD5:CPU_Mac地址_硬盘ID 操作系统唯一标示',
  `ClientTime` INT(10) UNSIGNED DEFAULT NULL COMMENT '客户端本地当前时间',
  `Os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `DiskID` VARCHAR(255) DEFAULT NULL COMMENT 'Diskid列号',
  `MacID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows 产品名称',
  `CpuID` VARCHAR(32) DEFAULT NULL COMMENT '软件标识',
  `online` VARCHAR(32) DEFAULT NULL COMMENT '是否在线安装 在线为1 离线为0',
  `appid` VARCHAR(255) DEFAULT NULL COMMENT '软件标志',
  `appversion` VARCHAR(255) DEFAULT NULL COMMENT '推广软件版本',
  `version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本',
  `errorcode` INT(10) DEFAULT '0' COMMENT '0：未知，1：进程启动失败，2：用户取消，3：进程退出异常，4：注册表项检查失败',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='启动接口数据';
            ",
            'download_error' => "
CREATE TABLE `download_error{[ymd]}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `TimeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `IP` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `UID` CHAR(32) DEFAULT NULL COMMENT 'MD5:CPU_Mac地址_硬盘ID 操作系统唯一标示',
  `ClientTime` INT(10) UNSIGNED DEFAULT NULL COMMENT '客户端本地当前时间',
  `Os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `DiskID` VARCHAR(255) DEFAULT NULL COMMENT 'Diskid列号',
  `MacID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows 产品名称',
  `CpuID` VARCHAR(32) DEFAULT NULL COMMENT '软件标识',
  `online` VARCHAR(32) DEFAULT NULL COMMENT '是否在线安装 在线为1 离线为0',
  `appid` VARCHAR(255) DEFAULT NULL COMMENT '软件标志',
  `appversion` VARCHAR(255) DEFAULT NULL COMMENT '推广软件版本',
  `version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本',
  `errorcode` INT(10) DEFAULT '0' COMMENT '0：未知，1：成功，2：写入异常，3：返回码异常（不是200），4：文件不匹配（Md5）,5:用户取消',
  `flag` VARCHAR(255) DEFAULT NULL COMMENT '安全软件标识（XXX，X = 0/1，1代表存在，0代表不存在 （360Q管金山））',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='启动接口数据';
            ",
            'bibeiuninst' => "
CREATE TABLE `bibeiuninst{[ymd]}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `TimeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `IP` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `UID` CHAR(32) DEFAULT NULL COMMENT 'MD5:CPU_Mac地址_硬盘ID 操作系统唯一标示',
  `ClientTime` INT(10) UNSIGNED DEFAULT NULL COMMENT '客户端本地当前时间',
  `Os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `DiskID` VARCHAR(255) DEFAULT NULL COMMENT 'Diskid列号',
  `MacID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows 产品名称',
  `CpuID` VARCHAR(32) DEFAULT NULL COMMENT '软件标识',
  `version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='启动接口数据';
            ",
            'noinstall' => "
CREATE TABLE `noinstall{[ymd]}` (
 `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `TimeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `IP` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `UID` CHAR(32) DEFAULT NULL COMMENT 'MD5:CPU_Mac地址_硬盘ID 操作系统唯一标示',
  `ClientTime` INT(10) UNSIGNED DEFAULT NULL COMMENT '客户端本地当前时间',
  `Os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `DiskID` VARCHAR(255) DEFAULT NULL COMMENT 'Diskid列号',
  `MacID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows 产品名称',
  `CpuID` VARCHAR(32) DEFAULT NULL COMMENT '软件标识',
  `online` VARCHAR(32) DEFAULT NULL COMMENT '是否在线安装 在线为1 离线为0',
  `version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='启动接口数据';
            ",
        ]
    ]
];