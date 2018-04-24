<?php
//小铃铛 壁纸统计配置
return [
    'uxiake_log'     => [
        'software'             => 'uxiake',
        'db_config_key'       => 'db_uxiake_log',//数据配置config key
        'redis_key_prefix'    => 'logs:uxiake:', //redis队列key前缀
        'rc4_key'              => 'u893#888%32)fkt',//rc4密钥

        'action_list' => [
            'systemghost',//安装系统
            'pcpackageinstall',//pc环境安装软件
            'pepackageinstall',//pe坏境安装软件
            'systemghost2',//还原系统
            'udiskmake',//制作U盘
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
                'SoftName' => '',//
                'SoftID' => '',//
                'Version' => '', //版本号
                'ClientTime' => '',  //客户端当前时间 时间戳
            ],
            'systemghost' => [
                'UUID' => '',  // 应用程序生命周期标识
                'tuuid' => 0, //技术员标识
            ],
            'pcpackageinstall' => [//更新安装
                'UUID' => '',  // 应用程序生命周期标识
                'tuuid' => 0, //技术员标识
                'package_id' =>	'',//软件包id
                'populariz_channel' =>	'',//软件推广渠道号
                'populariz_name' => '',//软件推广包名
                'populariz_args' => '',//软件推广运行参数
                'exitcode' => '',   //软件包退出码 正常情况下为0
                'successed' => '',	//是否安装成功	成功为true，失败为false
            ],
            'pepackageinstall' => [//状态
                'UUID' => '',  // 应用程序生命周期标识
                'tuuid' => 0, //技术员标识
                'package_id' =>	'',//软件包id
                'populariz_channel' =>	'',//软件推广渠道号
                'populariz_name' => '',//软件推广包名
                'populariz_args' => '',//软件推广运行参数
                'exitcode' => '',   //软件包退出码 正常情况下为0
                'successed' => '',	//是否安装成功	成功为true，失败为false
            ],
			'systemghost2' => [
                'UUID' => '',  // 应用程序生命周期标识
            ],
			'udiskmake' => [
                'UUID' => '',				//应用程序生命周期标识
				'program_version'=>'',		//制作工具版本号
				'vendor_id' => '',			//U盘制造商
				'size' => '',				//U盘大小
				'vid'  => '',				//生产厂商ID
				'pid'  => '',				//产品ID
				'searial_number' => '',		//U盘序列号
            ],
        ],

        'log_table_sql' => [//日志表建表sql
            'systemghost' => "
CREATE TABLE `systemghost{[ymd]}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `TimeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `UID` CHAR(32) DEFAULT NULL COMMENT 'MD5:CPU_Mac地址_硬盘ID 操作系统唯一标示',
  `DiskID` VARCHAR(255) DEFAULT NULL COMMENT 'Diskid列号',
  `CpuID` VARCHAR(32) DEFAULT NULL COMMENT '软件标识',
  `MacID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows 产品名称',
  `Os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `SoftName` VARCHAR(255) DEFAULT NULL COMMENT '软件名称',
  `SoftID` VARCHAR(255) DEFAULT NULL COMMENT '软件id',
  `Version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本号',
  `IP` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `ClientTime` INT(10) UNSIGNED DEFAULT NULL COMMENT '客户端本地当前时间',
  `UUID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows clinet生成的唯一健',
  `tuuid` INT(10) UNSIGNED DEFAULT NULL COMMENT '技术员标识',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='安装系统接口数据';
            ",
            'pcpackageinstall' => "
CREATE TABLE `pcpackageinstall{[ymd]}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `TimeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `SoftName` VARCHAR(255) DEFAULT NULL COMMENT '软件名称',
  `SoftID` VARCHAR(255) DEFAULT NULL COMMENT '软件id',
  `Version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本号',
  `IP` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `UID` CHAR(32) DEFAULT NULL COMMENT 'MD5:CPU_Mac地址_硬盘ID 操作系统唯一标示',
  `ClientTime` INT(10) UNSIGNED DEFAULT NULL COMMENT '客户端本地当前时间',
  `Os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `DiskID` VARCHAR(255) DEFAULT NULL COMMENT 'Diskid列号',
  `MacID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows 产品名称',
  `CpuID` VARCHAR(32) DEFAULT NULL COMMENT '软件标识',
  `UUID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows clinet生成的唯一健',
  `tuuid` INT(10) UNSIGNED DEFAULT NULL COMMENT '技术员标识',
  `package_id` VARCHAR(255) DEFAULT NULL COMMENT '软件包id',
  `populariz_channel` VARCHAR(255) DEFAULT NULL COMMENT '软件推广渠道号',
  `populariz_name` VARCHAR(255) DEFAULT NULL COMMENT '软件推广包名',
  `populariz_args` VARCHAR(255) DEFAULT NULL COMMENT '软件推广运行参数',
  `exitcode` VARCHAR(255) DEFAULT NULL COMMENT '软件包退出码,正常情况下为0',
  `successed` VARCHAR(255) DEFAULT NULL COMMENT '是否安装成功,成功为true,失败为false',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='pc环境安装软件接口数据';
            ",
            'pepackageinstall' => "
CREATE TABLE `pepackageinstall{[ymd]}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `TimeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `SoftName` VARCHAR(255) DEFAULT NULL COMMENT '软件名称',
  `SoftID` VARCHAR(255) DEFAULT NULL COMMENT '软件id',
  `Version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本号',
  `IP` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `UID` CHAR(32) DEFAULT NULL COMMENT 'MD5:CPU_Mac地址_硬盘ID 操作系统唯一标示',
  `ClientTime` INT(10) UNSIGNED DEFAULT NULL COMMENT '客户端本地当前时间',
  `Os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `DiskID` VARCHAR(255) DEFAULT NULL COMMENT 'Diskid列号',
  `MacID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows 产品名称',
  `CpuID` VARCHAR(32) DEFAULT NULL COMMENT '软件标识',
  `UUID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows clinet生成的唯一健',
  `tuuid` INT(10) UNSIGNED DEFAULT NULL COMMENT '技术员标识',
  `package_id` VARCHAR(255) DEFAULT NULL COMMENT '软件包id',
  `populariz_channel` VARCHAR(255) DEFAULT NULL COMMENT '软件推广渠道号',
  `populariz_name` VARCHAR(255) DEFAULT NULL COMMENT '软件推广包名',
  `populariz_args` VARCHAR(255) DEFAULT NULL COMMENT '软件推广运行参数',
  `exitcode` VARCHAR(255) DEFAULT NULL COMMENT '软件包退出码,正常情况下为0',
  `successed` VARCHAR(255) DEFAULT NULL COMMENT '是否安装成功,成功为true,失败为false',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='pe环境安装软件接口数据';
            ",
			            'systemghost2' => "
CREATE TABLE `systemghost2{[ymd]}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `TimeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `SoftName` VARCHAR(255) DEFAULT NULL COMMENT '软件名称',
  `SoftID` VARCHAR(255) DEFAULT NULL COMMENT '软件id',
  `Version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本号',
  `IP` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `UID` CHAR(32) DEFAULT NULL COMMENT 'MD5:CPU_Mac地址_硬盘ID 操作系统唯一标示',
  `ClientTime` INT(10) UNSIGNED DEFAULT NULL COMMENT '客户端本地当前时间',
  `Os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `DiskID` VARCHAR(255) DEFAULT NULL COMMENT 'Diskid列号',
  `MacID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows 产品名称',
  `CpuID` VARCHAR(32) DEFAULT NULL COMMENT '软件标识',
  `UUID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows clinet生成的唯一健',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='操作系统还原';
            ",
						            'udiskmake' => "
CREATE TABLE `udiskmake{[ymd]}` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `TimeStamp` INT(10) UNSIGNED DEFAULT NULL COMMENT '服务器记录时间',
  `SoftName` VARCHAR(255) DEFAULT NULL COMMENT '软件名称',
  `SoftID` VARCHAR(255) DEFAULT NULL COMMENT '软件id',
  `Version` VARCHAR(255) DEFAULT NULL COMMENT '软件版本号',
  `IP` VARCHAR(32) DEFAULT NULL COMMENT 'ip地址',
  `UID` CHAR(32) DEFAULT NULL COMMENT 'MD5:CPU_Mac地址_硬盘ID 操作系统唯一标示',
  `ClientTime` INT(10) UNSIGNED DEFAULT NULL COMMENT '客户端本地当前时间',
  `Os` VARCHAR(255) DEFAULT NULL COMMENT '操作系统',
  `DiskID` VARCHAR(255) DEFAULT NULL COMMENT 'Diskid列号',
  `MacID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows 产品名称',
  `CpuID` VARCHAR(32) DEFAULT NULL COMMENT '软件标识',
  `UUID` VARCHAR(255) DEFAULT NULL COMMENT 'Windows clinet生成的唯一健',
  `program_version` VARCHAR(255) DEFAULT NULL COMMENT '制作工具版本号',
  `vendor_id` VARCHAR(255) DEFAULT NULL COMMENT 'U盘制造商',
  `size` VARCHAR(255) DEFAULT NULL COMMENT 'U盘大小',
  `vid` VARCHAR(255) DEFAULT NULL COMMENT '生产厂商ID',
  `pid` VARCHAR(255) DEFAULT NULL COMMENT '产品ID',
  `searial_number` VARCHAR(255) DEFAULT NULL COMMENT 'U盘序列号',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='U盘制作';
            ",
        ]
    ]
];
