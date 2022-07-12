@include('frontend-partials.head', ['page' => 'register'])
@include('frontend-partials.header')


    <section>
        <div class="tr-register">
            <div class="tr-regi-form col-md-offset-3 col-md-6">
                <h4>{{__('createAnAccount')}}</h4>
                <p>{{__('register1')}}</p>
                <form class="col s12">
                    <div class="row">
                        <div class="input-field col m6 s12">
                            <input type="text" class="validate">
                            <label>{{__('firstName')}}</label>
                        </div>
                        <div class="input-field col m6 s12">
                            <input type="text" class="validate">
                            <label>{{__('lastName')}}</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <input type="number" class="validate">
                            <label>{{__('phone')}}</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <input type="email" class="validate">
                            <label>{{__('email')}}</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <input type="password" class="validate">
                            <label>{{__('password')}}</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <input type="password" class="validate">
                            <label>{{__('confirmPassword')}}</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <input type="submit" value="{{__('submit1')}}" class="waves-effect waves-light btn-large full-btn"> </div>
                    </div>
                </form>
                <p>{{__('alreadyMember')}} <a href="login.html">{{__('clickToLogin')}}</a>
                </p>
            </div>
        </div>
    </section>


@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'register'])

