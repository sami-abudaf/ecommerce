@extends('layouts.admin')

@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <h3 class="content-header-title">الاقسام  الرئيسية  </h3>
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">الرئيسية</a>
                                </li>
                                <li class="breadcrumb-item active"> الاقسام  الرئيسية
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
                                    <h4 class="card-title">جميع المتاجر  </h4>
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
                                        <table class="table display nowrap table-striped table-bordered scroll-horizontal text-center table-responsive">
                                            <thead class="">
                                            <tr>
                                                <th>الاسم</th>
                                                <th> الشعار</th>
                                                <th>الهاتف</th>
                                                <th>العنوان</th>
                                                <th> ألحالة </th>
                                                <th>القسم الرئيسي</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @isset($vendors)
                                                @foreach($vendors as $vendor)
                                                    <tr>
                                                        <td>{{$vendor -> name}}</td>
                                                        <td> <img style="width: 100px; height: 70px;" src="{{$vendor ->logo}}"></td>
                                                        <td>{{$vendor ->mobile}}</td>
                                                        <td>{{$vendor ->address}}</td>
                                                        <td>{{$vendor -> getActive()}}</td>
                                                        <td>{{$vendor -> category->name}}</td>

                                                        <td>
                                                            <div class="btn-group" role="group"
                                                                 aria-label="Basic example">
                                                                <a href="{{route('admin.vendors.edit',$vendor->id)}}"
                                                                   class="btn btn-outline-primary btn-sm round box-shadow-3 mr-1 mb-1">تعديل</a>
                                                            </div>
                                                        </td>
                                                        <td>
                                                                <a href="{{route('admin.vendors.delete',$vendor->id)}}"
                                                                   class="btn btn-outline-danger btn-sm round box-shadow-3 mr-1 mb-1">حذف</a>
                                                        </td>
                                                        <td>
                                                                <a href="{{route('admin.vendors.status',$vendor->id)}}"
                                                                    @if($vendor -> active == 0)
                                                                        class="btn btn-outline-success  btn-sm round box-shadow-3 mr-1 mb-1">


                                                                        تفعيل
                                                                    @else
                                                                        class="btn btn-outline-red btn-sm round box-shadow-3 mr-1 mb-1">
                                                                        الغاء تفعيل
                                                                    @endif
                                                                </a>
                                                        </td>


                                                    </tr>
                                                @endforeach
                                            @endisset


                                            </tbody>
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
