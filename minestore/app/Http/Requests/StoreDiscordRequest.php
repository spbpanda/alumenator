<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiscordRequest extends FormRequest
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
            'discord_guild_id' => ['nullable', 'string'],
            'discord_url' => ['nullable', 'string'],
            'discord_bot_token' => ['nullable', 'string'],
            'discord_bot_enabled' => ['sometimes'],
            'webhook_url' => ['nullable', 'string'],
        ];
    }
}
