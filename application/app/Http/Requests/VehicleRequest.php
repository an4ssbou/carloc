<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->is_active && auth()->user()->hasVerifiedEmail();
    }

    public function rules(): array
    {
        $rules = [
            'brand' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:1990|max:'.now()->year,
            'mileage' => 'nullable|integer|min:0',
            'color' => 'required|string',
            'condition' => 'required|in:new,good,used',
            'seats' => 'required|integer|min:1|max:20',
            'fuel_type' => 'required|in:gasoline,diesel,electric,hybrid',
            'transmission' => 'required|in:manual,automatic',
            'description' => 'nullable|string',
            'price_per_day' => 'required|numeric|min:0',
            'price_week' => 'nullable|numeric|min:0',
            'price_two_weeks' => 'nullable|numeric|min:0',
            'price_month' => 'nullable|numeric|min:0',
            'has_gps' => 'boolean',
            'is_featured' => 'boolean',
            'delete_photo_ids' => 'nullable|array',
            'delete_photo_ids.*' => 'integer|exists:vehicle_photos,id',
            'photos' => 'nullable|array|max:10',
            'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ];

        if ($this->isMethod('POST')) {
            $rules['registration_number'] = 'required|string|unique:vehicles';
            $rules['photos'] = 'required|array|min:1|max:10';
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['status'] = 'required|in:available,unavailable';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'brand.required' => 'La marque du véhicule est obligatoire.',
            'brand.max' => 'La marque ne peut pas dépasser 100 caractères.',
            'model.required' => 'Le modèle du véhicule est obligatoire.',
            'model.max' => 'Le modèle ne peut pas dépasser 100 caractères.',
            'year.required' => 'L\'année est obligatoire.',
            'year.integer' => 'L\'année doit être un nombre entier.',
            'year.min' => 'L\'année doit être supérieure ou égale à 1990.',
            'year.max' => 'L\'année ne peut pas dépasser '.now()->year.'.',
            'registration_number.required' => 'Le numéro d\'immatriculation est obligatoire.',
            'registration_number.unique' => 'Ce numéro d\'immatriculation est déjà utilisé.',
            'mileage.integer' => 'Le kilométrage doit être un nombre entier.',
            'mileage.min' => 'Le kilométrage ne peut pas être négatif.',
            'color.required' => 'La couleur est obligatoire.',
            'condition.required' => 'L\'état du véhicule est obligatoire.',
            'condition.in' => 'L\'état doit être : neuf, bon état ou usagé.',
            'seats.required' => 'Le nombre de places est obligatoire.',
            'seats.integer' => 'Le nombre de places doit être un entier.',
            'seats.min' => 'Le véhicule doit avoir au moins 1 place.',
            'seats.max' => 'Le véhicule ne peut pas avoir plus de 20 places.',
            'fuel_type.required' => 'Le type de carburant est obligatoire.',
            'fuel_type.in' => 'Le carburant doit être : essence, diesel, électrique ou hybride.',
            'transmission.required' => 'La transmission est obligatoire.',
            'transmission.in' => 'La transmission doit être manuelle ou automatique.',
            'price_per_day.required' => 'Le prix par jour est obligatoire.',
            'price_per_day.numeric' => 'Le prix par jour doit être un nombre.',
            'price_per_day.min' => 'Le prix par jour ne peut pas être négatif.',
            'price_week.numeric' => 'Le prix par semaine doit être un nombre.',
            'price_week.min' => 'Le prix par semaine ne peut pas être négatif.',
            'price_two_weeks.numeric' => 'Le prix pour 2 semaines doit être un nombre.',
            'price_two_weeks.min' => 'Le prix pour 2 semaines ne peut pas être négatif.',
            'price_month.numeric' => 'Le prix par mois doit être un nombre.',
            'price_month.min' => 'Le prix par mois ne peut pas être négatif.',
            'status.required' => 'Le statut du véhicule est obligatoire.',
            'status.in' => 'Le statut doit être disponible ou indisponible.',
            'photos.required' => 'Veuillez ajouter au moins une photo.',
            'photos.min' => 'Veuillez ajouter au moins une photo.',
            'photos.max' => 'Vous ne pouvez pas uploader plus de 10 photos.',
            'photos.*.image' => 'Chaque fichier doit être une image.',
            'photos.*.mimes' => 'Les photos doivent être au format JPG, PNG ou WEBP.',
            'photos.*.max' => 'Chaque photo ne doit pas dépasser 2MB.',
        ];
    }
}
