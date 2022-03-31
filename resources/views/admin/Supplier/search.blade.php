@extends('admin.mainlayout')
@section('content')

  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Manage Supplier </h1>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
    <!-- /.content-header -->

    <!-- Main content --> 
  <section class="content" >
    <div class="container-fluid">
      <div class="box-main">
        <div class="box-main-top">
          <div class="box-main-title">Supplier List</div>

            <div class="box-main-top-right" >
              <div class="box-serch-field">
                <input type="text" class="box-serch-input" name="" id="search" placeholder="Search">
                <i class="fa fa-search" aria-hidden="true"></i>
              </div>  
             <!--  <button class="btn btn-primary " data-toggle="modal" data-target="#exampleModal"><i class="fa fa-filter" aria-hidden="true"></i></button> -->
              <button class="btn btn-primary " data-toggle="modal" data-target="#exampleModal"><i class="fa fa-filter" aria-hidden="true"></i></button>
              <a href="{{URL('admin/pendingrequest/view')}}">{{ Form::submit('Pending Request',array('class'=>'btn btn-primary')) }}</a>
              
              <!-- <button class="btn btn-primary ">Pending Request (25)</button> -->
            </div>
          </div>
        </div>
       
        <div  id="maintable">
          <div class="box-main-table" id="maintable" >
            <div class="table-responsive">
              <table class="table table-bordered admin-table dataTable"  id="example2" >
               
                <thead >
                  <tr>
                    <th>S.No</th>
                    <th>Business Name</th> 
                    <th>Owner Name</th> 
                    <th>Email Address</th> 
                    <th>Phone Number</th> 
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($Supplier_manage as $no => $chip)
                  <tr>
                    <td>{{$no+1}}</td>
                    <td>{{$chip->business_name}}</td>
                    <td>{{$chip->first_name}}</td>
                    <td>{{$chip->email}}</td>
                    <td>{{$chip->phone_code.' '.$chip->phone_number}}</td>
                   
                    <td>
                      @if($chip->is_block == "")
                         <label class="switch ">
                        <a onClick="ChangeStatus({{$chip->id}},1)" class="toggle-btn " style="cursor: pointer">
                          <!-- <i class="fa fa-check-circle enablecheck fa-lg"></i> -->
                            <input type="checkbox" checked>
                             <span class="switchslider round"></span>
                        </a>
                        </label>  
                      @else
                         <label class="switch">
                        <a onClick="ChangeStatus({{$chip->id}},0)" class="toggle-btn" style="cursor: pointer">
                          <!-- <i class="fa fa-times-circle disblecheck fa-lg"></i> -->
                          <!-- <i class="fa fa-toggle-off "></i> -->
                          <input type="checkbox" >
                             <span class="switchslider round"></span>
                        </a>  
                      </label>
                      @endif()  
                    </td> 
                    <td>
                      <a href="{{URL('admin/supplier/edit')}}/{{$chip->id}}">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                      </a>  
                    </td>
                  </tr>
                  @endforeach()
                </tbody>
               
              </table>
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
         {!!Form::open(['url'=>'admin/supplier/search', 'enctype' => 'multipart/form-data', 'method'=>'get']) !!} 
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
          <!-- <div class="form-group">
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
          </div> -->
          <div class="form-group">
            <div class="row">
              <div class="col-md-4 col-12">
                
              </div>
              <div class="col-md-8 col-12">
                <button type="submit" class="btn btn-primary">Apply</button>
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
  //$('#exampleModal').on('keyup',function(){
  $/*('#searchfilter').click(function(){
    //alert('das');
    var fromDate = $('#from_date').val();
    var toDate = $('#to_date').val();
    $value=$(this).val();
      $.ajax({
        url: "{{ URL('admin/supplier/search') }}",
        type : 'get',
        data:{'fromDate':fromDate, 'toDate':toDate},
        success:function(data){
         // window.location.reload();
       // $("#LoadingProgress").fadeOut('fast');
         // $('#maintable').html(data);
        }
    });
  })*/

  //your code here
});

 $(function() {   
      $( "#from_date" ).datepicker({   
      defaultDate: "+1w",  
      changeMonth: true,   
     // numberOfMonths: 1,  
      onClose: function( selectedDate ) {  
        $( "#to_date" ).datepicker( "option", "minDate", selectedDate );  
      }  
    });  
    $( "#to_date" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      //  numberOfMonths: 1,
      onClose: function( selectedDate ) {
        $( "#from_date" ).datepicker( "option", "maxDate", selectedDate );
      }
    });  
  }); 
</script>
@stop
