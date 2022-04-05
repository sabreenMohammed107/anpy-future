@extends('layout.web')

@section('title', 'الشهور')

@section('content')



<div class="row">
    <div class="col-12">
        <div class="card" style="background: #ffffff;box-shadow: 0 1px 1px rgb(0 0 0 / 10%);">
            <div class="box-header">
                <h3 class="box-title">بيانات الرئيسية</h3>
                {{-- <a href="{{ route('month.create') }}" class="btn btn-info btn-lg pull-right"> اضافة </a> --}}

            </div>

            <div class="box-body ">
                <table id="table" data-toggle="table" data-pagination="true" data-search="true" data-resizable="true"
                    data-cookie="true" data-show-export="true" data-locale="ar-SA" style="direction: rtl">
                    <thead>
                        <th data-field="state" data-checkbox="false"></th>
                        <th data-field="id">#</th>

                        <th>التاريخ</th>
                        <th>الموضوع</th>
                        <th>الرسالة</th>
                        <th>الراسل</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $index => $row)
                            <tr>
                                <td></td>
                                <td>{{ $index + 1 }}</td>

                                <td>{{date('d-m-Y', strtotime($row->suggest_date))}}</td>
                                <td>{{ $row->subject }}</td>
                                <td>{{ $row->message }}</td>
                                <td>{{ $row->user->name ?? '' }}</td>




                            </tr>
                            <!--/Edit Customer-->
                            <!-- Delete Modal -->

                            <div class="modal modal-danger" id="del{{ $row->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <form action="{{ route('month.destroy', $row->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-content">
                                            <div class="modal-header ">
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h5 class="modal-title" id="exampleModalLabel">تأكيد الحذف</h5>
                                                </button>
                                            </div>
                                            <div class="modal-body bg-light">
                                                <p><i class="fa fa-fire "></i></p>
                                                <p>حذف جميع البيانات ؟</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-outline pull-left">موافق </button>

                                                <button type="button" class="btn btn-outline "
                                                    data-dismiss="modal">الغاء</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
    </div>
    <!-- /.row -->
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {

            $("#example1").dataTable();
            $('#example2').dataTable({
                "bPaginate": true,
                "bLengthChange": false,
                "bFilter": false,
                "bSort": true,
                "bInfo": true,
                "bAutoWidth": false
            });
        });
    </script>
@endsection
