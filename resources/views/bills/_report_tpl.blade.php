@section('specifics-scripts')
    @parent
    <script type="text/template" id="report-stat-table-body">
        <% for (var index_row in o) { %>

        <tr>
            <% if (typeof o[index_row]['bill_number'] !== 'undefined') { %>
            <td class=""><a href="{{ route('bills.show', ['bills' => '']) }}/<%=o[index_row]['id']%>"><%=o[index_row]['bill_number']%></a></td>
            <% } %>

            <% if (typeof o[index_row]['date'] !== 'undefined') { %>
            <td class=""><%=o[index_row]['date_formatted']%></td>
            <% } %>

            <% if (typeof o[index_row]['user_id'] !== 'undefined') { %>
            <td class=""><%=o[index_row]['user']['full_name']%></td>
            <% } %>

            <% if (typeof o[index_row]['amount_formatted'] !== 'undefined') { %>
            <td class="">$<%=o[index_row]['amount_formatted']%></td>
            <% } %>

        </tr>

        <% } %>
    </script>

@endsection