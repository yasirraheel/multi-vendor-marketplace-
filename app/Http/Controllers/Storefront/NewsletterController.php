<?php

namespace App\Http\Controllers\Storefront;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Newsletter\Facades\Newsletter;
use App\Http\Requests\Validations\NewsletterSubscribeRequest;

class NewsletterController extends Controller
{
    public function subscribe(NewsletterSubscribeRequest $request)
    {
        // if ( ! Newsletter::isSubscribed($request->input('email')) ) {
        Newsletter::subscribeOrUpdate($request->input('email'));

        return back()->with('success', trans('theme.notify.subscribed'));
        // }

        // return back()->with('info', trans('theme.notify.already_subscribed'));
    }
}
