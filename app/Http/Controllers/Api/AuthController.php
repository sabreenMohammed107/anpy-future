<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\UserDataResource;
use App\Models\Device;
use App\Models\Transaction;
use App\Models\User;
use App\Models\User_payrol_rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Validator;
use Illuminate\Support\Facades\DB;
class AuthController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        try
        {
            // Disable foreign key checks!
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $validator = Validator::make($request->all(), [
            'emp_code' => 'required|unique:users',
            'mobile' => 'required|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->convertErrorsToString($validator->messages());
        }


            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $input['register_approved'] = 0;
            $user = User::create($input);
            $user->accessToken = $user->createToken('MyApp')->accessToken;
//user payroll
            $pay = new User_payrol_rule();
            $pay->user_id = $user->id;
            $pay->save();
            DB::commit();
            // Enable foreign key checks!
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return $this->sendResponse(new UserDataResource($user), 'تم التسجيل بنجاح انتظر التفعيل !');

        }  catch (\Exception$e) {
            DB::rollback();
              return $this->sendError($e->getMessage(), 'حدث خطأ ما');
        }
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'emp_code' => 'required',
            'password' => 'required',
            'device_token' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->convertErrorsToString($validator->messages());
        }

        try
        {
            if (Auth::attempt(['emp_code' => $request->emp_code, 'password' => $request->password])) {
                $user = Auth::user();
                $user->accessToken = $user->createToken('MyApp')->accessToken;
//devices
                if ($user->register_approved == 1) {
                    $device = Device::where('token', '=', $request->device_token)->first(); //laravel returns an integer
                    $data = [
                        'token' => $request->device_token,
                        'user_id' => $user->id,
                        'status' => 1,
                    ];
                    if ($device) {
                        $device->update($data);

                    } else {
                        Device::create($data);
                    }
                    return $this->sendResponse(new UserDataResource($user), 'تم التسجيل بنجاح');
                } elseif ($user->register_approved == 0) {
                    return $this->sendError('عذرا جارى تأكيد بياناتك');
                } else {
                    return $this->sendError('عذرا تم رفض اشتراكك');
                }

            } else {
                return $this->sendError('كود المستخدم او كلمه السر غير صحيحة');
            }
        } catch (\Exception$e) {
            return $this->sendError($e->getMessage(), 'حدث خطأ ما !!');
        }
    }

    public function tokenUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',

        ]);

        if ($validator->fails()) {
            return $this->convertErrorsToString($validator->messages());
        }

        try
        {
            $user = Auth::user();
            if ($user) {
                $device = Device::where('token', '=', $request->token)->first(); //laravel returns an integer
                $data = [
                    'token' => $request->token,
                    'user_id' => $user->id,
                    'status' => 1,

                ];
                if ($device) {
                    $device->update($data);

                } else {
                    Device::create($data);
                }
                return $this->sendResponse(null, 'تم تعديل البيانات بنجاح');

            }

        } catch (\Exception$e) {
            return $this->sendError($e->getMessage(), 'حدث خطأ ما');
        }
    }
    public function allNofications(Request $request)
    {
        $user = Auth::user();
        $notifications = Transaction::where('user_id', '=', $user->id)->orderBy('id', 'DESC');
        dd($notifications);

        if ($notifications->count() > 0) {
            return $this->sendResponse($notifications, 'كل الاشعارات');
        } else {
            return $this->successResponse('لا يوجد اشعارات حتى الان');
        }
    }
}