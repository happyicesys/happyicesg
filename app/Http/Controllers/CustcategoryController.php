<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\CustcategoryRequest;
use App\Http\Requests;
use App\Attachment;
use App\Custcategory;
use App\User;
use Carbon\Carbon;
use Storage;

class CustcategoryController extends Controller
{
    //auth-only login can see
    public function __construct()
    {
        $this->middleware('auth');
    }

    // retrieve customer categories index api()
    public function getData()
    {
    	// $custcats = Custcategory::orderBy('name')->get();
    	// return $custcats;
                // showing total amount init
        $total_amount = 0;
        // initiate the page num when null given
        $pageNum = request('pageNum') ? request('pageNum') : 100;

        if(request('active')) {
            $active = request('active');
            if (count($active) == 1) {
                $active = [$active];
            }
        }else {
            $active = [];
        }

        $query = Custcategory::with(['custcategoryGroup', 'attachments'])->withCount(['people' => function($query) use ($active){
            if($active) {
                $query->whereIn('active', $active);
            }
        }]);

        if(request('name')) {
            $query = $query->where('name', 'LIKE', '%'.request('name').'%');
        }

        if(request('custcategory_groups')) {
            $custcategory_groups = request('custcategory_groups');
            if (count($custcategory_groups) == 1) {
                $custcategory_groups = [$custcategory_groups];
            }

            $query = $query->whereHas('custcategoryGroup', function($query) use ($custcategory_groups) {
                $query->whereIn('id', $custcategory_groups);
            });
        }

        if($active) {
            $query = $query->whereHas('people', function($query) use ($active) {
                $query->whereIn('active', $active);
            });
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
            'custcategories' => $query
        ];
    }

    // return create new cust category page()
    public function create()
    {
        return view('user.custcat.create');
    }

    // store new created cust category(FormRequest $request)
    public function store(CustcategoryRequest $request)
    {
        $input = $request->all();
        $custcat = Custcategory::create($input);
        return redirect()->action(
            'CustcategoryController@edit', ['id' => $custcat->id]
        );
    }

    // retrieve single custcategory api(int id)
    public function getCustcategoryApi($id)
    {
        $custcat = Custcategory::findOrFail($id);

        return $custcat;
    }

    // return cust category edit page(int custcategory_id)
    public function edit($id)
    {
    	$custcat = Custcategory::findOrFail($id);
        $attachments = $custcat->attachments;
    	return view('user.custcat.edit', compact('attachments', 'custcat'));
    }

    // update cust category(FormRequest $request, int $custcategory_id)
    public function update(CustcategoryRequest $request, $id)
    {
        $custcat = Custcategory::findOrFail($id);
        $input = $request->all();
        $custcat->update($input);
        return redirect()->action(
            'CustcategoryController@edit', ['id' => $custcat->id]
        );
    }

    // ajax destroy cust category (int $custcategory_id)
    public function destroyAjax($id)
    {
        $custcat = Custcategory::findOrFail($id);
        $custcat->delete();
        return $custcat->name . 'has been successfully deleted';
        return redirect('user');
    }

    // ajax destroy cust category (int $custcategory_id)
    public function destroy($id)
    {
        $custcat = Custcategory::findOrFail($id);
        $custcat->delete();
        return redirect('user');
    }

    // return custcategories by user id given
    public function getCustcategoryByUserIdApi($userId, $type = 1)
    {
        if($type == 1) {
            $user = User::findOrFail($userId);

            return $user->custcategories;

        }else if($type == 2) {
            $custcategories = Custcategory::whereDoesntHave('users', function($query) use ($userId) {
                            $query->where('id', $userId);
                        })->get();

            return $custcategories;
        }
    }

    // create attachment by custcategory(int custcategoryId)
    public function createAttachment($custcategoryId)
    {
        $custcategory = Custcategory::findOrFail($custcategoryId);
        $file = request()->file('file');

        $name = (Carbon::now()->format('dmYHi')).$file->getClientOriginalName();
        Storage::put('custcat_attachments/'.$name, file_get_contents($file->getRealPath()), 'public');
        $url = (Storage::url('custcat_attachments/'.$name));
        $custcategory->attachments()->create([
            'url' => 'custcat_attachments/'.$name,
            'full_url' => $url,
        ]);
    }

    // remove attachment from the custcategory(int custcategoryId, int attachmentId)
    public function removeAttachment($custcategoryId, $attachmentId, $type = 1)
    {
        $attachment = Attachment::findOrFail($attachmentId);
        Storage::delete($attachment->url);
        $attachment->delete();

        if($type == 1) {
            return true;
        }else {
            return redirect()->action(
                'CustcategoryController@edit', ['id' => $custcategoryId]
            );
        }
    }
}
