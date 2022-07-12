<?php

namespace App\Http\Controllers\Admin;

use App\Adminlog;
use App\Platform;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlatformController extends Controller
{
    public function index()
    {
        $platforms = Platform::all();

        return view('panel.platforms.index',['platforms'=> $platforms]);
    }

    public function create()
    {

        return view('panel.platforms.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3|max:120',
            'color' => 'required|min:3|max:120',
            'colorBg' => 'required|min:3|max:120',
        ]);
        $platform_id = Platform::insertGetId([
            'name' => $request->name,
            'color' => $request->color,
            'colorBg' => $request->colorBg,
        ]);
        $user = Auth::guard('admin')->user();

        $logDetail=$user->name.' '.$platform_id.' number of Platform Created With name='.$request->name.' color='.$request->color.' colorBg='.$request->colorBg;
        $adminLog = new AdminLog();
        $adminLog->userID = $user->id;
        $adminLog->page = 'Platform Add';
        $adminLog->url = $request->fullUrl();
        $adminLog->action = 'ADD';
        $adminLog->details = $logDetail;
        $adminLog->tableName = 'platform';
        $adminLog->columnName = 'name,color,colorBg';
        $platform_id < 0 ? $adminLog->result = 'successful' : $adminLog->result = 'failed('.$platform_id.')';
        $adminLog->save();

        if ($platform_id<0) return redirect('platform')->with(['error' => 'Changes has been not Stored!']);
        return redirect('platform')->with(['success' => 'Changes has been Stored Successfully']);
    }


    public function edit($id)
    {
        $platform=Platform::findOrFail($id);
        return view('panel.platforms.edit', ['platform' => $platform]);
    }
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|min:3|max:120',
        ]);

        $platform = Platform::findOrFail($id);

        $user = Auth::guard('admin')->user();
        $logDetail=$user->name.' '.$id.' number of Platform';

        $isChanged=false;
        $action='Update';

        if ($request->name != $platform->name){
            $logDetail.= ', changed Name '.$platform->name.' to '.$request->name;
            $isChanged=true;
        }
        if ($request->color != $platform->color){
            $logDetail.= ', changed Name '.$platform->color.' to '.$request->color;
            $isChanged=true;
        }
        if ($request->colorBg != $platform->colorBg){
            $logDetail.= ', changed Name '.$platform->colorBg.' to '.$request->colorBg;
            $isChanged=true;
        }
        if(!$isChanged){
            $logDetail.=' NOTHING Change';
            $action='NOTING';
        }
        $adminLog = new AdminLog();
        $adminLog->userID = $user->id;
        $adminLog->page = 'Platform Edit';
        $adminLog->url = $request->fullUrl();
        $adminLog->action = $action;
        $adminLog->details = $logDetail;
        $adminLog->tableName = 'platform';
        $adminLog->columnName = 'name,color,colorBg';

        $platform->name=$request->name;
        $platform->color=$request->color;
        $platform->colorBg=$request->colorBg;

        $saveStatus=$platform->save();

        $saveStatus ? $adminLog->result = 'successful' : $adminLog->result = 'failed';
        $adminLog->save();

        if (!$saveStatus) return redirect('platform')->with(['error' => 'Changes has been Fail!']);

        return redirect('platform')->with(['success' => 'Changes has been done successfully!']);
    }

    public function destroy($id)
    {
        $platform = Platform::findOrFail($id);

        $user = Auth::guard('admin')->user();

        $adminLog = new AdminLog();
        $adminLog->userID = $user->id;
        $adminLog->page = 'Platform Delete';
        $adminLog->url = "deletion";
        $adminLog->action = 'DELETE';
        $adminLog->details = $user->name.' '.$id.' number of Platform DELETED';
        $adminLog->tableName = 'platform';
        $adminLog->columnName = 'name,color,colorBg';

        $delStatus=$platform->delete();
        $delStatus ? $adminLog->result = 'successful' : $adminLog->result = 'failed';
        $adminLog->save();

        if(!$delStatus) return redirect('platform')->with(['error' => 'Platform Deletion Failed']);

        return redirect('platform')->with(['success' => 'Successfully Deleted!']);
    }




}
