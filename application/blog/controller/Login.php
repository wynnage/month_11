<?php

namespace app\blog\controller;

use think\captcha\Captcha;
use think\Controller;
use think\Request;

class Login extends Controller
{


    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        return view("login");

    }

    public function login(){
        //接收参数
        $param=input();
        //验证参数
        $validate=$this->validate($param,[
            'username|用户名'=>'require',
            'psw|密码'=>'require',
            "code|验证码"=>"require|captcha"
        ]);
        if ($validate!==true){
            $this->error($validate);
        }
        //验证登录数据
        $data=model("Login")->where("username",$param['username'])->find();
        if ($data){
            if ($data['psw']==md5(md5($param['psw']))){
                session("username",$param['username']);
                $this->redirect("Blog/index");
            }else{
                $this->error("密码不正确");
            }
        }else{
            $this->error("用户不存在");
        }
    }
    public function code(){
        $captcha = new Captcha();

        return $captcha->entry();
    }
    public function logout(){
        session_start();
        session("username",null);
        $this->redirect("Login/index");
    }
}
