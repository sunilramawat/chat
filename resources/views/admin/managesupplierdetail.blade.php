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
          <div class="box-main-title">Supplier Detail</div>
        </div>
        <div class="box-main-content">
          <div class="row">
            <div class="col-md-12 col-xl-6">
              <form>
                <div class="form-group">
                  <div class="row align-items-end">
                    <div class="col-md-4 col-12">
                      <lable class="control-label">Logo:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                      <div class="manage-supplierdetail-profile"><img src="{{URL('public/admin/dist/img/profile.png')}}"></div>
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
                      <lable class="control-label">Business Name:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                      <input type="text" name="" class="form-control" placeholder="Business Name">
                      
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-4 col-12">
                      <lable class="control-label">Email Address:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                      <input type="text" name="" class="form-control" placeholder="Email Address">
                      
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-4 col-12">
                      <lable class="control-label">Phone Number:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                      <input type="text" name="" class="form-control" placeholder="Phone Number">
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-4 col-12">
                      <lable class="control-label">Business Identification:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                      <input type="text" name="" class="form-control" placeholder="Business Identification">
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-4 col-12">
                      <lable class="control-label">Location:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                      <input type="text" name="" class="form-control" placeholder="Location">
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-4 col-12">
                      <lable class="control-label">Status:</lable>
                    </div>
                    <div class="col-md-8 col-12">
                      <label class="switch">
                        <input type="checkbox" checked>
                        <span class="switchslider round"></span>
                      </label>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-4 col-12">
                      
                    </div>
                    <div class="col-md-8 col-12">
                      <button type="button" class="btn btn-primary mr-2">Update</button>
                      <button type="button" class="btn btn-primary">View Products</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
    <!-- /.content -->


@stop