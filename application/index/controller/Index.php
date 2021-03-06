<?php
namespace app\index\controller;

use think\Db;

class Index
{
     
    public function __construct(){
        if(!isset($_SERVER['REQUEST_METHOD']) || strtoupper($_SERVER['REQUEST_METHOD'])!='POST'){
            rjson('不是post提交', '400', 'error');
        }
        if(empty(input("post.AKF100"))){
            rjson("请先登录", '400', 'error');
        }
        //添加验证登录凭证是否正确
    }
    
    public function index()
    {
        $list = Db::table("YD_KC21")->paginate(10);
        dump($list);
    }
    
    //医院科室列表
    public function departmentList(){
        
        $data = input('post.');
        $where = [];
        //医院编码
        if(!empty($data['AKB020'])){
            $where['AKB020'] = $data['AKB020'];
        }
        $data = Db::table("YD_KF52")->where($where)->select();
        rjson($data);
    }
    //住院、出院 查询
    public function hospitalizationList(){
        
        $data = input("post.");
        $type = $data['type'];      //0：全部查询；1：住院查询；2：出院查询
        
        $page_index = empty($data['page_index']) ? "1" : $data['page_index']; 
        $page_size = empty($data['page_size']) ? "10" : $data['page_size']; 
        
        $where = [];
        
        switch ($type){
            case '1': 
                $where['YD_KF51.BKC317'] = array('EQ', '0');
                break;
            case '2':
                $where['YD_KF51.BKC317'] = array('EQ', '1');
                break;
            default:
                break;
        }
        
        //患者姓名
        if (!empty($data['AAC003']))
        {
            $where['YD_KF51.AAC003'] = array("LIKE", "%".$data['AAC003']."%");
        }
        //患者科室号
        if (!empty($data['AKF001']))
        {
            $where['YD_KF51.AKF001'] = array('EQ', $data['AKF001']);
        }
        //患者床位号
        if (!empty($data['AKE020']))
        {
            $where['YD_KF51.AKE020'] = array("EQ", $data['AKE020']);
        }
        //患者身份证
        if (!empty($data['AAC002']))
        {
            $where['YD_KF51.AAC002'] = array("EQ", $data['AAC002']);
        }
        //入院时间
        if (!empty($data['BKC192']))
        {
            $where['YD_KF51.BKC192'] = array("EGT", $data['BKC192']);
        }
        //出院时间
        if (!empty($data['BKC194']))
        {
           $where['YD_KF51.BKC194'] = array("ELT", $data['BKC194']);
        }
		//科室名称（废弃）
		if (!empty($data['AKF002']))
		{
			$where['YD_KF52.AKF002'] = array("LIKE", "%".$data['AKF002']."%");
		}
        //医院编码
        if(!empty($data['AKB020']))
        {
            $where['YD_KF51.AKB020'] = array("EQ", $data['AKB020']);
        }
		
        $list = Db::table("YD_KF51")
            ->field(" YD_KF51.AKC190, YD_KF51.AAC002, YD_KF51.AAC003, YD_KF51.AKF001, YD_KF51.AKE020, YD_KF51.AKC273, YD_KF52.AKF002, YD_KF51.AKB020, YD_KF51.BKC317")
			->join("YD_KF52", "YD_KF52.AKF001=YD_KF51.AKF001", "LEFT")
			->where($where)->page($page_index, $page_size)->select();
        rjson($list);
    }
    
    /**
     * 获取图片
     *  @param AAC999 个人管理编码
     * */
    public function getImage(){
        
        $data = input('post.');
        if(empty($data['AAC999'])) rjson("参数不能为空", "400", "error");
        
        $where = array(
            'AAC999'    => $data['AAC999']
        );
        $info = Db::table("YD_KF55")->where($where)->find();
        if(empty($info)){
            rjson("该身份证没有图片", "202", "error");
        }
        $obj = stream_get_contents($info['AKF055']);
        
        $path = "./image/".$data['AAC999'].".jpg";

        if(file_put_contents($path, $obj)){
            rjson(array('url'=>$path));
        } else {
            rjson("失败", "400", "error");
        }

    }
    
    /** 
     * 认证
     * 
     * */
    public function authentication(){
        
        $data = input("post.");
        
        if(empty($data['AKC190'])) rjson("流水号为空", "400", "error");
            
        $list = array(
            "AKC190"    => $data['AKC190'],  
//             'AKF120'    => 2,
            'AAE001'    => date("Ymd"),
            'AAE030'    => date("YmdHis"),
        );
        
        if(!empty($data['AAC001'])) $list['AAC001'] = $data['AAC001'];
        if(!empty($data['AKF050'])) $list['AKF050'] = $data['AKF050'];
        if(!empty($data['AKF051'])) $list['AKF051'] = $data['AKF051'];
        if(!empty($data['AKF052'])) $list['AKF052'] = $data['AKF052'];
        if(!empty($data['AKF053'])) $list['AKF053'] = $data['AKF053'];
        if(!empty($data['AAA027'])) $list['AAA027'] = $data['AAA027'];
        if(!empty($data['AKF119'])) $list['AKF119'] = $data['AKF119'];
        if(!empty($data['AKF110'])) $list['AKF110'] = $data['AKF110'];
        if(!empty($data['AKF120'])) $list['AKF120'] = $data['AKF120'];
        
        if(empty($data['AKB020']))
        {
            rjson("医院编码不能为空", "400", "error");
        }
        else 
        {
            $list['AKB020'] = $data['AKB020'];
        }
        if(!empty($data['AKF056'])) $list['AKF056'] = $data['AKF056'];
        
        //判断验证
        $where = array(
            'AKC190'    =>array('EQ', $data['AKC190'])
            ,'AAE001'   =>date("Ymd")
        );
        $info = Db::table("YD_KF53")->where($where)->order('AAE030 DESC')->find();
        if(!empty($info))
        {
//             if( ($info['AKF050'] == '0') || (empty($info['AKF119'])) )
            if( ($info['AKF120'] != 2) && (!empty($info['AKF050']) || !empty($info['AKF052']) ))
            {
                if( Db::table("YD_KF53")->where($where)->update($list) )
                {
                    rjson('修改认证成功');
                }
                else
                {
                    rjson("修改认证失败", "400", "error");
                }
            } else {
                rjson("新添认证失败，今天已成功认证完成", "400", "error");
            }
        } 
        else 
        {
            //住院考勤添加
            if(Db::table("YD_KF53")->insert($list))
            {
                rjson("新添认证成功");
            } 
            else
            {
                rjson("新添认证失败", "400", "error");
            }
        }

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
//         if(empty($data['AKC190'])) rjson('参数不能为空', '400', 'error');
    
        $where = [];
        
        if(!empty($data['AKC190'])) $where['AKC190']=$data['AKC190'];
        if(!empty($data['AAC002'])) $where['AAC002']=$data['AAC002'];
        
        $info = Db::table("YD_DETAIL")->where($where)->order("AAE030 desc")->find();
        $info['KF54'] = Db::table("YD_KF54")->where(array("AKC190"=>$info["AKC190"]))->order("AAE030 desc")->select();
        rjson($info);
    }
    
    //获取通知消息
    public function noticeList(){
                
        $data = input("post.");
        $page_index = empty($data['page_index']) ? "1" : $data['page_index'];
        $page_size = empty($data['page_size']) ? "10" : $data['page_size']; 
        
        $where = [];
        
        $where['AMS103'] = array("EQ", '1');    //状态1发布2停止
//         $where['AMS104'] = empty($data['AMS104']) ? "3" : $data['AMS104'];

        $list = Db::table("YD_MS101")->where($where)->page($page_index, $page_size)->order("AMS102 desc")->select();
        rjson($list);
    }
    
    /** 
     * 上传文件
     *  */
    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('fileName');
        //获取绝对目录
        if(empty(config('file_path'))){
            $ROOT_PATH = ROOT_PATH;
        } else {
            $ROOT_PATH= config('file_path');
        }
        //获取子目录
        if( in_array(strtolower(pathinfo($_FILES['fileName']['name'], PATHINFO_EXTENSION)), ['jpg', 'png', 'gif']) ){
            $subdirectory = '/image/';
        } else {
            $subdirectory = '/video/';
        }
        
        $info = $file->validate(['size'=>15678000,'ext'=>'jpg,png,gif,mp4'])->move($ROOT_PATH.$subdirectory);

        if($info){
            // 成功上传后 获取上传信息
            $list = array(
                'ext'   => $info->getExtension(),
                'path'  => $subdirectory.$info->getSaveName(),
                'name'  => $info->getFilename(),
            );
            rjson($list);
        }else{
            // 上传失败获取错误信息
            rjson($file->getError(), '400', 'error');
        }
    }
    
    //获取天气  https://www.sojson.com/api/weather.html
    public function weatherList(){
        
        $url = "https://www.sojson.com/open/api/weather/json.shtml?city=张家口";
        echo file_get_contents($url);die;
    }
}
