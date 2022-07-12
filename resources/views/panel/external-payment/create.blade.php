@include('panel-partials.head', ['page' => 'external-payment-create'])
@include('panel-partials.header', ['page' => 'external-payment-create'])
@include('panel-partials.sidebar')


<div class="sb2-2-2">
    <ul>
        <li><a href="{{url('/')}}"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"><a href="#"> External Payment</a></li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
    </ul>
</div>
<div class="sb2-2-add-blog sb2-2-1">
    <div class="box-inn-sp">
        <div class="inn-title">
            <h4>External Payment</h4>
        </div>
        <div class="bor">
            <div class="row">
                <div class="input-field col s4">
                    <input id="email" type="email" class="validate">
                    <label for="email">Email</label>
                </div>
                <div class="input-field col s4">
                    <input id="price" type="number" class="validate" step="any">
                    <label for="price">Price</label>
                </div>
                <div class="input-field col s4">
                    <select name="currency" id="currency">
                        <option value="978">EUR</option>
                        <option value="840">USD</option>
                        <option value="949">TRY</option>
                        <option value="826">GBP</option>
                    </select>
                    <label for="price">Currency</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <textarea id="message" class="materialize-textarea"></textarea>
                    <label for="message">Your Message:</label>
                </div>
            </div>
            <div class="row">
                <div class="col s4">
                    <label for="bookings">Booking Reference Code</label>
                    <select id="bookings" class="select2 browser-default custom-select select2-hidden-accessible">
                        <option selected value="">Choose a Booking</option>
                        @foreach($bookingsToBeSelected as $booking)
                            <option value={{$booking}}>{{$booking}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input type="button" class="btn btn-large" value="Send Payment Link" id="sendPaymentLinkButton">
                </div>
                <div id="paymentLinkDiv" style="display:none;">
                    <div class="input-field col s6">
                        <input id="paymentLink" value="">
                    </div>
                    <div class="input-field col s6">
                        <button class="btn copyToClipboard" data-clipboard-target="#paymentLink">Copy</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'external-payment-create'])
