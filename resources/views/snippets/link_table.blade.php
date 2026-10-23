<table id="{{$table_id}}" class="table table-hover">
    <thead>
        <tr>
            <th>短碼</th>
            <th>原始網址</th>
            <th>點擊數</th>
            <th>建立日期</th>
            @if ($table_id == "admin_links_table")
            {{-- Show action buttons only if admin view --}}
            <th>建立者</th>
            <th>停用</th>
            <th>刪除</th>
            @endif
        </tr>
    </thead>
</table>
