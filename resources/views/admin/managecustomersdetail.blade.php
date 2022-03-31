@extends('admin.mainlayout')
@section('content')

  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Manage Customers </h1>
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
          <div class="box-main-title">Detail</div>
          <div class="box-main-top-right">
            <button type="button" class="btn btn-primary">Back</button>
          </div>
        </div>
        <div class="box-main-content">
          <div class="row">
            <div class="col-md-12 col-xl-6">
              <form>
                <div class="form-group">
                  <div class="row align-items-end">
                    <div class="col-md-4 col-12">
                      <lable class="control-label">Picture:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                      <div class="manage-supplierdetail-profile"><img src="http://localhost/material/public/admin/dist/img/profile.png"></div>
                      <label class="manage-supplierdetail-browse">
                        <input type="file" name="">
                        <button class="btn btn-primary">Browse</button>
                      </label>
                    </div>
                  </div>
                </div>
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
                  <th>S. No.</th>
                  <th>Order ID</th>
                 
                  <th>Supplier</th>
                  <th>Order Date</th>
                   <th>Order Amount</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>Carol Ramirez </td>
                  <td>carolremirez@gmail.com </td>
                  <td>+233 9854 251 </td>
                  <td>Sunset Blvd Los Angeles California</td>     
                  <td class="blue-text">Ready to Dispatch</td>
                  <td><a href=""><i class="fa fa-eye" aria-hidden="true"></i></a></td>
                </tr>
                <tr>
                <td>2</td>
                  <td>Carol Ramirez </td>
                  <td>carolremirez@gmail.com </td>
                  <td>+233 9854 251 </td>
                  <td>Sunset Blvd Los Angeles California</td>
                  <td class="green-text">Delivered</td>
                  <td><a href=""><i class="fa fa-eye" aria-hidden="true"></i></a></td>
                </tr>
                <tr>
                <td>3</td>
                  <td>Carol Ramirez </td>
                  <td>carolremirez@gmail.com </td>
                  <td>+233 9854 251 </td>
                  <td>Sunset Blvd Los Angeles California</td>
                  <td class="red-text">Cancelled</td>
                  <td><a href=""><i class="fa fa-eye" aria-hidden="true"></i></a></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="box-main-bottom">
          <div class="box-main-showing">Showing 1 to 10 of 57 entries</div>
          <ul class="pagination">
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
          </ul>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
    <!-- /.content -->
@stop