<?php
namespace App\Http\Requests;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class MeasurementCommitCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // See policies for more detailed authorization.
        return $this->user() !== null;
    }

    public function rules(): array
    {
        // TODO commit message
        return [
            'data.id'                             => 'required|uuid',
            'data.name'                           => 'required|string|min:1|max:50',
            'data.measurements'                   => 'nullable|array',
            'data.measurements.*.data.id'         => 'required|uuid|distinct',
            'data.measurements.*.data.setting_id' => [
                'required',
                'uuid',
                'distinct',
                /*
                 * Because measurement settings are never deleted from the database table the following check is only
                 * to prevent eg. hacking, or my own stupidity.
                 *
                 * Use of soft deleted measurement_settings is valid for alterations so it's up to the front end code
                 * to display the correct measurement settings.
                 */
                Rule::exists('measurement_settings', 'id')->where(function (Builder $query) {
                    $query->where('company_id', $this->route('company')->id);
                }),
            ],
            'data.measurements.*.data.comment'    => 'nullable|string|min:1|max:50',
            'data.measurements.*.data.value'      => 'nullable|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'data.id.required'                             => Messages::GENERIC_REQUIRED,
            'data.id.uuid'                                 => Messages::GENERIC_UUID,
            'data.name.required'                           => Messages::GENERIC_REQUIRED,
            'data.name.string'                             => Messages::GENERIC_STRING,
            'data.name.min'                                => Messages::GENERIC_STRING_MIN_LENGTH_(1),
            'data.name.max'                                => Messages::GENERIC_STRING_MAX_LENGTH_(50),
            'data.measurements.array'                      => Messages::GENERIC_ARRAY,
            'data.measurements.*.data.id.required'         => Messages::GENERIC_REQUIRED,
            'data.measurements.*.data.id.uuid'             => Messages::GENERIC_UUID,
            'data.measurements.*.data.id.distinct'         => Messages::GENERIC_DISTINCT,
            'data.measurements.*.data.setting_id.required' => Messages::GENERIC_REQUIRED,
            'data.measurements.*.data.setting_id.uuid'     => Messages::GENERIC_UUID,
            'data.measurements.*.data.setting_id.distinct' => Messages::GENERIC_DISTINCT,
            'data.measurements.*.data.setting_id.exists'   => Messages::GENERIC_INVALID,
            'data.measurements.*.data.comment.string'      => Messages::GENERIC_STRING,
            'data.measurements.*.data.comment.min'         => Messages::GENERIC_STRING_MIN_LENGTH_(1),
            'data.measurements.*.data.comment.max'         => Messages::GENERIC_STRING_MAX_LENGTH_(50),
            'data.measurements.*.data.value.integer'       => Messages::GENERIC_INTEGER,
        ];
    }
}