<?php
require '../../lib/lib.php';
/**
 * PHPUnit测试基类的再次封装
 */
class Unit_Test_Base extends PHPUnit_Framework_TestCase {
	/**
	 * 检查数据是否是数组，并且是否拥有预期的键值
	 * 主要应用场景为检测数据接口的返回值是否符合预期格式，包含了期望的字段
	 * @param array $data
	 * @param array $assert_keys
	 */
	protected function checkArrayKeys($data, $assert_keys) {
		$this->assertInternalType('array', $data);
		$keys = array_keys($data);
		foreach ($assert_keys as $k) {
			$this->assertContains($k, $keys);
		}
	}
}