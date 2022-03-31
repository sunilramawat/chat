<div  id="maintable">
            <div class="box-main-table" id="maintable" >
              <div class="table-responsive">
                <table class="table table-bordered admin-table dataTable"  id="example2s" >
               
                  <thead>
                    <tr>
                      <th>S.No</th>
                      <th>Sub Categories</th> 
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($SubTrades_manage as $no => $chip)
                    {{$chip->trade_manage}}
                    <tr>
                      <td>{{$no+1}}</td>
                      <td>{{$chip->sub_trade}}</td>
                      <td><!-- <a href=""><i class="fa fa-eye" aria-hidden="true"></i></a> -->
                       <!--  <a href="{{URL('admin/subtrade/detail')}}/{{$chip->id}}">
                          <i class="fa fa-eye" aria-hidden="true"></i>
                        </a>  
                        &nbsp; -->
                        <a href="{{URL('admin/subtrade/delete')}}/{{$chip->id}}">
                          <i class="fa fa-trash" aria-hidden="true"></i>
                        </a>          
                      </td>
                    </tr>
                    @endforeach()
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="box-main-bottom">
          <div class="box-main-showing">Showing {{$current_page}} to 10 of {{$total_count}} entries</div>
          {{$SubTrades_manage->links() }}
          <!-- <ul class="pagination">
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
          </ul> -->
        </div>  
        </div>