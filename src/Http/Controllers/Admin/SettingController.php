<?php

namespace Newnet\Setting\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

abstract class SettingController extends Controller
{
    protected $view;

    public function index()
    {
        $setting = setting()->all();
        $item = json_decode(json_encode($setting));

        return view($this->view, compact('item'));
    }

    public function save(Request $request)
    {
        setting($request->except(['_token']));

        return back()->with('success', __('admin::setting.notification.updated'));
    }
}
