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
          <div class="box-main-title">Product - Manila Oriental Market</div>
          <div class="box-main-top-right">
            <div class="box-serch-field">
              <input type="text" class="box-serch-input" name="" placeholder="Search">
              <i class="fa fa-search" aria-hidden="true"></i>
            </div>
            
          </div>
        </div>
        <div class="box-main-table">
          <div class="table-responsive">
            <table class="table table-bordered admin-table">
              <thead>
                <tr>
                  <th>S. No.</th>
                  <th>Product</th>
                  <th>Supplier</th>
                  <th>Price</th>
                  <th>Quantity</th>
                  <th>Status</th>
                  
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td class="Manage-supplier-product-record">
                    <div class="Manage-supplier-product-data">
                      <span><img src="{{URL('public/admin/dist/img/manage-product-img.png')}}"></span>
                      Metal Round Wire Nails for Hanging 
                    </div>
                  </td>
                  <td>Manila Oriental Market </td>
                  <td>$15.50 </td>
                  <td>10 </td>
                  <td>
                    <label class="switch">
                      <input type="checkbox" checked>
                      <span class="switchslider round"></span>
                    </label>
                  </td>
                  
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
@stop