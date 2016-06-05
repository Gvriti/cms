<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Mail\Mailer;

class AdminBugReportController extends Controller
{
    /**
     * Display a settings list.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('admin.bug_report.index');
    }

    /**
     * Send a bug report.
     *
     * @param  \Illuminate\Contracts\Mail\Mailer  $mail
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(Mailer $mail, Request $request)
    {
        $this->validate($request, [
            'title'       => 'required',
            'description' => 'required'
        ]);

        $data = $request->only(['title', 'description']);

        $host = $request->getHost();

        $view = 'admin.bug_report.mail_html';

        try {
            $mail->send($view, $data, function ($m) use ($data, $host) {
                $m->from(env('MAIL_USERNAME'))
                  ->to('bugs@digitaldesign.ge')
                  ->subject($host . ' - bug report');
            });

            $message = fill_data('success', trans('mail.message_sent'));
        } catch (Exception $e) {
            $message = fill_data('error', trans('mail.message_not_sent'));
        }

        return redirect(cms_route('bugReport.index'))->with('alert', $message);
    }
}
