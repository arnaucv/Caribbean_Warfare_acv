<?php

namespace App\Http\Controllers;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as FacadesRequest;

class ProductController extends Controller
{
    public function __construct()
    {
        // $this->authorizeResource(Product::class, 'product');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products=Product::where('available', 1)->where('id','>',1)-> orderBy("created_at","desc")->get();
        if($products != null && $products != '[]'){
            return view('shop.index', compact('products'));

        }else{
           //en el caso de que no haya resultados tenemos que enviar a otra ruta
           return back();
        }
    }

    /**
     * Display a listing of all the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        $products=Product::all();
        return view('shop.all', compact('products'));
    }



/**public function index(){
    $products=Product::where('available', 1)-> orderBy("created_at","desc") ->get();
    return view('shop.index')->with('products');

}

public function search($data){
    $result= Product::where([['name','LIKE','%'.$data.'%'],['category','LIKE','%'.$data.'%']])->where('available',1);
    return $result;
}

public function additem($arrayprod){
    foreach($arrayprod as $product){
        Inventory::create(['user_id'=>Auth::user()->id,'product_id',$product['id']]);
    }
    //return view('shop');
} */

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //nadie puede crear productos, ni el administrador, esta tarea queda excluida ya que por ahora
        //la lista de productos que se pueden adquirir es fija (catalogo)
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //nadie puede crear productos, ni el administrador, esta tarea queda excluida ya que por ahora
        //la lista de productos que se pueden adquirir es fija (catalogo)
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //intento de usar can y cannot
        //$request->user()->cannot('view')

        //conseguimos todos los datos de el producto referenciado
        $product = Product::where('id', $id)->get();
        //comprovamos que no esté vacio y solo nos devuelva unas llaves sin contenido
        if($product != '[]'){
            return view('shop.show')->with('response',$product[0]);
        }

        //no cuadra
        return back();

    }

    /**
     * Display the specified resource.
     *
     * @param  string  $name
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {

        $products = product::where('name', 'LIKE', '%'.$request->search.'%')->get();

        $collection = collect(new Product());
        foreach($products as $product){
            $collection->add($product);
        }
        if(count($collection) >= 1){
            return view('shop.search', ['products' => $collection]);
        }
        return back();

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //Solamente se puede modificar el atributo available de products, para que pueda aparecer o no en la tienda
        $product = Product::where('id', $id)->get();
        if($product != '[]'){
            return view('shop.edit')->with('product', $product[0]);
        }
        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $jsonProduct = json_decode($request->product);

        $product = Product::where('id', $jsonProduct->id);

          if($product != '[]'){

            if($request->available != null){
                $request = new Request(array_merge($request->all(), ['available' => 1]));
                $validatedData = $request->validate([
                    'available'=>'integer'
                ]);
            }else{
                $request = new Request(array_merge($request->all(), ['available' => 0]));
                $validatedData = $request->validate([
                    'available'=> "integer"
                ]);
            }


            if($product->update($validatedData)){
                return view('shop.show',['response' => true]);
            }else{
                return view('shop.show',['response' => false]);
            }


        }
        return back();

        //$product = Product::where('id', $id)->get();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

     /**
     * Shopping history
     *
     *
     */
    public function history()
    {
        return view('shop.history');
    }
}
