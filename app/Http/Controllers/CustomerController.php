<?php
namespace App\Http\Controllers;

use App\Http\Requests\CustomerStore;
use App\Http\Resources\CustomerResource;
use App\Model\Company;
use App\Model\Customer;
use App\Model\CustomerNote;
use Auth;
use DB;
use Illuminate\Support\Collection;

final class CustomerController extends Controller
{
    /**
     * @param CustomerStore $customerStore
     * @param Company       $company
     * @return CustomerResource
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Throwable
     */
    public function store(CustomerStore $customerStore, Company $company): CustomerResource
    {
        $request = $customerStore->validated();
        $user = Auth::user();

        $customer = Customer::fromRequest($request, $user, $company);
        $notes = (new Collection($request['data']['notes']))
            ->filter(function (array $note): bool {
                return isset($note['data']['note']) && $note['data']['note'] !== '';
            })
            ->map(function (array $note) use ($user): CustomerNote {
                return CustomerNote::fromRequest($note, $user);
            });

        DB::transaction(function () use ($customer, $notes) {
            $customer->save();
            $customer->notes()->saveMany($notes);
        });

        return new CustomerResource($customer);
    }
}
