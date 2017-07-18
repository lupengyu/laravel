<?php
/**
 * Created by PhpStorm.
 * User: 卢鹏宇
 * Date: 2017/7/17
 * Time: 12:27
 * Version: 1.0
 */

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Http\Request;
use Mail;

/**
 * 登录控制器
 * Class IndexController
 * @package App\Http\Controllers
 */
class IndexController extends Controller
{
    /**
     * 判断用户是否已经登录
     *          --管理员
     *          --普通用户
     *          --管理员 --》 后台首页
     *          --普通用户 --》 前台首页
     */
    private function judge() {
        //判断有没有管理员账号登录
        $admin_id = session('admin_id');
        if ($admin_id != null) {
            $admin = DB::table('admin')->where('id', $admin_id)->first();
            if ($admin == null) {
                return redirect('logout');
            }
            echo $admin->id;
            return redirect('backstage');
        }
        //判断有没有普通用户账号登录
        $user_id = session('user_id');
        if ($user_id != null) {
            if ($user_id == -1) {
                return redirect('logout');
            }
            $user = DB::table('users')->where('id', $user_id)->first();
            if ($user == null) {
                return redirect('logout');
            }
            return redirect('home');
        }
        return null;
    }

    /**
     * 用户登出
     */
    public function logout(Request $request) {
        $request->session()->flush();
        return redirect('/');
    }

    /**
     * 验证码生成器
     * @param $tmp
     */
    public function captcha($tmp)
    {
        $builder = new CaptchaBuilder;
        $builder->build($width = 200, $height = 80, $font = null);
        $phrase = $builder->getPhrase();

        session(['milkcaptcha'=>$phrase]);
        header("Cache-Control: no-cache, must-revalidate");
        header('Content-Type: image/jpeg');
        $builder->output();
    }

    /**
     * 登录界面首页
     * @return mixed 登录界面
     */
    public function index() {
        $judge = $this->judge();
        if ($judge != null) {
            return $judge;
        }
        $warning = session('warning');
        session(['warning'=>null]);
        return view('index.index',
            ['warning' => $warning]
        );
    }

    /**
     * 用户登录
     * @param Request $request
     */
    public function login(Request $request) {
        $judge = $this->judge();
        if ($judge != null) {
            return $judge;
        }
        $userInput = Input::get('code');
        if (session('milkcaptcha') != $userInput) {
            session(['warning'=>'验证码错误']);
            return redirect()->back();
        }
        $user_name = Input::get('username');
        $password = md5(md5(Input::get('password')));
        $admin = DB::table('admin')->where('name', $user_name)->where('password', $password)->first();
        if ($admin != null) {
            session(['admin_id'=>$admin->id]);
            return redirect('backstage');
        }
        $user = DB::table('users_pre')->where('pstudentid', $user_name)->where('ppwd', $password)->first();
        if ($user == null) {
            session(['warning'=>'用户名或密码错误']);
            return redirect()->back();
        }
        if (!is_numeric($user_name)) {
            //判断是否激活账号
            if($user->active == 0) {
                return '账号未激活，请查看邮箱寻找激活邮件';
            }
            //判断是否认证账号
            if ($user->certification == 0) {
                session(['certificate_id'=>$user->pid]);
                return redirect('certificate');
            } else {
                //判断是否认证通过
                if ($user->certificate_status == 0) {
                    return "认证仍在审核中，请等待邮件通知";
                }
            }
            //判断是否绑定有账号
            if ($user->uid == 0) {
                session(['perfect_id'=>$user->pid]);
                return redirect('perfectinformation');
            }
        } else {
            //判断是否绑定有账号
            if ($user->uid == 0) {
                session(['perfect_id'=>$user->pid]);
                return redirect('perfectinformation');
            } else {
                //判断是否绑定有邮箱
                if ($user->email == null) {
                    session(['email_id'=>$user->pid]);
                    return redirect('addemail');
                }
            }
        }
        session(['user_id'=>$user->uid]);
        return redirect('home');
    }

    /**
     * 用户认证
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View|null|string
     */
    public function certificate() {
        $judge = $this->judge();
        if ($judge != null) {
            return $judge;
        }

        $user_id = session('certificate_id');
        if ($user_id == null) {
            return redirect('logout');
        }
        $user = DB::table('users_pre')->where('pid', $user_id)->first();
        if($user->active == 0) {
            return '账号未激活，请查看邮箱寻找激活邮件';
        }
        if($user->certification == 1) {
            if ($user->certificate_status == 0) {
                return '认证仍在审核中，请等待邮件通知';
            }
            if ($user->uid == 0) {
                session(['perfect_id' => $user->pid]);
                return redirect('perfectinformation');
            }
            else {
                session(['user_id' => $user->uid]);
                return redirect('home');
            }
        }
        $warning = session('warning');
        session(['warning'=>null]);
        return view('index.certificate',
            ['warning' => $warning]
        );
    }

    /**
     * 提交认证信息
     * @param Request $request 认证信息
     */
    public function addapply(Request $request) {
        $judge = $this->judge();
        if ($judge != null) {
            return $judge;
        }
        //账号判断
        $user_id = session('certificate_id');
        if ($user_id == null) {
            return redirect('logout');
        }
        $user = DB::table('users_pre')->where('pid', $user_id)->first();
        if($user->certification == 1) {
            if ($user->certificate_status == 0) {
                return '认证仍在审核中，请等待邮件通知';
            }
            if ($user->uid == 0) {
                session(['perfect_id' => $user->pid]);
                return redirect('perfectinformation');
            }
            else {
                session(['user_id' => $user->uid]);
                return redirect('home');
            }
        }
        //输入判断
        $school = Input::get('school');
        if ($school == null) {
            session(['warning' => '学校为必填']);
            return redirect()->back();
        }
        if (strlen($school) > 50) {
            session(['warning' => '学校最多50位']);
            return redirect()->back();
        }
        //保存文件
        $file1 = $request->file('xsz');
        if($file1 == null) {
            session(['warning' => '文件未选择或过大']);
            return redirect()->back();
        } else {
            $allowed_extensions = ["png", "jpg"];
            if ($file1->getClientOriginalExtension() && !in_array($file1->getClientOriginalExtension(), $allowed_extensions)) {
                session(['warning' => '仅支持jpg与png文件']);
                return redirect()->back();
            }
            $destinationPath = 'uploads/apply/';
            $fileName = $user->code. 'xsz.png';
            $file1->move($destinationPath, $fileName);
            $xsz = $fileName;
        }
        $file2 = $request->file('ykt');
        if($file2 == null) {
            session(['warning' => '文件未选择或过大']);
            return redirect()->back();
        } else {
            $allowed_extensions = ["png", "jpg"];
            if ($file2->getClientOriginalExtension() && !in_array($file2->getClientOriginalExtension(), $allowed_extensions)) {
                session(['warning' => '仅支持jpg与png文件']);
                return redirect()->back();
            }
            $destinationPath = 'uploads/apply/';
            $fileName = $user->code. 'ykt.png';
            $file2->move($destinationPath, $fileName);
            $ykt = $fileName;
        }
        DB::table('apply')->insert([
            'pid' => $user->pid,
            'school' => $school,
            'xsz' => $xsz,
            'ykt' => $ykt
        ]);
        DB::table('users_pre')->where('pid', $user_id)->update([
            'certification' => 1
        ]);
        return '认证申请成功，请等待审核结果';
    }

    /**
     * 完善个人信息页面
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|null|string
     */
    public function perfectinformation() {
        $judge = $this->judge();
        if ($judge != null) {
            return $judge;
        }

        $user_id = session('perfect_id');
        if ($user_id == null) {
            return redirect('logout');
        }
        $user = DB::table('users_pre')->where('pid', $user_id)->first();
        if ($user == null) {
            return redirect('logout');
        }
        if (!is_numeric($user->pstudentid)) {
            if($user->active == 0) {
                return '账号未激活，请查看邮箱寻找激活邮件';
            }
            if ($user->certification == 0) {
                session(['certificate_id' => $user->pid]);
                return redirect('certificate');
            }
            if ($user->certificate_status == 0) {
                return '认证仍在审核中，请等待邮件通知';
            }
            $email = 1;
        } else {
            $email = 0;
        }
        if ($user->uid != 0) {
            if ($user->email == null) {
                session(['email_id' => $user->pid]);
                return redirect('addemail');
            }
            session(['user_id' => $user->uid]);
            return redirect('home');
        }
        $warning = session('warning');
        session(['warning'=>null]);
        return view('index.perfectinformation',
            [
                'warning' => $warning,
                'email' => $email
            ]
        );
    }

    /**
     * 完善个人信息
     * @param string $id 用户类型标识
     *                 --id = 1 外校学生
     *                 --id = 0 本校学生
     */
    public function addinformation($id = '') {
        $judge = $this->judge();
        if ($judge != null) {
            return $judge;
        }
        if($id != 1 && $id != 0) {
            return redirect('logout');
        }
        //判断账号状态
        $user_id = session('perfect_id');
        if ($user_id == null) {
            return redirect('logout');
        }
        $user = DB::table('users_pre')->where('pid', $user_id)->first();
        if ($user == null) {
            return redirect('logout');
        }
        if (!is_numeric($user->pstudentid)) {
            if($user->active == 0) {
                return '账号未激活，请查看邮箱寻找激活邮件';
            }
            if ($user->certification == 0) {
                session(['certificate_id' => $user->pid]);
                return redirect('certificate');
            }
            if ($user->certificate_status == 0) {
                return '认证仍在审核中，请等待邮件通知';
            }
        }
        if ($user->uid != 0) {
            if ($user->email == null) {
                session(['email_id' => $user->pid]);
                return redirect('addemail');
            }
            session(['user_id' => $user->uid]);
            return redirect('home');
        }
        //获取输入数据并判断格式
        $nickname = Input::get('nickname');
        if($nickname == null) {
            session(['warning' => '昵称不能为空']);
            return redirect()->back();
        }
        $name = Input::get('name');
        if($name == null) {
            session(['warning' => '真实姓名不能为空']);
            return redirect()->back();
        }
        $email_ = null;
        if ($id == 0) {
            $email = Input::get('email');
            if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
                session(['warning' => '邮件格式不规范']);
                return redirect()->back();
            }
            $judgeuser = DB::table('users_pre')->where('email', $email)->first();
            if ($judgeuser != null) {
                session(['warning' => '邮箱已被注册']);
                return redirect()->back();
            }
            $email_ = $email;
        }
        $college = Input::get('college');
        if($college == null) {
            session(['warning' => '院系不能为空']);
            return redirect()->back();
        }
        $record = Input::get('record');
        if($record == 0) {
            session(['warning' => '学历不能为空']);
            return redirect()->back();
        }
        $sex = Input::get('sex');
        if($sex == 0) {
            session(['warning' => '性别不能为空']);
            return redirect()->back();
        }
        $height = Input::get('height');
        if($height == null) {
            session(['warning' => '身高不能为空']);
            return redirect()->back();
        } elseif ($height < 0) {
            session(['warning' => '身高不能为负数']);
            return redirect()->back();
        } elseif ($height > 999) {
            session(['warning' => '身高最多3位']);
            return redirect()->back();
        }
        $age = Input::get('age');
        if($age == null) {
            session(['warning' => '年龄不能为空']);
            return redirect()->back();
        }elseif ($age < 0) {
            session(['warning' => '年龄不能为负数']);
            return redirect()->back();
        } elseif ($height > 999) {
            session(['warning' => '年龄最多3位']);
            return redirect()->back();
        }
        if (!is_numeric($user->pstudentid)) {
            $apply = DB::table('apply')->where('pid', $user->pid)->where('status', 1)->first();
            $school = $apply->school;
        } else {
            $school = null;
        }
        $code = md5($user->pid.date('Y-m-d H:i:s'));
        DB::table('users')->insert([
            'nickname' => $nickname,
            'name' => $name,
            'college' => $college,
            'height' => $height,
            'age' => $age,
            'isnwpu' => $school,
            'record' => $record,
            'sex' => $sex,
            'code' => $code
        ]);
        $user_info = DB::table('users')->where('code', $code)->first();
        if ($id == 1) {
            DB::table('users_pre')->where('pid', $user_id)->update([
                'uid' => $user_info->id
            ]);
        } else {
            DB::table('users_pre')->where('pid', $user_id)->update([
                'uid' => $user_info->id,
                'email' => $email_
            ]);
        }
        session(['user_id' => $user->uid]);
        return redirect('home');
    }

    /**
     * 绑定邮箱界面
     * @return mixed 绑定邮箱界面
     */
    public function addemail() {
        $judge = $this->judge();
        if ($judge != null) {
            return $judge;
        }

        $user_id = session('email_id');
        if ($user_id == null) {
            return redirect('logout');
        }
        $user = DB::table('users_pre')->where('pid', $user_id)->first();
        if ($user == null) {
            return redirect('logout');
        }
        if(!is_numeric($user->pstudentid)) {
            return redirect('logout');
        } else {
            if ($user->email != null) {
                if ($user->uid != 0) {
                    if ($user->email == null) {
                        session(['email_id' => $user->pid]);
                        return redirect('addemail');
                    }
                    session(['user_id' => $user->uid]);
                    return redirect('home');
                }
            }
        }
        $warning = session('warning');
        session(['warning'=>null]);
        return view('index.addemail',
            ['warning' => $warning]
        );
    }

    /**
     * 绑定邮箱
     */
    public function setemail() {
        $judge = $this->judge();
        if ($judge != null) {
            return $judge;
        }
        //判断该账号是否绑定有邮箱
        $user_id = session('email_id');
        if ($user_id == null) {
            return redirect('logout');
        }
        $user = DB::table('users_pre')->where('pid', $user_id)->first();
        if ($user == null) {
            return redirect('logout');
        }
        if(!is_numeric($user->pstudentid)) {
            return redirect('logout');
        } else {
            if ($user->email != null) {
                if ($user->uid != 0) {
                    if ($user->email == null) {
                        session(['email_id' => $user->pid]);
                        return redirect('addemail');
                    }
                    session(['user_id' => $user->uid]);
                    return redirect('home');
                }
            }
        }
        //查看该邮箱是否存在
        $email = Input::get('email');
        if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
            session(['warning' => '邮件格式不规范']);
            return redirect()->back();
        }
        $judgeuser = DB::table('users_pre')->where('email', $email)->first();
        if ($judgeuser != null) {
            session(['warning' => '邮箱已被注册']);
            return redirect()->back();
        }
        DB::table('users_pre')->where('pid', $user_id)->update([
           'email' => $email
        ]);
        session(['user_id' => $user->uid]);
        return redirect('home');
    }

    /**
     * 用户注册
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View|null
     */
    public function register() {
        $judge = $this->judge();
        if ($judge != null) {
            return $judge;
        }
        $warning = session('warning');
        session(['warning'=>null]);
        return view('index.register',
            ['warning' => $warning]
        );
    }

    /**
     * 新用户注册
     */
    public function adduser() {
        $judge = $this->judge();
        if ($judge != null) {
            return $judge;
        }
        //获取信息与格式判断
        $email = Input::get('email');
        $password = Input::get('password');
        $password2 = Input::get('password2');
        if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
            session(['warning' => '邮件格式不规范']);
            return redirect()->back();
        }
        if (strlen($email) > 50) {
            session(['warning' => '邮箱最多50位']);
            return redirect()->back();
        }
        if (strlen($password) < 6) {
            session(['warning' => '密码最少6位']);
            return redirect()->back();
        }
        if (strlen($password) > 18) {
            session(['warning' => '密码最多18位']);
            return redirect()->back();
        }
        if ($password != $password2) {
            session(['warning' => '两次输入密码不一致']);
            return redirect()->back();
        }

        $current_time = date('Y-m-d H:i:s');
        $user = DB::table('users_pre')->where('email', $email)->first();
        if ($user != null) {
            session(['warning' => '邮箱已被注册']);
            return redirect()->back();
        }
        $code = md5(md5($current_time + $email));
        DB::table('users_pre')->insert([
            'pstudentid' => $email,
            'email' => $email,
            'ppwd' => md5(md5($password)),
            'code' => $code
        ]);

        $data = ['email'=>$email, 'name'=>$email];
        Mail::send('emails.active', ['code' => $code], function($message) use($data)
        {
            $message->to($data['email'], $data['name'])->subject('青春521账号激活邮件');
        });

        return '激活邮件已发送，请注意查收';
    }

    /**
     * 激活账号
     * @param null $code 验证码
     */
    public function active($code = '') {
        $judge = $this->judge();
        if ($judge != null) {
            return $judge;
        }
        //判断验证码是否正确
        if ($code == null) {
            return redirect('logout');
        }
        $user = DB::table('users_pre')->where('code', $code)->first();
        if ($user == null) {
            return "参数错误";
        }
        if ($user->active == 0) {
            DB::table('users_pre')->where('code', $code)->update([
                'active' => 1
            ]);
        }
        if ($user->certification == 0) {
            session(['certificate_id' => $user->pid]);
            return redirect('certificate');
        }
        else {
            if ($user->uid == 0) {
                session(['perfect_id' => $user->pid]);
                return redirect('perfectinformation');
            }
            else {
                session(['user_id'=>$user->uid]);
                return redirect('home');
            }
        }
    }

    /**
     * 游客登录
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|null
     */
    public function tourists() {
        $judge = $this->judge();
        if ($judge != null) {
            return $judge;
        }

        session(['user_id' => -1]);
        return redirect('home');
    }

    /**
     * 忘记密码界面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View|null
     */
    public function losspassword() {
        $judge = $this->judge();
        if ($judge != null) {
            return $judge;
        }

        $warning = session('warning');
        session(['warning'=>null]);
        return view('index.losspassword',
            ['warning' => $warning]
        );
    }

    /**
     * 发送重置密码邮件
     */
    public function sendemail() {
        $judge = $this->judge();
        if ($judge != null) {
            return $judge;
        }
        $email = Input::get('email');
        if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
            session(['warning' => '邮件格式不规范']);
            return redirect()->back();
        }
        $user = DB::table('users_pre')->where('email', $email)->first();
        if ($user == null) {
            session(['warning' => '查无此邮箱']);
            return redirect()->back();
        }
        $current_time = date('Y-m-d H:i:s');
        $code = md5(md5($current_time + $email));
        DB::table('users_pre')->where('email', $email)->update([
            'code' => $code
        ]);

        $data = ['email'=>$email, 'name'=>$email];
        Mail::send('emails.passwordloss', ['code' => $code], function($message) use($data)
        {
            $message->to($data['email'], $data['name'])->subject('青春521密码找回邮件');
        });

        return '密码找回邮件已发送，请注意查收';
    }

    /**
     * 重置密码界面
     * @param null $code 验证码
     * @return mixed 重置密码界面
     */
    public function setpassword($code = null) {
        $judge = $this->judge();
        if ($judge != null) {
            return $judge;
        }

        if ($code == null) {
            return redirect('logout');
        }
        $user = DB::table('users_pre')->where('code', $code)->first();
        if ($user == null) {
            return "参数错误";
        }
        session(['password_id' => $user->pid]);
        $warning = session('warning');
        session(['warning'=>null]);
        return view('index.setpassword',
            [
                'warning' => $warning,
                'user' => $user
            ]
        );
    }

    /**
     * 重置密码
     */
    public function changepassword() {
        $judge = $this->judge();
        if ($judge != null) {
            return $judge;
        }
        $user_id = session('password_id');
        if($user_id == null) {
            return redirect('logout');
        }
        $user = DB::table('users_pre')->where('pid', $user_id)->first();
        if($user == null) {
            return redirect('logout');
        }
        //获取输入与验证
        $password = Input::get('password1');
        $password2 = Input::get('password2');
        if (strlen($password) < 6) {
            session(['warning' => '密码最少6位']);
            return redirect()->back();
        }
        if (strlen($password) > 18) {
            session(['warning' => '密码最多18位']);
            return redirect()->back();
        }
        if ($password != $password2) {
            session(['warning' => '两次输入密码不一致']);
            return redirect()->back();
        }
        DB::table('users_pre')->where('pid', $user_id)->update([
            'ppwd' => md5(md5($password))
        ]);
        return '密码重置成功！';
    }
}