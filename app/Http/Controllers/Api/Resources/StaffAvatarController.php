<?php

namespace App\Http\Controllers\Api\Resources;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\HR\Staff as StaffModel;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class StaffAvatarController extends Controller
{
    /**
     * Show staff avatar.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\HR\Staff $staff
     * @return mixed
     */
    public function show(Request $request, StaffModel $staff)
    {
        $size = intval($request->query('s', 0));
        $size = max($size, 0);
        $size = min($size, 500) === 500 ? 0 : $size;

        return response()->redirectTo($staff->avatar($size));
    }

    /**
     * Upload staff avatar.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function update(Request $request)
    {
        $this->validate($request, $this->uploadAvatarRules(), $this->uploadAvatarMessages());

        $avatar = $request->file('avatar');
        if (! $avatar->isValid()) {
            return response()->json(['messages' => $avatar->getErrorMessage()], 400);
        }
        $staff = StaffModel::find($request->user()->staff_sn);

        return $staff->storeAvatar($avatar)
            ? response()->make('', 204)
            : response()->json(['message' => '上传失败'], 500);
    }

    /**
     * Get upload valodate rules.
     *
     * @return array
     */
    protected function uploadAvatarRules(): array
    {
        return [
            'avatar' => [
                'required',
                'image',
                'max:'.$this->getMaxFilesize() / 1024,
                'dimensions:min_width=100,min_height=100,max_width=500,max_height=500,ratio=1/1',
            ],
        ];
    }

    /**
     * Get upload validate messages.
     *
     * @return array
     */
    public function uploadAvatarMessages(): array
    {
        return [
            'avatar.required' => '请上传头像.',
            'avatar.image' => '头像必须是 png/jpeg/bmp/gif/svg 图片',
            'avatar.max' => sprintf('头像尺寸必须小于%sMB', $this->getMaxFilesize() / 1024 / 1024),
            'avatar.dimensions' => '头像必须是正方形，宽高必须在 100px - 500px 之间',
        ];
    }

    /**
     * Get upload max file size.
     *
     * @return int
     */
    protected function getMaxFilesize()
    {
        return UploadedFile::getMaxFilesize();
    }
}
