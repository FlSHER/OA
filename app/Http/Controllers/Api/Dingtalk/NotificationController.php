<?php 

namespace App\Http\Controllers\Api\Dingtalk;

use App\Models\HR\Staff;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Dingtalk\Server\MessageService;


class NotificationController extends Controller
{
	protected $messageService;

	/**
     * MessageService constructor.
     *
     * @param \App\Services\Dingtalk\Server\MessageService
     */
	public function __construct(MessageService $messageService)
	{
		$this->messageService = $messageService;
	}
	
	/**
	 * @param \Illuminate\Http\Request $request
	 * 
	 * @return mixed
	 */
	public function send(Request $request)
	{
		$this->validate($request, [
			'msg' => 'required|max:100',
			'agent_id' => 'required|in:39806381,52370430,130038188',
			'userid_list.*' => 'required|exists:staff,staff_sn',
		], [], $this->attributes());

		$data = $request->all();
		$users = Staff::whereIn('staff_sn', $data['userid_list'])
			->pluck('dingtalk_number')
			->filter()
			->implode(',');

		$message = "{$data['msg']}\n".date('Y/m/d H:i:s');
		$result = $this->messageService
            ->ofAgent($data['agent_id'])
			->withReply($message)
			->toUser($users)
            ->send();

        if ($result['errcode'] !== 0) {
        	abort(500, $result['errmsg']);
        }

        return response()->json(['message' => 'ok']); 
	}

	/**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'msg' => '通知消息',
			'agent_id' => '通知应用',
			'userid_list.*' => '通知员工',
        ];
    }
}