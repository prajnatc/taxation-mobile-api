<?php
/**
 * UserController
 * @category Controller
 * @author ThinkPace
 */
namespace App\Http\Controllers\Api\V1;

use App\Models\Item;
use App\Models\Vendor;
use App\Models\Category;
use App\Models\Unit;
use App\Models\ReceiveItem;
use App\Models\TaxType;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
/**
 * UserController
 * @category Controller
 * @author ThinkPace
 */
class ItemController extends BaseController
{

  public function getTaxtType(){
      $tax_type = TaxType::orderBy('id','asc')->get();
      return $this->response->array(['tax_types'=>$tax_type]);
  }

  public function insertItemMaster(Request $request)
    {
    	// dd($request->all());
      $unit_id = Unit::select('id')->where('unit',$request->item_unit_id)->first(); 
      $exist_item = Item::where('item_name',$request->item_name)->where('unit_id',$unit_id->id)->where('weight',$request->item_weight)->get();
      if(count($exist_item)>0){
          return $this->errorBadRequest(array('item_name'=>array('Item already Exist')));
      } 
    	$item = new Item();
    	$item->item_name = $request->item_name;
    	$item->description = $request->item_desc;
      $category_id = Category::select('id')->where('category',$request->item_cat_id)->first();
     	$item->cat_id = $category_id->id;
    	$item->quantity = $request->item_qty;
      // $unit_id = Unit::select('id')->where('unit',$request->item_unit_id)->first();
    	$item->unit_id = $unit_id->id;
    	$item->unit_price = $request->item_unit_price;
    	$item->weight = $request->item_weight;
      $item->tax_type_id = $request->tax_type_id;
    	$item->input_vat = $request->item_iVat;
    	$item->output_vat = $request->item_oVat;
    	$item->discount = $request->item_discount;
    	$item->location = $request->item_location;
      $item->status = $request->item_status;
      $item->save();
      return $this->response->array(['message'=>'Item added successfully']);
    }

   public function getItemList(){
   		$items = Item::join('categories','categories.id','items.cat_id')->join('units','units.id','items.unit_id')->join('tax_types','tax_types.id','items.tax_type_id')->select('items.*','categories.category as category','units.unit as unit','tax_types.tax_type as tax_type')->orderBy('items.id','desc')->get();
   		return $this->response->array(['data'=>$items]);
   }

   public function getItemListOnSearch($slug){
      $items = Item::join('categories','categories.id','items.cat_id')->join('units','units.id','items.unit_id')->join('tax_types','tax_types.id','items.tax_type_id')->select('items.*','categories.category as category','units.unit as unit','tax_types.tax_type as tax_type')->where('item_name','like',$slug."%")->orWhere('categories.category','like',$slug."%")->orWhere('items.location','like',$slug."%")->orderBy('items.id','desc')->get();
      return $this->response->array(['data'=>$items]);
   }

   public function updateItemMaster(Request $request){
        $item = Item::find($request->id);//where('item_id',$request->item_id)->first();
        $item->item_name = $request->item_name;
        $item->description = $request->item_desc;
        $category_id = Category::select('id')->where('category',$request->item_cat_id)->first();
        $item->cat_id = $category_id->id;
        $item->quantity = $request->item_qty;
        $unit_id = Unit::select('id')->where('unit',$request->item_unit_id)->first();
        $item->unit_id = $unit_id->id;
        $item->unit_price = $request->item_unit_price;
        $item->weight = $request->item_weight;
        $item->tax_type_id = $request->tax_type_id;
        $item->input_vat = $request->item_iVat;
        $item->output_vat = $request->item_oVat;
        $item->discount = $request->item_discount;
        $item->location = $request->item_location;
        $item->status = $request->item_status;
        $item->save();
        return $this->response->array(['message'=>'Item updated successfully']);
   }

   public function deleteItemMaster($id){
        $item = Item::find($id)->delete();
        return $this->response->array(['message'=>'success']);
   }

   public function getItemCategory(){
        $categories = Category::select('id','category')->get();
        return $this->response->array(['categories'=>$categories]);
   }

   public function getItemUnitType(){
        $units = Unit::select('id','unit')->get();
        return $this->response->array(['units'=>$units]);
   }

   //Vendor
   public function getVendorList(){
      $vendors = Vendor::orderBy('id','desc')->get();
      return $this->response->array(['data'=>$vendors]);
   }

   public function getVendorListActive(){
      $vendors = Vendor::orderBy('id','desc')->where('status','Active')->get();
      return $this->response->array(['data'=>$vendors]);
   }

   public function getVendorListOnSearch($slug){
      $vendors = Vendor::where('vendor_name','like',$slug."%")->orWhere('contact_number','like',$slug."%")->orderBy('id','desc')->get();
      return $this->response->array(['data'=>$vendors]);
   }

   public function insertVendorMaster(Request $request){
      $vendors = Vendor::where('vendor_name',$request->vendor_name)->get();
      if(count($vendors)>0){
          return $this->errorBadRequest(array('vendor_name'=>array('Vendor already Exist')));
      }
      $vendor = new Vendor();
      $vendor->vendor_name = $request->vendor_name;
      $vendor->contact_number = $request->contact_number;
      $vendor->address = $request->address;
      $vendor->email_id = $request->email_id;
      $vendor->status = $request->status;
      $vendor->save();
      return $this->response->array(['message'=>'Vendor added successfully']);
   }

   public function updateVendorMaster(Request $request){
        $vendor = Vendor::find($request->id);//where('item_id',$request->item_id)->first();
        $vendor->vendor_name = $request->vendor_name;
        $vendor->contact_number = $request->contact_number;
        $vendor->address = $request->address;
        $vendor->email_id = $request->email_id;
        $vendor->status = $request->status;
        $vendor->save();
        return $this->response->array(['message'=>'Vendor updated successfully']);
   }

   public function deleteVendorMaster($id){
        $vendor = Vendor::find($id)->delete();
        return $this->response->array(['message'=>'success']);
   }

   //Receive Item
   public function insertReceivedItem(Request $request){
        $receive_items = new ReceiveItem();
        $vendor = Vendor::select('id')->where('vendor_name',$request->vendor_id)->first();
        $receive_items->vendor_id = $vendor->id;
        $receive_items->item_id = $request->item_id;
        $receive_items->quantity = $request->quantity;
        $receive_items->unit_price = $request->unit_price;
        $receive_items->vat_rate = $request->vat_rate;
        $receive_items->rec_date = $request->rec_date;
        $receive_items->save();

        $item = Item::find($request->item_id);
        $item->quantity=$item->quantity + $request->quantity;
        $item->save();
        return $this->response->array(['message'=>'Received Item updated successfully']);
   }

   public function getReceivedItemList(){
        $receive_items = ReceiveItem::join('vendors','receive_items.vendor_id','vendors.id')->join('items','items.id','receive_items.item_id')->join('units','units.id','items.unit_id')->join('categories','categories.id','items.cat_id')->join('tax_types','tax_types.id','items.tax_type_id')->select('receive_items.id as id','receive_items.vendor_id as vendor_id','receive_items.item_id as item_id','receive_items.quantity as quantity','receive_items.unit_price as unit_price','receive_items.vat_rate as vat_rate','receive_items.rec_date as rec_date','items.item_name as item_name','vendors.vendor_name as vendor_name','units.unit as unit','items.weight as weight','categories.category as category','tax_types.tax_type as tax_type')->whereNull('items.deleted_at')->get();
          return $this->response->array(['receive_items'=>$receive_items]);
   }

   public function getReceivedItemListOnSearch($slug){
        $receive_items = ReceiveItem::join('vendors','receive_items.vendor_id','vendors.id')->join('items','items.id','receive_items.item_id')->join('units','units.id','items.unit_id')->join('categories','categories.id','items.cat_id')->join('tax_types','tax_types.id','items.tax_type_id')->select('receive_items.id as id','receive_items.vendor_id as vendor_id','receive_items.item_id as item_id','receive_items.quantity as quantity','receive_items.unit_price as unit_price','receive_items.vat_rate as vat_rate','receive_items.rec_date as rec_date','items.item_name as item_name','vendors.vendor_name as vendor_name','units.unit as unit','items.weight as weight','categories.category as category')->whereNull('items.deleted_at')->where(function($q) use($slug) {
          $q->where('items.item_name','like',$slug."%")->orWhere('categories.category','like',$slug."%")->orWhere('vendors.vendor_name','like',$slug."%")->orWhere('receive_items.rec_date','like',$slug."%");})->get();
          return $this->response->array(['receive_items'=>$receive_items]);
   }

   public function getItemStock(){
        $stock = Item::join('units','units.id','items.unit_id')->join('categories','categories.id','items.cat_id')->select('items.item_name as item_name','units.unit as unit','items.weight as weight','categories.category as category','items.quantity as quantity','items.location as location')->orderBy('item_name','asc')->get();
        return $this->response->array(['stock'=>$stock]);
   }

   public function getItemStockOnSearch($slug){
        $stock = Item::join('units','units.id','items.unit_id')->join('categories','categories.id','items.cat_id')->select('items.item_name as item_name','units.unit as unit','items.weight as weight','categories.category as category','items.quantity as quantity','items.location as location')->where('items.item_name','like',$slug."%")->orWhere('categories.category','like',$slug."%")->orWhere('items.location','like',$slug."%")->orderBy('item_name','asc')->get();
        return $this->response->array(['stock'=>$stock]);
   }
}