<?php

namespace App\Http\Controllers;

use App\Group;
use App\GroupFinance;
use App\FinanceCompany;
use Illuminate\Http\Request;
use DB;

class GroupController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $modal = new Group();

        $query = $modal::query();

        $query->select($modal::getTableName() . '.*');

        if ($request->has('name') && !empty($request->get('name'))) {
            $query->where($modal::getTableName() . '.name', 'LIKE', '%' . $request->get('name') . '%');
        }

        if ($request->has('status') && !is_null($request->get('status')) && in_array($request->get('status'), array_keys($modal->statuses))) {
            $query->where($modal::getTableName() . '.status', '=', $request->get('status'));
        }

        $groups = $query->paginate(parent::DEFAULT_PAGINATION_SIZE);

        return view('group.index', compact('modal', 'groups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $modal            = new Group();

        $financeCompanies = FinanceCompany::all();

        return view('group.create', compact('modal', 'financeCompanies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $data              = $request->all();

            $modelGroup        = new Group();

            $modelGroupFinance = new GroupFinance();

            $validator = $modelGroup->validator($data);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $group = $modelGroup::create($data);

            if (!empty($data['finance_company_id'])) {
                $groupFinanceData = ['group_id' => $group->id, 'finance_company_id' => $data['finance_company_id']];

                $validator = $modelGroupFinance->validator($groupFinanceData);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();

                    DB::rollback();
                }

                foreach ($data['finance_company_id'] as $financeCompanyId) {
                    $modelGroupFinance::create([
                        'group_id'           => $group->id,
                        'finance_company_id' => $financeCompanyId
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('group.index')->with('success', __('Record added successfully!'));
        } catch (\Throwable $e) {
            DB::rollback();
        }

        return redirect()->route('group.index')->with('danger', __('Something went wrong! Please contact superadmin.'));
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
        $modal = new Group();

        $row   = Group::find($id);

        if (empty($row)) {
            return redirect(url()->previous())->with('danger', __('No record found!'));
        }

        $financeCompanies = FinanceCompany::all();

        return view('group.edit', compact('modal', 'row', 'financeCompanies'));
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
        DB::beginTransaction();

        try {
            $data              = $request->all();

            $modelGroup        = new Group();

            $modelGroupFinance = new GroupFinance();

            $validator = $modelGroup->validator($data);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $row = $modelGroup::find($id);

            if (empty($row)) {
                return redirect()->route('group.index')->with('danger', __('No record found!'));
            }

            $row->update($data);

            if (!empty($data['finance_company_id'])) {
                $groupFinanceData = ['group_id' => $id, 'finance_company_id' => $data['finance_company_id']];

                $validator = $modelGroupFinance->validator($groupFinanceData);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();

                    DB::rollback();
                }

                $modelGroupFinance::where('group_id', $id)->delete();

                foreach ($data['finance_company_id'] as $financeCompanyId) {
                    $modelGroupFinance::create([
                        'group_id'           => $id,
                        'finance_company_id' => $financeCompanyId
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('group.index')->with('success', __('Record updated successfully!'));
        } catch (\Throwable $e) {
            DB::rollback();
        }

        return redirect()->route('group.index')->with('danger', __('Something went wrong! Please contact superadmin.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        Group::where('id', $id)->delete();

        GroupFinance::where('group_id', $id)->delete();

        return redirect()->route('group.index')->with('success', __('Record deleted successfully!'));
    }
}
