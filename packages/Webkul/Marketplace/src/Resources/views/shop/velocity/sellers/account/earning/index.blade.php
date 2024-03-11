@extends('marketplace::shop.layouts.account')

@section('page_title')
    {{ __('marketplace::app.shop.sellers.account.earning.title') }}
@endsection

@push('css')
    <style>
        .cross-icon {
            position: absolute;
            right: 95px;
            margin-top: -26px;
        }

        #earningTable {
            margin-top: 10px;
        }
    </style>
@endpush

@section('content')

    <div class="account-layout dashboard right m10">

        <div class="account-head mb-10">
            <span class="account-heading">
                {{ __('marketplace::app.shop.sellers.account.earning.title') }}
            </span>

            <div class="account-action">

            </div>

            <div class="horizontal-rule"></div>
        </div>

        {!! view_render_event('marketplace.sellers.account.earning.before') !!}

        <div class="account-items-list" style="margin-top: 40px;">

            <div class="">
                <earning-filter></earning-filter>
            </div>

            <div class="graph-stats">

                <div class="card">
                    <div class="card-title" style="margin-bottom: 30px;">
                        {{ __('admin::app.dashboard.sales') }}
                    </div>

                    <div class="card-info">

                        <canvas id="earningChart" style="width: 100%; height: 100%; max-height:380px; min-height:380px;"></canvas>

                    </div>
                </div>
            </div>

            <div class="mt-5">
                <div class="table">
                    <div class="grid-container">
                        <table class="table display" id="earningTable">
                            <thead>
                              <tr style="height: 65px">
                                <th class="grid_head">{{ __('marketplace::app.shop.sellers.account.earning.interval') }}</th>
                                <th class="grid_head">{{ __('marketplace::app.shop.sellers.account.earning.orders') }}</th>
                                <th class="grid_head">{{ __('marketplace::app.shop.sellers.account.earning.total-amt') }}</th>
                                <th class="grid_head">{{ __('marketplace::app.shop.sellers.account.earning.total-earning') }}</th>
                                <th class="grid_head">{{ __('marketplace::app.shop.sellers.account.earning.total-discount') }}</th>
                                <th class="grid_head">{{ __('marketplace::app.shop.sellers.account.earning.admin-commission') }}</th>
                              </tr>
                            </thead>
                            <tbody>

                                @foreach ($statistics['sale_graph']['label'] as $key => $label)
                                    @if ($statistics['sale_graph']['total'][$key] == 0)
                                        <?php continue; ?>
                                    @endif
                   
                                    <tr>
                                        <td data-value="Interval">{{ $label }}</td>
                                        <td data-value="Orders">{{ $statistics['sale_graph']['orders'][$key] }}</td>
                                        <td data-value="Total Amount">{{ $statistics['sale_graph']['total'][$key] }}</td>
                                        <td data-value="Total Earning">{{ $statistics['sale_graph']['total_earning'][$key] }}</td>
                                        <td data-value="Total Discount">{{ $statistics['sale_graph']['discount'][$key] }}</td>
                                        <td data-value="Admin comission">{{ $statistics['sale_graph']['commission'][$key] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                          </table>
                    </div>
                </div>
            </div>
        </div>

        {!! view_render_event('marketplace.sellers.account.earning.after') !!}

    </div>

@endsection

@push('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.min.js" integrity="sha512-Wt1bJGtlnMtGP0dqNFH1xlkLBNpEodaiQ8ZN5JLA5wpc1sUlk/O5uuOMNgvzddzkpvZ9GLyYNa8w2s7rqiTk5Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
        $(document).ready(function () {
            let stateCheck = setInterval(() => {
                if (document.readyState === 'complete') {
                    clearInterval(stateCheck);

                    var ctx = document.getElementById("earningChart").getContext('2d');

                    var data = @json($statistics['sale_graph']);

                    var earningChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data['label'],
                            datasets: [{
                                data: data['total'],
                                backgroundColor: 'rgba(34, 201, 93, 1)',
                                borderColor: 'rgba(34, 201, 93, 1)',
                                borderWidth: 1,
                                label: '{{ __('marketplace::app.shop.layouts.earnings') }}',
                            }]
                        },
                        options: {
                            responsive: true,
                            legend: {
                                display: false
                            },
                            scales: {
                                xAxes: [{
                                    maxBarThickness: 20,
                                    gridLines : {
                                        display : false,
                                        drawBorder: false,
                                    },
                                    ticks: {
                                        beginAtZero: true,
                                        fontColor: 'rgba(162, 162, 162, 1)'
                                    }
                                }],
                                yAxes: [{
                                    gridLines: {
                                        drawBorder: false,
                                    },
                                    ticks: {
                                        padding: 20,
                                        beginAtZero: true,
                                        fontColor: 'rgba(162, 162, 162, 1)'
                                    }
                                }]
                            },
                            tooltips: {
                                mode: 'index',
                                intersect: false,
                                displayColors: false,
                                callbacks: {
                                    label: function(tooltipItem, dataTemp) {
                                        return data['formated_total'][tooltipItem.index];
                                    }
                                }
                            }
                        }
                    });
                }
            }, 100);
        });
</script>

<script type="text/x-template" id="earning-filter-template">
    <form action="" enctype="multipart/form-data" method="get" @submit.prevent="applyFilter($event)">
        <div class="wk-mp-design">

        <div class="form-group">
            <label class="required" for="period">{{ __('marketplace::app.shop.sellers.account.earning.period') }}</label>
            <select name="period" id="period" v-on:change="setDate()" v-model="period" class="form-style" aria-required="true">
                <option value="day">Day</option>
                <option value="month">Month</option>
                <option value="year">Year</option>
            </select>
        </div>

        <div class="form-group date">
            <label for="start_date">{{ __('marketplace::app.shop.sellers.account.earning.start-date') }}</label>
            <date><input type="text" class="form-style" id="start_date" placeholder="{{ __('admin::app.dashboard.from') }}" v-model="start"/></date>
        </div>

        <div class="form-group date">
            <label for="end_date">{{ __('marketplace::app.shop.sellers.account.earning.end-date') }}</label>
            <date><input type="text" class="form-style" id="end_date" placeholder="{{ __('admin::app.dashboard.to') }}" v-model="end"/></date>
        </div>

        <div class="wk-mp-page-title mt-3" id="wk-mp-earning-form">
            <button class="btn theme-btn" :disabled="isButtonDisabled" type="submit" id="save-btn"><span><span>{{ __('marketplace::app.shop.sellers.account.earning.show-report') }}</span>
            </button>
        </div>
    </form>
</script>

<script>
    Vue.component('earning-filter', {

        template: '#earning-filter-template',

        data() {
            return {
                start: "{{ date('Y-m-d') }}",
                end: "{{ date('Y-m-d') }}",
                period: "day",
                isButtonDisabled: false
            };
        },
        
        methods: {
            applyFilter(event) {
                window.location.href = "?start=" + this.start + '&end=' + this.end + "&period=" + this.period;
            },

            setDate() {
                if (this.period == "day") {
                    this.start = "{{ date('Y-m-d') }}",
                    this.end = "{{ date('Y-m-d') }}"
                } else if (this.period == "month") {
                    this.start = "{{ date('Y-m-d', strtotime( date( 'Y-m-d', strtotime( date('Y-m-d') ) ) . '-1 month' ) ) }}",
                    this.end = "{{ date('Y-m-d') }}"
                } else if (this.period == "year") {
                    this.start = "{{ date('Y-m-d', strtotime( date( 'Y-m-d', strtotime( date('Y-m-d') ) ) . '-1 year' ) ) }}",
                    this.end = "{{ date('Y-m-d') }}"
                } 
            },

            validateForm() {
                if (this.start > this.end || this.end < this.start) {
                    this.isButtonDisabled = true;
                } else {
                    this.isButtonDisabled = false;
                }
            }
        },

        mounted() {
            if ("{{ request()->get('period') }}") {
                this.period = "{{ request()->get('period') }}";
                this.start = "{{ request()->get('start') }}";
                this.end = "{{ request()->get('end') }}";
            }
        },

        computed: {
            isButtonDisabled() {
                return this.start > this.end || this.end < this.start;
            },
        },

        watch: {
            start(newVal, oldVal) {
                this.validateForm();
            },

            end(newVal, oldVal) {
                this.validateForm();
            }
        }
    });

</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready( function () {
        $('#earningTable').dataTable( {
            "oLanguage": {
            "sLengthMenu": 'Items Per Page <select>'+
                '<option value="10">10</option>'+
                '<option value="20">20</option>'+
                '<option value="30">30</option>'+
                '<option value="40">40</option>'+
                '<option value="50">50</option>'+
                '</select> '
            }
        } );

        const paginator = document.querySelector('#earningTable_paginate');

        if (!! document.querySelector('#earningTable .dataTables_empty')) {
            $( paginator ).hide();
        }

        paginator.setAttribute('class','pagination');
        paginator.children[0].classList.add('page-item');
        paginator.children[2].classList.add('page-item')

    });
</script>

@endpush
