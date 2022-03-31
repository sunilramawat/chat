@extends('admin.mainlayout')
@section('content')

  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Manage Reports </h1>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
    <!-- /.content-header -->

    <!-- Main content --> 
  <section class="content">
    <div class="container-fluid">
      <div class="box-main">
        <div class="box-main-top">
          <div class="box-main-title">Orders Placed</div>
            <div class="box-main-top-right">
               <div class="box-serch-field">
                <input type="text" class="box-serch-input" name="" id ="search" placeholder="Search">
                <i class="fa fa-search" aria-hidden="true"></i>
              </div> 
               <!--  <button class="btn btn-primary " data-toggle="modal" data-target="#exampleModal"><i class="fa fa-filter" aria-hidden="true"></i></button> -->
              <button class="btn btn-primary " data-toggle="modal" data-target="#exampleModal"><i class="fa fa-filter" aria-hidden="true"></i></button>
              <button class="btn btn-primary">Download
                <img src="../../../../dist/img/download-icon.png" alt="" class="download-icon">
              </button>  
               <!--  <a href="{{URL('admin/pendingrequest/view')}}">{{ Form::submit('Pending Request',array('class'=>'btn btn-primary')) }}</a> -->
              
              <!-- <button class="btn btn-primary ">Pending Request (25)</button> -->
            </div>
          </div>
        
        <div  id="maintable">
          <div class="box-main-table" id="maintable" >
            <div class="table-responsive">
              <table class="table table-bordered admin-table dataTable"  id="example2" >
               
                <thead>
                  <tr>
                    <th>S.No</th>
                    <th>Order ID</th> 
                    <th>Customer Name</th> 
                    <th>Supplier</th> 
                    <th>Order Date</th> 
                    <th>Order Amount</th> 
                  </tr>
                </thead>
                <tbody>
                  @foreach($Supplier_manage as $no => $chip)
                  <tr>
                    <td>{{$no+1}}</td>
                    <td>{{$chip->order_code}}</td>
                    <td>{{$chip->customer_name}}</td>
                    <td>{{$chip->supplier_name}}</td>
                    <td>{{date('M d,Y',strtotime($chip->order_date))}}</td>
                    <td>{{$chip->total_amount}}</td>
                  </tr>
                  @endforeach()
                </tbody>
              </table>
            </div>
          </div>
       
        </div>
      </div>
      
    </div><!-- /.container-fluid -->
  </section>
    <!-- /.content -->

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Filter</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <img src="{{URL('public/admin/dist/img/cancel-close.svg')}}" alt="">
        </button>
      </div>
      <div class="modal-body">
         {!!Form::open(['url'=>'admin/supplier/view', 'enctype' => 'multipart/form-data', 'method'=>'get']) !!} 
           <div class="form-group">
            <div class="row">
              <div class="col-md-4 col-12">
                <lable class="control-label">Suppliers:</lable>
              </div>
              <div class="col-md-8 col-12">
                <select class="form-control">
                  <option>All</option>
                </select>
              </div>
            </div>
          </div> 
          <div class="form-group">
            <div class="row">
              <div class="col-md-4 col-12">
                <lable class="control-label">Order Status:</lable>
              </div>
              <div class="col-md-8 col-12">
                <select class="form-control">
                  <option>All</option>
                </select>
              </div>
            </div>
          </div> 
          <div class="form-group">
            <div class="row">
              <div class="col-md-4 col-12">
                <lable class="control-label">From:</lable>
              </div>
              <div class="col-md-8 col-12">
                <input type="text" name="from_date" class="form-control" id="from_date" placeholder="MM-DD-YYYY">
                <i class="fa fa-calendar input-icon" aria-hidden="true"></i>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="row">
              <div class="col-md-4 col-12">
                <lable class="control-label">To:</lable>
              </div>
              <div class="col-md-8 col-12">
                <input type="text" name="to_date" class="form-control" id="to_date"  placeholder="MM-DD-YYYY">
                <i class="fa fa-calendar input-icon" aria-hidden="true"></i>
              </div>
            </div>
          </div>

          <div class="form-group">
            <div class="row">
              <div class="col-md-4 col-12">
                
              </div>
              <div class="col-md-8 col-12">
                <button type="submit" class="btn btn-primary">Apply</button>
                 <button type="reset" class="btn btn-primary">Reset</button>
              </div>
            </div>
          </div>
        {!!Form::close()!!}    
        
      </div>
      
    </div>
  </div>
</div>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css"/>
<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script type="text/javascript">


function ChangeStatus(Id, Status)
{ 
  //alert(Status);
  $("#LoadingProgress").fadeIn('fast');
    $.ajax({
      url: "{{ URL('admin/supplier/ChangeStatus') }}/"+Id+"/"+Status,
      type: "GET",
      contentType: false,
      cache: false,
      processData:false,
    success: function( data, textStatus, jqXHR ) {
      window.location.reload();
      $("#LoadingProgress").fadeOut('fast');
    },
    error: function( jqXHR, textStatus, errorThrown ) {

    }
  });
}
$(document).ready(function () {
  $('#search').on('keyup',function(){
    $value=$(this).val();
      $.ajax({
        url: "{{ URL('admin/supplier/search') }}",
        type : 'get',
        data:{'search':$value},
        success:function(data){
          $('#maintable').html(data);
        }
    });
  })

  //your code here
});
  
</script>
@stop
