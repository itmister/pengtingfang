<?php
//小铃铛 壁纸统计配置
return [
    'mykzip_log'     => [
        'software'             => 'mykzip',
        'db_config_key'       => 'db_mykzip_log',//数据配置config key
        'redis_key_prefix'    => 'logs:mykzip:', //redis队列key前缀
        'rc4_key'              => 'u893#888%32)fkt',//rc4密钥

        'action_list' => [
            'install',//安装
            'uninstall',//卸载
            'updateinstall',//更新安装
            'stat',//状态
            'kunbang',//
            'online',//启动,
            'active',//使用
            'jingpin',//竞品
			'userclick',//用户点击
			'fileoperate',//文件操作
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
                'package_md5' => '',//安装包md5
                'ClientTime' => '',  //客户端当前时间 时间戳
            ],
            'install' => [
                'install_count' => 0,  // 安装次数
                'is_wb' => 0, //是否网吧
                'install_type' => 0,//安装类型    1 静默安装
                'file_name' => '',//
                'cmd' => '' //命令行
            ],
            'updateinstall' => [//更新安装
                'old_version' => '',  // 旧版本
                'update_type' => 0, //更新策略
                'result' => '',//
                'is_wb' => 0, //是否网吧
                'install_type' => 0,//安装类型  界面安装   1 静默安装
            ],
            'stat' => [//状态
                'op_type'=> '',//操作类型
            ],
            'kunbang' => [
                'kunbang_software' => '',
                'is_show' => 0,//是否展示
                'is_checked' => 0, //是否勾选
                'is_downloaded' => 0, //是否下载成功
                'is_installed' => 0,//是否安装成功
                'show_position' => 0,//展示位置
                'kunbang_source' => 0,//捆绑来源
                'is_installed_before' => 0,//安装包执行安装前是否已经安装
                'is_installed_after' => 0,//安装包执行安装后是否安装
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
            'jingpin' => [//竞品
                //{soft}_{install}{service}{auto_start}| {soft2}_{install}{service}{auto_start}
                //例如：QQGJ_000|JSDB_000
                'software' => '',
                'is_install' => 0,
                'is_service' => 0,
                'is_autostart' => 0,
            ],
			'userclick' => [
				'clickdesc' => '',//点击详情
				'clicknum' => 0,//次数
			],//用户点击
			'fileoperate' => [
				'action' => '',//动作
				'filename' => '',//文件名
				'filezize' => '',//文件大小
				'yasuo' => '',//压缩比
			],//文件操作
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
  PRIMARY KEY (`id`),
  KEY `qid` (`UID`,`QID`) USING BTREE,
  KEY `ver` (`UID`,`Version`) USING BTREE
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
  PRIMARY KEY (`id`),
  KEY `qid` (`UID`,`QID`) USING BTREE,
  KEY `ver` (`UID`,`Version`) USING BTREE
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='卸载接口数据';
            ",
            'updateinstall' => "
CREATE TABLE `updateinstall{[ymd]}` (
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
  `old_version` VARCHAR(255) DEFAULT NULL COMMENT '旧版本',
  `update_type` tinyint(4) UNSIGNED DEFAULT NULL COMMENT '0:手动更新 1:提示手动更新 2:自动更新无提示  3:自动更新有提示',
  `result` tinyint(4) UNSIGNED DEFAULT NULL COMMENT '0 执行失败  1执行成功',
  `is_wb` tinyint(4) UNSIGNED DEFAULT NULL COMMENT '是否网吧',
  `install_type` tinyint(4) UNSIGNED DEFAULT NULL COMMENT '安装类型  0界面安装   1 静默安装',
  PRIMARY KEY (`id`),
  KEY `qid` (`UID`,`QID`) USING BTREE,
  KEY `ver` (`UID`,`Version`) USING BTREE
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='更新安装接口数据';
            ",
            'stat' => "
CREATE TABLE `stat{[ymd]}` (
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
  `op_type` VARCHAR(255) DEFAULT NULL COMMENT '操作类型,install_run        install_cancel          install_done',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='状态接口数据';
            ",
            'kunbang' => "
CREATE TABLE `kunbang{[ymd]}` (
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
  `kunbang_software` VARCHAR(255) DEFAULT NULL COMMENT '捆绑软件名称',
  `is_show` tinyint(4) UNSIGNED DEFAULT NULL COMMENT '是否展示',
  `is_checked` tinyint(4) UNSIGNED DEFAULT NULL COMMENT '是否勾选',
  `is_downloaded` tinyint(4) UNSIGNED DEFAULT NULL COMMENT '是否下载成功',
  `is_installed` tinyint(4) UNSIGNED DEFAULT NULL COMMENT '是否下载成功',
  `show_position` tinyint(4) UNSIGNED DEFAULT NULL COMMENT '展示位置',
  `kunbang_source` tinyint(4) UNSIGNED DEFAULT NULL COMMENT '捆绑来源,0  安装捆绑  1 更新捆绑  2 卸载捆绑 3 Tips 4 其他',
  `is_installed_before` tinyint(4) UNSIGNED DEFAULT NULL COMMENT '安装包执行安装前是否已经安装',
  `is_installed_after` tinyint(4) UNSIGNED DEFAULT NULL COMMENT '安装包执行安装后是否安装',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='捆绑安装接口数据';
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
  PRIMARY KEY (`id`),
  KEY `qid` (`UID`,`QID`) USING BTREE,
  KEY `ver` (`UID`,`Version`) USING BTREE
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
            "jingpin" => "
CREATE TABLE `jingpin{[ymd]}` (
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
  `software` VARCHAR(32) DEFAULT NULL COMMENT '竞品软件标识',
  `is_install` tinyint(4) UNSIGNED DEFAULT NULL COMMENT '是否安装',
  `is_service` tinyint(4) UNSIGNED DEFAULT NULL COMMENT '是否服务',
  `is_autostart` tinyint(4) UNSIGNED DEFAULT NULL COMMENT '是否自动启动',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='竞品数据';
            ",
	"userclick" => "
CREATE TABLE `userclick{[ymd]}` (
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
  `clickdesc` VARCHAR(255) DEFAULT NULL COMMENT '点击详情',
  `clicknum` int(11) UNSIGNED DEFAULT NULL COMMENT '次数',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='用户点击';
            ",
	"fileoperate" => "
CREATE TABLE `fileoperate{[ymd]}` (
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
  `action` VARCHAR(50) DEFAULT NULL COMMENT '动作',
  `filename` VARCHAR(1000) DEFAULT NULL COMMENT '文件名',
  `filezize` VARCHAR(1000) DEFAULT NULL COMMENT '文件大小',
  `yasuo` VARCHAR(255) DEFAULT NULL COMMENT '压缩比',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='文件操作';
            ",
        ]
    ]
];