{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
    <channel>
        <title>{{ $storeName }}</title>
        <link>{{ $storeUrl }}</link>
        <description>Product Feed for {{ $storeName }}</description>
        @foreach($products as $product)
            @php
                $imageUrl = count($product->images) > 0 ? asset('storage/' . $product->images[0]->path) : asset('images/placeholder.jpg');
                $description = strip_tags($product->description ?: $product->short_description ?: $product->name);
                $description = mb_substr($description, 0, 4900); // 5000 chars limit
                $description = htmlspecialchars($description, ENT_XML1, 'UTF-8');
                $title = htmlspecialchars($product->name, ENT_XML1, 'UTF-8');
                $brandName = htmlspecialchars($product->brand ? $product->brand->name : $storeName, ENT_XML1, 'UTF-8');
            @endphp
            <item>
                <g:id>{{ $product->id }}</g:id>
                <g:title>{{ $title }}</g:title>
                <g:description>{{ $description }}</g:description>
                <g:link>{{ route('storefront.product', $product->slug) }}</g:link>
                <g:image_link>{{ $imageUrl }}</g:image_link>
                <g:brand>{{ $brandName }}</g:brand>
                <g:condition>new</g:condition>
                <g:availability>in stock</g:availability>
                @if($product->compare_price_display > $product->selling_price)
                    <g:price>{{ number_format($product->compare_price_display, 2, '.', '') }} BDT</g:price>
                    <g:sale_price>{{ number_format($product->selling_price, 2, '.', '') }} BDT</g:sale_price>
                @else
                    <g:price>{{ number_format($product->selling_price, 2, '.', '') }} BDT</g:price>
                @endif
                @if($product->category)
                    <g:product_type>{{ htmlspecialchars($product->category->name, ENT_XML1, 'UTF-8') }}</g:product_type>
                @endif
            </item>
        @endforeach
    </channel>
</rss>
