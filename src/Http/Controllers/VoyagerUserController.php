<?php

namespace Navia\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Navia\Facades\Navia;

class VoyagerUserController extends VoyagerBaseController
{
    public function profile(Request $request)
    {
        $route = '';
        $dataType = Navia::model('DataType')->where('model_name', Auth::guard(app('NaviaGuard'))->getProvider()->getModel())->first();
        if (!$dataType && app('NaviaGuard') == 'web') {
            $route = route('navia.users.edit', Auth::user()->getKey());
        } elseif ($dataType) {
            $route = route('navia.'.$dataType->slug.'.edit', Auth::user()->getKey());
        }

        return Navia::view('navia::profile', compact('route'));
    }

    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        if (Auth::user()->getKey() == $id) {
            $request->merge([
                'role_id'                              => Auth::user()->role_id,
                'user_belongstomany_role_relationship' => Auth::user()->roles->pluck('id')->toArray(),
            ]);
        }

        return parent::update($request, $id);
    }
}
