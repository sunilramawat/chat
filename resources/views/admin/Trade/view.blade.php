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
  <section class="content">
    @include('admin.alert_message')
    <div class="container-fluid">
      <div class="box-main">
        <div class="box-main-top">
          <div class="box-main-title">Trade List</div>
          <div class="box-main-top-right">
            <div class="box-serch-field">
              <input type="text" class="box-serch-input" name="" id ="search" placeholder="Search">
              <i class="fa fa-search" aria-hidden="true"></i>
            </div>
           <!--  <button class="btn btn-primary " data-toggle="modal" data-target="#exampleModal"><i class="fa fa-filter" aria-hidden="true"></i></button> -->
            <a href="{{URL('admin/trade/add')}}">{{ Form::submit('Add New',array('class'=>'btn btn-primary')) }}</a>
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
                    <th>Name</th> 
                    <th>Sub Categories</th> 
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($Trades_manage as $no => $chip)
                  <tr>
                    <td>{{$no+1}}</td>
                    <td>{{$chip->trade}}</td>
                    <td>
                         <a href="{{URL('admin/subtrade/view')}}/{{$chip->id}}">
                        {{$chip->SubTradeManage->count()}}
                      </a>
                    </td>
                    <td>
                                @if($chip->block_status == 0)
                                   <label class="switch ">
                                  <a onClick="ChangeStatus({{$chip->id}},1)" class="toggle-btn " style="cursor: pointer">
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
                    <td><!-- <a href=""><i class="fa fa-eye" aria-hidden="true"></i></a> -->
                      <a href="{{URL('admin/subtrade/view')}}/{{$chip->id}}">
                                  <i class="fa fa-eye" aria-hidden="true"></i>
                                </a>  
                                &nbsp;
                      <a  onClick="DeleteTrade({{$chip->id}})" style="cursor: pointer">
                                  <i class="fa fa-trash aria-hidden="true""></i>
                                </a>          
                    </td>
                  </tr>
                  @endforeach()
                </tbody>
              </table>
              <hr>



            </div>
          </div>
          <!-- <div class="box-main-bottom">
          <div class="box-main-showing">Showing {{$current_page}} to {{$row_count}} of {{$total_count}} entries</div>
          {{$Trades_manage->links() }} -->
          <!-- <ul class="pagination">
            <li class="page-item disabled">
              <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
            </li>
            <li class="page-item"><a class="page-link" href="#">1</a></li>
            <li class="page-item active" aria-current="page">
              <a class="page-link" href="#">2</a>
            </li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item">
              <a class="page-link" href="#">Next</a>
            </li>
          </ul> -->
        <!-- </div> -->  
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
<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.3.1/css/select.dataTables.min.css">

<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
   var table = $('#example').DataTable({
      'ajax': 'https://gyrocode.github.io/files/jquery-datatables/arrays_id.json',
      'columnDefs': [
         {
            'targets': 0,
            'checkboxes': {
               'selectRow': true
            }
         }
      ],
      'select': {
         'style': 'multi'
      },
      'order': [[1, 'asc']]
   });
   
   // Handle form submission event 
   $('#frm-example').on('submit', function(e){
      var form = this;
      
      var rows_selected = table.column(0).checkboxes.selected();

      // Iterate over all selected checkboxes
      $.each(rows_selected, function(index, rowId){
         // Create a hidden element 
         $(form).append(
             $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'id[]')
                .val(rowId)
         );
      });

      // FOR DEMONSTRATION ONLY
      // The code below is not needed in production
      
      // Output form data to a console     
      $('#example-console-rows').text(rows_selected.join(","));
      
      // Output form data to a console     
      $('#example-console-form').text($(form).serialize());
       
      // Remove added elements
      $('input[name="id\[\]"]', form).remove();
       
      // Prevent actual form submission
      e.preventDefault();
   });   
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
    if (confirm('Are you sure you want to delete this trade?')) {
      $.ajax({
        url: "{{ URL('admin/trade/delete') }}/"+Id,
        type: "GET",
        contentType: false,
        cache: false,
        processData:false,
      success: function( data, textStatus, jqXHR ) {
        if(data == 0){
          alert('Sorry, this trade cannot be deleted. It is associated with multiple products.');
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
$(document).ready(function () {
  $('#search').on('keyup',function(){
    $value=$(this).val();
      $.ajax({
        url: "{{ URL('admin/trade/search') }}",
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
