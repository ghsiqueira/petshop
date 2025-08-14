<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBusinessHoursRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole('petshop') && auth()->user()->petshop;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $rules = [];

        // Regras para cada dia da semana
        foreach ($days as $day) {
            $rules[$day . '_open'] = 'required_if:' . $day . '_enabled,on|date_format:H:i';
            $rules[$day . '_close'] = [
                'required_if:' . $day . '_enabled,on',
                'date_format:H:i',
                'after:' . $day . '_open'
            ];
        }

        // Regras gerais
        $rules['slot_duration'] = 'required|integer|min:15|max:120';
        $rules['advance_booking_days'] = 'required|integer|min:1|max:365';
        $rules['lunch_break_start'] = 'nullable|date_format:H:i';
        $rules['lunch_break_end'] = [
            'nullable',
            'date_format:H:i',
            'after:lunch_break_start'
        ];

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            '*.required_if' => 'Este campo é obrigatório quando o dia está habilitado.',
            '*.date_format' => 'O formato do horário deve ser HH:MM (ex: 08:30).',
            '*.after' => 'O horário de fechamento deve ser depois do horário de abertura.',
            'slot_duration.min' => 'A duração mínima do slot é 15 minutos.',
            'slot_duration.max' => 'A duração máxima do slot é 120 minutos.',
            'advance_booking_days.min' => 'Deve permitir pelo menos 1 dia de antecedência.',
            'advance_booking_days.max' => 'Não pode ser mais que 365 dias de antecedência.',
            'lunch_break_end.after' => 'O fim do almoço deve ser depois do início.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'monday_open' => 'abertura da segunda-feira',
            'monday_close' => 'fechamento da segunda-feira',
            'tuesday_open' => 'abertura da terça-feira',
            'tuesday_close' => 'fechamento da terça-feira',
            'wednesday_open' => 'abertura da quarta-feira',
            'wednesday_close' => 'fechamento da quarta-feira',
            'thursday_open' => 'abertura da quinta-feira',
            'thursday_close' => 'fechamento da quinta-feira',
            'friday_open' => 'abertura da sexta-feira',
            'friday_close' => 'fechamento da sexta-feira',
            'saturday_open' => 'abertura do sábado',
            'saturday_close' => 'fechamento do sábado',
            'sunday_open' => 'abertura do domingo',
            'sunday_close' => 'fechamento do domingo',
            'slot_duration' => 'duração do slot',
            'advance_booking_days' => 'dias de antecedência',
            'lunch_break_start' => 'início do almoço',
            'lunch_break_end' => 'fim do almoço',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Verificar se pelo menos um dia está habilitado
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            $hasEnabledDay = false;

            foreach ($days as $day) {
                if ($this->has($day . '_enabled')) {
                    $hasEnabledDay = true;
                    break;
                }
            }

            if (!$hasEnabledDay) {
                $validator->errors()->add('general', 'Pelo menos um dia da semana deve estar habilitado.');
            }

            // Verificar se o horário de almoço está dentro do expediente
            if ($this->lunch_break_start && $this->lunch_break_end) {
                $lunchStart = \Carbon\Carbon::createFromFormat('H:i', $this->lunch_break_start);
                $lunchEnd = \Carbon\Carbon::createFromFormat('H:i', $this->lunch_break_end);

                foreach ($days as $day) {
                    if ($this->has($day . '_enabled') && $this->filled($day . '_open') && $this->filled($day . '_close')) {
                        $dayOpen = \Carbon\Carbon::createFromFormat('H:i', $this->input($day . '_open'));
                        $dayClose = \Carbon\Carbon::createFromFormat('H:i', $this->input($day . '_close'));

                        if ($lunchStart->lt($dayOpen) || $lunchEnd->gt($dayClose)) {
                            $validator->errors()->add('lunch_break_start', 'O horário de almoço deve estar dentro do expediente de todos os dias habilitados.');
                            break;
                        }
                    }
                }
            }
        });
    }
}
