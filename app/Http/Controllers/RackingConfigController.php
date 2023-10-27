<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Attachment;
use App\RackingConfig;
use App\Http\Requests;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;


class RackingConfigController extends Controller
{
    // get index api
    public function getData()
    {
        // showing total amount init
        $total_amount = 0;
        // initiate the page num when null given
        $pageNum = request('pageNum') ? request('pageNum') : 100;

        $query = RackingConfig::with(['attachments', 'vendings'])->withCount('vendings');

        if(request('name')) {
            $query = $query->where('name', 'LIKE', '%'.request('name').'%');
        }

        if(request('desc')) {
            $query = $query->where('desc', 'LIKE', '%'.request('desc').'%');
        }

        if(request('sortName')){
            $query = $query->orderBy(request('sortName'), request('sortBy') ? 'asc' : 'desc');
        }

        if($pageNum == 'All'){
            $query = $query->orderBy('name', 'asc')->get();
        }else{
            $query = $query->orderBy('name', 'asc')->paginate($pageNum);
        }

        return [
            'rackingConfigs' => $query
        ];
    }

    // destroy single
    public function deleteRackingConfigApi($id)
    {
        $model = RackingConfig::findOrFail($id);

        $model->delete();
    }

    // add new single
    public function store(Request $request)
    {
        RackingConfig::create([
            'name' => $request->name,
            'desc' => $request->desc,
        ]);

        return redirect('user');
    }

    public function create(Request $request)
    {
        return view('user.racking-config.create');
    }

    public function destroyAjax($id)
    {
        $rackingConfig = RackingConfig::findOrFail($id);
        $rackingConfig->delete();
    }

    public function edit($id)
    {
    	$rackingConfig = RackingConfig::findOrFail($id);
        $attachments = $rackingConfig->attachments;
    	return view('user.racking-config.edit', compact('attachments', 'rackingConfig'));
    }

    public function update(Request $request, $id)
    {
        $rackingConfig = RackingConfig::findOrFail($id);
        $input = $request->all();
        $rackingConfig->update($input);
        return redirect()->action(
            'RackingConfigController@edit', ['id' => $rackingConfig->id]
        );
    }

    // create attachment by racking config(int rackingConfigId)
    public function createAttachment($rackingConfigId)
    {
        $rackingConfig = RackingConfig::findOrFail($rackingConfigId);
        $file = request()->file('file');

        $name = (Carbon::now()->format('dmYHi')).$file->getClientOriginalName();
        Storage::put('racking_config_attachments/'.$name, file_get_contents($file->getRealPath()), 'public');
        $url = (Storage::url('racking_config_attachments/'.$name));
        $rackingConfig->attachments()->create([
            'url' => 'racking_config_attachments/'.$name,
            'full_url' => $url,
        ]);
    }

    // remove attachment from the racking config(int rackingConfigId, int attachmentId)
    public function removeAttachment($rackingConfigId, $attachmentId)
    {
        $attachment = Attachment::findOrFail($attachmentId);
        Storage::delete($attachment->url);
        $attachment->delete();

        return redirect()->action(
            'RackingConfigController@edit', ['id' => $rackingConfigId]
        );
    }
}
