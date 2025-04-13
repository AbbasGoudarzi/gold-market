<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TransactionController extends Controller
{
    public function __construct(public TransactionService $transactionService)
    {
    }

    public function index(IndexTransactionRequest $request): AnonymousResourceCollection
    {
        $perPage = $request->get('per_page', '10');
        $type = $request->get('type', 'all');
        $user = $request->user();
        $transactions = $this->transactionService->getTransactions($user, $type);
        return TransactionResource::collection($transactions->paginate($perPage));
    }
}
