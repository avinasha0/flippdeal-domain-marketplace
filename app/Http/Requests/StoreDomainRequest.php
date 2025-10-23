<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDomainRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'domain_name' => 'required|string|max:255|regex:/^[a-zA-Z0-9-]+$/',
            'domain_extension' => 'required|string|max:10',
            'asking_price' => 'required|numeric|min:0.01|max:9999999.99',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:2000',
            'registration_date' => 'nullable|date|before_or_equal:today',
            'expiry_date' => 'nullable|date|after:registration_date',
            'has_website' => 'boolean',
            'has_traffic' => 'boolean',
            'premium_domain' => 'boolean',
            'additional_features' => 'nullable|string|max:1000',
            // Buy Now options
            'enable_buy_now' => 'boolean',
            'buy_now_price' => 'nullable|required_if:enable_buy_now,1|numeric|min:0.01|max:9999999.99',
            'buy_now_expires_at' => 'nullable|date|after:now',
            // Make An Offer options
            'enable_offers' => 'boolean',
            'minimum_offer' => 'nullable|numeric|min:0.01|max:9999999.99',
            'maximum_offer' => 'nullable|numeric|min:0.01|max:9999999.99',
            'auto_accept_offers' => 'boolean',
            'auto_accept_threshold' => 'nullable|required_if:auto_accept_offers,1|numeric|min:0.01|max:9999999.99',
            // Auction/Bidding fields
            'enable_bidding' => 'boolean',
            'starting_bid' => 'nullable|required_if:enable_bidding,1|numeric|min:0.01|max:9999999.99',
            'reserve_price' => 'nullable|numeric|min:0.01|max:9999999.99',
            'auction_start' => 'nullable|required_if:enable_bidding,1|date|after:now',
            'auction_end' => 'nullable|required_if:enable_bidding,1|date|after:auction_start',
            'minimum_bid_increment' => 'nullable|integer|min:1|max:1000',
            'auto_extend' => 'boolean',
            'auto_extend_minutes' => 'nullable|integer|min:1|max:60',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Buy Now validation
            if ($this->boolean('enable_buy_now')) {
                if (!$this->filled('buy_now_price')) {
                    $validator->errors()->add('buy_now_price', 'Buy Now price is required when Buy Now is enabled.');
                }
                
                // Buy Now price should be reasonable (not required to be higher than asking price)
                if ($this->filled('buy_now_price') && $this->buy_now_price < 0.01) {
                    $validator->errors()->add('buy_now_price', 'Buy Now price must be at least $0.01.');
                }
            }
            
            // Make An Offer validation
            if ($this->boolean('enable_offers')) {
                // Minimum offer validation
                if ($this->filled('minimum_offer') && $this->minimum_offer < 0.01) {
                    $validator->errors()->add('minimum_offer', 'Minimum offer must be at least $0.01.');
                }
                
                // Maximum offer validation
                if ($this->filled('maximum_offer') && $this->maximum_offer < 0.01) {
                    $validator->errors()->add('maximum_offer', 'Maximum offer must be at least $0.01.');
                }
                
                // Maximum offer should be higher than minimum offer (only if both are provided)
                if ($this->filled('minimum_offer') && $this->filled('maximum_offer')) {
                    if ($this->maximum_offer <= $this->minimum_offer) {
                        $validator->errors()->add('maximum_offer', 'Maximum offer should be higher than minimum offer.');
                    }
                }
                
                // Auto-accept threshold validation
                if ($this->boolean('auto_accept_offers')) {
                    if (!$this->filled('auto_accept_threshold')) {
                        $validator->errors()->add('auto_accept_threshold', 'Auto-accept threshold is required when auto-accept is enabled.');
                    }
                    
                    if ($this->filled('auto_accept_threshold') && $this->auto_accept_threshold < 0.01) {
                        $validator->errors()->add('auto_accept_threshold', 'Auto-accept threshold must be at least $0.01.');
                    }
                    
                    // Auto-accept threshold should be higher than minimum offer (only if minimum offer is provided)
                    if ($this->filled('auto_accept_threshold') && $this->filled('minimum_offer')) {
                        if ($this->auto_accept_threshold <= $this->minimum_offer) {
                            $validator->errors()->add('auto_accept_threshold', 'Auto-accept threshold should be higher than minimum offer.');
                        }
                    }
                }
            }
            
            // Bidding validation
            if ($this->boolean('enable_bidding')) {
                if (!$this->filled('starting_bid')) {
                    $validator->errors()->add('starting_bid', 'Starting bid is required when bidding is enabled.');
                }
                
                if ($this->filled('starting_bid') && $this->starting_bid < 0.01) {
                    $validator->errors()->add('starting_bid', 'Starting bid must be at least $0.01.');
                }
                
                // Reserve price validation
                if ($this->filled('reserve_price') && $this->reserve_price < 0.01) {
                    $validator->errors()->add('reserve_price', 'Reserve price must be at least $0.01.');
                }
                
                // Reserve price should be at least the starting bid (only if both are provided)
                if ($this->filled('reserve_price') && $this->filled('starting_bid')) {
                    if ($this->reserve_price < $this->starting_bid) {
                        $validator->errors()->add('reserve_price', 'Reserve price should be at least the starting bid amount.');
                    }
                }
            }
        });
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'domain_name.required' => 'Domain name is required.',
            'domain_name.regex' => 'Domain name can only contain letters, numbers, and hyphens.',
            'domain_extension.required' => 'Domain extension is required.',
            'asking_price.required' => 'Asking price is required.',
            'asking_price.min' => 'Asking price must be at least $0.01.',
            'asking_price.max' => 'Asking price cannot exceed $9,999,999.99.',
            'expiry_date.after' => 'Expiry date must be after registration date.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'has_website' => $this->boolean('has_website'),
            'has_traffic' => $this->boolean('has_traffic'),
            'premium_domain' => $this->boolean('premium_domain'),
            'enable_buy_now' => $this->boolean('enable_buy_now'),
            'enable_offers' => $this->boolean('enable_offers'),
            'auto_accept_offers' => $this->boolean('auto_accept_offers'),
            'enable_bidding' => $this->boolean('enable_bidding'),
            'auto_extend' => $this->boolean('auto_extend'),
        ]);
    }
}
