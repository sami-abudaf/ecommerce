<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\VendorRequest;
use App\Models\MainCategory;
use App\Models\SubCategory;
use App\Models\Vendor;
use App\Notifications\VendorCreated;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Notification;

use DB;
use Illuminate\Support\Str;

class VendorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // Vendor::select('id','category_id',' name', 'logo', 'mobile')->paginante(PAGINATION_COUNT);
        $vendors =  Vendor::selection()->paginate(PAGINATION_COUNT);
        //return $vendors;
        return view('admin.vendors.index',compact('vendors'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

       // $treeView = MainCategory::where('translation_of', 0)->active()->get();

        $subcategories = SubCategory::where('translation_of', 0)->active()->get();
        $categories = MainCategory::where('translation_of', 0)->active()->get();



        return view('admin.vendors.create', compact('subcategories','categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VendorRequest $request)
    {
        //return $request;
        // make validation
        try {
            if (!$request->has('active'))
                $request->request->add(['active' => 0]);
            else
                $request->request->add(['active' => 1]);

            $filePath = "";
            if ($request->has('logo')) {

                $filePath = uploadImage('vendors', $request->logo);
            }
         $vendor =   Vendor::create([
                'name'=> $request->name,
                'mobile'=> $request->mobile,
                'email'=> $request->email,
                'active'=> $request->active,
                'address'=> $request->address,
                'logo' => $filePath,
                'password'=> $request->password,
                'subcategory_id' => $request->subcategory_id,
                 'latitude' => $request->latitude,
                 'longitude' => $request->longitude,
            ]);
            // send massege user
            Notification::send($vendor, new VendorCreated($vendor));
            notify()->success(' لقد تم  الحفظ  بنجاح . ' );

            return redirect()->route('admin.vendors');

        }

        catch (\Exception $ex) {
            notify()->error('لقد حصل خطاء ما  يرجي المحاولة فيما بعد .');

            return $ex;
            return redirect()->route('admin.vendors');
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
            $vendor =  Vendor::Selection()->find($id);
            if(!$vendor){
                notify()->warning('هذا المتجر غير موجود او ربما يكون محذوفا !');
                return redirect()->route('admin.vendors');
            }

                else{
                    $categories = MainCategory ::where('translation_of',0)->active()->get();
                    $subcategories = SubCategory::where('translation_of', 0)->active()->get();
                    return view('admin.vendors.edit', compact('vendor','categories','subcategories'));
                }

        }
        catch (\Exception $exception){
            return $exception;
            notify()->error('لقد حصل خطاء ما  يرجي المحاولة فيما بعد .');
            return redirect()->route('admin.vendors');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(VendorRequest $request, $id)
    {
        //return $request;

        try {
            $vendor = Vendor::Selection()->find($id);
            if (!$vendor)

                return redirect()->route('admin.vendors')->with(['error' => 'هذا المتجر غير موجود او ربما يكون محذوفا ']);


            DB::beginTransaction();
            //photo
            if ($request->has('logo') ) {
                $filePath = uploadImage('vendors', $request->logo);
                Vendor::where('id', $id)
                    ->update([
                        'logo' => $filePath,
                    ]);
            }
            if (!$request->has('active'))
                $request->request->add(['active' => 0]);
            else
                $request->request->add(['active' => 1]);

            $data = $request->except('_token', 'id', 'logo', 'password');


            if ($request->has('password') && !is_null($request->  password)) {

                $data['password'] = $request->password;
            }

            Vendor::where('id', $id)
                ->update(
                    $data
                );

            DB::commit();
            notify()->success(' لقد تم  التعديل   بنجاح . ' );
            return redirect()->route('admin.vendors');
        } catch (\Exception $exception) {
            return $exception;
            DB::rollback();
            notify()->error('لقد حصل خطاء ما  يرجي المحاولة فيما بعد .');
            return redirect()->route('admin.vendors');
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
            $vendor = Vendor::find($id);
            if (!$vendor)
                return redirect()->route('admin.vendors')->with(['error' => 'هذا المنتج غير موجود ']);

            //$vendors = $maincategory->vendors();

            if (isset($vendors) && $vendors->count() > 0) {

                return redirect()->route('admin.vendors')->with(['error' => 'لأ يمكن حذف هذا المنتج  ']);
            }

            $image = Str::after($vendor->logo, 'assets/');
            $image = base_path('assets/' . $image);
            unlink($image); //delete from folder*/

            $vendor->delete();
            notify()->warning('تم حذف المنتج بنجاح!');
            return redirect()->route('admin.vendors');

        } catch (\Exception $ex) {

            notify()->error('لقد حصل خطاء ما  يرجي المحاولة فيما بعد .');
            return redirect()->route('admin.vendors');
        }
    }

    public function changeStatus($id)
    {
        try {
            $vendor = Vendor::find($id);
            if (!$vendor)
                return redirect()->route('admin.vendors')->with(['error' => 'هذا القسم غير موجود ']);

            $status =  $vendor -> active  == 0 ? 1 : 0;

            $vendor -> update(['active' =>$status ]);
            notify()->success('تم حالة المنتج بنجاح!');
            return redirect()->route('admin.vendors');

        } catch (\Exception $ex) {
            notify()->error('لقد حصل خطاء ما  يرجي المحاولة فيما بعد .');
            return redirect()->route('admin.vendors');
        }
    }
}
