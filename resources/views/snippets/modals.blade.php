<script id="modal-template" type="text/x-handlebars-template">

    <div class="modal fade" id="@{{id}}" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">@{{title}}</h4>
                </div>
                <div class="modal-body">
                    @{{{body}}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('polr.admin.modals.close') }}</button>
                </div>
            </div>
        </div>
    </div>

</script>
