<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Domain\Pusher\Http\Requests\SendMessageRequest;
use Domain\Pusher\Http\Requests\SendMessageToUserRequest;
use Domain\Pusher\Http\Requests\UploadFileRequest;
use Domain\Pusher\Repositories\ChatRepository;
use Domain\Pusher\Repositories\MessageRepository;
use Domain\Pusher\Services\ChatService;
use Auth;
use Illuminate\Http\Request;
use Laravel\Passport\Bridge\UserRepository;

class ChatController extends Controller
{
    /**
     * @var ChatRepository
     */
    protected $repository;

    /**
     * @var MessageRepository
     */
    protected $messageRepository;

    /**
     * @var ChatService
     */
    protected $chatService;

    /**
     * ChatController constructor.
     * @param ChatRepository $repository
     */
    public function __construct(ChatRepository $repository, MessageRepository $messageRepository, ChatService $chatService)
    {
        $this->repository = $repository;
        $this->messageRepository = $messageRepository;
        $this->chatService = $chatService;
        parent::__construct();
    }


    /**
     * Возвращает список диалогов
     * @return \Illuminate\Http\JsonResponse
     */
    public function dialogs()
    {
        $dialogs = $this->repository->getChatsByUserId(Auth::user()->id);
        return response()->json($dialogs);
    }

    /**
     * @param int $chat_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function messages(int $chat_id)
    {
        $messages = $this->messageRepository->getAllOfChatById($chat_id, Auth::user()->id);
        return response()->json($messages);
    }

    /**
     * Отправка сообщения в чат
     * @param SendMessageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(SendMessageRequest $request)
    {
        $message = $this->chatService->send(
            $request->get('body'),
            $request->get('chatId'),
            Auth::user()->id,
            $request->get('replyOnMessageId'),
            $request->get('forwardFromChatId'),
            $request->get('attachmentIds') ? $request->get('attachmentIds') : []
        );
        return response()->json(['data' => $message]);
    }

    /**
     * @param SendMessageToUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendToUser(SendMessageToUserRequest $request)
    {
        $message = $this->chatService->sendToUser(
            $request->get('body'),
            $request->get('userId'),
            Auth::user()->id,
            $request->get('attachmentIds') ? $request->get('attachmentIds') : []
        );
        return response()->json(['data' => $message]);
    }

    /**
     * @param UploadFileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadAttachments(UploadFileRequest $request) {
        $attachment_ids = $this->chatService->uploadFiles($request->allFiles());
        return response()->json([
            'attachmentIds' => $attachment_ids
        ]);
    }
}
