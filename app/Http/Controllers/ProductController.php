<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductController extends Controller
{
    public function index(Request $request): JsonResource
    {
        $productQuery = Product::query();

        $productQuery->with('properties');

        if ($request->has('properties')) {
            $properties = $request->get('properties');

            foreach ($properties as $propertyID => $values) {
                $productQuery->whereHas('properties', function (Builder $query) use ($propertyID, $values) {
                    $query->where(function ($query) use ($propertyID, $values) {
                        $query->where('property_id', $propertyID);
                        $query->whereIn('value', $values);
                    });
                });
            }
        }

        $paginated = $productQuery->paginate(40);

        return ProductResource::collection($paginated);
    }
}
