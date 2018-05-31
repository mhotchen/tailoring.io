<?php
namespace App\Http\Controllers;

use App\Http\Requests\CustomerStoreRequest;
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
     * The reason for the company parameter is so that it's loaded in to the CompanyPolicy, which uses some Laravel/
     * reflection magic to load the model based on the parameters of this method. As policies need to become
     * stricter then we can move to using the Customer model only.
     *
     * @param Company  $company
     * @param Customer $customer
     * @return CustomerResource
     */
    public function get(Company $company, Customer $customer): CustomerResource
    {
        return new CustomerResource($customer);
    }

    /**
     * @param CustomerStoreRequest $customerStore
     * @param Company              $company
     * @return CustomerResource
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Throwable
     */
    public function store(CustomerStoreRequest $customerStore, Company $company): CustomerResource
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
