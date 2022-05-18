<?php

namespace App\Http\Controllers;

use auth;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ListingController extends Controller
{
    // show all listing
    public function index(){
        return view('listings.index', [
            // 'listings' => Listing::latest()->filter(request(['tag', 'search']))->paginate(2),
            'listings' => Listing::latest()->filter(request(['tag', 'search']))->simplePaginate(10),
        ]);
    }
    
    // sigle listing
    public function show(Listing $listing){
        return view('listings.show', [
            'listing' => $listing,
        ]);
    }

    // create form
    public function create(){
        return view('listings.create');
    }

    // store data
    public function store(Request $request){
       $formFields = $request->validate([
        //    'user_id' => auth()->id(),
           'title' => 'required',
            'company' => ['required', Rule::unique('listings', 'company')],
            'location' => 'required',
            'website' => 'required',
            'email' => ['required', 'email'],
            'tags' => 'required',
            'description' => 'required'
       ]);

       if($request->hasFile('logo')){
           $formFields['logo'] = $request->file('logo')->store('logos', 'public');
       }

    //    dd(auth()->id());

       $formFields['user_id'] = auth()->id();
        
       Listing::create($formFields);
    //    $request->session()->flash('message', 'Listing created');
       return redirect('/')->with('message', 'Listing created successfully');
    }

    // Show edit form
    public function edit(Listing $listing){
       return view('listings.edit', ['listing'=> $listing]);
    }

    // Update Listing
    public function update(Request $request, Listing $listing){
        // Make sure loggedin user is owner
        if($listing->user_id != auth()->id()){
            abort(403, 'Unauthorized action');
        }

       $formFields = $request->validate([
           'title' => 'required',
            'company' => ['required'],
            'location' => 'required',
            'website' => 'required',
            'email' => ['required', 'email'],
            'tags' => 'required',
            'description' => 'required'
       ]);

       if($request->hasFile('logo')){
           $formFields['logo'] = $request->file('logo')->store('logos', 'public');
        }
        
       $listing->update($formFields);
    //    $request->session()->flash('message', 'Listing created');
       return back()->with('message', 'Listing updated successfully');
    }

    // Delete Listing
    public function destroy(Listing $listing){
        // Make sure loggedin user is owner
        if($listing->user_id != auth()->id()){
            abort(403, 'Unauthorized action');
        }

        $listing->delete();
        return redirect('/')->with('message', 'Listing deleted successfully');
    }

    // Manage Listings
    public function manage(){
        return view('listings.manage', [
            'listings'=> auth()->user()->listings()->get()
        ]);
    }
    
}
