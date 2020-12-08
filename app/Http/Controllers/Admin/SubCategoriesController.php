<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SubCategoryRequest;
use App\Models\MainCategory;
use App\Models\SubCategory;
use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;
use Illuminate\Support\Str;

class SubCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $default_lang = get_default_lang();
        $subcategory = SubCategory::where('translation_lang', $default_lang)
            ->selection()
            ->get();
        return view('admin.subcategories.index',compact('subcategory'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $subcategories = MainCategory::where('translation_of', 0)->active()->get();
        return view('admin.subcategories.create',compact('subcategories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubCategoryRequest $request)
    {
        try {
            //return $request;

            $sub_categories = collect($request->category);

            $filter = $sub_categories->filter(function ($value, $key) {
                return $value['abbr'] == get_default_lang();
            });

            $default_category = array_values($filter->all()) [0];


            $filePath = "";
            if ($request->has('photo')) {

                $filePath = uploadImage('subcategories', $request->photo);
            }

            DB::beginTransaction();

            $default_category_id = SubCategory::insertGetId([
                'translation_lang' => $default_category['abbr'],
                'translation_of' => 0,
                'name' => $default_category['name'],
                'slug' => $default_category['name'],
                'category_id' => $request->category_id,
                'photo' => $filePath
            ]);

            $subcategories = $sub_categories->filter(function ($value, $key) {
                return $value['abbr'] != get_default_lang();
            });


            if (isset($subcategories) && $subcategories->count()) {

                $categories_arr = [];
                foreach ($subcategories as $category) {
                    $categories_arr[] = [
                        'translation_lang' => $category['abbr'],
                        'translation_of' => $default_category_id,
                        'name' => $category['name'],
                        'category_id' => $request->category_id,
                        'slug' => $category['name'],
                        'photo' => $filePath
                    ];
                }

                SubCategory::insert($categories_arr);
            }

            DB::commit();

            return redirect()->route('admin.subcategories')->with(['success' => 'تم الحفظ بنجاح']);

        } catch (\Exception $ex) {
            return $ex;
            DB::rollback();
            return redirect()->route('admin.subcategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }

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
    public function edit($subCat_id)
    {
        //get specific categories and its translations

       /* $subCategory = SubCategory::with('categories')
            ->selection()
            ->find($subCat_id);

        if (!$subCategory)
            return redirect()->route('admin.subcategories')->with(['error' => 'هذا القسم غير موجود ']);

        return view('admin.subcategories.edit', compact('subCategory'));*/

        try {
            $subCategory =  SubCategory::Selection()->find($subCat_id);
            if(!$subCategory)
                return redirect()->route('admin.subcategories')->with(['error' => 'هذا المتجر غير موجود او ربما يكون محذوفا ']);

            $categories =  MainCategory::where('translation_of',0)->active()->get();

            return view('admin.subcategories.edit', compact('subCategory','categories'));
        }
        catch (\Exception $exception){
            return $exception;
            return redirect()->route('admin.subcategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SubCategoryRequest $request, $subCat_id)
    {
        try {
            $sub_category = SubCategory::find($subCat_id);

            if (!$sub_category)
                return redirect()->route('admin.subcategories')->with(['error' => 'هذا القسم غير موجود ']);

            // update date

            $category = array_values($request->category) [0];

            if (!$request->has('category.0.active'))
                $request->request->add(['active' => 0]);
            else
                $request->request->add(['active' => 1]);


            SubCategory::where('id', $subCat_id)
                ->update([
                    'name' => $category['name'],
                    'active' => $request->active,
                    'category_id' => $request->category_id,
                ]);

            // save image

            if ($request->has('photo')) {
                $filePath = uploadImage('subcategories', $request->photo);
                SubCategory::where('id', $subCat_id)
                    ->update([
                        'photo' => $filePath,
                    ]);
            }




            return redirect()->route('admin.subcategories')->with(['success' => 'تم ألتحديث بنجاح']);
        } catch (\Exception $ex) {
            return $ex;
            return redirect()->route('admin.subcategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
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
            $subcategory = SubCategory::find($id);
            if (!$subcategory)
                return redirect()->route('admin.subcategories')->with(['error' => 'هذا القسم غير موجود ']);

            $vendors = $subcategory->vendors();

            if (isset($vendors) && $vendors->count() > 0) {
                return redirect()->route('admin.subcategories')->with(['error' => 'لأ يمكن حذف هذا القسم  ']);
            }

            $image = Str::after($subcategory->photo, 'assets/');
            $image = base_path('assets/' . $image);
            unlink($image); //delete from folder*/
            // detete translation
            //حذف جميع اللغات  التابعة  للقسم
            $subcategory->categories()->delete();
            $subcategory->delete();
            return redirect()->route('admin.subcategories')->with(['success' => 'تم حذف القسم بنجاح']);

        } catch (\Exception $ex) {
            return $ex;
            return redirect()->route('admin.subcategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }


    public function changeStatus($id)
    {
        try {
            $subcategory = SubCategory::find($id);
            if (!$subcategory)
                return redirect()->route('admin.subcategories')->with(['error' => 'هذا القسم غير موجود ']);

            $status =  $subcategory -> active  == 0 ? 1 : 0;

            $subcategory -> update(['active' =>$status ]);

            return redirect()->route('admin.subcategories')->with(['success' => ' تم تغيير الحالة بنجاح ']);

        } catch (\Exception $ex) {
            return redirect()->route('admin.subcategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
}
