@include('panel-partials.head', ['page' => 'attraction-edit'])
@include('panel-partials.header', ['page' => 'attraction-edit'])
@include('panel-partials.sidebar')


<div class="sb2-2-2">
    <ul>
        <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"><a href="#"> Edit Ticket Type</a></li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
    </ul>
</div>
<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Edit {{$ticketType->name}} Ticket Type</h4>
                </div>
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif

                    @if(session('warnTicket'))
                    <div class="alert alert-danger">
                        {{ session('warnTicket') }}
                    </div>
                    @endif
                <div class="tab-inn">
                    <form action="{{url('ticket-type/'.$ticketType->id.'/update')}}" enctype="multipart/form-data" method="POST">
                        @csrf
                        @method('POST')
                        <div style="text-align: center" class="row">
                            <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <input id="name" type="text" value="{{$ticketType->name}}" class="validate @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <label for="name">Ticket Type Name</label>
                            </div>
                            <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <input id="warnTicket" type="number" value="{{$ticketType->warnTicket}}" min="0" class="validate @error('warnTicket') is-invalid @enderror" name="warnTicket" value="{{ old('warnTicket') }}" required autocomplete="warnTicket" autofocus>
                                @error('warnTicket')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <label for="warnTicket">Minimum Ticket Number</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <select name="type" class="@error('type') invalid @enderror">
                                    <option value="" selected disabled>- Select Ticket Type Code -</option>
                                    <option value="QRCODE" @if(old('type') == 'QRCODE' || $ticketType->type == 'QRCODE') selected @endif>QrCode</option>
                                    <option value="BARCODE" @if(old('type') == 'BARCODE' || $ticketType->type == 'BARCODE') selected @endif>Barcode</option>
                                </select>
                                @error('type')
                                <span class="text-danger text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="input-field col s6" style="display: @if($ticketType->type  != 'BARCODE' && old('type') != 'BARCODE') none  @endif">
                                <select name="format" class="@error('format') invalid @enderror">
                                    <option value="" selected disabled>- Select Type Code -</option>
                                    <option value="TYPE_CODE_39" @if(old('format') == 'TYPE_CODE_39' || $ticketType->format == 'TYPE_CODE_39') selected @endif>C39</option>
                                    <option value="TYPE_CODE_39_CHECKSUM" @if(old('format') == 'TYPE_CODE_39_CHECKSUM' || $ticketType->format == 'TYPE_CODE_39_CHECKSUM') selected @endif>C39+</option>
                                    <option value="TYPE_CODE_39E" @if(old('format') == 'TYPE_CODE_39E' || $ticketType->format == 'TYPE_CODE_39E') selected @endif>C39E</option>
                                    <option value="TYPE_CODE_39E_CHECKSUM" @if(old('format') == 'TYPE_CODE_39E_CHECKSUM' || $ticketType->format == 'TYPE_CODE_39E_CHECKSUM') selected @endif>C39E+</option>
                                    <option value="TYPE_CODE_93" @if(old('format') == 'TYPE_CODE_93' || $ticketType->format == 'TYPE_CODE_93') selected @endif>C93</option>
                                    <option value="TYPE_STANDARD_2_5" @if(old('format') == 'TYPE_STANDARD_2_5' || $ticketType->format == 'TYPE_STANDARD_2_5') selected @endif>S25</option>
                                    <option value="TYPE_STANDARD_2_5_CHECKSUM" @if(old('format') == 'TYPE_STANDARD_2_5_CHECKSUM' || $ticketType->format == 'TYPE_STANDARD_2_5_CHECKSUM') selected @endif>S25+</option>
                                    <option value="TYPE_INTERLEAVED_2_5" @if(old('format') == 'TYPE_INTERLEAVED_2_5' || $ticketType->format == 'TYPE_INTERLEAVED_2_5') selected @endif>I25</option>
                                    <option value="TYPE_INTERLEAVED_2_5_CHECKSUM" @if(old('format') == 'TYPE_INTERLEAVED_2_5_CHECKSUM' || $ticketType->format == 'TYPE_INTERLEAVED_2_5_CHECKSUM') selected @endif>I25+</option>
                                    <option value="TYPE_CODE_128" @if(old('format') == 'TYPE_CODE_128' || $ticketType->format == 'TYPE_CODE_128') selected @endif>C128 (Recommended)</option>
                                    <option value="TYPE_CODE_128_A" @if(old('format') == 'TYPE_CODE_128_A' || $ticketType->format == 'TYPE_CODE_128_A') selected @endif>C128A</option>
                                    <option value="TYPE_CODE_128_B" @if(old('format') == 'TYPE_CODE_128_B' || $ticketType->format == 'TYPE_CODE_128_B') selected @endif>C128B</option>
                                    <option value="TYPE_CODE_128_C" @if(old('format') == 'TYPE_CODE_128_C' || $ticketType->format == 'TYPE_CODE_128_C') selected @endif>C128C</option>
                                    <option value="TYPE_EAN_2" @if(old('format') == 'TYPE_EAN_2' || $ticketType->format == 'TYPE_EAN_2') selected @endif>EAN2</option>
                                    <option value="TYPE_EAN_5" @if(old('format') == 'TYPE_EAN_5' || $ticketType->format == 'TYPE_EAN_5') selected @endif>EAN5</option>
                                    <option value="TYPE_EAN_8" @if(old('format') == 'TYPE_EAN_8' || $ticketType->format == 'TYPE_EAN_8') selected @endif>EAN8</option>
                                    <option value="TYPE_EAN_13" @if(old('format') == 'TYPE_EAN_13' || $ticketType->format == 'TYPE_EAN_13') selected @endif>EAN13</option>
                                    <option value="TYPE_UPC_A" @if(old('format') == 'TYPE_UPC_A' || $ticketType->format == 'TYPE_UPC_A') selected @endif>UPCA</option>
                                    <option value="TYPE_UPC_E" @if(old('format') == 'TYPE_UPC_E' || $ticketType->format == 'TYPE_UPC_E') selected @endif>UPCE</option>
                                    <option value="TYPE_MSI" @if(old('format') == 'TYPE_MSI' || $ticketType->format == 'TYPE_MSI') selected @endif>MSI</option>
                                    <option value="TYPE_MSI_CHECKSUM" @if(old('format') == 'TYPE_MSI_CHECKSUM' || $ticketType->format == 'TYPE_MSI_CHECKSUM') selected @endif>MSI+</option>
                                    <option value="TYPE_POSTNET" @if(old('format') == 'TYPE_POSTNET' || $ticketType->format == 'TYPE_POSTNET') selected @endif>POSTNET</option>
                                    <option value="TYPE_PLANET" @if(old('format') == 'TYPE_PLANET' || $ticketType->format == 'TYPE_PLANET') selected @endif>PLANET</option>
                                    <option value="TYPE_RMS4CC" @if(old('format') == 'TYPE_RMS4CC' || $ticketType->format == 'TYPE_RMS4CC') selected @endif>RMS4CC</option>
                                    <option value="TYPE_KIX" @if(old('format') == 'TYPE_KIX' || $ticketType->format == 'TYPE_KIX') selected @endif>KIX</option>
                                    <option value="TYPE_IMB" @if(old('format') == 'TYPE_IMB' || $ticketType->format == 'TYPE_IMB') selected @endif>IMB</option>
                                    <option value="TYPE_CODABAR" @if(old('format') == 'TYPE_CODABAR' || $ticketType->format == 'TYPE_CODABAR') selected @endif>CODABAR</option>
                                    <option value="TYPE_CODE_11" @if(old('format') == 'TYPE_CODE_11' || $ticketType->format == 'TYPE_CODE_11') selected @endif>CODE11</option>
                                    <option value="TYPE_PHARMA_CODE" @if(old('format') == 'TYPE_PHARMA_CODE' || $ticketType->format == 'TYPE_PHARMA_CODE') selected @endif>PHARMA</option>
                                    <option value="TYPE_PHARMA_CODE_TWO_TRACKS" @if(old('format') == 'TYPE_PHARMA_CODE_TWO_TRACKS' || $ticketType->format == 'TYPE_PHARMA_CODE_TWO_TRACKS') selected @endif>PHARMA2T</option>
                                </select>
                                @error('format')
                                <span class="text-danger text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <input type="submit" class="btn btn-primary large btn-large" value="Update" style="padding: 10px; font-size: 18px; height: 50px;">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'attraction-edit'])
<script>
    $(document).ready(function () {
        $('select[name="type"]').on('change', function () {
            const $type_code = $('select[name="format"]').closest('.input-field');

            if ($(this).val() === 'BARCODE') {
                $type_code.show()
            } else {
                $type_code.hide()
            }
        })
    });
</script>
