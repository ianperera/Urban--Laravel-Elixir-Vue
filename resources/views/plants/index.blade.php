@extends('partials._lists')

@section('table-items')
    <thead>
        <tr>
            <th class="col-md-1 text-center">ID</th>
            <th>Name</th>
            <th>Description</th>
            <th class="col-lg-1 col-md-1 col-sm-2 col-xs-1 text-center"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($params['data']['items'] as $item)
        <tr>
            <td class="text-center">{{ $item->id }}</td>
            <td>{{ $item->name }}</td>
            <td>{{ $item->description }}</td>
            <td class="text-center actions">
                <!-- Edit -->
                {{-- @if(Auth::user()->ability('sysadmin', $params['route_name'].'-edit')) --}}
                    <a href="{{ url($params['route'], [$item->id, 'edit']) }}" title="Edit"><i class="fa fa-pencil"></i></a>
                {{-- @endif --}}

                <!-- Delete -->
                {{-- @if(Auth::user()->ability('sysadmin', $params['route_name'].'-delete')) --}}
                    <a class="confirmation delete-row" href="#modalWindow" title="Delete" data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i></a>
                {{-- @endif --}}
            </td>
        </tr>
        @endforeach

        @if (count($params['data']['items']) == 0)
        <tr>
            <td colspan="3" valign="top" class="dataTables_empty h5 text-primary"><a href="{{ url($params['route']) }}/create" class="btn btn-primary"><i class="fa fa-fw fa-plus"></i> New</a></td>
        </tr>
        @endif
    </tbody>
@endsection
