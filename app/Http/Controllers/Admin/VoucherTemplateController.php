<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\VoucherTemplateRequest;
use App\Language;
use App\VoucherTemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VoucherTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $voucherTemplates = VoucherTemplate::all();
        return view('panel.voucher-template.index', compact('voucherTemplates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $langs = Language::all();
        return view('panel.voucher-template.create', compact('langs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(VoucherTemplateRequest $request)
    {
        try {
            $data = $request->validated();
            VoucherTemplate::create($data);
            return redirect('/voucher-template')->with('message', 'Voucher Template Created Successfully!');
        } catch (\Exception $e) {
            return redirect('/voucher-template')->with('error', 'Error Data: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return "voucher template show";
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $langs = Language::all();
        $template = VoucherTemplate::findOrFail($id);
        return view('panel.voucher-template.edit', compact('langs','template'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(VoucherTemplateRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $voucher = VoucherTemplate::find($id);
            $data['default'] = $data['default'] === "on" ? 1 : 0;
            if($data['default']){
                VoucherTemplate::where('id', '<>', $voucher->id)->update(['default' => 0]);
            }
            $voucher->update($data);
            return redirect('/voucher-template')->with('message', 'Voucher Template Updated Successfully!');
        } catch (\Exception $e) {
            return redirect('/voucher-template')->with('error', 'Error Data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            VoucherTemplate::findOrFail($id)->delete();
            return redirect()->with('message', 'Voucher Template Deleted Successfully!');
        }catch (\Exception $e){
           return  redirect()->back()->with('error', 'An Error Occured '.$e->getMessage());
        }
    }
}
