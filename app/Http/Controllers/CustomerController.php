<?php
namespace App\Http\Controllers;

use App\Http\Requests\CustomerStoreRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Company;
use App\Models\Customer;
use Auth;
use DB;

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
     * @param CustomerStoreRequest $customerStoreRequest
     * @param Company              $company
     * @return CustomerResource
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Throwable
     */
    public function post(CustomerStoreRequest $customerStoreRequest, Company $company): CustomerResource
    {
        $request = $customerStoreRequest->validated();
        $customer = new Customer;
        $customer->hydrateFromRequest($request, Auth::user(), $company);
        $notesData = collect($request['data']['notes'])->filter(function(array $data) {
            return isset($data['data']['note']) && $data['data']['note'] !== '';
        });

        DB::transaction(function () use ($customer, $notesData) {
            $customer->save();
            $customer->createNewNotes($notesData, Auth::user());
            $customer->load('notes');
        });

        return new CustomerResource($customer);
    }

    /**
     * @param CustomerStoreRequest $customerStoreRequest
     * @param Company              $company
     * @param Customer             $customer
     * @return CustomerResource
     * @throws \Throwable
     */
    public function put(
        CustomerStoreRequest $customerStoreRequest,
        Company $company,
        Customer $customer
    ): CustomerResource
    {
        $request = $customerStoreRequest->validated();
        $customer->hydrateFromRequest($request, Auth::user(), $company);
        $notesData = collect($request['data']['notes'])->filter(function(array $data) {
            return isset($data['data']['note']) && $data['data']['note'] !== '';
        });

        DB::transaction(function () use ($customer, $notesData) {
            $customer->save();
            $customer->loadMissing('notes');
            $customer
                ->deleteClearedNotes($notesData)
                ->updateExistingNotes($notesData, Auth::user())
                ->createNewNotes($notesData, Auth::user());
            $customer->load('notes');
        });

        return new CustomerResource($customer);
    }
}
