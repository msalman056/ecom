
@component('mail::message')
# 🎉 New Product Alert!

A new product has been added to our shop:

**{{ $product->name }}**  
Price: ${{ $product->regular_price }}


@component('mail::button', ['url' => route('product.show', $product->id)])View Product
@endcomponent

Thanks,  
{{ config('app.name') }}
@endcomponent
