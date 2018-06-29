<?php
/** 
 * 会员类
 *  */
namespace app\index\controller;

use think\Db;
class Member
{
    /** 
     * 登录
     *  AFK101 用户代码
     *  AKG105 用户密码
     *  */
    public function login(){
        
       $data = input("post.");
       if(empty($data['AFK101']) || empty($data['AKG105'])){
           rjson("账号或密码不能为空", '400', 'error');
       }
       
       $where = array(
           'AFK101' => $data['AFK101'],
           'AKG105' => $data['AKG105'],
       );
       $list = Db::table("YD_KF99")->where($where)->select();
       if(empty($list)){
           rjson("账号或密码错误", '400', 'error');
       } else {
           rjson($list);
       }
    }
    
    //退出登录
    public function outLogin(){
        
    }
}