<?php

namespace App\Http\Controllers\Back;

use App\{
    Models\EmailTemplate,
    Http\Controllers\Controller,
};
use App\Helpers\EmailHelper;
use App\Models\Setting;
use Illuminate\Http\Request;

class EmailSettingController extends Controller
{

    /**
     * Constructor Method.
     */
    public function __construct()
    {
        $this->middleware('adminlocalize');
        $this->middleware('auth:admin');
    }

    /**
     * Show the form for updating resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function email()
    {
        return view('back.settings.email', [
            'datas' => EmailTemplate::get(),
            'setting' => Setting::first()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(EmailTemplate $template)
    {
        return view('back.email_template.edit', compact('template'));
    }

    public function emailUpdate(Request $request)
    {
        $rules = [
            "email_from" => "required|max:100",
            "email_from_name" => "required|max:100",
            "contact_email" => "required|max:100",
        ];

        if ($request->has('smtp_check')) {
            $rules['email_host'] = "required|max:200";
            $rules['email_port'] = "required|max:10";
            $rules['email_encryption'] = "required|max:10";
            $rules['email_user'] = "required|max:100";
            $rules['email_pass'] = "required|max:100";
        }

        $request->validate($rules);

        $input = $request->all();
        $input['smtp_check'] = $request->has('smtp_check') ? 1 : 0;
        $input['order_mail'] = $request->has('order_mail') ? 1 : 0;
        $input['ticket_mail'] = $request->has('ticket_mail') ? 1 : 0;
        $input['is_queue_enabled'] = $request->has('is_queue_enabled') ? 1 : 0;
        $input['is_mail_verify'] = $request->has('is_mail_verify') ? 1 : 0;

        // Auto-clean SMTP credentials (remove accidental spaces in App Passwords or hostnames)
        if (isset($input['email_pass'])) {
            $input['email_pass'] = str_replace(' ', '', trim($input['email_pass']));
        }
        if (isset($input['email_user'])) {
            $input['email_user'] = trim($input['email_user']);
        }
        if (isset($input['email_host'])) {
            $input['email_host'] = trim($input['email_host']);
        }
        if (isset($input['email_port'])) {
            $input['email_port'] = trim($input['email_port']);
        }
        if (isset($input['email_encryption'])) {
            $input['email_encryption'] = trim($input['email_encryption']);
        }

        Setting::first()->update($input);
        return redirect()->back()->withSuccess(__('Email & SMTP Settings Updated Successfully.'));
    }

    /**
     * Send a test email to verify SMTP configuration
     */
    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email|max:150'
        ]);

        $emailHelper = new EmailHelper();
        $result = $emailHelper->sendTestMail($request->test_email);

        if ($result['status']) {
            return redirect()->back()->withSuccess($result['message']);
        } else {
            return redirect()->back()->withErrors([$result['message']]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmailTemplate $template)
    {
        $template->update($request->all());
        return redirect()->route('back.setting.email')->withSuccess(__('Email Template Updated Successfully.'));
    }
}
