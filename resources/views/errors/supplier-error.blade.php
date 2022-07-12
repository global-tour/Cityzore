@include('frontend-partials.head', ['page' => 'supplier-error'])
@include('frontend-partials.header')

    <section>
        <div class="rows tb-space pad-top-o pad-bot-redu" style="padding-top: 4%;">
            <div class="container">
                <div class="col-md-6">
                    <div class="spe-title">
                        <p>Your Account is not Active yet, If you have a question please contact with us:</p>
                        <p style="font-weight: bold;color: #253d52;">Contact Form</p>
                    </div>
                    <div class="col-md-8" style="margin-right: 15%;margin-left: 15%;">
                        <form>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Email address</label>
                                <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
                            </div>
                            <div class="form-group">
                                <label for="text">Your Message</label>
                                <input type="text" class="form-control" id="text" placeholder="Your Message" style="height: 100px;">
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
                <div class="col-md-6">
                    <img src="https://image.freepik.com/free-photo/black-friday-alarm-clock-mock-up_23-2148283740.jpg" style="width:100%;">
                </div>
            </div>
        </div>
    </section>

@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'supplier-error'])

