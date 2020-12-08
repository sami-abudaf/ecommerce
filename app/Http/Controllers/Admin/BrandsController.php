<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BrandsRequest;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Str;

class BrandsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       //$brands= Brand::orderBy('id','DESC')->paginate(PAGINATION_COUNT);
        $brands =  Brand::selection()->paginate(PAGINATION_COUNT);
        return view('admin.brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.brands.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BrandsRequest  $request)
    {
        //return $request;
        // make validation
        try {
            if (!$request->has('active'))
                $request->request->add(['active' => 0]);
            else
                $request->request->add(['active' => 1]);

            $fileName = "";
            if ($request->has('photo')) {

                $fileName = uploadImage('brands', $request->photo);
            }
            $brand = Brand::create($request->except('_token','photo'));

            $brand->name= $request->name;
            $brand->photo=$fileName;
            $brand->save();
            // send massege user
          //  Notification::send($vendor, new VendorCreated($vendor));
            notify()->success(' لقد تم  الحفظ  بنجاح . ' );

            return redirect()->route('admin.brands');

        }

        catch (\Exception $ex) {
            notify()->error('لقد حصل خطاء ما  يرجي المحاولة فيما بعد .');

            return $ex;
            return redirect()->route('admin.brands');
        }



        //insert to  DB



        //redirrect message
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $brand =  Brand::find($id);
            if(!$brand){
                notify()->warning('هذا الماركة غير موجود او ربما يكون محذوفا !');
                return redirect()->route('admin.brands');
            }

                return view('admin.brands.edit', compact('brand'));

        }
        catch (\Exception $exception){
            return $exception;
            notify()->error('لقد حصل خطاء ما  يرجي المحاولة فيما بعد .');
            return redirect()->route('admin.brands');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BrandsRequest $request, $id)
    {
        //return $request;

        try {
            $brand = Brand::find($id);
            if (!$brand)

                return redirect()->route('admin.vendors')->with(['error' => 'هذا المتجر غير موجود او ربما يكون محذوفا ']);


            DB::beginTransaction();
            //photo
            if ($request->has('photo') ) {
                $fileName = uploadImage('brands', $request->photo);
                Brand::where('id', $id)
                    ->update([
                        'photo' => $fileName,
                    ]);
            }
            if (!$request->has('active'))
                $request->request->add(['active' => 0]);
            else
                $request->request->add(['active' => 1]);

            $brand->update($request->except('_token', 'id', 'photo'));
          $brand->name=$request->name;
          $brand->save();

            DB::commit();

            notify()->success(' لقد تم  التعديل   بنجاح . ' );
            return redirect()->route('admin.brands');

        } catch (\Exception $ex) {
            //return $exception;
            DB::rollback();
            notify()->error('لقد حصل خطاء ما  يرجي المحاولة فيما بعد .');
            return redirect()->route('admin.brands');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $brand = Brand::find($id);
            if (!$brand)
                return redirect()->route('admin.brands')->with(['error' => 'هذا المنتج غير موجود ']);

            if (isset($brands) && $brands->count() > 0) {

                return redirect()->route('admin.brands')->with(['error' => 'لأ يمكن حذف هذا المنتج  ']);
            }

           $image = Str::after($brand->photo, 'assets/');
            $image = base_path('assets/' . $image);
            unlink($image); //delete from folder*/

            $brand->delete();
            notify()->warning('تم حذف المنتج بنجاح!');
            return redirect()->route('admin.brands');

        } catch (\Exception $ex) {

            notify()->error('لقد حصل خطاء ما  يرجي المحاولة فيما بعد .');
            return redirect()->route('admin.brands');
        }
    }

    public function changeStatus($id)
    {
        try {
            $brand = Brand::find($id);
            if (!$brand)
                return redirect()->route('admin.brands')->with(['error' => 'هذا القسم غير موجود ']);

            $status =  $brand -> active  == 0 ? 1 : 0;

            $brand -> update(['active' =>$status ]);
            notify()->success('تم حالة المنتج بنجاح!');
            return redirect()->route('admin.brands');

        } catch (\Exception $ex) {
            notify()->error('لقد حصل خطاء ما  يرجي المحاولة فيما بعد .');
            return redirect()->route('admin.brands');
        }
    }
}
