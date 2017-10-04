@extends('partials._base')

@section('panel-body')
    <div class="row reports">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="#bill-report">

            <div class="report_filter_area">

                {!! Form::open(['url' => '', 'class' => 'report_filter_form', 'data-url' => action('ReportsController@ajaxReport', ['report_type' => 'bills']) ]) !!}

                <div class="col-md-4 pull-right report-actions">

                    <div class="pull-right">
                        <button type="button" class="btn btn-primary button-run-report" data-loading-text="Running.." autocomplete="off">Run Report</button>
                    </div>

                    <div class="pull-right">
                        <ul class="nav nav-pills pull-right" role="tablist">

                            <li role="presentation pull-right" class="dropdown select-dimensions">
                                <a href="#" class="dropdown-toggle select-dimension-list-link" data-toggle="dropdown"
                                   aria-haspopup="true" role="button" aria-expanded="false">
                                    <i class="fa fa-columns"></i>
                                    Add dimensions
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu select-dimension-list" role="menu"
                                    aria-labelledby="select-dimension-list-link">
                                    <?php foreach ($params['rules']['bill_report']['dimensions'] as $dimension_id => $dimension): ?>
                                    <li role="presentation"
                                        class="select-dimension-item select-dimension-item-<?= $dimension_id; ?>"><a
                                                role="menuitem" tabindex="-1" class="fc-button cursor-pointer"
                                                data-dimension_id="<?= $dimension_id; ?>"><?= $dimension['title'] ?></a>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>

                            <li role="presentation pull-right" class="dropdown select-conditions">
                                <a href="#" class="dropdown-toggle select-condition-list-link" data-toggle="dropdown"
                                   aria-haspopup="true" role="button" aria-expanded="false">
                                    <i class="fa fa-search"></i>
                                    Add conditions
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu select-condition-list" role="menu"
                                    aria-labelledby="select-condition-list-link">
                                    <?php foreach ($params['rules']['bill_report']['conditions'] as $condition_id => $condition): ?>
                                    <li role="presentation"
                                        class="select-condition-item select-condition-item-<?= $condition_id; ?>"><a
                                                role="menuitem" tabindex="-1" class="cursor-pointer"
                                                data-condition_id="<?= $condition_id; ?>"><?= $condition['title'] ?></a>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        </ul>

                    </div>
                </div>
                <div class="col-md-12 filter-form">
                    <div class="report-dimension-area">
                        <label>Dimensions:</label>

                        <div class="filter-selector">
                            <ul class="nav nav-pills report-dimension-area-list">

                            </ul>
                        </div>
                    </div>

                    <div class="report-condition-area" style="clear: both;">
                        <label>Conditions:</label>

                        <div class="filter-selector">
                            <ul class="list-group list-group-horizontal report-condition-area-list">

                            </ul>
                        </div>
                    </div>
                </div>

                {!! Form::close() !!}

                <div class="clearfix"></div>
            </div>

            <div class="report_result_area">
                <div class="col-md-12">
                    <div class="report_result_msg_area"></div>
                    <div class="pull-right reports-stats-totals"></div>
                    <div class="pull-right reports-stats-pagination"></div>

                    <div class="reports-stats-table">
                        <table class="table table-hover table-responsive table-striped table-condensed table-bordered">
                            <thead class="report-stat-table-head">

                            </thead>
                            <tbody class="report-stat-table-body">
                            <tr><td class="text-center">Click "Run Report" to generate data</td></tr>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection

@section('specifics-stylesheet')
    <link rel="stylesheet" href="{{ asset('css/dynamic-report.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-daterangepicker/daterangepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-daterangepicker/daterangepicker-style.css') }}" />
@endsection


@section('specifics-scripts')
    @parent
    <script src="{{ asset('vendor/moment/moment.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('js/jquery.dynamic.report.js') }}"></script>
@endsection

@include('partials._base_report_tpl')
@include('bills._report_tpl')

@section('footer-scripts')
    @parent

    <script type="text/javascript" charset="utf-8">
        (function ($, _) {
            'use strict';

            var billReport = {
                dom: {},
                data: { form: {} },
                templates: {},
            };

            var date = {
                handlerType: 'datepicker',
                options: {
                    format: 'MM/DD/YYYY',
                    timeZone: 0,
                    startDate: moment().startOf('month'),
                    endDate: moment(),
                    dateLimit: false,
                    showDropdowns: true,
                    showWeekNumbers: true,
                    timePicker: false,
                    timePickerIncrement: 1,
                    timePicker12Hour: false,
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'),moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    opens: 'right',
                    drops: 'down',
                    buttonClasses: ['btn', 'btn-sm'],
                    applyClass: 'btn-primary',
                    cancelClass: 'btn-default',
                    separator: ' to ',
                    locale: {
                        applyLabel: 'Set',
                        cancelLabel: 'Cancel',
                        fromLabel: 'From',
                        toLabel: 'To',
                        customRangeLabel: 'Custom',
                        daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
                        monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                        firstDay: 1
                    }
                },
                callback: function(start, end, label) {
                    this.element.find('span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                    this.element.find('.el-condition-date-start').val(start.format('D MMM, YYYY'));
                    this.element.find('.el-condition-date-end').val(end.format('D MMM, YYYY'));
                }
            };

            billReport.data.form['report_type'] = 'bill';
            billReport.data.form['_token'] = $('input[name="_token"]').val();
            billReport.data.rules = {!! json_encode($params['rules']['bill_report']) !!};
            billReport.data.rules['date_params'] = date;

            var Report = $('#bill-report').dynamicReport(billReport);
            Report.initialize();
            Report.initialize_report();

        }).apply(this, [jQuery, _]);
    </script>
@endsection
