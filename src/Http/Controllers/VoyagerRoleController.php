<?php

namespace Navia\Http\Controllers;

use Illuminate\Http\Request;
use Navia\Facades\Navia;

class VoyagerRoleController extends VoyagerBaseController
{
    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Navia::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('edit', app($dataType->model_name));

        //Validate fields
        $val = $this->validateBread($request->all(), $dataType->editRows, $dataType->name, $id)->validate();

        $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);
        $this->insertUpdateData($request, $slug, $dataType->editRows, $data);

        $data->permissions()->sync($request->input('permissions', []));

        return redirect()
            ->route("navia.{$dataType->slug}.index")
            ->with([
                'message'    => __('navia::generic.successfully_updated')." {$dataType->getTranslatedAttribute('display_name_singular')}",
                'alert-type' => 'success',
            ]);
    }

    // POST BRE(A)D
    public function store(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Navia::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        //Validate fields
        $val = $this->validateBread($request->all(), $dataType->addRows)->validate();

        $data = new $dataType->model_name();
        $this->insertUpdateData($request, $slug, $dataType->addRows, $data);

        $data->permissions()->sync($request->input('permissions', []));

        return redirect()
            ->route("navia.{$dataType->slug}.index")
            ->with([
                'message'    => __('navia::generic.successfully_added_new')." {$dataType->getTranslatedAttribute('display_name_singular')}",
                'alert-type' => 'success',
            ]);
    }
}
