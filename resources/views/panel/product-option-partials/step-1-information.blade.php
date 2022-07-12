<div class="tab-pane fade in active option-setup-content" id="step1">
    <div class="form-group">
        <div class="input-field col s12">
            <input id="opt_title" name="opt_title" type="text" class="validate form-control">
            <label for="opt_title">Title</label>
        </div>
        <span class="opt_titleErrorSpan col s12 errorSpan" style="display: none!important; color: #ff0000;">This field is required.</span>
    </div>
    <div class="form-group">
        <div class="input-field col s12">
            <textarea id="opt_desc" name="opt_desc" type="text" class="materialize-textarea form-control"></textarea>
            <label for="opt_desc">Description</label>
        </div>
        <span class="opt_descErrorSpan col s12 errorSpan" style="display: none!important; color: #ff0000;">This field is required.</span>
    </div>
    <div class="form-group">
        <label for="opt_included">What's Included</label>
        <div class="input-field col s12">
            <input name="opt_included" id="opt_included" type="text" class="tags form-control"/>
        </div>
        <span class="opt_includedErrorSpan col s12 errorSpan" style="display: none!important; color: #ff0000;">This field is required.</span>

        <a id="opt_includedcollapsetrigger" data-toggle="collapse" href="#opt_includedcollapse" aria-expanded="false" aria-controls="opt_includedcollapse">
            Copy Paste Operation
        </a>

        <div class="collapse" id="opt_includedcollapse">
            <div class="form-group">
                <label for="opt_includedarea">Seperator: ⚈</label>
                <textarea class="form-control" id="opt_includedarea" rows="5"></textarea>
            </div>
            <button id="opt_includedprocess" class="btn" style="background-color: #1B3033; color: #FFF;">Process</button>
        </div>
    </div>
    <div class="form-group">
        <label for="opt_notIncluded">What's Not Included</label>
        <div class="input-field col s12">
            <input name="opt_notIncluded" id="opt_notIncluded" type="text" class="tags form-control"/>
        </div>
        <span class="opt_notIncludedErrorSpan col s12 errorSpan" style="display: none!important; color: #ff0000;">This field is required.</span>

        <a id="opt_notincludedcollapsetrigger" data-toggle="collapse" href="#opt_notincludedcollapse" aria-expanded="false" aria-controls="opt_notincludedcollapse">
            Copy Paste Operation
        </a>

        <div class="collapse" id="opt_notincludedcollapse">
            <div class="form-group">
                <label for="opt_includedarea">Seperator: ⚈</label>
                <textarea class="form-control" id="opt_notincludedarea" rows="5"></textarea>
            </div>
            <button id="opt_notincludedprocess" class="btn" style="background-color: #1B3033; color: #FFF;">Process</button>
        </div>
    </div>
    <div class="form-group">
        <label for="opt_knowBeforeYouGo">Know Before You Go</label>
        <div class="input-field col s12">
            <input name="opt_knowBeforeYouGo" id="opt_knowBeforeYouGo" type="text" class="tags form-control"/>
        </div>
        <span class="opt_knowBeforeYouGoErrorSpan col s12 errorSpan" style="display: none!important; color: #ff0000;">This field is required.</span>

        <a id="opt_beforeyougocollapsetrigger" data-toggle="collapse" href="#opt_beforeyougocollapse" aria-expanded="false" aria-controls="opt_beforeyougocollapse">
            Copy Paste Operation
        </a>

        <div class="collapse" id="opt_beforeyougocollapse">
            <div class="form-group">
                <label for="opt_includedarea">Seperator: ⚈</label>
                <textarea class="form-control" id="opt_beforeyougoarea" rows="5"></textarea>
            </div>
            <button id="opt_beforeyougoprocess" class="btn" style="background-color: #1B3033; color: #FFF;">Process</button>
        </div>
    </div>
</div>
