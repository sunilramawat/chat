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
          <div class="box-main-title">Pending Request List</div>
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
                  <th>Business Name</th>
                  <th>Owner Name</th>
                  <th>Email Address</th>
                  <th>Phone Number</th>
                  
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>Manila Oriental Market </td>
                  <td>Carol Ramirez </td>
                  <td>carolremirez@gmail.com </td>
                  <td>+233 9854 251 </td>
                  
                  <td><a href=""><i class="fa fa-eye" aria-hidden="true"></i></a></td>
                </tr>
                <tr>
                  <td>1</td>
                  <td>Manila Oriental Market </td>
                  <td>Carol Ramirez </td>
                  <td>carolremirez@gmail.com </td>
                  <td>+233 9854 251 </td>
                  
                  <td><a href=""><i class="fa fa-eye" aria-hidden="true"></i></a></td>
                </tr>
                <tr>
                  <td>1</td>
                  <td>Manila Oriental Market </td>
                  <td>Carol Ramirez </td>
                  <td>carolremirez@gmail.com </td>
                  <td>+233 9854 251 </td>
                  
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