<?php

namespace App\Http\Controllers\Api;

use App\Models\Utm;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Utm as UtmResource;

class UtmController extends Controller
{
    /**
     * Return all utms
     */
    public function index(Request $request)
    {
        try {
            // Set order by
            $orderBy = ['created_at', 'desc'];
            $utmType = $request->query('utm_type');

            if ( $request->order_by )
                $orderBy = explode('|', $request->order_by);


            $utmQuery = Utm::WhereUser()
                ->where('utm_type', '=', $utmType)
                ->orderBy($orderBy[0], $orderBy[1]);


            // If per_page is not set return all result
            $perPage = ( $request->per_page ? $request->per_page : $utmQuery->count() );

            return UtmResource::collection( $utmQuery->paginate($perPage) );

        }
        catch (\Exception $e) {
            return response(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show a specific utm
     */
    public function show($id)
    {
        try {

            $cta = new CallToActionResource( CallToAction::WhereUser()->where('id', $id)->firstOrFail() );

            return response($cta, 200);

        } catch (Exception $e) {
            return response(['message' => 'Unable to locate this call to action'], 404);
        }
    }

    /**
     * Store a new created utm
     */
    public function store(Request $request)
    {
        // Check if the user has the ability to add more custom scripts
        // Only check for non admin users
        if ( ! $request->user()->hasPermissionTo('access admin') ) {

            $canUse = $request->user()->subscription('membership')->ability()->canUse('utm');
            if ( ! $canUse )
                return response(['message' => 'You have maxed out the total number of Custom Scripts allowed on your current plan'], 422);

        }

        // Validate request server side
        $request->validate([
            'name'             => 'bail|required|string|max:40',
            'description'      => 'required'
        ]);

        try {

            Utm::create([
                'utm_name'          => trim( $request->name ),
                'utm_description'   => trim( $request->description ),
                'utm_type'          => $request->utmType,
                'user_id'           => $request->user()->id,
                'disabled'          => $request->disabled
            ]);

            // Record feature usage for non admin users
            if ( ! $request->user()->hasPermissionTo('access admin') )
                $request->user()->subscriptionUsage('membership')->record('utm');


            return response(['message' => 'Your UTM parameter was successfully added'], 201);

        } catch (Exception $e) {
            return response(['message' => 'Unable to add your UTM parameter at the moment'], 500);
        }
    }

    /**
     * Update a utm
     */
    public function update(Request $request, $id)
    {
        // Validate request server side
        $request->validate([
            'utm_name'          => 'bail|required|string|max:40',
            'utm_description'   => 'required'
        ]);

        try {
            // Update the utm data
            $utm = Utm::find($id);

            $utm->utm_name          = trim( $request->utm_name );
            $utm->utm_description   = $request->utm_description;
            $utm->disabled          = $request->disabled;
            $utm->save();

            return response(['message' => 'Your utm was successfully updated'], 201);

        } catch (Exception $e) {
            return response(['message' => 'Unable to update this utm at the moment'], 500);
        }
    }

    /**
     * Delete a utm
     */
    public function destroy(Request $request, $id)
    {
        try {

            Utm::WhereUser()->where('id', $id)->delete();

            // Reduce feature usage for non admin users
            if ( ! $request->user()->hasPermissionTo('access admin') )
                $request->user()->subscriptionUsage('membership')->reduce('utms');


            return response(['message' => 'Your utm was successfully deleted'], 201);

        } catch (Exception $e) {
            return response(['message' => 'Unable to delete this utm at the moment'], 500);
        }
    }

    /**
     * Change the utm status from disabled or enabled
     */
    public function changeStatus(Request $request, $id)
    {
        try {

            $utm = Utm::WhereUser()->where('id', $id)->first();

            $utm->disabled = $request->disabled;
            $utm->save();

            return response(['message' => 'Your utm status was successfully updated'], 201);

        }
        catch (Exception $e) {
            return response(['message' => 'Unable to update your utm status at the moment'], 500);
        }
    }
}
