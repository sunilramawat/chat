@extends('admin.mainlayout')
@section('content')

  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12">
            

          <h1 class="m-0 text-dark">Manage Reports </h1>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
    <!-- /.content-header -->
  {!!Form::open(['url'=>'admin/report/payout/', 'id' => 'orderForm' ,'name' => 'orderForm' ,'enctype' => 'multipart/form-data', 'method'=>'get']) !!} 
    <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="box-main">
        <div class="box-main-top flex-content">
          <div class="box-main-title">Total Payout</div>
            <select class="form-control my-select" name="cust_year" id="cust_year">
                  <option value="2021" {{Request::get('cust_year') == '2021' ? 'selected' : '' }}>2021</option>
                  <option value="2022" {{Request::get('cust_year') == '2022' ? 'selected' : '' }}>2022</option>
                  <option value="2023" {{Request::get('cust_year') == '2023' ? 'selected' : '' }}>2023</option>
                 
            </select>
        </div>

        
        <div class="box-main-content">
          <div class="row">
            <div class="col-md-12 col-xl-12">
              <form>
                
                <canvas id="sales-chart" height="470"></canvas>
              </form>
            </div>
          </div>
        </div>
             	
      </div>
    </div><!-- /.container-fluid -->
  </section>
  {!!Form::close()!!}  
    <!-- /.content -->
<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>

<script type="text/javascript"> 
 $(function(){
    $('#cust_year').change(function(){
       var dateVal = document.getElementById("cust_year").value;
       //alert(dateVal);
       document.getElementById('orderForm').submit(); 
    });
  });
 
 $(function () {
  'use strict'
  var ticksStyle = {
    fontColor: '#495057',
    fontStyle: 'bold'
  }

  var mode      = 'index'
  var intersect = true

  var $salesChart = $('#sales-chart')
  var salesChart  = new Chart($salesChart, {
    type   : 'bar',
    data   : {
      labels  : <?php echo json_encode($charArr) ;?>,
      datasets: [
        {
          backgroundColor: '#F7CD37',
          borderColor    : '#F7CD37',
          data           : <?php echo json_encode($charArr1) ;?>,
        }
      ]
    },
    options: {
      maintainAspectRatio: false,
      tooltips           : {
        mode     : mode,
        intersect: intersect
      },
      hover              : {
        mode     : mode,
        intersect: intersect
      },
      legend             : {
        display: false
      },
      series: {
            bars: {
                show: true,
              barWidth: 0.05,
              lineWidth: 1,
              order: 1,
              fillColor: {
                  colors: [{
                      opacity: 1
                  }, {
                      opacity: 1
                  }]
              }
            }
          },
      scales             : {
        yAxes: [{
          // display: false,
          gridLines: {
            display      : true,
            // lineWidth    : '0.05px',
            // color        : 'rgba(0, 0, 0, .2)',
            // zeroLineColor: 'transparent'
          },
          ticks    : $.extend({
            beginAtZero: true,

            // Include a dollar sign in the ticks
           /* callback: function (value, index, values) {
              if (value >= 1000) {
                value /= 1000
                value += 'k'
              }
              return  value
            }*/
          }, ticksStyle)
        }],
        xAxes: [{
          display  : true,
          lineWidth    : '0.05px',
          gridLines: {
            display: true,
          },
          ticks    : ticksStyle
        }]
      }
    }
  })
})

   
</script>
@stop