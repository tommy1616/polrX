<table id="{{$table_id}}" class="table table-hover">
    <thead>
        <tr>
            <th>{{ __('polr.admin.links.short_link') }}</th>
            <th>{{ __('polr.admin.links.long_link') }}</th>
            <th>{{ __('polr.admin.links.clicks') }}</th>
            <th>{{ __('polr.admin.links.date') }}</th>
            @if ($table_id == "admin_links_table")
            <th>{{ __('polr.admin.links.creator') }}</th>
            <th>{{ __('polr.admin.links.disable') }}</th>
            <th>{{ __('polr.admin.links.delete') }}</th>
            @endif
        </tr>
    </thead>
</table>
