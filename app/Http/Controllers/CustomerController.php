<?php
namespace App\Http\Controllers;

use App\Http\Requests\CustomerStoreRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Company;
use App\Models\Customer;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

final class CustomerController extends Controller
{
    /**
     * @param Company $company
     * @param Request $request
     * @return ResourceCollection
     * @throws \InvalidArgumentException
     */
    public function index(Company $company, Request $request): ResourceCollection
    {
        $query = $company->customers()->limit(20);
        if ($request->query('q')) {
            // Normalize whitespace and explode in to different tokens.
            $tokens = explode(' ', preg_replace('/\s+/', ' ', trim($request->query('q'))));
            foreach ($tokens as $token) {
                /*
                 * Thanks to Postgres the following matches an index.
                 *
                 * If you need to modify it then don't forget to update the index!
                 */
                $query->whereRaw(
                    "
                        COALESCE(name, '') ||
                        ' ' ||
                        COALESCE(email, '') ||
                        ' ' ||
                        COALESCE(REGEXP_REPLACE(telephone, '[^\+a-zA-Z0-9]', '', 'g'), '')
                        ~~* ?
                    ",
                    ["%$token%"]
                );
            }
        }

        return
            CustomerResource::collection($query->get())
                ->additional([
                    'meta' => ['total_customers' => $company->customers()->count()]
                ]);
    }

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
        // This line ensures the notes are added to the returned JSON since they're only added if they're loaded.
        // We don't load notes on the home page to improve performance.
        $customer->loadMissing('notes');

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
