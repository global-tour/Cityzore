<div id="galleryEditModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="margin-top: 50px;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close closeModal" data-dismiss="modal" aria-hidden="true" style="opacity: 1!important;">x</button>
                <p>Image Edit</p>
            </div>
            <div class="input-field col s12">
                <input id="imageAlt" name="imageAlt" type="text" class="validate form-control" value="">
                <label id="imageAltLabel" for="imageAlt">Alt</label>
            </div>
            <div class="input-field col s12">
                <input id="imageName" name="imageName" type="text" class="validate form-control" value="">
                <label id="imageNameLabel" for="imageName">Name</label>
            </div>
            <div class="input-field col s12">
                <span>Country/City: <span id="countryCityName" name="countryCityName" style="font-weight: bold"></span></span>
            </div>
            <div class="input-field col s12">
                <span>Uploaded By: <span id="uploadedBy" name="uploadedBy" style="font-weight: bold"></span></span>
            </div>
            <div class="input-field col s12">
                <span><b>Attractions</b></span>
                <select class="select2 browser-default custom-select" id="attraction" multiple>
                    @foreach($attractions as $attraction)
                        <option value="{{$attraction->id}}">{{$attraction->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="updateImageButton" style="margin-top: 10px">Update</button>
            </div>
        </div>
    </div>
</div>
