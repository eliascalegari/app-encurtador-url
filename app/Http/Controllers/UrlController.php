<?php

namespace App\Http\Controllers;

use App\Models\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UrlController extends Controller
{

    protected $url;

    public function __construct(Url $url)
    {
        $this->url = $url;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $urls = $this->url->all();
        return response()->json($urls, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUrlRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->url->rules());

        $url = new Url();
       
        $url->hash = Str::random(6);
        $url->target_url = $request->target_url;
        $url->user_id = $request->user()->id;
        $url->expired_at = date_add(now(),date_interval_create_from_date_string("60 days"));
        
        $url->save();
        //return response()->json('localhost:8000/'.$url->hash, 201);
        //return redirect()->away($url->target_url);
        return response()->json($url, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Url  $url
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $url = $this->url->with('user')->find($id);
        if($url === null){
            return response()->json(['erro'=>'O recurso solicitado não existe'], 404);
        }
        
        return response()->json($url, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUrlRequest  $request
     * @param  \App\Models\Url  $url
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $url = $this->url->find($id);
        if($url === null){
            return response()->json(['erro'=>'O recurso solicitado não existe'], 404);
        }

        if($request->method() === 'PATCH'){
            $rulesd = array();

            foreach($url->rules() as $input => $rule ){
                if(array_key_exists($input, $request->all())){
                    $rulesd[$input] = $rule;
                }
            }

            $request->validate($rulesd);
        }else{
            $request->validate($this->url->rules());
        }

        $url->update($request->all());
        return response()->json($url, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Url  $url
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $url = $this->url->find($id);
        if($url === null){
            return response()->json(['erro'=>'O recurso solicitado não existe'], 404);
        }
        $url->delete();
        return response()->json(['msg' => 'Removido com sucesso'], 200);
    }

    public function redirectToSite($hash){
        $url = $this->url->find($hash);
        return redirect()->away($url->target_url);
    }
}
