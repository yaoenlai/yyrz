<?php
namespace app\index\controller;

use think\Db;

class Index
{
    
    public function __construct(){
        if(!isset($_SERVER['REQUEST_METHOD']) || strtoupper($_SERVER['REQUEST_METHOD'])!='POST'){
            rjson('不是post提交', '1', 'error');
        }
    }
    
    public function index()
    {
        $list = Db::table("YD_KC21")->paginate(10);
        dump($list);
    }
    
    //医院科室列表
    public function departmentList(){
        
        $data = Db::table("YD_KF52")->select();
        rjson($data);
    }
    //住院、出院 查询
    public function hospitalizationList(){
        
        $data = input("post.");
        $type = $data['type'];      //0：全部查询；1：住院查询；2：出院查询
        
        $page_index = empty($data['page_index']) ? "1" : $data['page_index']; 
        $page_size = empty($data['page_size']) ? "10" : $data['page_size']; 
        
        $where = [];
        //患者姓名
        if (!empty($data['AAC003']))
        {
            $where['AAC003'] = array("LIKE", "%".$data['name']."%");
        }
        //患者科室
        if (!empty($data['AKF001']))
        {
            $where['AKF001'] = array('EQ', $data['department']);
        }
        //患者床位
        if (!empty($data['AKE020']))
        {
            $where['AKE020'] = array("EQ", $data['bedNumber']);
        }
        //患者身份证
        if (!empty($data['AAC002']))
        {
            $where['AAC002'] = array("EQ", $data['IDcode']);
        }
        //入院时间
        if (!empty($data['BKC192']))
        {
            $where['BKC192'] = array("EGT", $data['createTime']);
        }
        //出院时间
        if (!empty($data['BKC194']))
        {
           $where['BKC194'] = array("ELT", $data['outTime']);
        }
        
        $list = Db::table("YD_KF51")->field("AAC003,AKF001,AKE020,AKC273")->where($where)->page($page_index, $page_size)->select();

        rjson($list);
    }
    
    /**
     * 获取详情
     *  患者姓名、患者科室、患者床位、主治医师
     *  入院时间、入院诊断时间、上次认证时间
     *  入院诊断
     *  出院诊断
     *  医嘱内容
     * */
    public function hospitalizationDetail(){
        
        $data = input('post.');
        if(empty($data['AKC190'])) rjson('参数不能为空', '400', 'error');
    
        $where = array(
            'AKC190'=>array('EQ', $data['AKC190']),
        );
        $info = Db::table("YD_DETAIL")->where($where)->select();
        rjson($info);
    }
    
    //获取通知消息
    public function noticeList(){
                
        $data = input("post.");
        $page_index = empty($data['page_index']) ? "1" : $data['page_index'];
        $page_size = empty($data['page_size']) ? "10" : $data['page_size']; 
        
        $where = [];
        
        $where['AMS103'] = array("EQ", '1');    //状态1发布2停止
        
        $list = Db::table("YD_MS101")->where($where)->page($page_index, $page_size)->order("AMS102 desc")->select();
        rjson($list);
    }
    //获取天气  https://www.sojson.com/api/weather.html
    public function weatherList(){
        
        $url = "https://www.sojson.com/open/api/weather/json.shtml?city=张家口";
        echo file_get_contents($url);die;
    }
}