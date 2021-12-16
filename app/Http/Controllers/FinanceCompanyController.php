<?php

namespace App\Http\Controllers;

use App\FinanceCompany;
use App\FinanceHo;
use Illuminate\Http\Request;

class FinanceCompanyController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $modal = new FinanceCompany();

        $query = $modal::query();

        $query->select($modal::getTableName() . '.*');

        if ($request->has('name') && !empty($request->get('name'))) {
            $query->where($modal::getTableName() . '.name', 'LIKE', '%' . $request->get('name') . '%');
        }

        if ($request->has('branch_code') && !empty($request->get('branch_code'))) {
            $query->where($modal::getTableName() . '.branch_code', 'LIKE', '%' . $request->get('branch_code') . '%');
        }

        if ($request->has('branch_name') && !empty($request->get('branch_name'))) {
            $query->where($modal::getTableName() . '.branch_name', 'LIKE', '%' . $request->get('branch_name') . '%');
        }

        if ($request->has('contact_person') && !empty($request->get('contact_person'))) {
            $query->where($modal::getTableName() . '.contact_person', 'LIKE', '%' . $request->get('contact_person') . '%');
        }

        if ($request->has('contact_number') && !empty($request->get('contact_number'))) {
            $query->where($modal::getTableName() . '.contact_number', '=', $request->get('contact_number'));
        }

        if ($request->has('id') && !empty($request->get('id'))) {
            $query->where($modal::getTableName() . '.id', '=', $request->get('id'));
        }

        $financeCompanies = $query->paginate(parent::DEFAULT_PAGINATION_SIZE);

        return view('finance_company.index', compact('financeCompanies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $financeHos = FinanceHo::all();

        return view('finance_company.create', compact('financeHos'));
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

        $model     = new FinanceCompany();
        
        $validator = $model->validator($data);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $model::create($data);

        return redirect()->route('company.index')->with('success', __('Record added successfully!'));
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
        $row = FinanceCompany::find($id);

        if (empty($row)) {
            return redirect(url()->previous())->with('danger', __('No record found!'));
        }

        $financeHos = FinanceHo::all();

        return view('finance_company.edit', compact('row', 'financeHos'));
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

        $model     = new FinanceCompany();

        $validator = $model->validator($data, $id);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $row       = $model::find($id);

        if (empty($row)) {
            return redirect()->route('company.index')->with('danger', __('No record found!'));
        }

        $row->update($data);

        return redirect()->route('company.index')->with('success', __('Record updated successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        FinanceCompany::where('id', $id)->delete();

        return redirect()->route('company.index')->with('success', __('Record deleted successfully!'));
    }
}
