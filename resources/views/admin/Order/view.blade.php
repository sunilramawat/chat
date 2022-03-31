@extends('admin.mainlayout')
@section('content')

  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Manage Order </h1>
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
          <div class="box-main-title">Orders List</div>
            <div class="box-main-top-right">
              <div class="box-serch-field">
                <input type="text" class="box-serch-input" name="" id ="search" placeholder="Search">
                <i class="fa fa-search" aria-hidden="true"></i>
              </div> 
                 <!--  <button class="btn btn-primary " data-toggle="modal" data-target="#exampleModal"><i class="fa fa-filter" aria-hidden="true"></i></button> -->
                <button class="btn btn-primary " data-toggle="modal" data-target="#exampleModal"><i class="fa fa-filter" aria-hidden="true"></i></button>
                <!--  <a href="{{URL('admin/pendingrequest/view')}}">{{ Form::submit('Pending Request',array('class'=>'btn btn-primary')) }}</a> -->
                
                <!-- <button class="btn btn-primary ">Pending Request (25)</button> -->
            </div>
          </div>
        
        <div id="maintable" class="maintable">
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
                    <th>Status</th>
                    <th>Action</th>
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
                    <td class="currency-text">${{$chip->total_amount}}</td>
                   
                    @if($chip->order_status == 0)
                      <td class="blue-text">Confirmed</td>
                    @elseif($chip->order_status == 1)
                      <td class="red-text">Cancelled </td>
                    @elseif($chip->order_status == 2)
                      <td class="blue-text">Ready to Dispatch</td>
                    @elseif($chip->order_status == 3)
                      <td class="blue-text">Shipped </td>
                    @elseif($chip->order_status == 4)
                      <td class="green-text">Delivered </td>
                    @elseif($chip->order_status == 5)
                      <td class="red-text">Returned </td>
                    @elseif($chip->order_status == 6)
                      <td class="red-text">Payment Pending </td>  
                    @else
                      <td class="red-text">Payment Fail</td>
                    @endif
                    </td> 
                    <td><!-- <a href=""><i class="fa fa-eye" aria-hidden="true"></i></a> -->
                      <a href="{{URL('admin/order/detail')}}/{{$chip->id}}">
                                  <i class="fa fa-eye" aria-hidden="true"></i>
                                </a>  
                                 <!--  &nbsp;
                        <a href="{{URL('admin/supplier/delete')}}/{{$chip->id}}">
                                    <i class="fa fa-trash aria-hidden="true""></i>
                                  </a>        -->   
                    </td>
                  </tr>
                  @endforeach()
                </tbody>
              </table>
            </div>
          </div>
       
        </div>
      </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
    <!-- /.content -->

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
        <div class="form-group">
          <div class="row">
            <div class="col-md-12 col-12">
              <span class="red-text" id="error-all"></span>
            </div>
          </div>  
        </div>
         {!!Form::open(['url'=>'admin/order/view',  'name' => 'orderForm' ,'enctype' => 'multipart/form-data', 'method'=>'get','onsubmit'=>"return validateForm()"]) !!} 
           <div class="form-group">
            <div class="row">
              <div class="col-md-4 col-12">
                <lable class="control-label">Suppliers:</lable>
              </div>
              <div class="col-md-8 col-12">
                <select class="form-control" name="supplier" id="supplier">
                  <option value="">All</option>
                  @foreach($Supplier_list as $item)
                    <option value="{{$item->id}}" {{Request::get('supplier') == $item->id ? 'selected' : '' }} >{{$item->first_name}}</option>
                  @endforeach
             
                </select>
              </div>
            </div>
          </div> 
          <div class="form-group">
            <div class="row">
              <div class="col-md-4 col-12">
                <lable class="control-label">Order Status:</lable>
              </div>
             <input type="hidden" id="order_status_id" name="">
              <div class="col-md-8 col-12">
                <select class="form-control" name="order_status" id="order_status">
                  <option value="" {{Request::get('order_status') == '' ? 'selected' : '' }} >All</option>
                   <option value="0" {{Request::get('order_status') != '' ? 'selected' : '' }}>Confirmed</option>
                  <option value="1" {{Request::get('order_status') == 1 ? 'selected' : '' }}>Cancelled</option>
                  <option value="2" {{Request::get('order_status') == 2 ? 'selected' : '' }}>Read to Dispatch</option>
                  <option value="3" {{Request::get('order_status') == 3 ? 'selected' : '' }}>Shipped</option>
                  <option value="4" {{Request::get('order_status') == 4 ? 'selected' : '' }}>Delivered</option>
                  <option value="5" {{Request::get('order_status') == 5 ? 'selected' : '' }}>Returned</option>
                  <option value="6" {{Request::get('order_status') == 6 ? 'selected' : '' }}>Payment Pending</option>
                  <option value="7" {{Request::get('order_status') == 7 ? 'selected' : '' }}>Payment Fail</option>
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
                <input type="text" name="from_date" class="form-control" id="from_date" placeholder="MM-DD-YYYY" value="{{Request::get('from_date')}}" readonly="readonly">
                <i class="fa fa-calendar input-icon" aria-hidden="true"></i>
                <span class="red-text" id="error-from_date"></span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="row">
              <div class="col-md-4 col-12">
                <lable class="control-label">To:</lable>
              </div>
              <div class="col-md-8 col-12">
                <input type="text" name="to_date" class="form-control" id="to_date" placeholder="MM-DD-YYYY" value="{{Request::get('to_date')}}" readonly="readonly">
                <i class="fa fa-calendar input-icon" aria-hidden="true"></i>
                <span class="red-text" id="error-to_date"></span>
              </div>
            </div>
          </div>

          <div class="form-group">
            <div class="row">
              <div class="col-md-4 col-12">
                
              </div>
              <div class="col-md-8 col-12">
                <button type="submit" class="btn btn-primary">Apply</button>
                <a href="{{URL('admin/order/view')}}"> <button type="button" class="btn btn-primary">Reset</button></a>
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
//.datepicker("setDate",'now');  
$(function() {   
      $( "#from_date" ).datepicker({   
      defaultDate: "+1w",
      maxDate: 0,  
      //changeMonth: true,
      //changeYear: true,
      //changeDay: true,
      //showButtonPanel: true,   
     // numberOfMonths: 1,  
      onClose: function( selectedDate ) {  
        $( "#to_date" ).datepicker( "option", "minDate", selectedDate );  
      }  
    })
    $( "#to_date" ).datepicker({
      defaultDate: "+1w",
      maxDate: 0,
      //  changeMonth: true,
      //  numberOfMonths: 1,
      onClose: function( selectedDate ) {
        $( "#from_date" ).datepicker( "option", "maxDate", selectedDate );
      }
    });  
  }); 


  function validateForm() {
    var from_date = document.forms["orderForm"]["from_date"].value;
    var to_date = document.forms["orderForm"]["to_date"].value;
    var order_status = document.forms["orderForm"]["order_status"].value;
    var supplier = document.forms["orderForm"]["supplier"].value;
    if (from_date != "" || to_date != "") {
      if (from_date == ""){
        document.getElementById('error-from_date').innerHTML = " Please Enter From Date"
        //alert("Please Select From Date");
        return false;
      }
      if(to_date == ""){
         $('#to_date').val(from_date);
        /*document.getElementById('error-to_date').innerHTML = " Please Enter To Date"
        return false;*/
      }
      
    }
    /*if (from_date == "" && to_date == "" && order_status == "" && supplier == "") {
      
      document.getElementById('error-all').innerHTML = "Please Select Atleast one filter";
      return false;
    }*/
  } 
/*$(document).ready(function () {
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
  */
</script>
@stop
