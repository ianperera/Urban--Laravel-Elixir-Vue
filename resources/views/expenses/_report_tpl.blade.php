@section('specifics-scripts')
    @parent
    <script type="text/template" id="report-stat-table-body">
        <% for (var index_row in o) { %>

        <tr>

            <% if (typeof o[index_row]['date_formatted'] !== 'undefined') { %>
            <td class=""><%=o[index_row]['date_formatted']%></td>
            <% } %>

            <% if (typeof o[index_row]['user'] !== 'undefined') { %>
            <td class=""><%=o[index_row]['user']['full_name']%></td>
            <% } %>

            <% if (typeof o[index_row]['building'] !== 'undefined') { %>
                <td class="">
                    <% if ( o[index_row]['building'] !== null ) { %>
                        <a href="{{ route('buildings.show', ['buildings' => '']) }}/<%=o[index_row]['building']['id']%>"><%=o[index_row]['building']['serial_number']%></a>
                    <% } %>
                </td>
            <% } %>

            <% if (typeof o[index_row]['expense_type_group'] !== 'undefined') { %>
                <td class=""><%=o[index_row]['expense_type_formatted']%></td>
            <% } %>

            <% if (typeof o[index_row]['expense_type_item_group'] !== 'undefined') { %>

                <% if ( o[index_row]['expense_type'] == 'location' &&
                        typeof o[index_row]['building_locations'] !== 'undefined'
                      ) { %>
                    <td class="">Delivered to <u><%=o[index_row]['building_locations'][0]['location']['name']%></u></td>
                <% } %>

                <% if ( o[index_row]['expense_type'] == 'status' &&
                        typeof o[index_row]['building_history'] !== 'undefined'
                      ) { %>
                    <td class=""><%=o[index_row]['building_history'][0]['building_status']['name']%></td>
                <% } %>

            <% } %>

            <% if (typeof o[index_row]['cost_formatted'] !== 'undefined') { %>
            <td class="">$<%=o[index_row]['cost_formatted']%></td>
            <% } %>

             <% if (typeof o[index_row]['bill'] !== 'undefined') {  %>

                <td class="">
                    <% if (o[index_row]['bill'] !== null) { %>
                    <a href="{{ route('bills.show', ['bills' => '']) }}/<%=o[index_row]['bill']['id']%>"><%=o[index_row]['bill']['number']%></a>
                        <% } %>
                </td>
            <% } %>

        </tr>

        <% } %>
    </script>

@endsection