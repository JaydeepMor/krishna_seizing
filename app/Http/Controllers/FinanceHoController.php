<?php

namespace App\Http\Controllers;

use App\FinanceHo;
use App\City;
use Illuminate\Http\Request;

class FinanceHoController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $modal = new FinanceHo();

        $query = $modal::query();

        $query->select($modal::getTableName() . '.*');

        if ($request->has('name') && !empty($request->get('name'))) {
            $query->where($modal::getTableName() . '.name', 'LIKE', '%' . $request->get('name') . '%');
        }

        if ($request->has('vendor_code') && !empty($request->get('vendor_code'))) {
            $query->where($modal::getTableName() . '.vendor_code', 'LIKE', '%' . $request->get('vendor_code') . '%');
        }

        if ($request->has('city_name') && !empty($request->get('city_name'))) {
            $cityModal = new City();

            $query->join($cityModal::getTableName(), $modal::getTableName() . '.city_id', '=', $cityModal::getTableName() . '.id')
                  ->where($cityModal::getTableName() . '.name', 'LIKE', '%' . $request->get('city_name') . '%');
        }

        if ($request->has('gst_number') && !empty($request->get('gst_number'))) {
            $query->where($modal::getTableName() . '.gst_number', '=', $request->get('gst_number'));
        }

        if ($request->has('contact_person') && !empty($request->get('contact_person'))) {
            $query->where($modal::getTableName() . '.contact_person', 'LIKE', '%' . $request->get('contact_person') . '%');
        }

        $financeHos = $query->paginate(parent::DEFAULT_PAGINATION_SIZE);

        return view('finance_ho.index', compact('financeHos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cities = City::all();

        return view('finance_ho.create', compact('cities'));
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

        $model     = new FinanceHo();
        
        $validator = $model->validator($data);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $model::create($data);

        return redirect()->route('ho.index')->with('success', __('Record added successfully!'));
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
        $row = FinanceHo::find($id);

        if (empty($row)) {
            return redirect(url()->previous())->with('danger', __('No record found!'));
        }

        $cities = City::all();

        return view('finance_ho.edit', compact('row', 'cities'));
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
        $data      = $request->all();

        $model     = new FinanceHo();

        $validator = $model->validator($data, $id);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $row       = $model::find($id);

        if (empty($row)) {
            return redirect()->route('ho.index')->with('danger', __('No record found!'));
        }

        $row->update($data);

        return redirect()->route('ho.index')->with('success', __('Record updated successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        FinanceHo::where('id', $id)->delete();

        return redirect()->route('ho.index')->with('success', __('Record deleted successfully!'));
    }
}
