<?php
//小铃铛 壁纸统计配置
return [
    'udashi_soft_uefi'     => [
        'software'             => 'udashiuefi',
        'db_config_key'       => 'db_udashi_soft_uefi_log',//数据配置config key
        'redis_key_prefix'    => 'logs:udashiuefi:', //redis队列key前缀
        'rc4_key'              => 'u893#888%32)fkt',//rc4密钥

        'action_list' => [
            'install',//安装
            'uninstall',//卸载
            'apprun',//启动
            'manufacture',//制作
            'buttonclick',
            'updaterun',
            'updateresult',
            'updatecontinue',
            'setupinfo',
            'safesoft',
            'checknav',
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
                'QID' => '',  // 安装次数
                'Version' => '', //是否网吧
            ],
            'uninstall' => [//更新安装
                'QID' => '',  // 安装次数
                'Version' => '', //是否网吧
            ],
            'apprun' => [//状态
                'QID' => '',  // 安装次数
                'Version' => '', //是否网吧
            ],
            'manufacture' => [
                'QID' => '',  // 安装次数
                'Version' => '', //	定制工具版本号
                'DeviceId' => '', //U盘硬件ID
                'Size' => 0, //U盘容量	单位kb
                'Vender' => '',//U盘厂商
                'result' => 1,
                'safe'=>'000',
            ],
            'buttonclick' => [
                'QID' => '',  // 安装次数
                'Version' => '', //	定制工具版本号
                'clt' => '', //按钮名称
            ],
            'updaterun' => [
                'QID' => '',
                'Version' => '', //是否网吧
            ],
            'updateresult' => [
                'QID' => '',
                'Version' => '', //是否网吧
                'updateresult'=>''
            ],
            'updatecontinue' => [
                'QID' => '',
                'Version' => '', //是否网吧
                'continue'=>''
            ],
            'setupinfo' => [
                'QID' => '',  // 安装次数
                'Version' => '', //是否网吧
                'flag' => '',
            ],
            'safesoft' => [
                'QID' => '',  // 安装次数
                'Version' => '', //是否网吧
                'flag' => '',
            ],
            'checknav' => [
                'QID' => '',  // 安装次数
                'Version' => '', //是否网吧
                'flag' => '',
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
  `QID` VARCHAR(32) DEFAULT NULL COMMENT '渠道ID',
  `Version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本号',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='安装接口数据';
            ",
            'uninstall' => "
CREATE TABLE `uninstall{[ymd]}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `TimeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `IP` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `UID` CHAR(32) DEFAULT NULL COMMENT 'MD5:CPU_Mac地址_硬盘ID 操作系统唯一标示',
  `ClientTime` INT(10) UNSIGNED DEFAULT NULL COMMENT '客户端本地当前时间',
  `Os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `DiskID` VARCHAR(255) DEFAULT NULL COMMENT 'Diskid列号',
  `MacID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows 产品名称',
  `CpuID` VARCHAR(32) DEFAULT NULL COMMENT '软件标识',
  `QID` VARCHAR(32) DEFAULT NULL COMMENT '渠道ID',
  `Version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本号',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='卸载接口数据';
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
  `QID` VARCHAR(32) DEFAULT NULL COMMENT '渠道ID',
  `Version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本号',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='启动接口数据';
            ",
            'manufacture' => "
CREATE TABLE `manufacture{[ymd]}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `TimeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `IP` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `UID` CHAR(32) DEFAULT NULL COMMENT 'MD5:CPU_Mac地址_硬盘ID 操作系统唯一标示',
  `ClientTime` INT(10) UNSIGNED DEFAULT NULL COMMENT '客户端本地当前时间',
  `Os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `DiskID` VARCHAR(255) DEFAULT NULL COMMENT 'Diskid列号',
  `MacID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows 产品名称',
  `CpuID` VARCHAR(32) DEFAULT NULL COMMENT '软件标识',
  `QID` VARCHAR(32) DEFAULT NULL COMMENT '渠道ID',
  `Version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本号',
  `DeviceId` VARCHAR(255) DEFAULT NULL COMMENT 'U盘硬件ID',
  `Size` VARCHAR(32) DEFAULT NULL COMMENT 'U盘容量	单位kb',
  `Vender` VARCHAR(255) DEFAULT NULL COMMENT 'U盘厂商',
  `result` VARCHAR(255) DEFAULT NULL COMMENT '制作结果（1/0，1代表成功，0代表失败）',
  `safe` VARCHAR(255) DEFAULT NULL COMMENT '安全软件标识（XXX，X = 0/1，1代表存在，0代表不存在 （360Q管金山））',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='制作接口数据';
            ",
            'buttonclick' => "
CREATE TABLE `buttonclick{[ymd]}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `TimeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `IP` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `UID` CHAR(32) DEFAULT NULL COMMENT 'MD5:CPU_Mac地址_硬盘ID 操作系统唯一标示',
  `ClientTime` INT(10) UNSIGNED DEFAULT NULL COMMENT '客户端本地当前时间',
  `Os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `DiskID` VARCHAR(255) DEFAULT NULL COMMENT 'Diskid列号',
  `MacID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows 产品名称',
  `CpuID` VARCHAR(32) DEFAULT NULL COMMENT '软件标识',
  `QID` VARCHAR(32) DEFAULT NULL COMMENT '渠道ID',
  `Version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本号',
  `clt` VARCHAR(255) DEFAULT NULL COMMENT '按钮名称',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='点击接口数据';
            ",
            'updaterun' => "
CREATE TABLE `updaterun{[ymd]}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `TimeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `IP` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `UID` CHAR(32) DEFAULT NULL COMMENT 'MD5:CPU_Mac地址_硬盘ID 操作系统唯一标示',
  `ClientTime` INT(10) UNSIGNED DEFAULT NULL COMMENT '客户端本地当前时间',
  `Os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `DiskID` VARCHAR(255) DEFAULT NULL COMMENT 'Diskid列号',
  `MacID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows 产品名称',
  `CpuID` VARCHAR(32) DEFAULT NULL COMMENT '软件标识',
  `QID` VARCHAR(32) DEFAULT NULL COMMENT '渠道ID',
  `Version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本号',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='更新接口数据';
            ",
            'updateresult' => "
CREATE TABLE `updateresult{[ymd]}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `TimeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `IP` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `UID` CHAR(32) DEFAULT NULL COMMENT 'MD5:CPU_Mac地址_硬盘ID 操作系统唯一标示',
  `ClientTime` INT(10) UNSIGNED DEFAULT NULL COMMENT '客户端本地当前时间',
  `Os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `DiskID` VARCHAR(255) DEFAULT NULL COMMENT 'Diskid列号',
  `MacID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows 产品名称',
  `CpuID` VARCHAR(32) DEFAULT NULL COMMENT '软件标识',
  `QID` VARCHAR(32) DEFAULT NULL COMMENT '渠道ID',
  `Version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本号',
  `updateresult` VARCHAR(255) DEFAULT NULL COMMENT '更新结果(succeed / failed)',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='更新接口数据';
            ",
            'updatecontinue' => "
CREATE TABLE `updatecontinue{[ymd]}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `TimeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `IP` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `UID` CHAR(32) DEFAULT NULL COMMENT 'MD5:CPU_Mac地址_硬盘ID 操作系统唯一标示',
  `ClientTime` INT(10) UNSIGNED DEFAULT NULL COMMENT '客户端本地当前时间',
  `Os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `DiskID` VARCHAR(255) DEFAULT NULL COMMENT 'Diskid列号',
  `MacID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows 产品名称',
  `CpuID` VARCHAR(32) DEFAULT NULL COMMENT '软件标识',
  `QID` VARCHAR(32) DEFAULT NULL COMMENT '渠道ID',
  `Version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本号',
  `continue` VARCHAR(255) DEFAULT NULL COMMENT '是否执行更新（yes / no）',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='更新接口数据';
            ",
            'setupinfo' => "
CREATE TABLE `setupinfo{[ymd]}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `TimeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `IP` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `UID` CHAR(32) DEFAULT NULL COMMENT 'MD5:CPU_Mac地址_硬盘ID 操作系统唯一标示',
  `ClientTime` INT(10) UNSIGNED DEFAULT NULL COMMENT '客户端本地当前时间',
  `Os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `DiskID` VARCHAR(255) DEFAULT NULL COMMENT 'Diskid列号',
  `MacID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows 产品名称',
  `CpuID` VARCHAR(32) DEFAULT NULL COMMENT '软件标识',
  `QID` VARCHAR(32) DEFAULT NULL COMMENT '渠道ID',
  `Version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本号',
  `flag` VARCHAR(255) DEFAULT NULL COMMENT '过程标识（setup.callup / setup.wndinit）',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='安装接口数据';
            ",
            'safesoft' => "
CREATE TABLE `safesoft{[ymd]}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `TimeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `IP` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `UID` CHAR(32) DEFAULT NULL COMMENT 'MD5:CPU_Mac地址_硬盘ID 操作系统唯一标示',
  `ClientTime` INT(10) UNSIGNED DEFAULT NULL COMMENT '客户端本地当前时间',
  `Os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `DiskID` VARCHAR(255) DEFAULT NULL COMMENT 'Diskid列号',
  `MacID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows 产品名称',
  `CpuID` VARCHAR(32) DEFAULT NULL COMMENT '软件标识',
  `QID` VARCHAR(32) DEFAULT NULL COMMENT '渠道ID',
  `Version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本号',
  `flag` VARCHAR(255) DEFAULT NULL COMMENT '安全软件标识（XXX，X = 0/1，1代表存在，0代表不存在 （360Q管金山））',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='安装接口数据';
            ",
            'checknav' => "
CREATE TABLE `checknav{[ymd]}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `TimeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `IP` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `UID` CHAR(32) DEFAULT NULL COMMENT 'MD5:CPU_Mac地址_硬盘ID 操作系统唯一标示',
  `ClientTime` INT(10) UNSIGNED DEFAULT NULL COMMENT '客户端本地当前时间',
  `Os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `DiskID` VARCHAR(255) DEFAULT NULL COMMENT 'Diskid列号',
  `MacID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows 产品名称',
  `CpuID` VARCHAR(32) DEFAULT NULL COMMENT '软件标识',
  `QID` VARCHAR(32) DEFAULT NULL COMMENT '渠道ID',
  `Version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本号',
  `flag` VARCHAR(255) DEFAULT NULL COMMENT '是否勾选【推荐导航】，1：勾选，2：不勾选',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='安装接口数据';
            ",
        ]
    ]
];