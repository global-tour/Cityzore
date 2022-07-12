<style>
    body,
    .modal-open .page-container,
    .modal-open .page-container .navbar-fixed-top,
    .modal-open .modal-container {
        overflow-y: scroll;
    }

    @media (max-width: 979px) {
        .modal-open .page-container .navbar-fixed-top{
            overflow-y: visible;
        }
    }
</style>

<div class="modal fade" id="modalnew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Modal title</h4>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('.openmodal').on('click', function(e) {
        $('#modalnew').modal('show');
    });
</script>
