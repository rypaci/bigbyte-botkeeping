<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\Vendor;
use App\Inventory;
use App\ProductBatch;
use App\AllImages;
use DateTime;
use DB;
use File;

class ProductController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index(Request $request){
        $search_products = $request->search_products;
        $inventory_status = $request->inventory_status;

        $products = DB::table('products')
        ->leftjoin('vendors', 'vendors.id', '=', 'products.vendor_id')
        ->select('products.*', 'vendors.*', 'products.id as id')
        ->orderBy('products.name', 'ASC')
        ->paginate(10);

        $prod_images = AllImages::where('keys', 'prod_img')
        ->get();

        // $products = Product::orderBy('id','DESC')->paginate(10);
        return view('product.index',compact('products','inventory_status','search_products','prod_images'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $vendors = DB::table('vendors')
        ->where('vendors_status','=',' ')
        ->orWhere('vendors_status','=','Active')
        ->orderBy('first_name', 'ASC')
        ->orderBy('company_name', 'ASC')
        ->get();
        return view('product.create',compact('vendors'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'  => 'required',
            'price' => 'required',
            'cost_price' => 'required',
            'vendor_id' => 'required',
            'inventory_status' => 'required',
            'barcode' => 'required',
            'filename' => 'required',
            'filename.*' => 'image|mimes:jpeg,png,jpg,gif,svg'
        ]);

        // $request->merge([ 
        //     'product_image' => json_encode($img_name)
        // ]);
        
        Product::create($request->all());
        
        $currDate = date("Y-m-d");
        $latestRec = Product::latest()->first();
        $latestId = $latestRec->id;
        
        $addToInventory = new Inventory();
        $addToInventory->pro_id = $latestId;
        $addToInventory->added_qty = $request->added_qty;
        $addToInventory->date = $currDate;
        $addToInventory->save();
        
        if($request->hasfile('filename'))
        {
            foreach ($request->file('filename') as $image) {
                $name = strtolower($image->getClientOriginalName());
                $imgname = date('mdYHis').$name;
                $image->move(public_path().'/uploads/images/', $imgname);
                $img_names[] = $imgname;
            }

            foreach($img_names as $img_name){

                $addtoImages = new AllImages();
                $addtoImages->image_name = $img_name;
                $addtoImages->foreign_id = $latestId;
                $addtoImages->keys = 'prod_img';
                $addtoImages->date = $currDate;
                $addtoImages->save();
            }
        }

        return redirect()
            ->route('product.index')
            ->with('success','Product created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $product = Product::find($id);
        $product = DB::table('products')
        ->leftjoin('vendors', 'vendors.id', '=', 'products.vendor_id')
        ->select('products.*', 'vendors.*', 'products.id as id')
        ->where('products.id', $id)
        ->first();
        
        $product_images = AllImages::where('foreign_id', $id)
        ->where('keys', 'prod_img')
        ->get();
    
        return view('product.show',compact('product', 'product_images'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id);
        $product_images = AllImages::where('foreign_id', $id)
        ->where('keys', 'prod_img')
        ->get();

        $vendors = DB::table('vendors')
        ->where('vendors_status','=',' ')
        ->orWhere('vendors_status','=','Active')
        ->orderBy('first_name', 'ASC')
        ->orderBy('company_name', 'ASC')
        ->get();

        return view('product.edit',compact('product','vendors','product_images'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $this->validate($request, [
            'name'  => 'required',
            'price' => 'required',
            'cost_price' => 'required',
            'vendor_id' => 'required',
            'inventory_status' => 'required',
            'barcode' => 'required',
        ]);
        Product::find($id)->update($request->all());

        return redirect()
            ->route('product.index')
            ->with('success','Product updated successfully');
    }

    public function productDetails(Request $request, $id){

        $productID = Product::find($id); // finding product ID
        $productName = Product::find($id); // finding product Name

        $vendorName = DB::table('products')
        ->leftjoin('vendors', 'vendors.id', '=', 'products.vendor_id')
        ->select('products.*', 'vendors.*', 'vendors.id as v_id')
        ->where('products.id', '=', $id)
        ->first();

        $details = DB::table('products')
        ->leftjoin('inventories', 'inventories.pro_id', '=', 'products.id')
        ->leftjoin('vendors', 'vendors.id', '=', 'products.vendor_id')
        ->select('products.*', 'inventories.*', 'vendors.*', 'inventories.id as inv_id')
        ->where('products.id', '=', $id)
        ->orderBy('inventories.date', 'DESC')
        ->paginate(10);

        return view('product.details',compact('details', 'productID', 'productName', 'vendorName'))
        ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    public function addStock(Request $request, $id){

        $product_ID = Product::find($id); // finding product ID
        return view('product.addstock', compact('product_ID'));
    }

    public function restockInfo($id){

        $product = Product::findOrFail($id);
        $stock = ProductBatch::stockFor($id);

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->barcode,
            'current_qty' => $stock['current_qty'],
            'batches' => $stock['batches'],
        ]);
    }

    public function restock(Request $request){

        $this->validate($request, [
            'pro_id' => 'required|exists:products,id',
            'added_qty' => 'required|integer|min:1',
            'expiry_date' => 'date',
            'batch_code' => 'max:50',
            'batch_note' => 'max:255',
        ]);

        $proId = $request->pro_id;
        $batchCode = trim($request->batch_code);

        if($batchCode !== '' && ProductBatch::where('product_id', $proId)->where('batch_code', $batchCode)->exists()){
            return redirect()->back()->withInput()
                ->withErrors(['batch_code' => 'Batch code "'.$batchCode.'" already exists for this product.']);
        }

        $currDate = date('Y-m-d');

        DB::transaction(function() use ($request, $proId, $batchCode, $currDate){

            $inventory = new Inventory();
            $inventory->pro_id = $proId;
            $inventory->added_qty = $request->added_qty;
            $inventory->date = $currDate;
            $inventory->save();

            $batch = ProductBatch::create([
                'product_id' => $proId,
                'inventory_id' => $inventory->id,
                'batch_code' => $batchCode !== '' ? $batchCode : 'pending',
                'original_qty' => $request->added_qty,
                'expiry_date' => $request->expiry_date ?: null,
                'received_date' => $currDate,
                'note' => $request->batch_note ?: null,
            ]);

            if($batchCode === ''){
                $batch->batch_code = 'B-'.str_pad($batch->id, 3, '0', STR_PAD_LEFT);
                $batch->save();
            }
        });

        return redirect()->back()->with('success','Product is successfully restocked');
    }

    public function saveStock(Request $request){

        $date = $request->date;
        $date = date("Y-m-d", strtotime($date) );

        $addToInventory = new Inventory();
        $addToInventory->pro_id = $request->pro_id;
        $addToInventory->added_qty = $request->added_qty;
        $addToInventory->date = $date;
        $addToInventory->save();
        
        return redirect()->back()->with('success','Stock is added successfully');
    }

    public function updateQty(Request $request){

        foreach ($request->invrow as $rowdata) {

            $addedQty = $rowdata['added_qty'];
            $inv_id = $rowdata['inv_id'];

            if(!empty($addedQty)){
                Inventory::where('id',$inv_id)->update(
                    array(
                        'added_qty' => $rowdata['added_qty']
                    )
                );
            ProductBatch::where('inventory_id', $inv_id)->update(['original_qty' => $addedQty]);
            }
        }
        return redirect()->back()->with('success','Stock quantity is successfully updated');
    }

    public function searchProduct(Request $request){

        $search_products = $request->search_products;
        $inventory_status = $request->inventory_status;

        $prod_images = AllImages::where('keys', 'prod_img')
        ->get();

        if($inventory_status == 'All'){
            
            $products = DB::table('products')
            ->leftjoin('vendors', 'vendors.id', '=', 'products.vendor_id')
            ->select('products.*', 'vendors.*', 'products.id as id')
            // ->where('products.inventory_status', '=', 'Active')
            // ->where('products.inventory_status', '=', 'Inactive')
            // ->orWhere('products.inventory_status', '=', " ")
            ->orderBy('products.name', 'ASC')
            ->paginate(10);
    
            return view('product.index',compact('products','inventory_status','search_products','prod_images'))
                ->with('i', ($request->input('page', 1) - 1) * 5);

        }elseif($inventory_status){

            $products = DB::table('products')
            ->leftjoin('vendors', 'vendors.id', '=', 'products.vendor_id')
            ->select('products.*', 'vendors.*', 'products.id as id')
            ->where('products.inventory_status', '=', $inventory_status)
            ->orderBy('products.name', 'ASC')
            ->paginate(10);
    
            return view('product.index',compact('products','inventory_status','search_products','prod_images'))
                ->with('i', ($request->input('page', 1) - 1) * 5);
        }

        if(!empty($search_products)){
            $products = DB::table('products')
            ->leftjoin('vendors', 'vendors.id', '=', 'products.vendor_id')
            ->select('products.*', 'vendors.*', 'products.id as id')
            ->where('products.name', 'LIKE', "%$search_products%")
            ->orderBy('products.name', 'ASC')
            ->paginate(10);
    
            return view('product.index',compact('products','inventory_status','search_products','prod_images'))
                ->with('i', ($request->input('page', 1) - 1) * 5);
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
        Product::find($id)->delete();

        $images = AllImages::where('foreign_id', $id)
        ->where('keys', 'prod_img')
        ->get();

        foreach($images as $image){

            $image_name = $image->image_name;
            $image_path = public_path().'/uploads/images/'.$image_name;
            
            if(File::exists($image_path)){
                File::delete($image_path);
            }
        }
        
        AllImages::where('foreign_id', $id)
        ->where('keys', 'prod_img')
        ->delete();

        return redirect()
            ->route('product.index')
            ->with('success','Product deleted successfully');
    }

    public function detailsDestroy($id=null){
        Inventory::where('id', '=', $id)->delete();
        ProductBatch::where('inventory_id', '=', $id)->delete();
        return redirect()->back()->with('success','Stock detail is successfully deleted');
    }

    public function imageDelete($id){
        
        $image = AllImages::where('id', $id)->first();

        $image_name = $image->image_name;
        $image_path = public_path().'/uploads/images/'.$image_name;
        
        if(File::exists($image_path)){
            File::delete($image_path);
        }

        AllImages::where('id', $id)
        ->where('keys', 'prod_img')
        ->delete();

        return redirect()->back()->with('success','Image is successfully deleted');
    }

    public function editImage($id){

        $image = AllImages::where('id', $id)
        ->where('keys', 'prod_img')
        ->first();

        return view('product.edit-image',compact('image'));
    }

    public function updateImage(Request $request){

        $this->validate($request, [
            'filename' => 'required',
            'filename.*' => 'image|mimes:jpeg,png,jpg,gif,svg'
        ]);

        $id = $request->id;
        
        // This code will delete the old image store in the directory and replace the new image
        $image = AllImages::where('id', $id)->first();
        $image_name = $image->image_name;
        $image_path = public_path().'/uploads/images/'.$image_name;
        
        if(File::exists($image_path)){
            File::delete($image_path);
        }

        // This code will upload new image and update the old image name in the database
        if($request->hasfile('filename'))
        {
            $image = $request->file('filename');
            $name = strtolower($image->getClientOriginalName());
            $imgname = date('mdYHis').$name;
            $image->move(public_path().'/uploads/images/', $imgname);
        }

        AllImages::where('id', $id)
        ->where('keys', 'prod_img')
        ->update([
            'image_name' => $imgname
        ]);

        return redirect()->route('product.index')
        ->with('success','Product updated successfully');
    }

    public function addImage($id){

        $image = Product::where('id', $id)
        ->first();

        return view('product.add-image',compact('image'));
    }

    public function saveImage(Request $request){

        $id = $request->id;

        $this->validate($request, [
            'filename' => 'required',
            'filename.*' => 'image|mimes:jpeg,png,jpg,gif,svg'
        ]);

        if($request->hasfile('filename'))
        {
            foreach ($request->file('filename') as $image) {
                $name = strtolower($image->getClientOriginalName());
                $imgname = date('mdYHis').$name;
                $image->move(public_path().'/uploads/images/', $imgname);
                $img_names[] = $imgname;
            }
        }
        
        $currDate = date("Y-m-d");

        foreach($img_names as $img_name){

            $addtoImages = new AllImages();
            $addtoImages->image_name = $img_name;
            $addtoImages->foreign_id = $id;
            $addtoImages->keys = 'prod_img';
            $addtoImages->date = $currDate;
            $addtoImages->save();
        }

        return redirect()->route('product.index')
            ->with('success','Image is added successfully');
    }
}