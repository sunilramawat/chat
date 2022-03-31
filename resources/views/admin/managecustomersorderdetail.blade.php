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
                <span class="dark-text">#HX251258</span>
                <span class="title-label">Delivered</span>
              </div>
          </div>
          <div class="box-main-top-right">
            <button type="button" class="btn btn-primary">Back</button>
          </div>
        </div>
        <div class="box-main-content">
          <div class="row">
            <div class="col-md-12 col-xl-6">
              <form>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-4 col-12">
                      <lable class="control-label">Full Name:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                      <div class="request-detail-text">Jonathan Grey</div>
                      
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-4 col-12">
                      <lable class="control-label">Email Address:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                      <div class="request-detail-text">jonthangreey@gmail.com</div>
                      
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-4 col-12">
                      <lable class="control-label">Phone Number:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                      <div class="request-detail-text">+233 9854 251</div>
                      
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-4 col-12">
                      <lable class="control-label">Shipping Address:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                      <div class="request-detail-text">20 Maple Avenue San Pedro, CA 90731</div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-4 col-12">
                      <lable class="control-label">Order Amount:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                      <div class="request-detail-text pl-9"><span class="currency-sign">$</span> 57.00</div>
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
                        <span class="logo-img">
                            <img src="../public/admin/dist/img/order-logo.png" alt="">
                        </span>
                        Manila Oriental Market
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
          <div class="box-main-top-right">
            <div class="box-serch-field">
                <input type="text" name="" class="form-control" placeholder="Choose Date">
                <i class="fa fa-calendar input-icon" aria-hidden="true"></i>
            </div>
          </div>
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
                <tr>
                  <td class="Manage-supplier-product-record">
                    <div class="Manage-supplier-product-data">
                      <span><img src="{{URL('public/admin/dist/img/manage-product-img.png')}}"></span>
                      Metal Round Wire Nails for Hanging 
                    </div>
                  </td>
                  <td>2</td>
                  <td>$15.00 </td>
                  <td>$30.00</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="box-order-total-cover">
          <div class="box-order-total">
              <div class="order-total">
                <div class="order-label">
                  Subtotal (2 Items):
                </div>
                <div class="order-value">
                  $55.00
                </div>
              </div>
              <div class="order-total">
                <div class="order-label">
                  Discount:
                </div>
                <div class="order-value">
                  -5.00
                </div>
              </div>
              <div class="order-total">
                <div class="order-label">
                  Delivery Fee:
                </div>
                <div class="order-value">
                  $5.00
                </div>
              </div>
              <div class="order-total mb-0">
                <div class="order-label">
                  Service Fee:
                </div>
                <div class="order-value">
                  $2.00
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
                  $57.00
                </div>
              </div>
            </div>
        </div>
      </div>
    </div><!-- /.container-fluid -->  
  </section>
    <!-- /.content -->
@stop