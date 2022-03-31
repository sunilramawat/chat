@extends('admin.mainlayout')
@section('content')

  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Manage Trades </h1>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
    <!-- /.content-header -->

    <!-- Main content --> 
  <section class="content" >
     @include('admin.alert_message')
    <div class="container-fluid">
      <div class="box-main">
        <div class="box-main-table">
          <div class="table-responsive">
            <div class="box-main-title">Sub Categories -  {{ $Trade_name }} </div>
              <div class="box-main-top-right">
                <div class="box-serch-field">
                  <input type="text" class="box-serch-input" name="" id ="search"  placeholder="Search">
                  <i class="fa fa-search" aria-hidden="true"></i>
                </div>
                <!--  <button class="btn btn-primary " data-toggle="modal" data-target="#exampleModal"><i class="fa fa-filter" aria-hidden="true"></i></button> -->
                <a href="{{URL('admin/subtrade/add')}}">{{ Form::submit('Add New',array('class'=>'btn btn-primary')) }}</a>
                <!-- <button class="btn btn-primary ">Pending Request (25)</button> -->
              </div>
            </div>
          </div>
          <div  id="maintable" class="maintable">
            <div class="box-main-table" id="maintable" >
              <div class="table-responsive">
                <table class="table table-bordered admin-table dataTable"  id="example2" >
               
                  <thead>
                    <tr>
                      <th>S.No</th>
                      <th>Sub Categories</th> 
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($SubTrades_manage as $no => $chip)
                    {{$chip->trade_manage}}
                    @php $trade_id = $chip->trade_id
                    @endphp
                    <tr>
                      <td>{{$no+1}}</td>
                      <td>{{$chip->sub_trade}}</td>
                      <td><!-- <a href=""><i class="fa fa-eye" aria-hidden="true"></i></a> -->
                       <!--  <a href="{{URL('admin/subtrade/detail')}}/{{$chip->id}}">
                          <i class="fa fa-eye" aria-hidden="true"></i>
                        </a>  
                        &nbsp; -->
                        <!-- <a href="{{URL('admin/subtrade/delete')}}/{{$chip->id}}">
                          <i class="fa fa-trash" aria-hidden="true"></i>
                        </a>  -->
                        <a  onClick="DeleteTrade({{$chip->id}})" style="cursor: pointer">
                                  <i class="fa fa-trash aria-hidden="true""></i>  
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
        <form>
          <div class="form-group">
            <div class="row">
              <div class="col-md-4 col-12">
                <lable class="control-label">From:</lable>
              </div>
              <div class="col-md-8 col-12">
                <input type="text" name="" class="form-control" placeholder="MM-DD-YYYY">
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
                <input type="text" name="" class="form-control" placeholder="MM-DD-YYYY">
                <i class="fa fa-calendar input-icon" aria-hidden="true"></i>
              </div>
            </div>
          </div>
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
                
              </div>
              <div class="col-md-8 col-12">
                <button type="button" class="btn btn-primary">Apply</button>
              </div>
            </div>
          </div>
        </form>
        
      </div>
      
    </div>
  </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> 
<script type="text/javascript">
$(document).ready(function(){
  //$(".box-main-title").html("list");
 // $(".sunil").html("Hello <b>worldq!</b>");
});
function ChangeStatus(Id, Status)
{	
	$("#LoadingProgress").fadeIn('fast');
		$.ajax({
			url: "{{ URL('admin/trade/ChangeStatus') }}/"+Id+"/"+Status,
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


function DeleteTrade(Id, Status)
{ 
  $("#LoadingProgress").fadeIn('fast');
    if (confirm('Are you sure you want to delete this sub trade?')) {
      $.ajax({
        url: "{{ URL('admin/subtrade/delete') }}/"+Id,
        type: "GET",
        contentType: false,
        cache: false,
        processData:false,
      success: function( data, textStatus, jqXHR ) {
        if(data == 0){
          alert('Sorry, this sub trade cannot be deleted. It is associated with multiple products.');
        }else{
          window.location.reload();
         $("#LoadingProgress").fadeOut('fast');
        }
        //alert('das');
         //window.location.reload();
         //$("#LoadingProgress").fadeOut('fast');
      },
      error: function( jqXHR, textStatus, errorThrown ) {
        //alert('dassa');
      }
    });
  }
}
/**/
</script>
@stop
