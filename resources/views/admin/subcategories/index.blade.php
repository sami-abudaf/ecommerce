@extends('layouts.admin')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <h3 class="content-header-title">الاقسام  الفرعية   </h3>
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">الرئيسية</a>
                                </li>
                                <li class="breadcrumb-item active"> الاقسام  الفرعية
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- DOM - jQuery events table -->
                <section id="dom">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">جميع الاقسام  الرئيسية </h4>
                                    <a class="heading-elements-toggle"><i
                                            class="la la-ellipsis-v font-medium-3"></i></a>
                                    <div class="heading-elements">
                                        <ul class="list-inline mb-0">
                                            <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                            <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                            <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                            <li><a data-action="close"><i class="ft-x"></i></a></li>
                                        </ul>
                                    </div>
                                </div>

                                @include('admin.includes.alerts.success')
                                @include('admin.includes.alerts.errors')

                                <div class="card-content collapse show">
                                    <div class="card-body card-dashboard">
                                        <table class="table table-striped table-bordered dataex-html5-export table-responsive">
                                            <thead>
                                            <tr>
                                                <th> القسم </th>
                                                <th> اللغة </th>
                                                <th>الحالة</th>
                                                <th>صوره القسم</th>
                                                <th>القسم  الرئيسي </th>
                                                <th>التعديل </th>
                                                <th>الحذف</th>
                                                <th>التفعيل </th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @isset($subcategory)
                                                @foreach($subcategory as $subcat)
                                                    <tr>
                                                        <td>{{$subcat -> name}}</td>
                                                        <td>{{get_default_lang()}}</td>
                                                        <td>{{$subcat -> getActive()}}</td>
                                                        <td> <img class="img-fluid" style="width: 100px; height: 100px; border-radius: 50%;" src="{{$subcat ->photo}}"></td>
                                                        <td>{{$subcat->mainCategory->name}}</td>
                                                        <td>

                                                                <a href="{{route('admin.subcategories.edit',$subcat->id)}}"
                                                                   class="btn btn-sm btn-outline-primary btn-sm btn-min-width round box-shadow-3 mr-1 mb-1">تعديل</a>

                                                        </td>
                                                        <td>
                                                                <a href="{{route('admin.subcategories.delete',$subcat->id)}}"
                                                                   class="btn btn-sm btn-outline-danger btn-sm btn-min-width round box-shadow-3 mr-1 mb-1">حذف

                                                                </a>
                                                        </td>
                                                        <td>
                                                                <a href="{{route('admin.subcategories.status',$subcat->id)}}"
                                                                   @if($subcat -> active == 0)
                                                                   class="btn btn-outline-success btn-sm btn-min-width round box-shadow-3 mr-1 mb-1">
                                                                    تفعيل
                                                                    @else
                                                                        class="btn btn-outline-warning  btn-sm btn-min-width round box-shadow-3 mr-1 mb-1">
                                                                        الغاء تفعيل
                                                                    @endif
                                                                </a>

                                                        </td>


                                                    </tr>
                                                @endforeach
                                            @endisset


                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <th> القسم </th>
                                                <th> اللغة </th>
                                                <th>الحالة</th>
                                                <th>صوره القسم</th>
                                                <th>القسم  الرئيسي </th>
                                                <th>التعديل </th>
                                                <th>الحذف</th>
                                                <th>التفعيل </th>
                                            </tr>
                                            </tfoot>
                                        </table>
                                        <div class="justify-content-center d-flex">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
