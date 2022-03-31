@extends('admin.mainlayout')
@section('content')

  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Manage Orders</h1>
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
          <div class="box-main-title delivered-title">
              <div class="title">
               
                <span class="light-text">Order ID</span>
                <span class="dark-text">#{{$Supplier->order_code}}</span>
                
                @if($Supplier['order_status'] == 0)
                       <span class="title-label-blue">Confirmed</span>
                    @elseif($Supplier['order_status'] == 1)
                       <span class="title-label-red">Cancelled </span>
                    @elseif($Supplier['order_status'] == 2)
                      <span class="title-label-blue">Ready to Dispatch</span>
                    @elseif($Supplier['order_status'] == 3)
                      <span class="title-label-blue">Shipped </span>
                    @elseif($Supplier['order_status'] == 4)
                       <span class="title-label-green">Delivered </span>
                    @elseif($Supplier['order_status'] == 5)
                       <span class="title-label-red">Retured </span>
                    @elseif($Supplier['order_status'] == 6)
                       <span class="title-label-red">Payment Pending </span>  
                    @else
                       <span class="title-label-red">Payment Fail</span>
                    @endif
              </div>
          </div>
          <div class="box-main-top-right">
            <a href="{{URL('admin/payment/view')}}"><button type="button" class="btn btn-primary">Back</button></a></div>
          </div>
        
          <div class="box-main-content">
            <div class="row">
              <div class="col-md-12 col-xl-6">
                <form>
                  <div class="form-group">
                    <div class="row">
                      <div class="col-md-4 col-12">
                        <lable class="control-label">Customer Name:</lable>
                      </div>
                      <div class="col-md-8 col-12">
                        <div class="request-detail-text">{{$Supplier->customer_name}}</div>
                        
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="row">
                      <div class="col-md-4 col-12">
                        <lable class="control-label">Order Date:</lable>
                      </div>
                      <div class="col-md-8 col-12">
                        <div class="request-detail-text">{{date('M d,Y',strtotime($Supplier->order_date))}}</div>
                        
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="row">
                      <div class="col-md-4 col-12">
                        <lable class="control-label">Phone Number:</lable>
                      </div>
                      <div class="col-md-8 col-12">
                        <div class="request-detail-text">+{{$Supplier->customer_phone_code}} {{$Supplier->customer_phone_number}}</div>
                        
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="row">
                      <div class="col-md-4 col-12">
                        <lable class="control-label">Shipping Address:</lable>
                      </div>
                      <div class="col-md-8 col-12">
                        <div class="request-detail-text">{{$Supplier->address1}} {{$Supplier->address2}}, {{$Supplier->city_name}}, {{$Supplier->state_name}}, {{$Supplier->country_name}}, {{$Supplier->zip_code}}</div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="row">
                      <div class="col-md-4 col-12">
                        <lable class="control-label">Order Amount:</lable>
                      </div>
                      <div class="col-md-8 col-12">
                        <div class="request-detail-text pl-9"><span class="currency-sign">${{$Supplier->total_amount}}</span> </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="row">
                      <div class="col-md-4 col-12">
                        <lable class="control-label">Supplier:</lable>
                      </div>
                      <div class="col-md-8 col-12">
                        <div class="request-detail-text">
                          <span class="logo-img manage-supplierdetail-profile">
                              <img src="{{$Supplier->photo}}" alt="">
                          </span>
                          {{$Supplier->supplier_name}}
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div class="box-separator">
              <hr>
          </div>
          <div class="box-main-top">
            <div class="box-main-title">Orders List</div>
            <!--   <div class="box-main-top-right">
              <div class="box-serch-field">
                  <input type="text" name="" class="form-control" placeholder="Choose Date">
                  <i class="fa fa-calendar input-icon" aria-hidden="true"></i>
              </div>
            </div> -->
          </div>
          <div class="box-main-table">
            <div class="table-responsive">
              <table class="table table-bordered admin-table">
                <thead>
                  <tr>
                    
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Amount</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($Supplier->OrderProductlManage as $product_key => $product_value)
                  <tr>
                    <td class="Manage-supplier-product-record">
                      <div class="Manage-supplier-product-data">
                        <span><img src="{{$product_value->ProductManage[0]['product_image_url']}}"></span>
                        {{$product_value->ProductManage[0]['product_name']}}
                      </div>
                    </td>
                    <td>{{$product_value['quantity']}}</td>
                    <td>${{$product_value['per_unit_price']}}</td>
                    <td>${{$product_value['total_amount']}}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <div class="box-order-total-cover">
            <div class="box-order-total">
                <div class="order-total">
                  <div class="order-label">
                    Subtotal:
                  </div>
                  <div class="order-value">
                   ${{$Supplier->sub_total}}
                  </div>
                </div>
                <div class="order-total">
                  <div class="order-label">
                    Tax:
                  </div>
                  <div class="order-value">
                   ${{$Supplier->tax_fee}}
                  </div>
                </div>
                @if($Supplier->tip_fee != 0)
                <div class="order-total">
                  <div class="order-label">
                    Tip:
                  </div>
                  <div class="order-value">
                    ${{$Supplier->tip_fee}}
                  </div>
                </div>
                @endif
                <div class="order-total">
                  <div class="order-label">
                    Discount:
                  </div>
                  <div class="order-value">
                    $0
                  </div>
                </div>
                <div class="order-total">
                  <div class="order-label">
                    Delivery Fee:
                  </div>
                  <div class="order-value">
                    ${{$Supplier->delivery_fee}}
                  </div>
                </div>
                <div class="order-total mb-0">
                  <div class="order-label">
                    Service Fee:
                  </div>
                  <div class="order-value">
                    ${{$Supplier->service_fee}}
                  </div>
                </div>
                <div class="order-total mb-0">
                  <div class="order-separator">
                    <hr>
                  </div>
                </div>
                <div class="order-total">
                  <div class="order-label-total">
                    Total:
                  </div>
                  <div class="order-value-total">
                    ${{$Supplier->total_amount}}
                  </div>
                </div>
              </div>
          </div>
      </div>
      </div>
    </div><!-- /.container-fluid -->  
  </section>
    <!-- /.content -->
@stop