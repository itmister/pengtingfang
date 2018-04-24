<?php
//QQ管家统计配置
return [
    'qqpcmgr_log'     => [
        'software'             => 'qqpcmgr',
        'db_config_key'       => 'db_qqpcmgr_log',//数据配置config key
        'redis_key_prefix'    => 'logs:qqpcmgr:', //redis队列key前缀
        'rc4_key'              => 'u893#888%32)fkt',//rc4密钥

        'action_list' => [
            'install',//安装
            'uninstall',//卸载
            'online',//启动,
            'active',//使用
            'desktoplnklist',//桌面快捷方式
            'processlist',//进程列表
        ],

        //记录的参数，客户端约定好顺序不能更改
        'params' => [
            //共公参数
            'common' => [
                'QID' => '',  // 子渠道号ID
                'UID' => '', //MD5（根据CPU_Mac地址_硬盘ID）
                'SoftName' => '',//
                'SoftID' => '',//
                'Version' => '', //版本号
                'DiskID' => '',  // 硬盘序列号
                'CpuID' => '',  // CPU ID,
                'MacID' => '',  // Mac地址
                'Os' => '',   //操作系统
                'ClientTime' => '',  //客户端当前时间 时间戳
                'package_md5' => '',//安装包md5
            ],
            'install' => [
                'install_count' => 0,  // 安装次数
                'is_wb' => 0, //是否网吧
                'install_type' => 0,//安装类型    1 静默安装
                'file_name' => '',//
                'cmd' => '' //命令行
            ],
            'uninstall' => [
                'contact_type' => '',  // 联系方式
                'reason' => '',  //自定义原因
                'reason_1' => 0, //原因1是否选中
                'reason_2' => 0, //原因1是否选中
                'reason_3' => 0, //原因1是否选中
                'reason_4' => 0, //原因1是否选中
                'reason_5' => 0, //原因1是否选中
                'reason_6' => 0, //原因1是否选中
            ],
            'online' => [//启动
            ],
            'active' => [//使用
            ],
            'desktoplnklist' => [//桌面快捷方式
                //{desktoplnklist}|{desktoplnklist}
                //例如：
                'lnklist' => '',
            ],
            'processlist' => [//进程列表
                //{processlist}|{processlist}
                //例如：
                'processlist' => '',
            ],
        ],

        'log_table_sql' => [//日志表建表sql
            'install' => "
CREATE TABLE `install{[ymd]}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `TimeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `SoftName` VARCHAR(255) DEFAULT NULL COMMENT '软件名称',
  `SoftID` VARCHAR(255) DEFAULT NULL COMMENT '软件id',
  `Version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本号',
  `IP` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `UID` CHAR(32) DEFAULT NULL COMMENT 'MD5:CPU_Mac地址_硬盘ID 操作系统唯一标示',
  `ClientTime` INT(10) UNSIGNED DEFAULT NULL COMMENT '客户端本地当前时间',
  `QID` VARCHAR(32) DEFAULT NULL COMMENT '渠道ID',
  `InstallYmd` INT(10) UNSIGNED DEFAULT NULL COMMENT '安装时间，从qid截取得到',
  `Os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `DiskID` VARCHAR(255) DEFAULT NULL COMMENT 'Diskid列号',
  `MacID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows 产品名称',
  `CpuID` VARCHAR(32) DEFAULT NULL COMMENT '软件标识',
  `UUID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows clinet生成的唯一健',
  `package_md5` VARCHAR(32) DEFAULT NULL COMMENT '安装包Md5',
  `install_count` INT(10) UNSIGNED DEFAULT NULL COMMENT '安装时间，从qid截取得到',
  `is_wb` tinyint(4) UNSIGNED DEFAULT NULL COMMENT '安装时间，从qid截取得到',
  `install_type` tinyint(4) UNSIGNED DEFAULT NULL COMMENT '安装时间，从qid截取得到',
  `file_name` VARCHAR(255) DEFAULT NULL COMMENT 'Windows clinet生成的唯一健',
  `cmd` VARCHAR(255) DEFAULT NULL COMMENT 'Windows clinet生成的唯一健',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='安装接口数据';
            ",
            'uninstall' => "
CREATE TABLE `uninstall{[ymd]}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `TimeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `SoftName` VARCHAR(255) DEFAULT NULL COMMENT '软件名称',
  `SoftID` VARCHAR(255) DEFAULT NULL COMMENT '软件id',
  `Version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本号',
  `IP` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `UID` CHAR(32) DEFAULT NULL COMMENT 'MD5:CPU_Mac地址_硬盘ID 操作系统唯一标示',
  `ClientTime` INT(10) UNSIGNED DEFAULT NULL COMMENT '客户端本地当前时间',
  `QID` VARCHAR(32) DEFAULT NULL COMMENT '渠道ID',
  `InstallYmd` INT(10) UNSIGNED DEFAULT NULL COMMENT '安装时间，从qid截取得到',
  `Os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `DiskID` VARCHAR(255) DEFAULT NULL COMMENT 'Diskid列号',
  `MacID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows 产品名称',
  `CpuID` VARCHAR(32) DEFAULT NULL COMMENT '软件标识',
  `UUID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows clinet生成的唯一健',
  `package_md5` VARCHAR(32) DEFAULT NULL COMMENT '安装包Md5',
  `contact_type` VARCHAR(255) DEFAULT NULL COMMENT '联系方式',
  `reason` VARCHAR(255) DEFAULT NULL COMMENT '联系方式',
  `reason_1` tinyint(4) UNSIGNED DEFAULT NULL COMMENT '原因1是否选中',
  `reason_2` tinyint(4) UNSIGNED DEFAULT NULL COMMENT '原因2是否选中',
  `reason_3` tinyint(4) UNSIGNED DEFAULT NULL COMMENT '原因3是否选中',
  `reason_4` tinyint(4) UNSIGNED DEFAULT NULL COMMENT '原因4是否选中',
  `reason_5` tinyint(4) UNSIGNED DEFAULT NULL COMMENT '原因5是否选中',
  `reason_6` tinyint(4) UNSIGNED DEFAULT NULL COMMENT '原因6是否选中',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='卸载接口数据';
            ",
            'online' => "
CREATE TABLE `online{[ymd]}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `TimeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `SoftName` VARCHAR(255) DEFAULT NULL COMMENT '软件名称',
  `SoftID` VARCHAR(255) DEFAULT NULL COMMENT '软件id',
  `Version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本号',
  `IP` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `UID` CHAR(32) DEFAULT NULL COMMENT 'MD5:CPU_Mac地址_硬盘ID 操作系统唯一标示',
  `ClientTime` INT(10) UNSIGNED DEFAULT NULL COMMENT '客户端本地当前时间',
  `QID` VARCHAR(32) DEFAULT NULL COMMENT '渠道ID',
  `InstallYmd` INT(10) UNSIGNED DEFAULT NULL COMMENT '安装时间，从qid截取得到',
  `Os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `DiskID` VARCHAR(255) DEFAULT NULL COMMENT 'Diskid列号',
  `MacID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows 产品名称',
  `CpuID` VARCHAR(32) DEFAULT NULL COMMENT '软件标识',
  `UUID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows clinet生成的唯一健',
  `package_md5` VARCHAR(32) DEFAULT NULL COMMENT '安装包Md5',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='启动数据';
            ",

            'active' => "
CREATE TABLE `active{[ymd]}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `TimeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `SoftName` VARCHAR(255) DEFAULT NULL COMMENT '软件名称',
  `SoftID` VARCHAR(255) DEFAULT NULL COMMENT '软件id',
  `Version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本号',
  `IP` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `UID` CHAR(32) DEFAULT NULL COMMENT 'MD5:CPU_Mac地址_硬盘ID 操作系统唯一标示',
  `ClientTime` INT(10) UNSIGNED DEFAULT NULL COMMENT '客户端本地当前时间',
  `QID` VARCHAR(32) DEFAULT NULL COMMENT '渠道ID',
  `InstallYmd` INT(10) UNSIGNED DEFAULT NULL COMMENT '安装时间，从qid截取得到',
  `Os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `DiskID` VARCHAR(255) DEFAULT NULL COMMENT 'Diskid列号',
  `MacID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows 产品名称',
  `CpuID` VARCHAR(32) DEFAULT NULL COMMENT '软件标识',
  `UUID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows clinet生成的唯一健',
  `package_md5` VARCHAR(32) DEFAULT NULL COMMENT '安装包Md5',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='使用数据';
            ",
            "processlist" => "
CREATE TABLE `processlist{[ymd]}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `TimeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `SoftName` VARCHAR(255) DEFAULT NULL COMMENT '软件名称',
  `SoftID` VARCHAR(255) DEFAULT NULL COMMENT '软件id',
  `Version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本号',
  `IP` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `UID` CHAR(32) DEFAULT NULL COMMENT 'MD5:CPU_Mac地址_硬盘ID 操作系统唯一标示',
  `ClientTime` INT(10) UNSIGNED DEFAULT NULL COMMENT '客户端本地当前时间',
  `QID` VARCHAR(32) DEFAULT NULL COMMENT '渠道ID',
  `InstallYmd` INT(10) UNSIGNED DEFAULT NULL COMMENT '安装时间，从qid截取得到',
  `Os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `DiskID` VARCHAR(255) DEFAULT NULL COMMENT 'Diskid列号',
  `MacID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows 产品名称',
  `CpuID` VARCHAR(32) DEFAULT NULL COMMENT '软件标识',
  `UUID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows clinet生成的唯一健',
  `package_md5` VARCHAR(32) DEFAULT NULL COMMENT '安装包Md5',
  `processlist` text  DEFAULT NULL COMMENT '进程改变列表',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='进程数据';
            ",
            "desktoplnklist" => "
CREATE TABLE `desktoplnklist{[ymd]}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `TimeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `SoftName` VARCHAR(255) DEFAULT NULL COMMENT '软件名称',
  `SoftID` VARCHAR(255) DEFAULT NULL COMMENT '软件id',
  `Version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本号',
  `IP` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `UID` CHAR(32) DEFAULT NULL COMMENT 'MD5:CPU_Mac地址_硬盘ID 操作系统唯一标示',
  `ClientTime` INT(10) UNSIGNED DEFAULT NULL COMMENT '客户端本地当前时间',
  `QID` VARCHAR(32) DEFAULT NULL COMMENT '渠道ID',
  `InstallYmd` INT(10) UNSIGNED DEFAULT NULL COMMENT '安装时间，从qid截取得到',
  `Os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `DiskID` VARCHAR(255) DEFAULT NULL COMMENT 'Diskid列号',
  `MacID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows 产品名称',
  `CpuID` VARCHAR(32) DEFAULT NULL COMMENT '软件标识',
  `UUID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows clinet生成的唯一健',
  `package_md5` VARCHAR(32) DEFAULT NULL COMMENT '安装包Md5',
  `lnklist` text  DEFAULT NULL COMMENT '桌面快捷方式列表',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='桌面快捷方式数据';
            "
        ]
    ]
];
