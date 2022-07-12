<?php

namespace App\Http\Controllers\Admin;

use App\FAQ;
use App\FaqCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FaqController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $faqs = FAQ::all();
        return view('panel.faq.faq-index',['faqs' => $faqs]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('panel.faq.faq-create');
    }

    /**
     * @param FAQ $faq
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(FAQ $faq)
    {
        $faqCategories = FaqCategory::all();

        return view('panel.faq.edit', ['faq' => $faq, 'faqCategories' => $faqCategories]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request)
    {
        $faq = FAQ::findOrFail($request->faqID);
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->category = $request->category;
        $faq->save();

        return redirect('faq');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $faq = FAQ::findOrFail($id);
        $faq->delete();

        return redirect('/faq');
    }

    /**
     * @param Request $request
     * @return array
     */
    public function addFaqCategory(Request $request)
    {
        $faqCategories = FaqCategory::all();
        foreach ($faqCategories as $oldFaqCategory) {
            if ($oldFaqCategory->name == $request->name) {
                return ['error' => 'This category name has been recorded before!'];
            }
        }
        $faqCategory = new FaqCategory();
        $faqCategory->name = $request->name;
        $faqCategory->save();
        return ['faqCategory' => $faqCategory];
    }

    /**
     * @return array
     */
    public function getOldFaqCategories()
    {
        $faqCategories = FaqCategory::all();
        return ['faqCategories' => $faqCategories];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function saveFaqQuestionAnswer(Request $request)
    {
        $faqQuestionAnswerArray = $request->faqQuestionAnswerArray;
        $faqCategory = FaqCategory::findOrFail($request->faqCategoryID)->name;
        foreach ($faqQuestionAnswerArray as $faqQuestionAnswer) {
            $faq = new FAQ();
            $faq->question = $faqQuestionAnswer['question'];
            $faq->answer = $faqQuestionAnswer['answer'];
            $faq->category = $request->faqCategoryID;
            $faq->save();
        }

        return ['success' => 'All questions and answers are saved successfully for '.$faqCategory. ' category!'];
    }
}
