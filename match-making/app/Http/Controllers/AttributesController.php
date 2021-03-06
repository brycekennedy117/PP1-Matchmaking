<?php

namespace App\Http\Controllers;

use App\MingleLibrary\Models\Postcode;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\MingleLibrary\Models\UserAttributes;
use Illuminate\Http\Request;
use Image;
use Illuminate\Support\Facades\Storage;

class AttributesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        if (Auth::user()->Attributes != null) {
            return redirect('/dashboard');
        }
        return view('attributes');

    }

    public function store(Request $request)
    {

        UserAttributes::create([
            'user_id' => Auth::user()->id,
            'openness' => $request['openness']/10,
            'conscientiousness' => $request['conscientiousness']/10,
            'extraversion' => $request['extraversion']/10,
            'agreeableness' => $request['agreeableness']/10,
            'neuroticism' => $request['neuroticism']/10,
            'postcode' => (int)$request['postcode-id'],
            'date_of_birth' => $request['date_of_birth'],
            'gender' => $request['gender'],
            'interested_in' => $request['interested_in'],
            'image_url' =>  $request['image_url'] ? $request['image_url'] : "https://profiles.utdallas.edu/img/default.png",
            'greeting' => $request['greeting'] ? $request['greeting'] : "Hello"

        ]);
        return redirect('dashboard');
    }


    public function showAvatar(Request $request)
    {
        $this->validate($request, [
            'file' => 'required'
        ]);
        $file = $request->file('file');

        if ($file->isValid()) {
            $name = $file->getClientOriginalName();
            $key = 'documents/' . $name;
            Storage::disk('s3')->put($key, file_get_contents($file));
            $url = Storage::disk('s3')->url('documents/' . $name);

            $id = Auth::user()->id;

            $attr = UserAttributes::all()
            ->where('user_id', '==', $id)->first();
            $attr->image_url = $url;
            $attr->save();

        }

        return redirect('/profile');

    }


    public function suburbs(Request $request) {
        $postcode = $request['postcode'];
        $suburbs = Postcode::all()->where('postcode', $postcode);
        return $suburbs;
    }

    public function getUserAttribute(Request $request) {
        $attr = UserAttributes::all()->where('user_id', $request['user_id'])->first();
        $user = $attr->user;
        $postcode = $attr->postcodeObject;

        return ['attributes' => $attr, 'user' => $user, 'postcode' => $postcode];
    }
}
