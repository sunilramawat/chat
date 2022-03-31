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
    {!!Form::open(['url'=>'admin/report/earning/', 'id' => 'orderForm' ,'name' => 'orderForm' ,'enctype' => 'multipart/form-data', 'method'=>'get']) !!} 
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="box-main">
          <div class="box-main-top flex-content">
            <div class="box-main-title">Total Earnings</div>
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
                  
                  <div class="card-body">
                  
                  <div class="position-relative mb-4">
                    <canvas id="visitors-chart" height="380"></canvas>
                  </div>

                 
                </div>
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

  var $visitorsChart = $('#visitors-chart')
  var visitorsChart  = new Chart($visitorsChart, {
    data   : {
      labels  :  <?php echo json_encode($charArr) ;?>,
      datasets: [{
        type                : 'line',
        data                : <?php echo json_encode($charArr1) ;?>,
        backgroundColor     : 'transparent',
        borderColor         : '#007bff',
        pointBorderColor    : '#007bff',
        pointBackgroundColor: '#007bff',
        fill                : false
        // pointHoverBackgroundColor: '#007bff',
        // pointHoverBorderColor    : '#007bff'
      }]
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
      scales             : {
        yAxes: [{
          // display: false,
          gridLines: {
            display      : true,
            /*lineWidth    : '4px',
            color        : 'rgba(0, 0, 0, .2)',
            zeroLineColor: 'transparent'*/
          },
          ticks    : $.extend({
            beginAtZero : true,
           /* suggestedMax: 200*/
          }, ticksStyle)
        }],
        xAxes: [{
          display  : true,
          gridLines: {
            display: true
          },
          ticks    : ticksStyle
        }]
      }
    }
  })
})

   
</script>
@stop