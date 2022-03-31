@extends('admin.mainlayout')
@section('content')

<style type="text/css">
  table.dataTable.select tbody tr,
table.dataTable thead th:first-child {
  cursor: pointer;
}
.pagination{
  padding-top: 40px;
}
</style>
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Manage Payout </h1>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
    <!-- /.content-header -->

    <!-- Main content --> 
  
 <form id="frm-example" action="{{URL('admin/payout/view')}}"> 
  <section class="content">
    <div class="container-fluid">
      <div class="box-main">
        <div class="box-main-top">
          <div class="box-main-title">Payout List -
             @if(@Request::get('cust_month') == '')
                ( {{date("F", mktime(0, 0, 0, date("m"), 10))}}, {{date("Y")}} )

              @else
                ( {{date("F", mktime(0, 0, 0, Request::get('cust_month'), 10))}}, {{date("Y")}} )
              @endif
          </div>
            <!-- Image loader -->
                <div id='loader' style='display: none;'>
                <img src="{{URL('public/admin/dist/img/loder.gif')}}" width='80px' height='80px' style="position:fixed; top: 35%; left: 50%;">
              </div>
              <!-- Image loader -->
            <div class="box-main-top-right">
              <!-- <div class="box-serch-field">
                <input type="text" class="box-serch-input" name="" id ="search" placeholder="Search">
                <i class="fa fa-search" aria-hidden="true"></i>
              </div>  -->
                    @if(date("m") <=  Request::get('cust_month'))         
                    @else
                      <button id="payout" class="btn btn-primary">Process Payout  </button> 
                    @endif
                
                
               
                 <!--  <button class="btn btn-primary " data-toggle="modal" data-target="#exampleModal"><i class="fa fa-filter" aria-hidden="true"></i></button> -->
                <!-- <a href="#">{{ Form::submit('Process Payout',array('class'=>'btn btn-primary','id'=>'Payout')) }}</a>  -->
                <button class="btn btn-primary"  data-toggle="modal" data-target="#exampleModal"><i class="fa fa-filter" aria-hidden="true"></i></button>
                  
               
                <!-- <button class="btn btn-primary ">Pending Request (25)</button> -->
            </div>
          </div>
        
        <div id="maintable" class="maintable">
          <div class="box-main-table" id="maintable" >
            <div class="table-responsive">
             
             <!--  <table class="display select table table-bordered admin-table dataTable"  id="example" > -->
               <table class="display select table table-bordered admin-table dataTable"  id="example" >
               
                <thead>
                  <tr>
                    <th><input name="select_all" value="1" type="checkbox"></th>
                    <th>S.No</th>
                    <th>Business / Supplier Name</th> 
                    <th>No. of Orders</th> 
                    <th>Total Amount</th> 
                    <th>Pending Amount</th> 
                    <th>Payment Status</th> 
                    <th>Last Payout Date</th> 
                    
                  </tr>
                </thead>
                <tbody>
                  @foreach($Supplier_manage as $no => $chip)
                  <tr>
                    <td>
                      @if($chip->payment_status ==  2 || $chip->payment_status ==  1 )         
                          {{0}}    
                      @else
                        @if(date("m") <=  Request::get('cust_month')) 
                          {{0}}            
                        @else
                          {{$chip->id}}       
                        @endif

                             
                      @endif
                      </td>
                    <td>{{$no+1}}</td>
                    <td>{{$chip->supplier_name}}</td>
                    <td>{{$chip->no_of_order}}</td>
                    <td class="currency-text">${{$chip->total_amount}}</td>
                    <td class="currency-text">

                      ${{$chip->pending_amount?$chip->pending_amount:0}}</td>
                    <td >
                      @if($chip->payment_status ==  0)         
                             Payment Pending    
                      @elseif($chip->payment_status ==  1)       
                         Paid 
                      @elseif($chip->payment_status ==  2)
                        No Payment Method             
                      @endif
                    </td>
                    <td>{{ $chip->last_payout_date? date('M d,Y',strtotime($chip->last_payout_date)):'-'}}</td>
                  </tr>
                  @endforeach()
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      </div>
   <!-- /.container-fluid -->
  </section>
    <!-- /.content -->
  {!!Form::close()!!}    
        
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Filter</h5>
        <button type="button"  class="close" data-dismiss="modal" aria-label="Close">
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
         {!!Form::open(['url'=>'admin/payout/view',  'name' => 'orderForm' ,'enctype' => 'multipart/form-data', 'method'=>'get','onsubmit'=>"return validateForm()"]) !!} 
         
          <div class="form-group">
            <div class="row">
              <div class="col-md-4 col-12">
                <lable class="control-label">Year:</lable> 
              </div>
              <div class="col-md-8 col-12">
                  <select class="form-control my-select" name="cust_year" id="cust_year">
                    <option value="00" {{Request::get('cust_year') == '00' ? 'selected' : '' }}>Select Year</option>
                    <option value="2021" {{Request::get('cust_year') == '2021' ? 'selected' : '' }}>2021</option>
                   <!--  <option value="2022" {{Request::get('cust_year') == '2022' ? 'selected' : '' }}>2022</option>
                    <option value="2023" {{Request::get('cust_year') == '2023' ? 'selected' : '' }}>2023</option>
                    <option value="2024" {{Request::get('cust_year') == '2024' ? 'selected' : '' }}>2024</option>
                    <option value="2025" {{Request::get('cust_year') == '2025' ? 'selected' : '' }}>2025</option> -->
                  </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="row">
              <div class="col-md-4 col-12">
                <lable class="control-label">Month:</lable>
              </div>
              <div class="col-md-8 col-12">
               <select class="form-control my-select" name="cust_month" id="cust_month">
                    <option value="00" {{Request::get('cust_month') == '00' ? 'selected' : '' }}>Select Month</option>
                    <option value="01" {{Request::get('cust_month') == '01' ? 'selected' : '' }}>Jan</option>
                    <option value="02" {{Request::get('cust_month') == '02' ? 'selected' : '' }}>Feb</option>
                    <option value="03" {{Request::get('cust_month') == '03' ? 'selected' : '' }}>Mar</option>
                    <option value="04" {{Request::get('cust_month') == '04' ? 'selected' : '' }}>Apr</option>
                    <option value="05" {{Request::get('cust_month') == '05' ? 'selected' : '' }}>May</option>
                   <!--  <option value="06" {{Request::get('cust_month') == '06' ? 'selected' : '' }}>Jun</option>
                    <option value="07" {{Request::get('cust_month') == '07' ? 'selected' : '' }}>Jul</option>
                    <option value="08" {{Request::get('cust_month') == '08' ? 'selected' : '' }}>Aug</option>
                    <option value="09" {{Request::get('cust_month') == '09' ? 'selected' : '' }}>Sep</option>
                    <option value="10" {{Request::get('cust_month') == '10' ? 'selected' : '' }}>Oct</option>
                    <option value="11" {{Request::get('cust_month') == '11' ? 'selected' : '' }}>Nov</option>
                    <option value="12" {{Request::get('cust_month') == '12' ? 'selected' : '' }}>Dec</option> -->
                  </select>
              </div>
            </div>
          </div>

          <div class="form-group">
            <div class="row">
              <div class="col-md-4 col-12">
                
              </div>
              <div class="col-md-8 col-12">
                <button type="submit" class="btn btn-primary">Apply</button>
                <a href="{{URL('admin/payout/view')}}"> <button type="button" class="btn btn-primary">Reset</button></a>
              </div>
            </div>
          </div>
        {!!Form::close()!!}    
        
      </div>
      
    </div>
  </div>
</div>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css"/>

    
    
    

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>

<script type="text/javascript">

//
// Updates "Select all" control in a data table
//
function updateDataTableSelectAllCtrl(table){
   var $table             = table.table().node();
   var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
   var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
   var chkbox_select_all  = $('thead input[name="select_all"]', $table).get(0);

   // If none of the checkboxes are checked
   if($chkbox_checked.length === 0){
      chkbox_select_all.checked = false;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = false;
      }

   // If all of the checkboxes are checked
   } else if ($chkbox_checked.length === $chkbox_all.length){
      chkbox_select_all.checked = true;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = false;
      }

   // If some of the checkboxes are checked
   } else {
      chkbox_select_all.checked = true;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = true;
      }
   }
}

$(document).ready(function (){
  $("#fill").attr('title', 'This is the hover-over text');
   // Array holding selected row IDs
   var rows_selected = [];
   var table = $('#example').DataTable({
      //'ajax': 'https://gyrocode.github.io/files/jquery-datatables/arrays_id.json',
    "processing": true,
    "paging": true,
    "lengthChange": false,
    "searching": true,
    "ordering": false,
    "bFilter":false,
    "info": true,
    "autoWidth": true,
    "responsive": false,
      'columnDefs': [{
         'targets': 0,
         'searchable':false,
         'orderable':false,
         'width':'1%',
         'className': 'dt-body-center',
         
         'render': function (data, type, full, meta){
          $("#loader").hide();
          if(data != 0){

             return '<input type="checkbox">';
          }else{
             return '<input type="checkbox" id="fill" disabled >';
          }
         }
      }],
      'order': [1, 'asc'],
      'rowCallback': function(row, data, dataIndex){
         // Get row ID
         var rowId = data[0];

         // If row ID is in the list of selected row IDs
         if($.inArray(rowId, rows_selected) !== -1){
            $(row).find('input[type="checkbox"]').prop('checked', true);
            $(row).addClass('selected');
         }
      }
   });

   // Handle click on checkbox
   $('#example tbody').on('click', 'input[type="checkbox"]', function(e){
      var $row = $(this).closest('tr');

      // Get row data
      var data = table.row($row).data();

      // Get row ID
      var rowId = data[0];

      // Determine whether row ID is in the list of selected row IDs 
      var index = $.inArray(rowId, rows_selected);

      // If checkbox is checked and row ID is not in list of selected row IDs
      if(this.checked && index === -1){
         rows_selected.push(rowId);

      // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
      } else if (!this.checked && index !== -1){
         rows_selected.splice(index, 1);
      }

      if(this.checked){
         $row.addClass('selected');
      } else {
         $row.removeClass('selected');
      }

      // Update state of "Select all" control
      updateDataTableSelectAllCtrl(table);

      // Prevent click event from propagating to parent
      e.stopPropagation();
   });

   // Handle click on table cells with checkboxes
   $('#example').on('click', 'tbody td, thead th:first-child', function(e){
      $(this).parent().find('input[type="checkbox"]').trigger('click');
   });

   // Handle click on "Select all" control
   $('thead input[name="select_all"]', table.table().container()).on('click', function(e){
      if(this.checked){
         $('#example tbody input[type="checkbox"]:not(:checked)').trigger('click');
      } else {
         $('#example tbody input[type="checkbox"]:checked').trigger('click');
      }

      // Prevent click event from propagating to parent
      e.stopPropagation();
   });

   // Handle table draw event
   table.on('draw', function(){
      // Update state of "Select all" control
      updateDataTableSelectAllCtrl(table);
   });
    
   // Handle form submission event 
   $('#frm-example').on('submit', function(e){
      //alert(rows_selected);
      var form = this;
      console.log(rows_selected);
      // Iterate over all selected checkboxes
      $.each(rows_selected, function(index, rowId){
         // Create a hidden element 
         $(form).append(
             $('<input>')
                .attr('type', 'text')
                .attr('name', 'id[]')
                .val(rowId)
         );
      });
        $.ajax({
        url: "{{ URL('admin/payout/payout') }}",
        type : 'get',
        data:{'search':rows_selected},
        beforeSend: function(){
          // Show image container
          //$("#loader").show();
        },
        success:function(data){
          var result = JSON.parse(data);
          //alert(result);
          console.log(result);
          if(result.succeed == true){
            alert('Payout request has been processed. Payment will be credited to the suppliers default account/card.')
            $("#loader").hide();
            location.reload(); 
            $('#maintable').html(data);
          }else{
             $("#loader").hide();
            //location.reload(); 
            alert(result.title)
          }
        }
      });
      // FOR DEMONSTRATION ONLY     
      
      // Output form data to a console     
      $('#example-console').text($(form).serialize());
      console.log("Form submission", $(form).serialize());
       
      // Remove added elements
      $('input[name="id\[\]"]', form).remove();
       
      // Prevent actual form submission
      e.preventDefault();
   });
});




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
//.datepicker("selectedDate",'now');  
$(function() {   
      /*$( "#from_date" ).datepicker({   
       changeYear: true,
        showButtonPanel: true,
        dateFormat: 'yy',
        onClose: function(dateText, inst) { 
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).datepicker('setDate', new Date(year, 1));
        }
    })
      $(".#from_date").focus(function () {
        $(".ui-datepicker-month").hide();
    });*/

    $('#from_date').datepicker( {
    dateFormat: "yy",
    yearRange: "c-100:c",
    changeMonth: false,
    changeYear: true,
    showButtonPanel: false,
    closeText:'Select',
    currentText: 'This year',
    onClose: function(dateText, inst) {
      var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
      $(this).val($.datepicker.formatDate('yy', new Date(year, 1, 1)));
    },
    onChangeMonthYear : function () {
      $(this).datepicker( "hide" );
    }
  }).focus(function () {
    $(".ui-datepicker-month").hide();
    $(".ui-datepicker-calendar").hide();
    $(".ui-datepicker-current").hide();
    $(".ui-datepicker-prev").hide();
    $(".ui-datepicker-next").hide();
    $("#ui-datepicker-div").position({
      my: "left top",
      at: "left bottom",
      of: $(this)
    });
  }).attr("readonly", false);
  
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

 /*$('#payout').on('click',function(){
    //$value= rows_selected;
    alert('asd');
      $.ajax({
        url: "{{ URL('admin/payout/payout') }}",
        type : 'get',
        data:{'search':rows_selected},
        success:function(data){
          $('#maintable').html(data);
        }
      });
  });*/
 

  
 
</script>
@stop
