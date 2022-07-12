@include('frontend-partials.head', ['page' => 'reset'])
@include('frontend-partials.header')

<section>
    <div class="spe-title col-md-12">
        <h2>Reset Password</h2>
        <div class="title-line">
            <div class="tl-1"></div>
            <div class="tl-2"></div>
            <div class="tl-3"></div>
        </div>
    </div>
    <div class="container py-5" style="margin-top: 50px; margin-bottom: 50px;">
        <div class="row">
            <div class="col-lg-12 mx-auto">
                <input type="hidden" id="translationArray" value="{{$translationArray}}">
                <input type="hidden" id="hiddenEmailForPasswordReset" value="{{$email}}">
                <input type="hidden" id="hiddenTokenForPasswordReset" value="{{$token}}">
                <div id="nav-tab-card" class="">
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            <input type="password" name="password" id="passwordForResetPassword" placeholder="Please Type Your Password" required class="form-control">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            <input type="password" name="passwordConfirmation" id="passwordConfirmationForResetPassword" placeholder="Please Type Your Password Again" required class="form-control">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-12">
                            <button id="sendPasswordForPasswordReset" class="btn btn-primary col-md-offset-4 col-md-4">Save New Password</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('frontend-partials.general-scripts', ['page' => 'reset'])
