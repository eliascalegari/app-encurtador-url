<?php

namespace App\Http\Controllers;

use App\Models\Url;
use App\Services\UrlCache;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\RabbitMQService;


class UrlController extends Controller
{

    protected $url;
    protected $urls;
    private $rabbitMQService;

    public function __construct(RabbitMQService $rabbitMQService, Url $url)
    {
        $this->url = $url;
        $this->urls = new UrlCache();
        $this->rabbitMQService = $rabbitMQService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
        // $urls = $this->url->all();

        $msgBody = json_encode('Consulta de todas as URLs criadas');
        $this->rabbitMQService->publishMessage('url_index', $msgBody);
        
        return response()->json($this->urls->getAll(), 200);
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

        $user = $request->user();

        $url = new Url([
            'hash' => Str::random(6),
            'target_url' => $request->input('target_url'),
            'user_id' => $user->id,
            'expired_at' => Carbon::now()->addYear(1)
        ]);

        $user->urls()->save($url);

        $msgBody = json_encode($url->hash);
        $this->rabbitMQService->publishMessage('url_store', $msgBody);

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
        //$url = $this->url->with('user')->find($id);
        $url = $this->urls->getById($id);
        if($url === null){
            return response()->json(['erro'=>'O recurso solicitado não existe'], 404);
        }

        $msgBody = json_encode($url->hash);
        $this->rabbitMQService->publishMessage('url_show', $msgBody);
        
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

        $msgBody = json_encode($url);
        $this->rabbitMQService->publishMessage('url_update', $msgBody);

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

        $msgBody = json_encode($url);
        $this->rabbitMQService->publishMessage('url_delete', $msgBody);

        return response()->json(['msg' => 'Removido com sucesso'], 200);
    }

    public function redirectToSite($hash){
        //$url = $this->url->find($hash);
        $url = $this->urls->getById($hash);
        if($url === null){
            return response()->json(['erro'=>'O recurso solicitado não existe'], 404);
        }

        $msgBody = json_encode($url);
        $this->rabbitMQService->publishMessage('url_acess', $msgBody);

        return redirect()->away($url->target_url);
    }

}


