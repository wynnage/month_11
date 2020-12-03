<?php

namespace app\blog\controller;

use think\Controller;
use think\Request;

class Blog extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //查询数据
        $data = model("Blog")->paginate(2);
        //返回视图
        return view("list", ['data' => $data]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //验证是否登录
        session_start();
        $username=session("username");
        if (empty($username)){
            $this->error("请登录");
        }
        return view("add");
    }

    /**
     * 保存新建的资源
     *
     * @param \think\Request $request
     * @return \think\Response
     */
    public function save(Request $request)
    {

        //接收数据
        $param = $request->param();
        //接收图片
        $file = $request->file("blog_img");
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        $param['blog_img'] = "/uploads" . DS . $info->getSaveName();
        //验证数据
        $validate = $this->validate($param, [
            "blog_title|博客标题" => "require",
            "blog_desc|博客内容" => "require",
        ]);
        if ($validate !== true) {
            $this->error($validate);
        }
        //添加入库
        $res = model("Blog")->allowField(true)->save($param);
        if ($res) {
            $this->success("发布成功！", "Blog/index");
        } else {
            $this->error("发布失败");
        }
    }

    /**
     * 显示指定的资源
     *
     * @param int $id
     * @return \think\Response
     */
    public function read($id)
    {
        //验证id
        if (!is_numeric($id)) {
            $this->error("操作异常");
        }

        //根据id 查询一条
        $data = model("Blog")->find($id);
        //根据id 展示所有的评论
        $comment = model("Comment")->where("blog_id", $id)->select();
        return view("info", ['data' => $data, 'comment' => $comment]);
    }

    /**
     * 评论
     *
     * @param \think\Request $request
     *
     * @return \think\Response
     */
    public function comment(Request $request)
    {
        session_start();
        $username=session("username");
        if (empty($username)){
            $this->error("请登录");
        }
        //评论内容
        $param = input();
        $param['user']=session("username");
        $res=model("Comment")->allowField(true)->save($param);
        if ($res){
            return json(['code' => 0, "msg" => "评论成功", "data" => $param]);
        }else{
            return json(['code' => 1, "msg" => "评论失败 ", "data" => ""]);

        }

    }


}
