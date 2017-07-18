<?php
/**
 * Created by PhpStorm.
 * User: 卢鹏宇
 * Date: 2017/7/17
 * Time: 16:14
 * Version: 1.0
 */

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rules\In;
use App\Http\Model\Admin;
use Illuminate\Http\Request;
use Mail;

class BackstageController extends Controller
{
    private function judge() {
        $admin = DB::table('admin')->where('id', session('admin_id'))->first();
        if ($admin == null) {
            return redirect('backstage/logout');
        }
        return null;
    }

    public function logout(Request $request) {
        $request->session()->flush();
        return redirect('/');
    }

    public function index() {
        $judge = $this->judge();
        if ($judge != null) {
            return $judge;
        }
        $admin = DB::table('admin')->where('id', session('admin_id'))->first();

        $user_count = DB::table('users')->count();
        $new_user = DB::table('users')->orderBy('id','desc')->limit(6)->get();//->limit(6)->find();

        $post_count = DB::table('post')->count();
        $new_post = DB::table('post')->orderBy('id','desc')->limit(6)->get();

        $cnt_man = DB::table('users')->where('sex', 1)->count();
        $cnt_wowan = DB::table('users')->where('sex', 2)->count();
        $sum = $cnt_man + $cnt_wowan;
        $cnt_man = (int)($cnt_man*100/$sum);
        $cnt_wowan = 100 - $cnt_man;

        $cnt_nwpu = DB::table('users')->whereNull('isnwpu')->count();
        $cnt_other = DB::table('users')->whereNotNull('isnwpu')->count();
        $sum = $cnt_nwpu + $cnt_other;
        $cnt_nwpu = (int)($cnt_nwpu*100/$sum);
        $cnt_other = 100 - $cnt_nwpu;

        return view('backstage.index',
            [
                'user_count' => $user_count,
                'user' => $new_user,
                'post_count' => $post_count,
                'post' => $new_post,
                'man' => $cnt_man,
                'woman' => $cnt_wowan,
                'nwpu' => $cnt_nwpu,
                'other' => $cnt_other,
                'admin_name' => $admin->name
            ]
        );
    }

    public function approval() {
        $judge = $this->judge();
        if ($judge != null) {
            return $judge;
        }
        $admin = DB::table('admin')->where('id', session('admin_id'))->first();
        $list = DB::table('apply')->where('status', 0)->get();

        return view('backstage.approval',
            [
                'list' => $list,
                'admin_name' => $admin->name
            ]
        );
    }

    public function applypass($id='') {
        $judge = $this->judge();
        if ($judge != null) {
            return $judge;
        }
        $admin = DB::table('admin')->where('id', session('admin_id'))->first();

        $apply = DB::table('apply')->where('id', $id)->first();
        $user = DB::table('users_pre')->where('pid', $apply->pid)->first();

        $data = ['email'=>$user->email, 'name'=>$user->pstudentid];
        Mail::send('emails.applypass', ['code' => $user->code], function($message) use($data)
        {
            $message->to($data['email'], $data['name'])->subject('青春521账号审核结果');
        });
        unlink(public_path() . '/uploads/apply/' .$apply->xsz);
        unlink(public_path() . '/uploads/apply/' .$apply->ykt);
        DB::table('apply')->where('id', $id)->update([
            'status' => 1
        ]);
        DB::table('users_pre')->where('pid', $apply->pid)->update([
            'certificate_status' => 1
        ]);
        return redirect()->back();
    }

    public function applydispass($id='') {
        $judge = $this->judge();
        if ($judge != null) {
            return $judge;
        }
        $admin = DB::table('admin')->where('id', session('admin_id'))->first();

        $reason = Input::get('reason');
        if ($reason == null) {
            $reason = "无";
        }

        $apply = DB::table('apply')->where('id', $id)->first();
        $user = DB::table('users_pre')->where('pid', $apply->pid)->first();

        $data = ['email'=>$user->email, 'name'=>$user->pstudentid];
        Mail::send('emails.applydispass', [
            'code' => $user->code,
            'reason' => $reason
        ], function($message) use($data)
        {
            $message->to($data['email'], $data['name'])->subject('青春521账号审核结果');
        });
        unlink(public_path() . '/uploads/apply/' .$apply->xsz);
        unlink(public_path() . '/uploads/apply/' .$apply->ykt);
        DB::table('apply')->where('id', $id)->update([
            'status' => -1
        ]);
        DB::table('users_pre')->where('pid', $apply->pid)->update([
            'certification' => 0
        ]);
        return redirect()->back();
    }
}