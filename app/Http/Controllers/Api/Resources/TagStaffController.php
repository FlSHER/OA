<?php 

namespace App\Http\Controllers\Api\Resources;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller;
use App\Models\HR\Staff;
use App\Models\Tag as TagModel;


class TagStaffController extends Controller
{
    
    /**
     * get all tags of the staff.
     * 
     * @param  Request $request
     * @return  mixed
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $staff = Staff::find($user->staff_sn);
        $staff->load('tags', 'tags.category');

        return response()->json($staff->tags, 200);
    }

    /**
     * attach a tag for the staff.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \APP\Models\Tag $tag
     * @return  mixed
     */
    public function store(Request $request, TagModel $tag)
    {
        $user = $request->user();
        $staff = Staff::find($user->staff_sn);

        if ($staff->tags()->newPivotStatementForId($tag->id)->first()) {
            return response()->json([
                'message' => "你已拥有「{$tag->name}」标签，请勿重复添加",
            ], 422);
        }

        $staff->tags()->attach($tag);

        return response()->json(null, 204);
    }

    /**
     * detach a tag for the staff.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \APP\Models\Tag $tag
     * @return  mixed
     */
    public function destroy(Request $request, TagModel $tag)
    {
        $user = $request->user();
        $staff = Staff::find($user->staff_sn);

        if ($staff->tags()->newPivotStatementForId($tag->id)->first()) {
            return response()->json([
                'message' => "你没有「{$tag->name}」标签",
            ], 422);
        }

        $staff->tags()->detach($tag);

        return response()->json(null, 204);
    }
}