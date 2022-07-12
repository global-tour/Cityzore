<?php

namespace App\Http\Controllers\Admin;

use App\Adminlog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Language;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class LocalizationController extends Controller
{

    public $langCodes = [
        'bg' => 'Bulgarian',
        'cs' => 'Czech',
        'da' => 'Danish',
        'de' => 'German',
        'el' => 'Greek',
        'es' => 'Spanish',
        'et' => 'Estonian',
        'fi' => 'Finnish',
        'fr' => 'French',
        'hi' => 'Hindi',
        'hr' => 'Croatian',
        'hu' => 'Hungarian',
        'it' => 'Italian',
        'ja' => 'Japanese',
        'lt' => 'Lithuanian',
        'lv' => 'Latvian',
        'mk' => 'Macedonian',
        'nl' => 'Dutch',
        'no' => 'Norwegian',
        'pl' => 'Polish',
        'pt' => 'Portuguese',
        'ro' => 'Romanian',
        'ru' => 'Russian',
        'sk' => 'Slovak',
        'sl' => 'Slovenian',
        'sr' => 'Serbian',
        'sv' => 'Swedish',
        'uk' => 'Ukrainian',
        'zh' => 'Chinese'
    ];

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function languages()
    {
        $langs = Language::all();
        return view('panel.language.index',
            [
                'langs' => $langs
            ]
        );
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createLang()
    {
        $langCodes = $this->langCodes;
        return view('panel.language.create',
            [
                'langCodes' => $langCodes
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function storeLang(Request $request)
    {
        $lang = new Language();
        $lang->fileName = $request->langCode . '.json';
        $lang->name = $this->langCodes[$request->langCode];
        $lang->displayName = $request->displayName;
        $lang->code = $request->langCode;
        $lang->isActive = 0;
        $user = Auth::guard('admin')->user();
        $adminLog = new Adminlog();
        $adminLog->userID = $user->id;
        $adminLog->page = 'Localization';
        $adminLog->url = $request->fullUrl();
        $adminLog->action = 'Added New Language';
        $adminLog->details = $user->name. ' added '. $lang->name. ' language';
        $adminLog->tableName = 'languages';
        $adminLog->columnName = 'code';
        if ($lang->save()) {
            $adminLog->result = 'successful';
            $adminLog->save();
            File::copy(base_path(). '/resources/lang/en.json', base_path(). '/resources/lang/'.$lang->fileName);
            return redirect('/languages');
        } else {
            $adminLog->result = 'failed';
            $adminLog->save();
        }

    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editLang($id)
    {
        $lang = Language::findOrFail($id);
        $langArr = file_get_contents(base_path().'/resources/lang/'.$lang->fileName, true);
        $langArr = json_decode($langArr, true);
        $pageCount = ceil(count($langArr) / 10);
        $langArr = array_splice($langArr, 0, 10);
        return view('panel.language.edit',
            [
                'lang' => $lang,
                'langArr' => $langArr,
                'pageCount' => $pageCount
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function nextPrevPage(Request $request)
    {
        $keyValues = $request->keyValues;
        $step = $request->step;
        $lang = Language::findOrFail($request->languageID);
        $langArr = file_get_contents(base_path().'/resources/lang/'.$lang->fileName, true);
        $langArr = json_decode($langArr, true);
        //Save new values
        foreach ($langArr as $key => $line) {
            if (array_key_exists($key, $keyValues)) {
                $langArr[$key] = is_null($keyValues[$key]) ? '' : $keyValues[$key];
            }
        }
        file_put_contents(base_path().'/resources/lang/' . $lang->fileName, json_encode($langArr));
        //
        $value = $request->value;
        if ($value != '') {
            $langArr = $this->array_search_partial($langArr, $value);
        }
        $totalCount = count($langArr);
        $langArr = array_splice($langArr, ($step * 10), 10);
        return response()->json(['success' => 'Next step is retrieved successfully', 'langArr' => $langArr, 'isEnd' => $totalCount <= (($step + 1) * 10)]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateLang(Request $request)
    {
        $jsonString = file_get_contents(base_path().'/resources/lang/' . $request->fileName, true);
        $langArr = json_decode($jsonString, true);
        foreach ($langArr as $key => $line) {
            if ($request->has($key)) {
                $langArr[$key] = is_null($request->$key) ? '' : $request->$key;
            }
        }
        file_put_contents(base_path().'/resources/lang/' . $request->fileName, json_encode($langArr));
        return redirect('/languages');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setIsActive(Request $request)
    {
        $lang = Language::findOrFail($request->id);
        $lang->isActive = $request->isActive;
        $activeStatus = $lang->isActive == 0 ? ' deactivated ' : ' activated ';
        $user = Auth::guard('admin')->user();
        $adminLog = new Adminlog();
        $adminLog->userID = $user->id;
        $adminLog->page = 'Localization';
        $adminLog->url = $request->fullUrl();
        $adminLog->action = 'Activated/Deactivated Language';
        $adminLog->details = $user->name. $activeStatus . $lang->name. ' language';
        $adminLog->tableName = 'languages';
        $adminLog->columnName = 'isActive';
        if ($lang->save()) {
            $adminLog->result = 'successful';
        } else {
            $adminLog->result = 'failed';
        }
        $adminLog->save();

        return response()->json(['success'=>'Status changed successfully.']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $lang = Language::findOrFail($request->languageID);
        $langArr = file_get_contents(base_path().'/resources/lang/'.$lang->fileName, true);
        $langArr = json_decode($langArr, true);
        if ($request->value != '') {
            $langArr = $this->array_search_partial($langArr, $request->value);
        }
        $totalCount = count($langArr);
        $langArr = array_splice($langArr, 0, 10);
        return response()->json(['success' => 'Searched key-values are fetched successfully!', 'langArr' => $langArr, 'isEnd' => $totalCount <= 10]);
    }

    /**
     * @param $arr
     * @param $keyword
     * @return array
     */
    public function array_search_partial($arr, $keyword)
    {
        $foundStrs = [];
        foreach ($arr as $index => $string) {
            if (strpos(strtolower($index), strtolower($keyword)) != false || strpos(strtolower($string), strtolower($keyword)) !== false) {
                $foundStrs[$index] = $string;
            }
        }
        return $foundStrs;
    }

}
