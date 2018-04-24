<?php
namespace VendorData\DB;
/**
 * 小黑记事本
 * Class Promotion_xiaohei
 * @package VendorData\Attachment
 */
class Promotion_xiaohei extends Base{

    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }

    public function get_data($date=''){
        $date  = $date ? $date:date("Y-m-d",strtotime("-1 days"));
        $ymd   = date("Ymd",strtotime($date));

        $sql = "SELECT qid,ymd,install,install_uninstall,kpzip_install,xishu
                FROM gs_user_channel_data
                where qid like '7654_%' and ymd={$ymd} and install>0
                ORDER BY install DESC";
        $kuaizip_data = \Dao\Huayangnianhua_admin\Gs\User_channel_data::get_instance()->query($sql);

        $xiaoheiData = [];
        foreach ($kuaizip_data as $val) {
            $xiaoheiData[$val['qid']] = [
                 'ymd'=>$val['ymd'],
                 'install'=>$val['install'],
                 'install_uninstall'=>$val['install_uninstall'],
                 'kpzip_install'=>$val['kpzip_install'],
                 'xishu'=>$val['xishu']
            ];
        }

        $qidArray = array_keys($xiaoheiData);
        $qidStr = implode("','",$qidArray);
        $qidStr = "'".$qidStr."'";
        $sqlF = "SELECT aa.qid as qid,aa.fcid as fcid,aa.effective_date
                  FROM (
                         SELECT qid,fcid,effective_date
                         from gs_formula_application
                         where effective_date<={$ymd} and qid in ({$qidStr})
                         ORDER BY effective_date DESC
                  ) as aa GROUP BY aa.qid;";
        #每一个子渠道使用的公式
        $formulaData = \Dao\Huayangnianhua_admin\Gs\Formula_application::get_instance()->query($sqlF);
        $gsData = [];
        foreach ($formulaData as $valGs) {
            $gsData[$valGs['qid']] = $valGs['fcid'];
        }
        //公式列表
        $formulaList = [];
        $tmpFormulaList = \Dao\Huayangnianhua_admin\Gs\Formula_config::get_instance()->select();
        foreach ($tmpFormulaList as $key => $_list){
            $formulaList[$_list['id']] = $_list;
        }


        $arr  = [];
        foreach ($xiaoheiData as $qid => $item) {
            $temp = [];
            $formula_config = [];
            if($gsData[$qid] > 0){
                $formula_config = $formulaList[$gsData[$qid]];
            }else{
                $formula_config['uninstall'] = 1;
                $formula_config['repeat_install'] = 1;
                $formula_config['xishu'] = 1;
            }
            $install_uninstall = ($formula_config['uninstall'] == 1) ? 0 : $item['install_uninstall'];
            $kpzip_install     = ($formula_config['repeat_install'] == 1) ? 0 : $item['kpzip_install'];
            $xishu             = ($formula_config['xishu'] == 1) ? 1 : $item['xishu'];

            //有效推广数据(有效推广数据=新安装量-安装且卸载-快压共存率)
            $install_num  = (string)(($item['install'] - $install_uninstall - $kpzip_install) * $xishu);
            $install_num  = ($install_num > 0 && $install_num < 1) ? 1 :  (($install_num < 0) ? 0 : intval($install_num));

            $temp['org_id'] = $qid;
            $temp['count']  = $install_num;
            $arr[] = $temp;
        }
        return $arr;
    }
}