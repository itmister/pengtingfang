<?php
require_once '../unit_test_base.php';
use \Union\Promotion;
class Test_Promotion extends Unit_Test_Base
{
    public function test_get_list()
    {
        $ret  = Promotion::get_instance()->get_list();
        $this->assertNotEmpty($ret);
    }
}