@extends('layouts.main')
@section('content')

<h1>Dashboard</h1>
<ul class="dashCatBtns">
    <li>
        {{$orders_count}}
        <span>Open Orders</span>
    </li>
    <li>
        {{$users_count}}
        <span>Total Users</span>
    </li>
    <li>
        {{$sales_count}}
        <span>Total Sales</span>
    </li>
    <li>
        {{$categories_count}}
        <span>Categories</span>
    </li>
</ul>

<div class="contentHolderV1" id="graph_container">

</div>

<div class="contentHolderV1">
    <h2>Recent Orders</h2>
    <div class="tableHolder">
    <table style="width: 100%;" class="tableV1" cellpadding="0" cellspacing="0">
        <thead class="tableHeader">
            <tr>
                <td>Order Number</td>
                <td>Product Name</td>
                <td>Seller Name</td>
                <td>Buyer Name</td>
                <td>Date</td>
                <td>Order Status</td>
                <td class="actionHolder">Actions</td>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($orders as $order) {
                ?>
                <tr>
                    <td>{{$order->orderNumber}}</td>
                    <td>{{$order->productName}}</td>
                    <td>{{$order->sellerFirstName}} {{$order->sellerLastName}}</td>
                    <td>{{$order->buyerFirstName}} {{$order->buyerLastName}}</td>
                    <td>{{$order->created_at}}</td>
                    <td><select id = "{{$order->id}}" class="orderShipped tableSelect"><option value="0" @if($order->shippingStatus ==0) selected = true @endif >Payment Done</option>
                            <option value="1" @if($order->shippingStatus ==1) selected = true @endif >Shipped</option>
                            <option value="2" @if($order->shippingStatus ==2) selected = true @endif >Delivered</option>
                        </select>
                    </td>
                    <td class="actionHolder">
                        <a class="actionBtn view" href="{{ URL::to('orders/view', ['id' => ($order->id)]) }}" title="Edit"></a>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    </div>

</div>
<script>
    Highcharts.setOptions({
        global: {
            useUTC: false
        }
    });

    Highcharts.chart('graph_container', {
        chart: {
            type: 'line'
        },
        title: {
            text: 'This Month Sales'
        },
        xAxis: {
            type: 'datetime',
            dateTimeLabelFormats: {// don't display the dummy year
                month: '%e. %b',
                year: '%b'
            },
            title: {
                text: 'Date'
            }
        },
        yAxis: {
            title: {
                text: 'Amount'
            },
            labels: {
                formatter: function () {
                    return this.value;
                }
            }
        },
        tooltip: {
            headerFormat: '{point.x:%b %e}<br>',
            pointFormat: '{series.name} produced <b>{point.y:.0f} QAR</b>'
        },
        plotOptions: {
            area: {
                pointStart: 1,
                marker: {
                    enabled: false,
                    symbol: 'circle',
                    radius: 2,
                    states: {
                        hover: {
                            enabled: true
                        }
                    }
                }
            }
        },
        series: <?php echo $full_area_graph_data; ?>
    });
</script>
@endsection

