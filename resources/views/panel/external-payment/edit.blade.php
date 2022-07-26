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
            <form action="{{ url('/external-payment/' . $externalPayment->id) }}" method="post">
                @csrf
                <div class="row">

                    <div class="input-field col s12">
                        <input id="link" type="text" readonly class="validate" value="{{ $externalPayment->payment_link }}">
                        <label for="email">Link</label>
                    </div>

                    <div class="input-field col s4">
                        <input id="email" type="text" readonly class="validate" value="{{ $externalPayment->email }}">
                        <label for="email">Email</label>
                    </div>
                    <div class="input-field col s4">
                        <input id="price" type="number" class="validate" step="any" name="price" value="{{ $externalPayment->price }}">
                        <label for="price">Price</label>
                    </div>

                    <div class="input-field col s4">
                        <select name="currency" id="currency">
                            <option value="978" @if($externalPayment->currency == 978) selected @endif>EUR</option>
                            <option value="840" @if($externalPayment->currency == 840) selected @endif>USD</option>
                            <option value="949" @if($externalPayment->currency == 949) selected @endif>TRY</option>
                            <option value="826" @if($externalPayment->currency == 826) selected @endif>GBP</option>
                        </select>
                        <label for="price">Currency</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12">
                        <textarea id="message" name="message" class="materialize-textarea">{{ $externalPayment->message }}</textarea>
                        <label for="message">Your Message:</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col s4">
                        <label for="bookings">Booking Reference Code</label>
                        <select id="bookings" name="bookingRefCode" class="select2 browser-default custom-select select2-hidden-accessible">
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="input-field col s12">
                        <button type="submit" class="btn btn-primary">
                            Update
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'external-payment-edit'])
