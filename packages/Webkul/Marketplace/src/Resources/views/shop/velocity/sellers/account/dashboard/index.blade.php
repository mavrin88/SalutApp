@extends('marketplace::shop.layouts.account')

@section('page_title')
    {{ __('marketplace::app.shop.sellers.account.dashboard.title') }}
@endsection

@push('css')
    <style>
        .cross-icon {
            position: absolute;
            margin-left: -55px;
            margin-top: 8px;
        }
    </style>
@endpush

@section('content')
    <div class="account-layout dashboard right m10">

        <div class="account-head mb-10">
            <span class="account-heading">
                {{ __('marketplace::app.shop.sellers.account.dashboard.title') }}
            </span>

            <div class="account-action">
                <date-filter></date-filter>
            </div>

            <div class="horizontal-rule"></div>
        </div>

        {!! view_render_event('marketplace.sellers.account.dashboard.before') !!}

        <div class="account-items-list" style="margin-top: 40px;">

            <div class="dashboard-stats">

                <div class="dashboard-card">
                    <div class="title">
                        {{ __('admin::app.dashboard.total-orders') }}
                    </div>

                    <div class="data">
                        {{ $statistics['total_orders']['current'] }}
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="title">
                        {{ __('admin::app.dashboard.total-sale') }}
                    </div>
                    @php  $currentCurrencyCode = core()->getCurrentCurrencyCode(); @endphp
                    <div class="data">
                        {{ core()->formatPrice($statistics['total_sales']['current'], $currentCurrencyCode) }}
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="title">
                        {{ __('marketplace::app.shop.dashboard.total-revenue') }}
                    </div>
                    @php  $currentCurrencyCode = core()->getCurrentCurrencyCode(); @endphp
                    <div class="data">
                        {{ core()->formatPrice($statistics['total_revenue']['current'], $currentCurrencyCode) }}
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="title">
                        {{ __('marketplace::app.shop.dashboard.average-revenue') }}
                    </div>

                    <div class="data">
                        @php 
                        if ($statistics['avg_sales']['current'] != 0) {
                            $avg_sale = ($statistics['avg_sales']['current'])/($statistics['avg_sales_count']['current']);
                        } else {
                            $avg_sale = 0;
                        }
                        @endphp
                        {{ core()->formatPrice($avg_sale, $currentCurrencyCode) }}
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="title">
                        {{ __('marketplace::app.shop.dashboard.total-payout') }}
                    </div>

                    <div class="data">
                        {{ core()->formatPrice($statistics['seller_payout']['total_payout'], $currentCurrencyCode) }}
                        </span>
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="title">
                        {{ __('marketplace::app.shop.dashboard.remaining-payout') }}
                    </div>

                    <div class="data">
                        {{ core()->formatPrice($statistics['seller_payout']['remaining_payout'], $currentCurrencyCode) }}
                    </div>
                </div>

            </div>

            <div class="graph-stats">
                <div class="card">
                    <div class="card-title" style="margin-bottom: 30px;">
                        {{ __('marketplace::app.shop.sellers.account.dashboard.sales-by-location') }}
                    </div>

                    <div class="card-info">
                        <div id="myMap" style="width: 100%; height:100%"></div>
                    </div>
                </div>
            </div>

            <div class="graph-stats">

                <div class="card">
                    <div class="card-title" style="margin-bottom: 30px;">
                        {{ __('admin::app.dashboard.sales') }}
                    </div>

                    <div class="card-info">

                        <canvas id="myChart"
                            style="width: 100%; height: 100%; min-height:380px; max-height:380px;"></canvas>

                    </div>
                </div>

            </div>

            <div class="sale-stock">
                <div class="card">
                    <div class="card-title">
                        {{ __('admin::app.dashboard.top-selling-products') }}
                    </div>

                    <div class="card-info {{ !count($statistics['top_selling_products']) ? 'center' : '' }}">
                        <ul>

                            @foreach ($statistics['top_selling_products'] as $item)
                                @php
                                    $getProductImageData = app('Webkul\Product\Repositories\ProductImageRepository')
                                        ->where('product_id', $item->product_id)
                                        ->get();
                                    
                                    $productImage = $getProductImageData->toArray();

                                    $product = app('Webkul\Product\Repositories\ProductRepository')
                                        ->where('id', $item->product_id)
                                        ->first();

                                    if (
                                        $product->status
                                        && $product->visible_individually
                                        && ! empty($product->url_key)
                                    ) {
                                        $product_url = route('shop.productOrCategory.index', $product->url_key);
                                    } else {
                                        $product_url = '';
                                    }
                                @endphp
                           
                                <li>
                                    <a href="{{ $product_url }}" target="{{ $product_url ? '_blank' : ''}}" style="text-decoration: none; pointer-events: {{ $product_url ? '' : 'none'}}">
                                        <div class="product image">
                                            @if (isset($item->path))
                                                <img class="item-image"
                                                    src="{{ bagisto_asset('storage/' . $item->path) }}" />
                                            @elseif (!empty($productImage))
                                                <img class="item-image"
                                                    src="{{ bagisto_asset('storage/' . $productImage[0]['path']) }}" />
                                            @endif
                                        </div>

                                        <div class="description">
                                            <div class="name">
                                                {{ $item->name }}
                                            </div>

                                            <div class="info">
                                                {{ __('admin::app.dashboard.sale-count', ['count' => $item->total_qty_ordered]) }}
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach

                        </ul>

                        @if (!count($statistics['top_selling_products']))
                            <div class="no-result-found">

                                <i class="icon no-result-icon"></i>
                                <p>{{ __('admin::app.common.no-result-found') }}</p>

                            </div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-title">
                        {{ __('admin::app.dashboard.customer-with-most-sales') }}
                    </div>

                    <div class="card-info {{ !count($statistics['customer_with_most_sales']) ? 'center' : '' }}">
                        <ul>

                            @foreach ($statistics['customer_with_most_sales'] as $item)
                                <li>
                                    @if ($item->customer_id)
                                        <a href="{{ route('marketplace.account.customers.order.index', encrypt($item->customer_email)) }}"
                                            style="text-decoration: none;">
                                    @endif

                                    <div class="image">
                                        @php
                                            $customer = app('Webkul\Customer\Repositories\CustomerRepository')->find($item->customer_id);
                                        @endphp
                                        @if ($customer->image)
                                            <div>
                                                <img style="width:60px; border-radius:50%;" src="{{ $customer->image_url }}" alt="{{ $customer->first_name }}" />
                                            </div>
                                        @else
                                            <div class="customer-name col-12 text-uppercase">
                                                {{ substr($customer->first_name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="description">
                                        <div class="name">
                                            {{ $item->customer_full_name }}
                                        </div>

                                        <div class="info">
                                            {{ __('admin::app.dashboard.order-count', ['count' => $item->total_orders]) }}
                                            <br>
                                            {{ __('marketplace::app.shop.sellers.account.sales.transactions.total-sale') .' '. core()->formatBasePrice($item->total_base_grand_total) }} 
                                        </div>
                                    </div>
                                    @if ($item->customer_id)
                                        </a>
                                    @endif
                                </li>
                            @endforeach

                        </ul>

                        @if (!count($statistics['customer_with_most_sales']))
                            <div class="no-result-found">

                                <i class="icon no-result-icon"></i>
                                <p>{{ __('admin::app.common.no-result-found') }}</p>

                            </div>
                        @endif
                    </div>

                </div>

                <div class="card">
                    <div class="card-title">
                        {{ __('admin::app.dashboard.stock-threshold') }}
                    </div>

                    <div class="card-info {{ !count($statistics['stock_threshold']) ? 'center' : '' }}">
                        <ul>

                            @foreach ($statistics['stock_threshold'] as $item)
                                <li>
                                    <div class="image">
                                        <?php $productBaseImage = product_image()->getProductBaseImage($item->product); ?>

                                        <img class="item-image" src="{{ $productBaseImage['small_image_url'] }}" />
                                    </div>

                                    <div class="description">
                                        <div class="name text-dark">
                                            {{ $item->product->name }}
                                        </div>

                                        <div class="info">
                                            {{ __('admin::app.dashboard.qty-left', ['qty' => $item->total_qty]) }}
                                        </div>
                                    </div>
                                </li>
                            @endforeach

                        </ul>

                        @if (!count($statistics['stock_threshold']))
                            <div class="no-result-found">

                                <i class="icon no-result-icon"></i>
                                <p>{{ __('admin::app.common.no-result-found') }}</p>

                            </div>
                        @endif
                    </div>

                </div>
            </div>

        </div>

        {!! view_render_event('marketplace.sellers.account.dashboard.after') !!}

    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.min.js"
        integrity="sha512-Wt1bJGtlnMtGP0dqNFH1xlkLBNpEodaiQ8ZN5JLA5wpc1sUlk/O5uuOMNgvzddzkpvZ9GLyYNa8w2s7rqiTk5Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.3/d3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/topojson/1.6.9/topojson.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datamaps/0.5.8/datamaps.all.js"></script>

    <script>
        let stateCheck = setInterval(() => {
            if (document.readyState === 'complete') {
                clearInterval(stateCheck);
                var orders = {!! str_replace("'", "\'", $mapOrdersArray) !!}

                // example data from server
                var series = [];

                orders.forEach(order => {
                    series.push([order.country, order.seller_order_count, order.seller_total])
                });

                // Datamaps expect data in format:
                // { "USA": { "fillColor": "#42a844", numberOfWhatever: 75},
                //   "FRA": { "fillColor": "#8dc386", numberOfWhatever: 43 } }
                var dataset = {};

                // We need to colorize every country based on "numberOfWhatever"
                // colors should be uniq for every value.
                // For this purpose we create palette(using min/max series-value)
                var onlyValues = series.map(function(obj) {
                    return obj[1];
                });
                var minValue = Math.min.apply(null, onlyValues),
                    maxValue = Math.max.apply(null, onlyValues);

                // create color palette function
                // color can be whatever you wish
                var paletteScale = d3.scale.linear()
                    .domain([minValue, maxValue])
                    .range(["#EFEFFF", "#02386F"]); // blue color

                // fill dataset in appropriate format
                series.forEach(function(item) { //
                    // item example value ["USA", 70]
                    var iso = item[0],
                        value = item[1];
                    dataset[iso] = {
                        numberOfThings: value,
                        fillColor: paletteScale(value),
                        sellerTotal: item[2]
                    };
                });
                // console.log(dataset);
                // render map
                const map = new Datamap({
                    element: document.getElementById('myMap'),
                    responsive: true,
                    projection: 'mercator',
                    fills: {
                        defaultFill: '#E8E8E8',
                        highlightedFill: '#FFCE00',
                        darkerFill: '#333333'
                    },
                    data: dataset,
                    geographyConfig: {
                        borderColor: '#DEDEDE',
                        highlightBorderWidth: 2,
                        highlightFillColor: 'highlightedFill',
                        highlightBorderColor: '#B7B7B7',
                        popupTemplate: function(geo, data) {
                            return `
                            <div class="hoverinfo">
                                <strong>${geo.properties.name}</strong>
                                <br>Orders: <strong>${data.numberOfThings}</strong>
                                <br>Total: <strong>${data.sellerTotal}</strong>
                            </div>`;
                        }
                    }
                });

                var ctx = document.getElementById("myChart").getContext('2d');

                var data = @json($statistics['sale_graph']);

                var myChart = new Chart(ctx, {
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
                                gridLines: {
                                    display: false,
                                    drawBorder: false,
                                },
                                ticks: {
                                    beginAtZero: true,
                                    fontColor: '#000'
                                }
                            }],
                            yAxes: [{
                                gridLines: {
                                    drawBorder: false,
                                },
                                ticks: {
                                    padding: 20,
                                    beginAtZero: true,
                                    fontColor: '#000'
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
    </script>

    <script type="text/x-template" id="date-filter-template">
        <div class="row">
            <div class="control-group date">
                <date><input type="text" class="control" id="start_date" placeholder="{{ __('admin::app.dashboard.from') }}" v-model="start"/></date>
            </div>

            <div class="control-group date">
                <date><input type="text" class="control" id="end_date" v-model="end"/></date>
            </div>

            <div class="control-group ml-4">
                <button class="btn theme-btn btn-sm mr-3" id="save-btn" :disabled="isButtonDisabled" v-on:click="applyFilter()" style="padding: 6px 20px;">
                    <span>{{ __('marketplace::app.shop.sellers.account.earning.show-report') }}</span>
                </button>
            </div>
        </div>
    </script>

    <script>
        Vue.component('date-filter', {

            template: '#date-filter-template',

            data() {
                return {
                    start: "{{ date('Y-m-d') }}",
                    end: "{{ date('Y-m-d') }}",
                    isButtonDisabled: false
                };
            },

            methods: {
                applyFilter(field, date) {
                    this[field] = date;

                    window.location.href = "?start=" + this.start + '&end=' + this.end;
                },

                validateForm() {
                    if (
                        this.start > this.end
                        || this.end < this.start
                        || this.start == ''
                        || this.end == ''
                    ) {
                        this.isButtonDisabled = true;
                    } else {
                        this.isButtonDisabled = false;
                    }
                }
            },

            mounted() {
                if ("{{ request()->get('start') }}") {
                    this.start = "{{ request()->get('start') }}";
                }

                if ("{{ request()->get('end') }}") {
                    this.end = "{{ request()->get('end') }}";
                }
            },

            computed: {
                isButtonDisabled() {
                    return this.start > this.end || this.end < this.start || this.start == '' || this.end == '';
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
@endpush
