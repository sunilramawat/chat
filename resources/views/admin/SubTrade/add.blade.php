@extends('admin.mainlayout')
@section('content')

  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Manage Trade </h1>
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
          <div class="box-main-title">Add New</div>
          <div class="box-main-top-right">
             <a href="{{URL('admin/subtrade/view/1')}}"><button type="button" class="btn btn-primary">Back</button></a>
          </div>
        </div>
        <div class="box-main-content mb-3">
          <div class="row">
            <div class="col-md-12 col-xl-6">
              {!!Form::open(['url'=>'admin/subtrade/save', 'enctype' => 'multipart/form-data', 'method'=>'post']) !!}   
                <div class="form-group custom-select-group">
                  <div class="row">
                    <div class="col-md-3 col-12">
                      <label class="control-label">Trade:</label>
                    </div>
                    <div class="col-md-6 col-12">
                      <select name="trade_id" id="trade_id" class="form-control">
                          @foreach($Trade_list as $item)
                           <option value="{{$item->id}}">{{$item->trade}}</option>
                          @endforeach
                      </select>
                      <span class="down-separator"></span>                    
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 col-12">
                      <label class="control-label">Sub Category:</label>
                    </div>
                    <div class="col-md-6 col-12">
                      <!-- <input type="text" name="" class="form-control" placeholder="Enter Sub Category"> -->
                      {!!Form::text('sub_trade',null,['class' => 'form-control','placeholder' => 'Enter Sub Category','required' => 'required']) !!}
                      
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <div class="col-md-3 col-12">
                      
                    </div>
                    <div class="col-md-9 col-12">
                       {!! Form::submit('Submit',array('class'=>'btn btn-primary')) !!}
                      <!-- <button type="button" class="btn btn-primary">Submit</button> -->
                    </div>
                  </div>
                </div>
              {!!Form::close()!!}
            </div>
          </div>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
    <!-- /.content -->


@stop



