@extends('admin.mainlayout')
@section('content')

  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Manage Suppliers </h1>
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
          <div class="box-main-title">Product - {{$Product_name}}</div>
          <div class="box-main-top-right">
            <div class="box-serch-field">
              <input type="text" class="box-serch-input" name="" id ="search" placeholder="Search">
              <i class="fa fa-search" aria-hidden="true"></i>
            </div>
            
          </div>
        </div>
        <div  id="maintable">
        <div class="box-main-table" id="maintable">
          <div class="table-responsive">
            <table class="table table-bordered admin-table dataTable"  id="example2" >   <thead>
                <tr>
                  <th>S. No.</th>
                  <th>Product</th>
                  <th>Price</th>
                  <th>Quantity</th>
                  <th>Status</th>
                  
                </tr>
              </thead>
              <tbody>
                @foreach($Product_manage as $product_key => $product_value)
                <tr>
                   <td>{{$product_key+1}}</td>
                  <td class="Manage-supplier-product-record">
                    <div class="Manage-supplier-product-data">
                      <span><img src="{{$product_value->product_image_url}}"></span>
                      {{$product_value->product_name}}
                    </div>
                  </td>
                  <td> ${{$product_value->ProductDetailManage[0]->price_per_unit}}</td>
                  <td>{{$product_value->ProductDetailManage[0]->quantity}} </td>
                   <td>
                      @if($product_value->block_status == "")
                         <label class="switch ">
                        <a onClick="ProductStatus({{$product_value->id}},1)" class="toggle-btn " style="cursor: pointer">
                          <!-- <i class="fa fa-check-circle enablecheck fa-lg"></i> -->
                            <input type="checkbox" checked>
                             <span class="switchslider round"></span>
                        </a>
                        </label>  
                      @else
                         <label class="switch">
                        <a onClick="ProductStatus({{$product_value->id}},0)" class="toggle-btn" style="cursor: pointer">
                          <!-- <i class="fa fa-times-circle disblecheck fa-lg"></i> -->
                          <!-- <i class="fa fa-toggle-off "></i> -->
                          <input type="checkbox" >
                             <span class="switchslider round"></span>
                        </a>  
                      </label>
                      @endif()  
                    </td> 
                  
                </tr>
                @endforeach
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
                
              </div>
              <div class="col-md-8 col-12">
                <button type="button" class="btn btn-primary">Apply</button>
                 <a href="{{URL('admin/supplier/product')}}"> <button type="button" class="btn btn-primary">Reset</button></a>
              </div>
            </div>
          </div>
        </form>
        
      </div>
      
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script type="text/javascript">

function ProductStatus(Id, Status)
{ 
  //alert(Status);
  $("#LoadingProgress").fadeIn('fast');
    $.ajax({
      url: "{{ URL('admin/supplier/ProductStatus') }}/"+Id+"/"+Status,
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
  $('#searchs').on('keyup',function(){
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