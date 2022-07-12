@include('panel-partials.head', ['page' => 'translatecity'])
@include('panel-partials.header', ['page' => 'translatecity'])
@include('panel-partials.sidebar')


<div class="col-md-12">
    <div class="sb2-2-2">
        <ul>
            <li>
                <a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
            </li>
            <li class="active-bre">
                <a href="#"> Translate FAQ from English to {{$languageToTranslate->name}}</a>
            </li>
        </ul>
    </div>
    <div class="sb2-2-add-blog sb2-2-1">
        <h2>Translate FAQ from English to {{$languageToTranslate->name}}</h2>
        <form id="translateCityForm" method="POST" action="{{url('general-config/saveFaqTranslation/'.$faqID.'/'.$languageID)}}" class="form-horizontal form-label-left">
            <input type="hidden" name="_token" class="csrfToken" value="{{ csrf_token() }}">
            <div class="row">
                <div class="col-md-12" style="margin-top: 50px;">
                    <div class="col-md-2">
                        <label>Question</label>
                    </div>
                    <div class="col-md-5">
                        <input readonly class="form-control" type="text" value="{{$faq->question}}" name="questionEnglish" id="questionEnglish">
                    </div>
                    <div class="col-md-5">
                        <input class="form-control" type="text" value="@if($faqTranslation) {{$faqTranslation->question}} @endif" name="question" id="question">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12" style="margin-top: 50px;">
                    <div class="col-md-2">
                        <label>Answer</label>
                    </div>
                    <div class="col-md-5">
                        <input readonly class="form-control" type="text" value="{{$faq->answer}}" name="answerEnglish" id="answerEnglish">
                    </div>
                    <div class="col-md-5">
                        <input class="form-control" type="text" value="@if($faqTranslation) {{$faqTranslation->answer}} @endif" name="answer" id="answer">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input type="submit" class="btn btn-large bnt-primary" value="Save Translation">
                </div>
            </div>
        </form>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'translatecity'])
