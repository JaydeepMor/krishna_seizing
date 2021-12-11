<?php

namespace App\Http\Controllers;

use App\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class ConstantController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $modal = new Constant();

        $query = $modal::query();

        $query->select($modal::getTableName() . '.*');

        if ($request->has('key_value') && !empty($request->get('key_value'))) {
            $query->where($modal::getTableName() . '.key', 'LIKE', '%' . $request->get('key_value') . '%')
                  ->orWhere($modal::getTableName() . '.value', 'LIKE', '%' . $request->get('key_value') . '%');
        }

        $constants = $query->paginate(parent::DEFAULT_PAGINATION_SIZE);

        return view('constant.index', compact('constants'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('constant.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data      = $request->all();

        $model     = new Constant();
        
        $validator = $model->validator($data);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $model::create($data);

        return redirect()->route('constant.index')->with('success', __('Record added successfully!'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $row = Constant::find($id);

        if (empty($row)) {
            return redirect(url()->previous())->with('danger', __('No record found!'));
        }

        return view('constant.edit', compact('row'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data        = $request->all();

        $model       = new Constant();

        $uploadedApp = $pathInfos = NULL;

        if (!empty($data['key']) && $data['key'] == 'RELEASED_APPLICATION') {
            if (!empty($data['value']) && $data['value'] instanceof UploadedFile) {
                $uploadedApp = $data['value'];
                $pathInfos   = pathinfo($uploadedApp->getClientOriginalName());

                if (!empty($pathInfos['basename'])) {
                    $data['value'] = $pathInfos['basename'];
                }
            }
        }

        $validator = $model->validator($data, $id);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $row = $model::find($id);

        if ($data['key'] == 'RELEASED_APPLICATION') {
            $folder = $model->appPath;

            if (!empty($folder)) {
                $fileName  = (empty($pathInfos['filename']) ? time() : $pathInfos['filename']) . '_' . time() . '.' . $pathInfos['extension'];
                $fileName  = removeSpaces($fileName);
                $storeFile = $uploadedApp->storeAs($folder, $fileName, $model->fileSystem);

                if (!empty($storeFile)) {
                    $data['value'] = $fileName;

                    $row->update($data);

                    return redirect()->route('constant.index')->with('success', __('Record updated successfully!'));
                }
            }

            return redirect()->route('constant.index')->with('danger', __('Something went wrong! Please try again.'));
        } else {
            $row->update($data);
        }

        if (empty($row)) {
            return redirect()->route('constant.index')->with('danger', __('No record found!'));
        }

        return redirect()->route('constant.index')->with('success', __('Record updated successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        /* Constant::where('id', $id)->delete();

        return redirect()->route('constant.index')->with('success', __('Record deleted successfully!')); */

        return redirect()->route('constant.index')->with('danger', __('This operation does not allow yet.'));
    }
}
