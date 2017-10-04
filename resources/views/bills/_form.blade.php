@extends('partials._base')

@section('panel-body')
    <h5>
        This will generate a bill number for all unbilled status and location changes
    </h5>

    @include('partials._forms')
    <div class="form-horizontal">
        <div class="form-group">
            <div class="col-xs-12 col-md-3">
                {!! Form::label('user_id', 'User', ['class' => 'control-label']) !!}
                {!! Form::custom_select('user_id', $params['data']['users'], null, ['id' => 'user_id', 'name' => 'user_id', 'data-plugin-selectTwo', 'class' => 'form-control populate']) !!}
            </div>
        </div>
    </div>

    <div id="form-msg-area" class="mt-sm">

    </div>
@endsection


@section('panel-footer')
    <div class="panel-footer clearfix">
        <div class="pull-left">
            {!! Form::button('Generate', ['class' => 'btn btn-primary', 'id' => 'create_bill_button', 'data-loading-text' => 'Generating..']) !!}
        </div>
        {!! Form::close() !!}
    </div>
@endsection

@section('footer-scripts')
    @parent

    <script type="text/template" id="tpl-create-bill-res">

        <% if( typeof(data['bills']) !== 'undefined' && !_.isEmpty(data['bills']) ) { %>
        <dl class="dl-horizontal">
            <dt>Count bills</dt>
            <dd><%= data.summary.count %></dd>

            <dt>Total amount</dt>
            <dd>$<%= data.summary.total %></dd>
        </dl>

        <div class="dataTables_wrapper no-footer">
            <div class="table-responsive col-xs-12 col-md-6">
                <table class="table table-striped table-condensed mb-none no-footer">

                    <thead>
                    <tr>
                        <th class="text-left">User</th>
                        <th class="text-left">Bill #</th>
                        <th class="text-left">Date</th>
                        <th class="text-left">Amount</th>
                    </tr>
                    </thead>
                    <tbody>

                    <% _.forEach(data['bills'], function(bill) { %>
                        <tr>
                            <td class="text-left"><%= bill['full_name'] %></td>
                            <td class="text-left"><a href="{{ route('bills.show', ['bills' => '']) }}/<%= bill['id'] %>"><%= bill['number'] %></a></td>
                            <td class="text-left"><%= bill['date'] %></td>
                            <td class="text-left">$<%= bill['amount'] %></td>
                        </tr>
                    <% }); %>

                    </tbody>
                </table>
            </div>
        </div>
        <% } %>

    </script>

    <script type="text/javascript" charset="utf-8">
        (function( $, _ ) {
            'use strict';

            var BillCreate = {
                dom: {
                    $panel: {},
                    $form: {},
                    $form_fields: {},
                    $form_create_button: {},
                    $form_msg_area: {},
                },
                templates: {},

                initialize: function() {
                    this.initializeDom();
                    this.initializeTemplates();
                    this.initializeFormCreateButton();
                },

                initializeDom: function() {
                    var $form = $('form');
                    this.dom.$form = $form;
                    this.dom.$panel = $form.parent().parent().parent();
                    this.dom.$form_create_button = $('#create_bill_button');
                    this.dom.$form_msg_area = $('#form-msg-area');

                    this.dom.$form_fields['_token'] = $form.find('input[name="_token"]');
                    this.dom.$form_fields['user_id'] = $form.find('#user_id');
                },

                initializeTemplates: function() {
                    this.templates.tpl_create_bills_res = _.template($('#tpl-create-bill-res').html());
                },

                initializeFormCreateButton: function() {
                    var self = this;

                    this.dom.$form_create_button.on('click', function() {
                        var $this = $(this);

                        self.createBills(
                                self.dom.$form.attr('action'),
                                {},
                                {},
                                function() {
                                    //$this.button('loading');
                                    self.dom.$form_msg_area.empty();
                                    self.dom.$panel.loadingOverlay({
                                        //startShowing: true,
                                        css: {
                                            'background-color': 'rgba(0, 0, 0, 0.075)'
                                        }
                                    });

                                    self.dom.$panel.trigger( "beforeSend" );
                                },
                                function(data) {
                                    var is_success = self.parseCreateBills('success', data, self.dom.$form_msg_area);
                                    if(is_success) {
                                        self.renderResults(data);
                                    }
                                    //$this.button('reset');
                                },
                                function(data) {
                                    self.parseCreateBills('error', data, self.dom.$form_msg_area);
                                    //$this.button('reset');
                                }
                        );
                    });
                },

                createBills: function(request_url, form_fields, form_data, precall, callback_success, callback_error) {
                    var params = this.collectFormData(this.dom.$form_fields, form_fields);

                    $.extend(true, params, form_data);
                    precall();
                    $.ajax({
                        url: request_url,
                        headers: {},
                        dataType: "json",
                        type: 'POST',
                        data: params,
                        success: function(data) {
                            callback_success(data);

                        },
                        error: function(data) {
                            callback_error(data);
                        }
                    });
                },

                parseCreateBills: function(type, data, $_result_msg_area) {
                    var self = this;
                    var is_success = false;
                    $_result_msg_area.empty();

                    if ( type == 'error') {
                        if( data.status === 422 && typeof data['responseJSON'] === 'object') {
                            $.each(data['responseJSON'], function(index, value) {
                                $_result_msg_area.append('<div class="alert alert-danger"><i class="fa fa-remove"></i> ' +
                                        value +
                                        '</div>');
                            });
                        } else {
                            $_result_msg_area.append('<div class="alert alert-danger"><i class="fa fa-remove"></i> ' +
                                    'An internal error was encountered while processing this request' +
                                    '</div>');
                        }
                    }

                    if ( type == 'success') {
                        if ( data == null )
                            return is_success;

                        var json = data;
                        if( typeof json === 'object') {
                            if ( typeof json['success'] !== 'undefined' && json['success'] )
                                is_success = true;

                            if ( typeof json['message'] !== 'undefined' ) {
                                $.each(json['message'], function(index, value) {
                                    $_result_msg_area.append('<div class="alert alert-success"><i class="fa fa-check"></i> ' +
                                            value +
                                            '</div>');
                                });
                            }
                        }
                    }

                    self.dom.$panel.trigger( "complete" );
                    return is_success;
                },

                collectFormData: function($_form, params) {
                    var data = {};
                    $.each(this.dom.$form_fields, function(index, value) {
                        data[index] = value.val();
                    });

                    return data;
                },

                renderResults: function(data) {
                    var resultTable = this.templates.tpl_create_bills_res({
                        data: data['payload']
                    });

                    var $resultTable = $(resultTable);
                    this.dom.$form_msg_area.append($resultTable);
                }
            };

            BillCreate.initialize();

        }).apply(this, [ jQuery, _ ]);
    </script>
@endsection
