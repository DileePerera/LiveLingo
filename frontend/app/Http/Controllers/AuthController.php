<?php

namespace App\Http\Controllers;

use App\Models\User;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function reg_index()
    {
        return view('auth.register');
    }
    public function log_index()
    {
        return view('auth.login');
    }

    public function reg(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required',
                'email' => 'required|unique:users,email',
                'password' => 'required',
            ]);

            $hash_password = Hash::make($request->password);

            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $hash_password
            ]);

            return redirect()->route('log_index')->with('success', 'Account created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            return redirect()->back()->withErrors($errors)->withInput()->with('error', $errors);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['stack' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function log(Request $request)
    {
        try {
            $request->validate(
                [
                    'email' => 'required',
                    'password' => 'required',
                ]
            );
            $user = user::where(['email' => $request->email, 'status' => 1])->first();

            if ($user && Hash::check($request->password, $user->password)) {
                $request->session()->put('user', $user);
                $responseData = ['success' => true, 'message' => 'Login successfully'];
            } else {
                $responseData = ['success' => false, 'message' => 'Wrong email or password'];
            }
        } catch (ValidationException $e) {
            $responseData = ['success' => false];
        } catch (Exception $e) {
            $responseData = ['success' => false, 'message' => 'Email or password is wrong. Please try again.'];
        }


        if ($responseData['success']) {
            Session()->flash('success', $responseData['message']);
            return redirect()->route('dashboard');
        } else {
            return back()->with('error', $responseData['message']);
        }
    }
}
