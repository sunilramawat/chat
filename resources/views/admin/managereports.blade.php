@extends('admin.mainlayout')
@section('content')

  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Manage Orders </h1>
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
          <div class="box-main-title">Orders List</div>
          <div class="box-main-top-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                <i class="fa fa-filter" aria-hidden="true"></i>
            </button>
            <button class="btn btn-primary">Download
                <img src="../../../../dist/img/download-icon.png" alt="" class="download-icon">
            </button>  
          </div>
        </div>
        <div class="box-main-table">
          <div class="table-responsive">
            <table class="table table-bordered admin-table">
              <thead>
                <tr>
                  <th>S. No.</th>
                  <th>Business / Supplier Name</th>
                  <th>No. of Orders</th>
                  <th class="text-right">Total Amount</th>
                  <th class="text-right">Pending Amount</th>
                  <th>Last Payout Date</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>Manila Oriental Market </td>
                  <td>15</td>
                  <td class="text-right">$150.50</td>
                  <td class="text-right">$15.50 </td>
                  <td>21 July, 2020 19</td>
                </tr>
                <tr>
                  <td>2</td>
                  <td>Smart & Final</td>
                  <td>8</td>
                  <td class="text-right">$200.50</td>
                  <td class="text-right">$20.50 </td>
                  <td>19 ly, 2020 19</td>
                </tr>
                <tr>
                  <td>3</td>
                  <td>Manila Oriental Market </td>
                  <td>15</td>
                  <td class="text-right">$200.50</td>
                  <td class="text-right">$20.50 </td>
                  <td>15 July, 2020 19</td>
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
                <lable class="control-label">Business / Supplier:</lable>
              </div>
              <div class="col-md-8 col-12">
                <input type="text" name="" class="form-control" placeholder="Search Business / Supplier">
                
              </div>
            </div>
          </div>
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