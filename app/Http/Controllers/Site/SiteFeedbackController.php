<?php

namespace App\Http\Controllers\Site;

use Exception;
use Models\Page;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Mail\Mailer;
use App\Http\Requests\Site\FeedbackRequest;

class SiteFeedbackController extends Controller
{
    /**
     * The Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Display the specified resource.
     *
     * @param  \Models\Page  $page
     * @return Response
     */
    public function index(Page $page)
    {
        $data['current'] = $page;

        $data['files'] = $page->getFiles($page->id);

        return view('site.' . $page->type, $data);
    }

    /**
     * Send an e-mail.
     *
     * @param  \Illuminate\Contracts\Mail\Mailer  $mail
     * @param  \App\Http\Requests\Site\FeedbackRequest  $request
     * @return Response
     */
    public function send(Mailer $mail, FeedbackRequest $request)
    {
        $siteSettings = app('db')->table('site_settings')->first();

        $data = $request->only(['name', 'email', 'phone', 'text']);

        $email = $siteSettings->email;

        $subject = $request->getHost() . ' - feedback';

        try {
            $result = $mail->send('site.mail.feedback', $data, function ($m) use ($data, $email, $subject) {
                $m->from(config('mail.username'), $this->request->getHost())
                  ->to($email)
                  ->subject($subject);
            });

            $message = fill_data(true, trans('message_success'));
        } catch (Exception $e) {
            $message = fill_data(false, trans('message_failed'));
        }

        return redirect()->back()->with('alert', $message);
    }
}
